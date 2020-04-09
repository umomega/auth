<?php

namespace Umomega\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilePassword extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_password' => ['required', 'min:8', function($attribute, $value, $fail) {
                if ( ! \Hash::check($value, $this->user()->password)) {
                    $fail(__('auth::users.old_password_didnt_match'));
                }
            }],
            'password' => 'required|min:8'
        ];
    }
}
