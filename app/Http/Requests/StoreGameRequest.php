<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'title'=>'required',

            'slug'=>'required|unique:games',

            'description'=>'required',

            'category_id'=>'required',

            'type'=>'required',

            'thumbnail'=>'required|image',

            'banner'=>'nullable|image',

            'gallery.*'=>'nullable|image',

            'game'=>'required'

        ];
    }
}