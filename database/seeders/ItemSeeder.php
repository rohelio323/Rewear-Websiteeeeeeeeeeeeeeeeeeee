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

        $items = [
            [
                'item_name'   => 'Vintage Denim Jacket 90s',
                'description' => 'Jaket denim vintage era 90an, warna biru klasik dengan sedikit fading alami. Cocok untuk tampilan casual retro.',
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
                'description' => 'Dress midi motif bunga warna pastel, bahan ringan dan nyaman. Hanya dipakai 2x untuk acara.',
                'size'        => 'S',
                'condition'   => 'like_new',
                'price'       => 195000,
                'photo_path'  => ['https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=500&h=667&fit=crop&q=80'],
                'status'      => 'available',
                'users_id'    => $seller->id,
                'category_id' => 4,
            ],
            [
                'item_name'   => 'Oversized Cream Knit Sweater',
                'description' => 'Sweater rajut oversized warna krem hangat, sangat nyaman untuk musim dingin atau AC. Bahan tebal berkualitas.',
                'size'        => 'L',
                'condition'   => 'good',
                'price'       => 165000,
                'photo_path'  => ['https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=500&h=667&fit=crop&q=80'],
                'status'      => 'available',
                'users_id'    => $seller->id,
                'category_id' => 3,
            ],
            [
                'item_name'   => 'Slim Fit White Oxford Shirt',
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
                'item_name'   => 'Black Slip Dress Satin',
                'description' => 'Slip dress satin hitam elegan, potongan simpel tapi mewah. Cocok untuk dinner atau acara semi-formal.',
                'size'        => 'S',
                'condition'   => 'new_with_tags',
                'price'       => 310000,
                'photo_path'  => ['https://images.unsplash.com/photo-1566479353521-c8e1c4cbab6c?w=500&h=667&fit=crop&q=80'],
                'status'      => 'available',
                'users_id'    => $seller->id,
                'category_id' => 4,
            ],
            [
                'item_name'   => 'Graphic Tee Band Vintage',
                'description' => 'Kaos grafis bergambar band rock vintage, bahan katun 100%. Dipakai beberapa kali, masih sangat bagus.',
                'size'        => 'L',
                'condition'   => 'good',
                'price'       => 75000,
                'photo_path'  => ['https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=500&h=667&fit=crop&q=80'],
                'status'      => 'available',
                'users_id'    => $seller->id,
                'category_id' => 1,
            ],
            [
                'item_name'   => 'Olive Green Cargo Pants',
                'description' => 'Celana cargo warna olive green dengan banyak kantong fungsional. Cocok untuk outdoor atau gaya streetwear.',
                'size'        => '30',
                'condition'   => 'like_new',
                'price'       => 290000,
                'photo_path'  => ['https://images.unsplash.com/photo-1594938298603-c8148c4b4671?w=500&h=667&fit=crop&q=80'],
                'status'      => 'available',
                'users_id'    => $seller->id,
                'category_id' => 2,
            ],
            [
                'item_name'   => 'Pastel Yellow Hoodie',
                'description' => 'Hoodie warna kuning pastel, bahan fleece tebal dan lembut. Jarang dipakai, masih sangat fresh.',
                'size'        => 'M',
                'condition'   => 'like_new',
                'price'       => 180000,
                'photo_path'  => ['https://images.unsplash.com/photo-1556821840-3a63f15732ce?w=500&h=667&fit=crop&q=80'],
                'status'      => 'available',
                'users_id'    => $seller->id,
                'category_id' => 3,
            ],
            [
                'item_name'   => 'Classic Navy Blazer',
                'description' => 'Blazer formal warna navy biru tua, slim fit. Bahan wool blend berkualitas. Sempurna untuk wawancara atau presentasi.',
                'size'        => 'L',
                'condition'   => 'like_new',
                'price'       => 450000,
                'photo_path'  => ['https://images.unsplash.com/photo-1593030761757-71fae45fa0e7?w=500&h=667&fit=crop&q=80'],
                'status'      => 'available',
                'users_id'    => $seller->id,
                'category_id' => 5,
            ],
            [
                'item_name'   => 'Linen Striped Button-Up',
                'description' => 'Kemeja linen garis-garis tipis, sangat ringan dan breathable. Ideal untuk cuaca panas. Beli di Bali, hanya dipakai sekali.',
                'size'        => 'M',
                'condition'   => 'new_with_tags',
                'price'       => 140000,
                'photo_path'  => ['https://images.unsplash.com/photo-1596755389378-c31d21fd1273?w=500&h=667&fit=crop&q=80'],
                'status'      => 'available',
                'users_id'    => $seller->id,
                'category_id' => 1,
            ],
            [
                'item_name'   => 'Sporty Running Jacket',
                'description' => 'Jaket lari warna hitam dengan aksen reflektif, bahan windbreaker ringan. Cocok untuk jogging pagi hari.',
                'size'        => 'M',
                'condition'   => 'good',
                'price'       => 210000,
                'photo_path'  => ['https://images.unsplash.com/photo-1539185441755-769473a23570?w=500&h=667&fit=crop&q=80'],
                'status'      => 'available',
                'users_id'    => $seller->id,
                'category_id' => 6,
            ],
        ];

        foreach ($items as $item) {
            Item::firstOrCreate(
                ['item_name' => $item['item_name'], 'users_id' => $item['users_id']],
                $item
            );
        }
    }
}
