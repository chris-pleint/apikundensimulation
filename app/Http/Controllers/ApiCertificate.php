<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\ApiCertificateRequest;
use App\Http\Requests\ApiCertificateCreateRequest;
use App\Http\Requests\ApiCertificatePrepareOrderRequest;
use Domainrobot\Lib\DomainrobotException;
use Domainrobot\Model\AuthMethodConstants;
use Domainrobot\Model\CertAuthentication;
use Domainrobot\Model\Certificate;
use Domainrobot\Model\CertificateData;
use Domainrobot\Model\SslContact;
use Domainrobot\Model\TimePeriod;
use Domainrobot\Model\TimeUnitConstants;
use Domainrobot\Model\Query;
use Domainrobot\Model\QueryFilter;
use Domainrobot\Model\QueryView;

class ApiCertificate extends SslController
{
    /*
    Create Example Request

    POST /api/certificate
    {
      "name": "domainname.com",
      "sslcontact_id": "2112"
    }
    */

    /**
     * Create a Certificate
     * 
     * @param  ApiCertificateCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ApiCertificateCreateRequest $request)
    {
        $domainrobot = $this->getDomainrobot();

        try {

            $certificate = new Certificate();
            $certificate->setName($request->name);
    
            $sslContact = $domainrobot->sslContact->info($request->sslcontact_id);
    
            $certificate->setAdminContact($sslContact);
            $certificate->setTechnicalContact($sslContact);
    
            $certificate->setProduct('SSL123');
            $certificate->setLifetime(new TimePeriod([
                'unit' => TimeUnitConstants::MONTH,
                'period' => 12
            ]));
    
            $certificate->setAuthentication(new CertAuthentication([
                'method' => AuthMethodConstants::FILE
            ]));

            $csr = $this->generateCsr($request->name);
    
            $certificate->setCsr($csr);

            $job = $domainrobot->certificate->create($certificate);

        } catch ( DomainrobotException $exception ) {
            return response()->json(
                $exception->getError(),
                $exception->getStatusCode()
            );
        }
        
        return response()->json(
            $domainrobot::getLastDomainrobotResult()->getResult(),
            $domainrobot::getLastDomainrobotResult()->getStatusCode()
        );
    }

    /*
    Create Example Request

    POST /api/certificate
    {
      "name": "domainname.com",
      "sslcontact_id": "2112"
    }
    */

    /**
     * Create a Certificate in realtime
     * 
     * @param  ApiCertificateCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createRealtime(ApiCertificateCreateRequest $request)
    {
        $domainrobot = $this->getDomainrobot();

        try {

            $certificate = new Certificate();
            $certificate->setName($request->name);
    
            $sslContact = $domainrobot->sslContact->info($request->sslcontact_id);
    
            $certificate->setAdminContact($sslContact);
            $certificate->setTechnicalContact($sslContact);
    
            $certificate->setProduct('SSL123');
            $certificate->setLifetime(new TimePeriod([
                'unit' => TimeUnitConstants::MONTH,
                'period' => 12
            ]));
    
            $certificate->setAuthentication(new CertAuthentication([
                'method' => AuthMethodConstants::FILE
            ]));

            $csr = $this->generateCsr($request->name);
    
            $certificate->setCsr($csr);

            $job = $domainrobot->certificate->realtime($certificate);

        } catch ( DomainrobotException $exception ) {
            return response()->json(
                $exception->getError(),
                $exception->getStatusCode()
            );
        }
        
        return response()->json(
            $domainrobot::getLastDomainrobotResult()->getResult(),
            $domainrobot::getLastDomainrobotResult()->getStatusCode()
        );
    }

    /*
    Create Example Request

    POST /api/certificate
    {
      "name": "domainname.com"
    }
    */

    /**
     * Prepare a order of a Certificate
     * 
     * @param  ApiCertificatePrepareOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function prepareOrder(ApiCertificatePrepareOrderRequest $request)
    {
        $domainrobot = $this->getDomainrobot();

        try {

            $certificateData = new CertificateData();
            $certificateData->setName($request->name);
    
            $certificateData->setProduct('SSL123');

            $csr = $this->generateCsr($request->name);

            $certificateData->setPlain($csr);

            $job = $domainrobot->certificate->prepareOrder($certificateData);

        } catch ( DomainrobotException $exception ) {
            return response()->json(
                $exception->getError(),
                $exception->getStatusCode()
            );
        }
        
        return response()->json(
            $domainrobot::getLastDomainrobotResult()->getResult(),
            $domainrobot::getLastDomainrobotResult()->getStatusCode()
        );
    }

    /**
     * Generate an Certificate Signing Request (csr)
     * 
     * @param  string commonName
     * @return string $csrOut
     */
    protected function generateCsr($commonName) {

        $subject = [
            'commonName' => $commonName
        ];

        $privateKey = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'prime256v1'
        ]);

        $csr = openssl_csr_new($subject, $privateKey, [ 'digest_alg' => 'sha384' ]);

        openssl_csr_export($csr, $csrOut);

        return $csrOut;
    }

    /*
    Read Example Request

    GET /api/certificate/{id}
    */

    /**
     * Get an Certificate Info
     * 
     * @param  ApiCertificateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(ApiCertificateRequest $request) 
    {
        $domainrobot = $this->getDomainrobot();

        try {
            $certificate = $domainrobot->certificate->info($request->id);
        } catch ( DomainrobotException $exception ) {
            return response()->json(
                $exception->getError(),
                $exception->getStatusCode()
            );
        }
        
        return response()->json(
            $domainrobot::getLastDomainrobotResult()->getResult(),
            $domainrobot::getLastDomainrobotResult()->getStatusCode()
        );
    }

    /*
    Delete Example Request

    DELETE /api/certificate/{id}
    */

    /**
     * Delete an Certificate
     * 
     * @param  ApiCertificateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(ApiCertificateRequest $request) 
    {
        $domainrobot = $this->getDomainrobot();

        try {
            $certificate = $domainrobot->certificate->delete($request->id);
        } catch ( DomainrobotException $exception ) {
            return response()->json(
                $exception->getError(),
                $exception->getStatusCode()
            );
        }
        
        return response()->json(
            $domainrobot::getLastDomainrobotResult()->getResult(),
            $domainrobot::getLastDomainrobotResult()->getStatusCode()
        );
    }

    /**
     * List Certificates
     * 
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $domainrobot = $this->getDomainrobot();

        try {

            $filters = [];
            foreach ( $request->filters as $filter ) {
                // Overview of Permitted List Query Operators
                // https://help.internetx.com/display/APIXMLEN/List+Inquire#ListInquire-PermittedOperatorsforaListQuery
                $filters[] = new QueryFilter([
                    'key' => $filter['key'],
                    'value' => $filter['value'],
                    'operator' => $filter['operator']
                ]);
            }

            $query = new Query([
                'filters' => $filters,
                'view' => new QueryView([
                    'children' => 1,
                    'limit' => 10
                ])
            ]);

            $list = $domainrobot->certificate->list($query);

        } catch ( DomainrobotException $exception ) {
            return response()->json(
                $exception->getError(),
                $exception->getStatusCode()
            );
        }

        return response()->json(
            $domainrobot::getLastDomainrobotResult()->getResult(),
            $domainrobot::getLastDomainrobotResult()->getStatusCode()
        );
    }
}
