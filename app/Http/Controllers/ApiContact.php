<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\ApiContactCreateRequest;
use Domainrobot\Lib\DomainrobotException;
use Domainrobot\Model\ContactTypeConstants;
use Domainrobot\Model\Contact;
use Domainrobot\Model\ContactExtensions;
use Domainrobot\Model\ContactGeneralExtensions;
use Domainrobot\Model\ContactItExtensions;
use Domainrobot\Model\ContactReference;
use Domainrobot\Model\NicMember;
use Domainrobot\Model\Query;
use Domainrobot\Model\QueryFilter;
use Domainrobot\Model\QueryView;
use Domainrobot\Model\ObjectJob;

class ApiContact extends Controller
{
    /*
    Create Example Request

    POST /api/contact
    {
      "type": "PERSON",
      "alias": "SOMEALIAS",
      "city": "Regensburg",
      "country": "DE",
      "state": "",
      "street_no": "Johanna-Dachs-Straße 55",
      "address_info": "ADDITIONAL INFO",
      "pcode": "93055",
      "fname": "SOME FIRSTNAME",
      "lname": "SOME LASTNAME",
      "email": "SOME@MAIL.COM",
      "phone": "",
      "commment": "SOME COMMENTS"
    }
    */

    /**
     * Create an Contact
     * 
     * @param  ApiContactCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ApiContactCreateRequest $request)
    {
        $domainrobot = app('Domainrobot');

        try {

            $contact = new Contact();
            $contact->setType($request->type);

            if ( $request->type === ContactTypeConstants::ORG ) {
                $contact->setOrganization($request->organization); 
            }

            $contact->setAlias($request->alias ?? '');

            $contact->setCity($request->city);
            $contact->setCountry($request->country);
            $contact->setState($request->state ?? '');
            $contact->setAddress([
                $request->street_no ?? '',
                $request->address_info ?? ''
            ]);
            $contact->setPcode($request->pcode);
            $contact->setFname($request->fname ?? '');
            $contact->setLname($request->lname);
            $contact->setEmail($request->email ?? '');
            $contact->setPhone($request->phone ?? '');
            $contact->setFax($request->fax ?? '');
            $contact->setComment($request->comment ?? '');

            // set nic references if desired
            $contact->setNicRef([
                new ContactReference([
                    'nic' => new NicMember([
                        'label' => 'tld' // e.g. de,com,cloud etc.
                    ])
                ])
            ]);

            // Overview of all Contact Extensions
            // https://help.internetx.com/display/APIXMLEN/Contact+Extensions
            $contact->setExtensions(new ContactExtensions([
                'general' => new ContactGeneralExtensions([
                    'gender' => 'MALE'
                ]),
                'it' => new ContactItExtensions([
                    'entityType' => 1 // Italian and foreign natural persons
                ])
            ]));

            $job = $domainrobot->contact->create($contact);

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

    GET /api/contact/{id}
    */

    /**
     * Get an Contact Info
     * 
     * @param  integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function info($id) 
    {
        $domainrobot = app('Domainrobot');

        try {
            $contact = $domainrobot->contact->info($id);
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

    PUT /api/contact/{id}
    {
      "alias": "NEWALIAS",
      "city": "Regensburg",
      "country": "DE",
      "state": "Bayern",
      "street_no": "Johanna-Dachs-Straße 55",
      "address_info": "ADDITIONAL INFO",
      "pcode": "93055",
      "fname": "SOME FIRSTNAME",
      "email": "SOME@MAIL.COM",
      "phone": "",
      "commment": "SOME COMMENTS"
    }
    */

    /**
     * Update an existing Contact
     * 
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $domainrobot = app('Domainrobot');

        try {

            $contact = $domainrobot->contact->info($request->id);

            if ( isset($request->alias) ) {
                $contact->setAlias($request->alias);
            }

            if ( isset($request->city) ) {
                $contact->setCity($request->city);
            }

            if ( isset($request->country) ) {
                $contact->setCountry($request->country);
            }
            
            if ( isset($request->state) ) {
                $contact->setState($request->state);
            }
            
            if ( isset($request->street_no) ) {
                $contact->setAddress([
                    $request->street_no,
                    $request->address_info ?? ''
                ]);
            }

            if ( isset($request->pcode) ) {
                $contact->setPcode($request->pcode);
            }

            if ( isset($request->fname) ) {
                $contact->setFname($request->fname);
            }

            if ( isset($request->lname) ) {
                $contact->setLname($request->lname);
            }

            if ( isset($request->email) ) {
                $contact->setEmail($request->email);
            }

            if ( isset($request->phone) ) {
                $contact->setPhone($request->phone);
            }

            if ( isset($request->fax) ) {
                $contact->setFax($request->fax);
            }

            if ( isset($request->comment) ) {
                $contact->setComment($request->comment);
            }

            // Overview of all Contact Extensions
            // https://help.internetx.com/display/APIXMLEN/Contact+Extensions
            $contact->setExtensions(new ContactExtensions([
                'it' => new ContactItExtensions([
                    'entityType' => 1 // Italian and foreign natural persons
                ])
            ]));

            $job = $domainrobot->contact->update($contact);

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

    DELETE /api/contact/{id}
    */

    /**
     * Get an Contact Info
     * 
     * @param  integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $domainrobot = app('Domainrobot');

        try {
            $job = $domainrobot->contact->delete($id);
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

    POST /api/contact/_search
    {
      "filters": [
        {
          "key": "fname",
          "value": "First%",
          "operator": "LIKE"
        },
        {
          "key": "lname",
          "value": "%name%",
          "operator": "NOT_LIKE"
        }
      ]
    }
    */

    /**
     * List Contact
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

            $list = $domainrobot->contact->list($query);

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
