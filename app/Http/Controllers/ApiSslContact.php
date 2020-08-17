<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\ApiSslContactRequest;
use App\Http\Requests\ApiSslContactCreateRequest;
use Domainrobot\Lib\DomainrobotException;
use Domainrobot\Model\SslContact;
use Domainrobot\Model\Query;
use Domainrobot\Model\QueryFilter;
use Domainrobot\Model\QueryView;
use Domainrobot\Model\ObjectJob;

class ApiSslContact extends SslController
{
    /*
    Create Example Request

    POST /api/sslcontact
    {
      "organization": "InterNetX GmbH",
      "city": "Regensburg",
      "state": "Bavaria",
      "country": "DE",
      "street_no": "Johanna-Dachs-Straße 55",
      "address_info": "Second Floor",
      "pcode": "93055",
      "title": "Mr.",
      "fname": "John",
      "lname": "Doe",
      "email": "john.doe@internetx.com",
      "phone": "+49 123 45678",
      "fax": "+49 123 45679"
    }
    */

    /**
     * Create an SslContact
     * 
     * @param  ApiSslContactCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ApiSslContactCreateRequest $request)
    {
        $domainrobot = $this->getDomainrobot();
        
        try {

            $sslContact = new SslContact();

            $sslContact->setOrganization($request->organization ?? '');
            $sslContact->setCity($request->city ?? '');
            $sslContact->setState($request->state ?? '');
            $sslContact->setCountry($request->country ?? '');
            $sslContact->setAddress([
                $request->street_no ?? '',
                $request->address_info ?? ''
            ]);
            $sslContact->setPcode($request->pcode ?? '');
            $sslContact->setTitle($request->title ?? '');
            $sslContact->setFname($request->fname ?? '');
            $sslContact->setLname($request->lname ?? '');
            $sslContact->setEmail($request->email ?? '');
            $sslContact->setPhone($request->phone ?? '');
            $sslContact->setFax($request->fax ?? '');

            $job = $domainrobot->sslContact->create($sslContact);

        } catch (DomainrobotException $exception) {
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

    GET /api/sslcontact/{id}
    */

    /**
     * Get an SslContact Info
     * 
     * @param  ApiSslContactRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(ApiSslContactRequest $request) 
    {
        $domainrobot = $this->getDomainrobot();

        try {
            $sslContact = $domainrobot->sslContact->info($request->id);
        } catch (DomainrobotException $exception) {
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

    PUT /api/sslcontact/{id}
    {
      "address_info": "Third Floor",
      "title": "Mrs.",
      "fname": "Jane",
      "email": "jane.doe@internetx.com",
      "phone": "+49 321 45678"
    }
    */

    /**
     * Update an existing Contact
     * 
     * @param  ApiSslContactRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ApiSslContactRequest $request)
    {
        $domainrobot = $this->getDomainrobot();

        try {

            $sslContact = $domainrobot->sslContact->info($request->id);

            if ( isset($request->organization) ) {
                $sslContact->setOrganization($request->organization);
            }

            if ( isset($request->city) ) {
                $sslContact->setCity($request->city);
            }

            if ( isset($request->state) ) {
                $sslContact->setState($request->state);
            }

            if ( isset($request->country) ) {
                $sslContact->setCountry($request->country);
            }
            
            if ( isset($request->street_no) ) {
                $sslContact->setAddress([
                    $request->street_no,
                    $request->address_info ?? ''
                ]);
            }

            if ( isset($request->pcode) ) {
                $sslContact->setPcode($request->pcode);
            }

            if ( isset($request->title) ) {
                $sslContact->setTitle($request->title);
            }

            if ( isset($request->fname) ) {
                $sslContact->setFname($request->fname);
            }

            if ( isset($request->lname) ) {
                $sslContact->setLname($request->lname);
            }

            if ( isset($request->email) ) {
                $sslContact->setEmail($request->email);
            }

            if ( isset($request->phone) ) {
                $sslContact->setPhone($request->phone);
            }

            if ( isset($request->fax) ) {
                $sslContact->setFax($request->fax);
            }

            $job = $domainrobot->sslContact->update($sslContact);

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

    DELETE /api/sslcontact/{id}
    */

    /**
     * Delete an existing SslContact
     * 
     * @param  ApiSslContactRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(ApiSslContactRequest $request)
    {
        $domainrobot = $this->getDomainrobot();

        try {
            $job = $domainrobot->sslContact->delete($request->id);
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

    POST /api/sslcontact/_search
    {
      "filters": [
        {
          "key": "id",
          "value": "2110",
          "operator": "GREATER"
        },
        {
          "key": "organization",
          "value": "InterNetX%",
          "operator": "LIKE"
        }
      ]
    }
    */

    /**
     * List SslContact
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

            $list = $domainrobot->sslContact->list($query);

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

