<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCommentRequest extends FormRequest
{
    //Determine if the user is authorized to make this request
    public function authorize()
    {
        return true;
    }
    //Get the validation rules that apply to the request.
    //return array
    public function rules()
    {
        return [
            'post_id' => 'required|string',
            'comment' => 'required|string',
            'attachment' => 'required',
            // 'commentable_id' => 'required|string',
            // 'commentable_type' => 'required|string',
        ];
    }
}
