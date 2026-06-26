<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::where('email', 'seller@rewear.com')->first();

        Item::truncate();

        $items = [
            [
                'item_name'   => 'Vintage Denim Jacket',
                'description' => 'Jaket denim vintage warna biru klasik dengan sedikit fading alami. Kondisi sangat baik, hanya dipakai beberapa kali.',
                'size'        => 'M',
                'condition'   => 'like_new',
                'price'       => 285000,
                'photo_path'  => ['https://images.unsplash.com/photo-1543076447-215ad9ba6923?w=500&h=667&fit=crop&q=80'],
                'status'      => 'available',
                'users_id'    => $seller->id,
                'category_id' => 5,
            ],
            [
                'item_name'   => 'Floral Midi Dress',
                'description' => 'Dress midi motif bunga warna pastel, bahan ringan dan nyaman untuk daily wear. Hanya dipakai 2x untuk acara.',
                'size'        => 'S',
                'condition'   => 'like_new',
                'price'       => 195000,
                'photo_path'  => ['https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=500&h=667&fit=crop&q=80'],
                'status'      => 'available',
                'users_id'    => $seller->id,
                'category_id' => 4,
            ],
            [
                'item_name'   => 'Oversized Knit Sweater',
                'description' => 'Sweater rajut oversized warna krem hangat. Bahan tebal berkualitas, cocok untuk cuaca dingin atau ber-AC.',
                'size'        => 'L',
                'condition'   => 'good',
                'price'       => 165000,
                'photo_path'  => ['https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=500&h=667&fit=crop&q=80'],
                'status'      => 'available',
                'users_id'    => $seller->id,
                'category_id' => 3,
            ],
            [
                'item_name'   => 'High-Waist Mom Jeans',
                'description' => 'Celana jeans mom jeans high-waist warna biru medium wash. Potongan klasik yang tidak pernah ketinggalan zaman.',
                'size'        => '28',
                'condition'   => 'good',
                'price'       => 230000,
                'photo_path'  => ['https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=500&h=667&fit=crop&q=80'],
                'status'      => 'available',
                'users_id'    => $seller->id,
                'category_id' => 2,
            ],
            [
                'item_name'   => 'Classic White Oxford Shirt',
                'description' => 'Kemeja putih Oxford slim fit, bahan katun premium. Cocok untuk formal maupun casual. Kondisi sangat baik.',
                'size'        => 'M',
                'condition'   => 'like_new',
                'price'       => 120000,
                'photo_path'  => ['https://images.unsplash.com/photo-1598033129183-c4f50c736f10?w=500&h=667&fit=crop&q=80'],
                'status'      => 'available',
                'users_id'    => $seller->id,
                'category_id' => 1,
            ],
            [
                'item_name'   => 'Navy Slim Fit Blazer',
                'description' => 'Blazer formal warna navy, slim fit. Bahan wool blend berkualitas. Sempurna untuk presentasi atau acara semi-formal.',
                'size'        => 'L',
                'condition'   => 'like_new',
                'price'       => 450000,
                'photo_path'  => ['https://images.unsplash.com/photo-1593030761757-71fae45fa0e7?w=500&h=667&fit=crop&q=80'],
                'status'      => 'available',
                'users_id'    => $seller->id,
                'category_id' => 5,
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
