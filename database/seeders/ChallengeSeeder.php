<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Challenge;

class ChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $challenges = [
            [
                'title'              => '30-Day Thrift Only Challenge',
                'hashtag'            => 'thriftonly30',
                'description'        => 'For 30 days, commit to buying ONLY second-hand clothing. No new fast fashion. Share your best thrift finds with us! Every pre-loved piece you choose saves thousands of litres of water and kilograms of CO2 from entering our atmosphere. Document your journey, show off your unique style, and inspire your community to rethink consumption.',
                'reward_points'      => 500,
                'start_date'         => '2026-06-01',
                'end_date'           => '2026-06-30',
                'is_active'          => true,
            ],
            [
                'title'              => 'Style 1 Item 5 Ways',
                'hashtag'            => 'oneitemfiveways',
                'description'        => 'Pick ONE second-hand clothing item from your wardrobe or a recent ReWear purchase and style it in 5 completely different outfits. Post all 5 looks and show the world how versatile sustainable fashion can be! The most creative styling wins bonus points.',
                'reward_points'      => 300,
                'start_date'         => '2026-06-15',
                'end_date'           => '2026-07-15',
                'is_active'          => true,
            ],
            [
                'title'              => 'Wardrobe Audit & Donate',
                'hashtag'            => 'cleanclosetchallenge',
                'description'        => 'Go through your entire wardrobe and identify items you no longer wear. List at least 5 items on ReWear for others to love, or donate them to a local charity. Share a "before & after" photo of your cleared-out closet. A decluttered wardrobe is the first step to mindful fashion.',
                'reward_points'      => 250,
                'start_date'         => '2026-07-01',
                'end_date'           => '2026-07-31',
                'is_active'          => false,
            ],
            [
                'title'              => 'Upcycle It Challenge',
                'hashtag'            => 'upcyclewithrewe ar',
                'description'        => 'Take an old, worn-out clothing item and transform it into something new and beautiful! Whether it\'s turning an old tee into a tote bag, patching a pair of jeans, or dyeing a faded dress — show us your creative transformation. Upcycling is the highest form of sustainable fashion.',
                'reward_points'      => 450,
                'start_date'         => '2026-08-01',
                'end_date'           => '2026-08-31',
                'is_active'          => false,
            ],
            [
                'title'              => 'Zero Waste Outfit of the Day',
                'hashtag'            => 'zerowasteootd',
                'description'        => 'Post your Outfit of the Day (OOTD) every day for 7 days, where EVERY single item — clothes, shoes, and accessories — is second-hand, thrifted, borrowed, or upcycled. No exceptions! Show us that a complete, stylish look can be achieved with zero new purchases.',
                'reward_points'      => 600,
                'start_date'         => '2026-05-01',
                'end_date'           => '2026-05-31',
                'is_active'          => false,
            ],
        ];

        foreach ($challenges as $challenge) {
            Challenge::firstOrCreate(
                ['hashtag' => $challenge['hashtag']],
                $challenge
            );
        }
    }
}
