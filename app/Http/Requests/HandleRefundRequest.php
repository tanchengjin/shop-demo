<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HandleRefundRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'agree'=>['required','boolean'],
            'reason'=>['required_if:agree,false']
        ];
    }
}
