<?php

namespace App\Logging;

use Exception;
use Illuminate\Support\Str;
use Monolog\Formatter\LineFormatter;
use SimpleXMLElement;

class LogFormatter extends LineFormatter
{
    const SIMPLE_FORMAT = "%message%\n";
    const DATE_FORMAT = 'Y-m-d H:i:s P';

    /**
     * IxFormatter constructor.
     *
     * @param string|null $sFormat
     * @param string|null $sDateFormat
     */
    public function __construct(string $sFormat = null, string $sDateFormat = null)
    {
        parent::__construct($sFormat ?? static::SIMPLE_FORMAT, $sDateFormat ?? static::DATE_FORMAT, false, true);
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record): string
    {
        $record['message'] = $this->anonymizeLogMessage($record['message']);
        return $this->shortenMessage(
            $this->replacePlaceholder(
                parent::format($record),
                $record
            ),
            $record
        );
    }

    /**
     * @param string $output
     * @param array $record
     * @return string
     */
    private function shortenMessage(string $output, array $record): string
    {
        if ($record['level'] < 300 && (
            !is_null(config('ixhelper.log.limit')) && config('ixhelper.log.limit') !== -1
        ) &&
            strlen($output) > config('ixhelper.log.limit')) {
            return Str::limit($output, config('ixhelper.log.limit')) . "\n";
        }
        return $output;
    }

    /**
     * @param string $output
     * @param array $record
     * @return string
     */
    private function replacePlaceholder(string $output, array $record): string
    {
        try {
            $oException = isset($record['context']) &&
            isset($record['context']['exception']) && (
                get_class($record['context']['exception']) === 'Exception' ||
                is_subclass_of($record['context']['exception'], 'Exception')
            ) ?
                $record['context']['exception'] : null;
            $aMessages = $this->getStackTrace($oException);
            foreach ($aMessages as $sKey => $sMessage) {
                $output = str_replace('[%' . $sKey . '%]', '[' . $sMessage . ']', $output);
            }
        } catch (Exception $e) {
            // explicitly write to error_log, as a crashing log formatter probably just
            // re-crashes the formatter again
            error_log('Error formatting log entry: ' . $e->getMessage());
        }
        return is_string($output) ? $output : '';
    }

    public function getStackTrace(Exception $oException = null)
    {
        global $logid;
        $logid = $logid ?? uniqid();
        //log entries coming from a fatal exception, need special care
        if (!is_null($oException)) {
            return [
                'file' => str_replace(base_path() . '/', '', $oException->getFile()),
                'line' => $oException->getLine(),
                'method' => ($oException->getTrace()[0]['function'] ?? ' < no method > '),
                'uuid' => $logid,
                'exception' => $oException->getMessage(),
            ];
        }
        $aDebugStack = debug_backtrace();
        array_shift($aDebugStack);
        $aReturn = [
            'file' => '',
            'line' => 0,
            'method' => '',
            'uuid' => '',
            'exception' => '',
        ];
        foreach ($aDebugStack as $iKey => $aDebug) {
            //filter invalid debug entries
            //skip all like laravel vendor classes
            //go into vendor/ix-intern though, otherwise the logging would be wrong
            if (!isset($aDebug['file']) ||
                (strpos($aDebug['file'], base_path('vendor')) !== false /*&& strpos($aDebug['file'],
                        base_path('vendor / ix - intern')) === false*/)) {
                continue;
            }

            return [
                'file' => str_replace(base_path() . '/', '', ($aDebug['file'] ?? '<no file>')),
                'line' => ($aDebug['line'] ?? '<no line>'),
                'method' => ($aDebugStack[$iKey + 1]['function'] ?? '<no method>'),
                'uuid' => $logid,
                'exception' => '',
            ];
        }

        return $aReturn;
    }

