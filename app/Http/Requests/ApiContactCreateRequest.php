<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiContactCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|in:PERSON,ORG,ROLE',
            'city' => 'required',
            'country' => 'required',
            'pcode' => 'required',
            'lname' => 'required'
        ];
    }
}
