<?php

namespace Umomega\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUser extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->route('user'))],
            'roles_list' => 'nullable|array',
            'permissions_list' => 'nullable|array',
            'locale' => 'required'
        ];
    }
}