    /**
     * function checking for keywords, that trigger the obfuscation.
     * @param string $msg
     * @return string
     */
    private function anonymizeLogMessage(string $msg): string
    {
        // do not obfuscate logs, that do not contain json, xml or header authorization.
        if (strpos($msg, '{') != false || strpos($msg, '<') != false || strpos($msg, 'Authorization') != false ||
            strpos(strtolower($msg), 'array') != false) {
            // first of all lets check, if this log msg needs some kind of obfuscation.
            // if we find any keyword, we need to obfuscate the msg. dont forget to break; obfuscate() gets called once.
            foreach (Formatter::ANONYMIZE_KEYS as $keys) {
                if (strpos($msg, $keys) === false) {
                    continue;
                }
                return preg_replace_callback_array([
                    // Authorization header
                    '/(Authorization:.Basic.)([A-Za-z0-9+\/=]*)/' => [self::class, 'obfuscateBasicAuthHeader'],
                    // JSON obejcts
                    '/\{(?:[^{}]|(?R))*\}/' => [self::class, 'obfuscateJson'],
                    // arrays
                    '/[aA]rray\s*\((?:[^()]|(?R))*\)/' => [self::class, 'obfuscateArray'],
                    // XML
                    '/<\?xml.*?>([\r\n]*?)<(.*?)>([\r\n]*?).*?([\r\n]*?)<\/.*>/' => [self::class, 'obfuscateXml']
                ], $msg) ?: $msg;
            }
        }
        return $msg;
    }

    private function obfuscateBasicAuthHeader($aData)
    {
        if (isset($aData[1])) {
            return $aData[1] . '***';
        } else {
            return $aData[0];
        }
    }

    /**
     * @param array $aData
     * @return string
     */
    private function obfuscateJson(array $aData): string
    {
        $jsonString = $this->replaceNewlines($aData[0]);
        $jsonArray = json_decode($jsonString, true);
        if (is_null($jsonArray)) {
            return $aData[0];
        }
        return json_encode(Formatter::anonymizeArray($jsonArray));
    }

    /**
     * @param array $aData
     * @return string
     */
    private function obfuscateArray(array $aData): string
    {
        $array_string = $aData[0];
        if (strpos($array_string, 'array') === 0) {
            $array_string = str_replace(["array ", ",", "'"], ["Array\n", "", ""], $array_string);
        }
        $array = Formatter::printrReverse($array_string);
        if (is_null($array)) {
            return $aData[0];
        }
        return print_r(Formatter::anonymizeArray($array), 1);
    }

    /**
     * @param array $aData
     * @return string
     */
    private function obfuscateXml(array $aData): string
    {
        $rawxmlstr = $aData[0];
        if (!function_exists('simplexml_load_string') || !function_exists('libxml_use_internal_errors')) {
            return $rawxmlstr;
        }
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($rawxmlstr, "SimpleXMLElement", 0, "", true);
        $errors = libxml_get_errors();
        if (empty($errors)) {
            $json = json_encode($xml);
            if (is_null($json)) {
                return $rawxmlstr;
            }
            $jsonArray = json_decode($json, true);
            $json = Formatter::anonymizeArray($jsonArray);
            $first_key = $xml->getName();
            $xml_data = new SimpleXMLElement("<{$first_key}/>");
            Formatter::arrayToXml($json, $xml_data);
            $msg = $xml_data->asXML();
            return is_string($msg) ? $msg : $rawxmlstr;
        } else {
            // could not load XML from string. doing regular search and replace!
            $obfuscatedMsg = $rawxmlstr;
            foreach (Formatter::ANONYMIZE_KEYS as $key) {
                if (strpos($rawxmlstr, "<{$key}>") != false) {
                    $search = "/<{$key}>(.*)<\/?{$key}>/";
                    $replace = "<{$key}>***</{$key}>";
                    $obfuscatedMsg = preg_replace($search, $replace, $rawxmlstr, 1);
                }
            }
            libxml_clear_errors();
            return $obfuscatedMsg;
        }
    }
}
