<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
class CommentController extends Controller
{

    public function store(CommentRequest $request, $item_id)
    {
        $user = $request->user();

        // コメントを作成し保存
        Comment::create([
            'user_id' => $user->id,
            'item_id' => $item_id,
            'content' => $request->input('content'),
        ]);

        return redirect()->back();
    }
}