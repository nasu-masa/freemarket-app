<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'avatar'      => ['nullable', 'mimes:jpeg,png'],
            'name'        => ['required', 'string', 'max:20'],
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address'     => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'avatar.mimes'         => 'jpegまたはpng形式の画像ファイルを選択してください',

            'name.required'        => 'ユーザー名を入力してください',
            'name.max'             => 'ユーザー名は20文字以内で入力してください',

            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex'    => '郵便番号は123-4567の形式で入力してください',

            'address.required'     => '住所を入力してください',
        ];
    }
}
