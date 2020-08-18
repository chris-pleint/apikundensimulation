<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\CsrUtility;
use App\Http\Requests\ApiCertificateCreateRequest;
use App\Http\Requests\ApiCertificatePrepareOrderRequest;
use App\Http\Utilities;
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

class ApiCertificate extends Controller
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
        $domainrobot = app('DomainrobotSSL');

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

            $csrUtility = new CsrUtility();

            $csr = $csrUtility->generateCsr($request->name);
    
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
        $domainrobot = app('DomainrobotSSL');

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

            $csrUtility = new CsrUtility();

            $csr = $csrUtility->generateCsr($request->name);
    
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
        $domainrobot = app('DomainrobotSSL');

        try {

            $certificateData = new CertificateData();
            $certificateData->setName($request->name);
    
            $certificateData->setProduct('SSL123');

            $csrUtility = new CsrUtility();

            $csr = $csrUtility->generateCsr($request->name);

            $certificateData->setPlain(trim($csr));

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

    /*
    Read Example Request

    GET /api/certificate/{id}
    */

    /**
     * Get an Certificate Info
     * 
     * @param  integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function info($id) 
    {
        $domainrobot = app('DomainrobotSSL');

        try {
            $certificate = $domainrobot->certificate->info($id);
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
     * @param  integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) 
    {
        $domainrobot = app('DomainrobotSSL');

        try {
            $certificate = $domainrobot->certificate->delete($id);
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
        $domainrobot = app('DomainrobotSSL');

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
