<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;
use App\Models\MyListItem;
use App\Models\Category;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // keyword が送られてきたらセッションを更新
        if ($request->filled('keyword')) {
            session(['keyword' => $request->keyword]);
        } elseif ($request->has('keyword')) {
            session()->forget('keyword');
        }

        $keyword = $request->keyword ?? session('keyword');

        $tab     = $request->query('tab', 'recommend');

        // タブ切り替え
        if ($tab === 'myList') {
            $items = auth()->check()
                ? auth()->user()
                    ->myListItems
                    ->map(fn($myListItem) => $myListItem->item)
                    ->filter(fn($item) => $item && $item->user_id !== auth()->id())
                : collect();
        } else {
            $items = Item::where('user_id', '!=', auth()->id())->get();
        }

        // 検索（タブの結果に対して絞り込み）
        if ($keyword) {
            $items = $items->filter(fn($item) => str_contains($item->name, $keyword));
        }

        return view('items.index', compact('items', 'keyword', 'tab'));
    }

    public function show($item_id)
    {
        $item = Item::with(['images', 'categories'])
            ->findOrFail($item_id);

        $categories = $item->categories;

        $likeCount = MyListItem::where('item_id', $item->id)->count();

        $contentCount = $item->comments()->count();

        // このユーザーがいいね済みか？
        $isLiked = auth()->check()
            ? MyListItem::where('user_id', auth()->id())
                ->where('item_id', $item->id)
                ->exists()
            : false;

        $content = $item->comments()->latest()->first();

        // コメントがあればコメントしたユーザーのプロフィール画像を取得
        $avatar = $content?->user?->avatar_path ?? null;

        return view('items.show', compact(
            'item',
            'categories',
            'likeCount',
            'contentCount',
            'isLiked',
            'avatar',
            'content'
        ));
    }

    public function create()
    {
        $categories = Category::all();

        return view('items.create', compact('categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        $path = $request->file('image')->store('items', 'public');

        $item = Item::create([
            'name'        => $request->name,
            'brand'       => $request->brand,
            'description' => $request->description,
            'price'       => $request->price,
            'condition'   => $request->condition,
            'user_id'     => auth()->id(),
        ]);

        $item->categories()->sync($request->categories);

        $item->images()->create([
            'image_path'  => $path,
        ]);

        return redirect()->route('item.show', $item->id)
            ->with('success', '出品が完了しました');
    }
}
