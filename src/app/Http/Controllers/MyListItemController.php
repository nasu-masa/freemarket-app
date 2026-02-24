<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyListItem;

class MyListItemController extends Controller
{
    public function store(Request $request, $item_id)
    {
        $user = $request->user();

        // すでにいいねしているか確認
        $existing = MyListItem::where('user_id', $user->id)
            ->where('item_id', $item_id)
            ->first();

        if (!$existing) {
            // いいねを作成
            MyListItem::create([
                'user_id' => $user->id,
                'item_id' => $item_id,
            ]);
        }

        return redirect()->back();
    }

    public function destroy(Request $request, $item_id)
    {
        $user = $request->user();

        // いいねを検索
        $existing = MyListItem::where('user_id', $user->id)
            ->where('item_id', $item_id)
            ->first();

        if ($existing) {
            // いいねを削除
            $existing->delete();
        }

        return redirect()->back();
    }
}
