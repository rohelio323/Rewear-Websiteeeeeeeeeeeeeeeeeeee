<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'Tops & T-Shirts',
                'co2_constant' => 7.80,
                'reference_note' => 'WRAP (Waste and Resources Action Programme) estimates the carbon footprint of a standard cotton t-shirt at roughly 7.8 kg CO2, heavily driven by energy-intensive wet pre-treatment and manufacturing.',
                'reference_url' => 'https://wrap.org.uk/resources/report/valuing-our-clothes-cost-uk-fashion',
            ],
            [
                'category_name' => 'Denim & Trousers',
                'co2_constant' => 33.40,
                'reference_note' => "According to Levi Strauss & Co.'s comprehensive Lifecycle Assessment, producing a single pair of iconic 501 jeans generates 33.4 kg of CO2 equivalent. Buying pre-loved eliminates this primary manufacturing phase.",
                'reference_url' => 'https://www.levistrauss.com/wp-content/uploads/2015/03/Full-LCA-Results-Deck-FINAL.pdf',
            ],
            [
                'category_name' => 'Knitwear & Sweaters',
                'co2_constant' => 15.50,
                'reference_note' => 'Wool production has a high carbon footprint due to livestock methane emissions, while acrylic requires fossil fuels. Extending the life of a sweater saves an average of 15.5 kg CO2.',
                'reference_url' => 'https://www.thredup.com/fashionfootprint',
            ],
            [
                'category_name' => 'Dresses & Jumpsuits',
                'co2_constant' => 21.40,
                'reference_note' => 'Full-body garments often utilize mixed materials and complex stitching. Displacing the purchase of a new dress with a second-hand alternative avoids approximately 21.4 kg of manufacturing carbon emissions.',
                'reference_url' => 'https://www.ellenmacarthurfoundation.org/fashion-and-the-circular-economy-deep-dive',
            ],
            [
                'category_name' => 'Outerwear & Jackets',
                'co2_constant' => 39.00,
                'reference_note' => "Heavy outerwear requires complex synthetic extraction (often petroleum-based) and intensive manufacturing. ThredUp's independent environmental study calculates the footprint of heavy jackets at roughly 39 kg CO2.",
                'reference_url' => 'https://www.thredup.com/resale/',
            ],
            [
                'category_name' => 'Activewear & Swimwear',
                'co2_constant' => 9.50,
                'reference_note' => 'Activewear relies heavily on virgin polyester and nylon, which are plastics derived from crude oil. Avoiding new production saves roughly 9.5 kg CO2 per garment based on the Higg Materials Sustainability Index.',
                'reference_url' => 'https://higg.com/materials-sustainability-index/',
            ],
            [
                'category_name' => 'Footwear & Shoes',
                'co2_constant' => 14.00,
                'reference_note' => 'A comprehensive MIT lifecycle assessment of standard shoes found that manufacturing and material processing accounts for over 65% of environmental impact, totaling roughly 14 kg CO2 per pair.',
                'reference_url' => 'https://news.mit.edu/2013/footwear-carbon-footprint-0522',
            ]
        ];

        foreach ($categories as $cat) {
            // firstOrCreate prevents duplicate entries if you run the seeder twice
            Category::firstOrCreate(
                ['category_name' => $cat['category_name']], 
                $cat
            );
        }
    }
}