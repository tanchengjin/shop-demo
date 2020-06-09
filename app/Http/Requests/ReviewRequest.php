<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reviews'=>['required','array'],
            'reviews.*.id'=>['required'],
            'reviews.*.rating'=>['required','between:1,5'],
            'reviews.*.review'=>['required'],
        ];
    }

    public function attributes()
    {
        return [
            'reviews.*.review'=>'评价'
        ];
    }
}
