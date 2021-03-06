<?php

namespace Umomega\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRole extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255|unique:roles,name',
            'permissions_list' => 'nullable|array'
        ];
    }
}