<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiDomainRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'nameservers' => 'array|min:2|max:4',
            'contact_id' => 'integer'
        ];
    }

    /**
     * Get all of the input and files for the request
     * add the id URL Parameter
     *
     * @param  array|mixed|null  $keys
     * @return array
     */
    public function all($keys = null)
    {
        $data = parent::all();

        if ( array_key_exists('name', $data) === FALSE ) {
            $data['name'] = $this->route('name');
        }

        return $data;
    }

}
