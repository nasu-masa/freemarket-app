<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;

class AddressController extends Controller
{
    public function editAddress($item_id)
    {
        // 商品と関連データを取得
        $item = Item::findOrFail($item_id);

        // ユーザーの住所を取得
        $address = auth()->user()->address;

        return view('purchase.address_edit', compact('item', 'address'));
    }

    public function updateAddress(AddressRequest $request, $item_id)
    {
        $user = auth()->user();

        // 住所が無ければ新規作成
        if (!$user->address) {
            $user->address()->create(
                $request->only('postal_code', 'address', 'building')
            );
        } else {
            // 住所があれば更新
            $user->address->update(
                $request->only('postal_code', 'address', 'building')
            );
        }

        return redirect()->route('purchase.checkout', ['item_id' => $item_id]);
    }
}