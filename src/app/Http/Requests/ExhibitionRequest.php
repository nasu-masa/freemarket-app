<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->price) {
            $this->merge([
                'price' => str_replace(',', '', $this->price),
            ]);
        }
    }


    public function rules()
    {
        return [
            'image'       => ['required', 'mimes:jpeg,png'],
            'categories'  => ['required'],
            'condition'   => ['required'],
            'name'        => ['required', 'string'],
            'description' => ['required', 'string', 'max:255'],
            'price'       => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'image.required'       => '商品画像をアップロードしてください。',
            'image.mimes'          => '商品画像はjpegまたはpng形式でアップロードしてください。',

            'categories.required'  => '商品のカテゴリーを選択してください。',

            'condition.required'   => '商品の状態を選択してください。',

            'name.required'        => '商品名を入力してください。',

            'description.required' => '商品説明を入力してください。',
            'description.max'      => '商品説明は255文字以内で入力してください。',

            'price.required'       => '商品価格を入力してください。',
            'price.numeric'        => '商品価格は数値で入力してください。',
            'price.min'            => '商品価格は0円以上で入力してください。',
        ];
    }
}
