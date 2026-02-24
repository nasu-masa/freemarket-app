<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Models\Item;
use App\Models\Purchase;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('page', 'sell');
        $user = auth()->user();

        // 購入タブの場合は購入履歴から商品を取得
        if ($tab === 'buy') {
            $items = Item::whereIn(
                'id',
                Purchase::where('user_id', $user->id)->pluck('item_id')
            )->get();
        } else {
            // 出品タブの場合は自分の出品商品
            $items = Item::where('user_id', $user->id)->get();
        }

        return view('mypage.index', compact('tab', 'user', 'items'));
    }

    public function edit()
    {
        $user = auth()->user()->load('address');

        return view('mypage.profile_edit', compact('user'));
    }

    public function update(ProfileRequest $request)
    {
        $user = auth()->user();

        // プロフィール画像アップロード
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_path = '/storage/' . $path;
            $user->save();
        }

        // 住所が未登録なら作成、あれば更新
        if (!$user->address) {
            $user->address()->create([
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
                'building'    => $request->building,
                'type'        => 'home',
            ]);
        } else {
            $user->address()->update([
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
                'building'    => $request->building,
            ]);
        }

        return redirect()->route('items.index');
    }
}
