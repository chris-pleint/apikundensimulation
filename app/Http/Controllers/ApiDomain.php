<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\ApiDomainRequest;
use Domainrobot\Lib\DomainrobotAuth;
use Domainrobot\Lib\DomainrobotException;
use Domainrobot\Lib\DomainrobotHeaders;
use Domainrobot\Model\Domain;
use Domainrobot\Model\DomainRestore;
use Domainrobot\Model\NameServer;
use Domainrobot\Model\ObjectJob;
use Domainrobot\Model\Query;
use Domainrobot\Model\QueryFilter;
use Domainrobot\Model\QueryView;

class ApiDomain extends Controller
{
    /*
    Create Example Request

    POST /api/domain
    {
      "name": "sdk-autodns.com",
      "nameservers": [
	    "ns1.example.com",
		"ns2.example.com",
		"ns3.example.com",
		"ns4.example.com"
	  ],
      "contact_id": "23250350"
    }
    */

    /**
     * Create an Domain
     * 
     * @param  ApiDomainRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ApiDomainRequest $request)
    {
        $domainrobot = app('Domainrobot');

        try {

            // Domainrobot\Model\Domain
            $domain = new Domain();
            $domain->setName($request->name);

            $nameServers = [];
            foreach ( $request->nameservers as $nameServer ) {
                $nameServers[] = new NameServer([
                    "name" => $nameServer
                ]);
            }

            $domain->setNameServers($nameServers);

            // Domainrobot\Model\Contact
            $contact = $domainrobot->contact->info($request->contact_id);

            $domain->setAdminc($contact);
            $domain->setOwnerc($contact);
            $domain->setTechc($contact);
            $domain->setZonec($contact);

            // Domainrobot\Model\ObjectJob
            $job = $domainrobot->domain->create($domain);

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

    GET /api/domain/{name}
    */

    /**
     * Get an Domain Info
     * 
     * @param  ApiDomainRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(ApiDomainRequest $request) 
    {
        $domainrobot = app('Domainrobot');

        try {
            // Domainrobot\Model\Domain
            $domain = $domainrobot->domain->info($request->name);
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
    Update Example Request

    PUT /api/domain/{name}
    {
      "comment": "SOME DOMAIN COMMENT",
      "nameservers": [
	    "ns1.example.de",
		"ns2.example.de",
		"ns3.example.de"
      ],
      "confirm_owner_consent": true,
      "contact_id": "23249337",
      "generalRequestEmail": "request@mail.com"
    }
    */

    /**
     * Update Domain Data
     * 
     * @param  ApiDomainRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ApiDomainRequest $request)
    {
        $domainrobot = app('Domainrobot');

        try {

            // Domainrobot\Model\Domain
            $domain = $domainrobot->domain->info($request->name);

            if ( isset($request->comment) ) {
                $domain->setComment($request->comment);
            }

            if ( isset($request->nameservers) ) {

                $nameServers = [];
                foreach ( $request->nameservers as $nameServer ) {
                    $nameServers[] = new NameServer([
                        "name" => $nameServer
                    ]);
                }

                $domain->setNameServers($nameServers);
            }

            // Confirms the consent of the domainowner for the changes. 
            // Required for gTLDs and new gTLDs when changing the name, the email address or the organization of the domain owner.
            if ( isset($request->confirm_owner_consent) ) {
                $domain->setConfirmOwnerConsent($request->confirm_owner_consent);
            }
            
            if ( isset($request->contact_id) ) {

                $contact = $domainrobot->contact->info($request->contact_id);

                $domain->setAdminc($contact);
                $domain->setOwnerc($contact);
                $domain->setTechc($contact);
                $domain->setZonec($contact);
            }

            if ( isset($request->generalRequestEmail) ) {
                $domain->setGeneralRequestEmail($request->generalRequestEmail);
            }

            // Domainrobot\Model\ObjectJob
            $job = $domainrobot->domain->update($domain);

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
    List Example Request

    POST /api/domain/_search
    {
      "filters": [
        {
          "key": "name",
          "value": "%.de",
          "operator": "LIKE"
        },
	    {
          "key": "created",
          "value": "2020-08-10T00:00:00.000+0200",
          "operator": "GREATER"
        }
      ]  
    }
    */

    /**
     * List Domains
     * 
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $domainrobot = app('Domainrobot');

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

            $list = $domainrobot->domain->list($query);

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
    Create Authinfo1 Example Request

    POST /api/domain/{name}/_authinfo1
    */

