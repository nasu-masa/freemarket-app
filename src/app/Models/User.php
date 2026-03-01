<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function latestAddress()
    {
        return $this->hasOne(Address::class)->latestOfMany();
    }

    //保存処理共通化のため
    public function addAddress(array $attributes)
    {
        return $this->addresses()->create([
            'postal_code' => $attributes['postal_code'],
            'address'     => $attributes['address'],
            'building'    => $attributes['building'],
        ]);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

        //購入履歴保存処理共通化のため
    public function purchaseItem(Item $item, string $paymentMethod): Purchase
    {
        $item->update(['status' => 'sold']);

        return $this->purchases()->create([
            'item_id'        => $item->id,
            'address_id'     => $this->latestAddress->id,
            'payment_method' => $paymentMethod,
            'purchased_at'   => now(),
        ]);
    }

    public function myListItems()
    {
        return $this->hasMany(MyListItem::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
}