<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAddressRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address'=>['required'],
            'zip'=>['required'],
            'contact_name'=>['required'],
            'contact_phone'=>['required'],
            'province'=>['required'],
            'city'=>['required'],
            'district'=>['required']
        ];
    }

    public function messages()
    {
        return [
            'zip.required'=>'请输入邮编',
            'province.required'=>'请输入省',
            'city.required'=>'请输入市',
            'district.required'=>'请输入区',
            'contact_name.required'=>'请输入联系人',
            'contact_phone.required'=>'请输入联系电话',
            'address.required'=>'请输入详细收货地址'
        ];
    }
}
