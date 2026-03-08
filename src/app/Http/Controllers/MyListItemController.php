<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyListItem;

class MyListItemController extends Controller
{
    public function store(Request $request, $item_id)
    {
        if(!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user = $request->user();

        // すでにいいねしているか確認
        $existing = MyListItem::where('user_id', $user->id)
            ->where('item_id', $item_id)
            ->first();

        if ($existing) {
            //いいね解除 (unlike)
            $existing->delete();

            return response()->json([
                'isLiked' => false,
                'likeCount' => MyListItem::where('item_id', $item_id)->count(),
            ]);
        }

        MyListItem::create([
            'user_id' => $user->id,
            'item_id' => $item_id,
        ]);

        return response()->json([
            'isLiked' => true,
            'likeCount' => MyListItem::where('item_id', $item_id)->count(),
        ]);
    }
}
