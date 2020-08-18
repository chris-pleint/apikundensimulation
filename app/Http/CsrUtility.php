<?php

namespace App\Http;

class CsrUtility
{
    /**
     * Generate an Certificate Signing Request (csr)
     * 
     * @param  string $commonName
     * @return string $csrOut
     */
    public function generateCsr($commonName) {

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
}