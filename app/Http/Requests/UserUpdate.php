<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdate extends FormRequest
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
        $user = $this->route('user');
        return [
            //
            'name'=>'required',
            'email'=>['required', Rule::unique('users')->ignore($user->id), 'email:rfc,dns,strict,filter'],
            'mobile_number'=>['required', Rule::unique('users')->ignore($user->id),  'digits:10'],
            'category_id'=>'required',
            'hobby'=>'required',
            'profile_pic'=>'image'
        ];
    }
}