    /**
     * Create Domain Authinfo1
     * 
     * @param  ApiDomainRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAuthinfo1(ApiDomainRequest $request)
    {
        $domainrobot = app('Domainrobot');

        try {
            // Domainrobot\Model\Domain
            $domain = $domainrobot->domain->createAuthinfo1($request->name);
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
    Delete Authinfo1 Example Request

    DELETE /api/domain/{name}/_authinfo1
    */

    /**
     * Delete Domain Authinfo1
     * 
     * @param  ApiDomainRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAuthinfo1(ApiDomainRequest $request)
    {
        $domainrobot = app('Domainrobot');

        try {
            $domainrobot->domain->deleteAuthinfo1($request->name);
        } catch  ( DomainrobotException $exception ) {
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
    Create Authinfo2 Example Request

    POST /api/domain/{name}/_authinfo2
    */

    /**
     * Create Domain Authinfo2
     * 
     * @param  ApiDomainRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAuthinfo2(ApiDomainRequest $request)
    {
        $domainrobot = app('Domainrobot');

        try {
            // Domainrobot\Model\Domain
            $domain = $domainrobot->domain->createAuthinfo2($request->name);
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
    Renew Domain Example Request

    PUT /api/domain/{name}/_renew
    */

    /**
     * Renew Domain
     * 
     * @param  ApiDomainRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function renew(ApiDomainRequest $request) 
    {
        $domainrobot = app('Domainrobot');

        try {

            // Domainrobot\Model\Domain
            $domain = $domainrobot->domain->info($request->name);

            // Domainrobot\Model\ObjectJob
            $job = $domainrobot->domain->renew($domain);

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
    Restore Domain Example Request

    PUT /api/domain/{name}/_restore
    {
      "nameservers": [
	    "ns1.example.de",
		"ns2.example.de"
      ],
      "contact_id": "23249337"
    }
    */

    /**
     * Restore Domain
     * 
     * @param  ApiDomainRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(ApiDomainRequest $request)
    {
        $domainrobot = app('Domainrobot');

        try {
            // Domainrobot\Model\DomainRestore
            $domain = new DomainRestore();
            $domain->setName($request->name);

            if ( isset( $request->nameservers ) ) {

                $nameServers = [];
                foreach ( $request->nameservers as $nameServer ) {
                    $nameServers[] = new NameServer([
                        "name" => $nameServer
                    ]);
                }
    
                $domain->setNameServers($nameServers);
            }

            if ( isset($request->contact_id) ) {

                $contact = $domainrobot->contact->info($request->contact_id);

                $domain->setAdminc($contact);
                $domain->setOwnerc($contact);
                $domain->setTechc($contact);
                $domain->setZonec($contact);
            }

            // Domainrobot\Model\ObjectJob
            $job = $domainrobot->domain->restore($domain);

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
    Domain Restore List Request

    POST /api/domain/restore/_search
    {
      "filters": [
        {
          "key": "name",
          "value": "%.de",
          "operator": "LIKE"
        }
      ]
    }
    */

    /**
     * Domain Restore List
     * 
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restoreList(Request $request)
    {
        $domainrobot = app('Domainrobot');

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

            $domainList = $domainrobot->domain->restoreList($query);

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
    Transfer Domain Example Request

    POST /api/domain/_transfer
    {
      "name": "",
      "nameservers": [
	    "ns1.example.de",
		"ns2.example.de"
      ],
      "contact_id": "23249337"
    }
    */

    /**
     * Transfer Domain
     * 
     * @param  ApiDomainRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transfer(ApiDomainRequest $request)
    {
        $domainrobot = app('Domainrobot');

        try {

            // Domainrobot\Model\Domain
            $domain = new Domain();
            $domain->setName($request->name);

            if ( isset( $request->nameservers ) ) {

                $nameServers = [];
                foreach ( $request->nameservers as $nameServer ) {
                    $nameServers[] = new NameServer([
                        "name" => $nameServer
                    ]);
                }
    
                $domain->setNameServers($nameServers);
            }

            if ( isset($request->contact_id) ) {

                // in this example we use an already existing user
                // but you can also create a completely new contact and use that one
                // for an example of contact creation please go to
                // https://github.com/InterNetX/php-domainrobot-sdk/blob/master/example/domain/DomainCreate.php
                $contact = $domainrobot->contact->info($request->contact_id);

                $domain->setAdminc($contact);
                $domain->setOwnerc($contact);
                $domain->setTechc($contact);
                $domain->setZonec($contact);
            }

            // this is just the bare minimum configuration for a transfer
            // please refer https://help.internetx.com/display/APIXMLEN/JSON+Technical+Documentation
            // for additional configuration options

            // Domainrobot\Model\ObjectJob
            $job = $domainrobot->domain->transfer($domain);

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
