<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiSslContactCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'city' => 'required',
            'state' => 'required',
            'title' => 'required',
            'street_no' => 'required',
            'fname' => 'required',
            'lname' => 'required',
            'pcode' => 'required'
        ];
    }
}