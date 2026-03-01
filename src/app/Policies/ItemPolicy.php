<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    public function purchase(User $user, Item $item)
    {
        return $item->user_id !== $user->id && $item->status === 'selling';
    }
}
