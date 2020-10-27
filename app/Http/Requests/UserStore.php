<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStore extends FormRequest
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
            //
            'name'=>'required',
            'email'=>'required|unique:users|email:rfc,dns,strict,filter',
            'mobile_number'=>'required|unique:users|digits:10',
            'category_id'=>'required',
            'hobby'=>'required',
            'profile_pic'=>'required|image',
        ];
    }
}
