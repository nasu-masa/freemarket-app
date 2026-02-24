<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemImage;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();

        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'condition' => '良好',
                'status' => 'selling',
                'categories' => [1, 12],
                'image' => 'Clock.jpg',
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'condition' => '目立った傷や汚れなし',
                'status' => 'selling',
                'categories' => [10],
                'image' => 'HDD.jpg',
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'condition' => 'やや傷や汚れあり',
                'status' => 'selling',
                'categories' => [10],
                'image' => 'Onion.jpg',
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'condition' => '状態が悪い',
                'status' => 'selling',
                'categories' => [1, 5],
                'image' => 'Leather-Shoes.jpg',
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'condition' => '良好',
                'status' => 'selling',
                'categories' => [2],
                'image' => 'Laptop.jpg',
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'condition' => '目立った傷や汚れなし',
                'status' => 'selling',
                'categories' => [2],
                'image' => 'Mic.jpg',
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'condition' => 'やや傷や汚れあり',
                'status' => 'selling',
                'categories' => [1, 4],
                'image' => 'Bag.jpg',
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'condition' => '状態が悪い',
                'status' => 'selling',
                'categories' => [10],
                'image' => 'Tumbler.jpg',
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'condition' => '良好',
                'status' => 'selling',
                'categories' => [10],
                'image' => 'Coffee-Grinder.jpg',
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'condition' => '目立った傷や汚れなし',
                'status' => 'selling',
                'categories' => [6],
                'image' => 'Make-Up.jpg',
            ],
        ];

        foreach ($items as $data) {

            $item = Item::create([
                'user_id' => Arr::random($userIds),
                'name' => $data['name'],
                'price' => $data['price'],
                'brand' => $data['brand'],
                'description' => $data['description'],
                'condition' => $data['condition'],
                'status' => $data['status'],
            ]);

            $item->categories()->attach($data['categories']);

            $source = public_path('products/' . $data['image']);
            $filename = 'items/' . Str::random(20) . '.jpg';

            Storage::disk('public')->put($filename, File::get($source));

            ItemImage::create([
                'item_id' => $item->id,
                'image_path' => $filename,
            ]);
        }
    }
}
