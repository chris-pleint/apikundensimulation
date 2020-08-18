<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\ApiUserRequest;
use Domainrobot\Lib\DomainrobotException;
use Domainrobot\Model\Query;
use Domainrobot\Model\QueryFilter;
use Domainrobot\Model\QueryView;
use Domainrobot\Model\User;

class ApiUser extends Controller
{
    /*
    Read Example Request

    GET /api/user/{username}/{context}
    */

    /**
     * Get an User Info
     * 
     * @param  ApiUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(ApiUserRequest $request)
    {
        $domainrobot = app('Domainrobot');

        try {
            // Domainrobot\Model\User
            $user = $domainrobot->user->info($request->username, $request->context);
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

    POST /api/user/_search
    {
      "filters": [
        {
          "key": "status",
          "value": "2",
          "operator": "EQUAL"
        }
      ]
    }
    */

    /**
     * List User
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

            $list = $domainrobot->user->list($query);

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
