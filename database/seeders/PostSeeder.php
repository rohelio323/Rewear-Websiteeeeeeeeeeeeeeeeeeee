<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;

class PostSeeder extends Seeder
{
    /**
     * Seeds community posts only — vote data is handled by PostVoteSeeder.
     * upvote_count starts at 0 here and will be recalculated after votes are inserted.
     */
    public function run(): void
    {
        $seller = User::where('email', 'seller@rewear.com')->first();
        $buyer  = User::where('email', 'buyer@rewear.com')->first();
        $admin  = User::where('email', 'admin@rewear.com')->first();

        $posts = [
            // ─── Posts linked to a challenge (via hashtag in tags) ──────────────────
            [
                'title'      => 'Day 1 of #ThriftOnly30 — Found this gem at the flea market!',
                'content'    => "Starting my 30-day thrift-only journey today and I couldn't be more excited! 🌿\n\nHeaded to the weekend flea market early this morning and stumbled upon this beautiful linen button-down shirt for only Rp 25.000. The tag says it was barely worn — honestly in better condition than half the \"new\" stuff I see at malls.\n\nDid you know that producing a single cotton shirt releases roughly 7.8 kg of CO2? By choosing this pre-loved piece instead, I've already saved that footprint today. One shirt, one small act, one big impact.\n\nJoin me on this challenge! Drop your thrift finds below 👇 #ThriftOnly30 #SlowFashion #ReWear",
                'image_path' => 'placeholder.jpg',
                'users_id'   => $seller?->id,
                'tags'       => 'ThriftOnly30,thrift,sustainable,fashion,challenge',
            ],
            [
                'title'      => '#OneItemFiveWays — My vintage denim jacket, 5 looks!',
                'content'    => "Challenge accepted! Here's my vintage Levi's denim jacket styled 5 completely different ways 🧥✨\n\n1️⃣ **Classic casual** — paired with a white thrifted tee and straight-leg jeans.\n2️⃣ **Office chic** — layered over a floral midi dress with block heels.\n3️⃣ **Weekend street** — oversized hoodie underneath, bike shorts, and chunky sneakers.\n4️⃣ **Evening glam** — worn as a statement piece over a slip dress with gold accessories.\n5️⃣ **Cozy layers** — wrapped over a knit sweater with wide-leg trousers for those cooler evenings.\n\nThis jacket cost me Rp 80.000 at a local thrift store six months ago and it's already replaced five different \"new\" outfits I would have bought. The math doesn't lie 💚\n\nWhat's YOUR one item? #OneItemFiveWays #CapsuleWardrobe #ReWear",
                'image_path' => 'placeholder.jpg',
                'users_id'   => $buyer?->id,
                'tags'       => 'OneItemFiveWays,ootd,styling,denim,capsulewardrobe,challenge',
            ],
            [
                'title'      => 'Full #ZeroWasteOOTD — 7 days, 0 new items, all the style 🌎',
                'content'    => "I completed the Zero Waste OOTD challenge and I'm genuinely emotional about it 🥹\n\n7 days. 7 outfits. Zero new purchases. Every single piece — from earrings to shoes — was second-hand, borrowed, or upcycled. Here's a quick breakdown:\n\n📅 Day 1: Thrifted batik blouse + inherited grandma's pearl earrings\n📅 Day 2: ReWear haul — a silk blazer I bought here + borrowed belt\n📅 Day 3: Upcycled my old band tee into a crop + vintage skirt\n📅 Day 4: Full thrifted suit for a work presentation (got compliments!)\n📅 Day 5: Beachwear edition — all second-hand swimwear + sarong\n📅 Day 6: Cozy Sunday — borrowed oversized knit from my sister\n📅 Day 7: Dressed up for dinner in a pre-loved evening dress from ReWear\n\nTotal carbon footprint from clothing purchases this week: ZERO ✊\n\nThis challenge genuinely changed how I think about getting dressed. Who else is in for the next round? #ZeroWasteOOTD #SustainableFashion #ReWear",
                'image_path' => 'placeholder.jpg',
                'users_id'   => $seller?->id,
                'tags'       => 'ZeroWasteOOTD,zerowaste,ootd,sustainable,7days,challenge',
            ],

            // ─── General community posts ─────────────────────────────────────────────
            [
                'title'      => 'How I saved Rp 2 juta AND the planet this month 🌿',
                'content'    => "Monthly recap time! This month I tracked every clothing purchase and I'm honestly shocked.\n\nTotal spent: Rp 180.000 (3 second-hand pieces from ReWear)\nEstimated CO2 saved vs buying new: ~62 kg — that's like not driving a car for 4 days!\nWater saved: ~15,000 litres (equivalent to 100+ showers)\n\nThe three items:\n• A barely-worn cotton trench coat (Rp 95.000)\n• Two vintage graphic tees (Rp 85.000 for both)\n\nCompared to buying similar items new (roughly Rp 2.2 juta for all three), I saved Rp 2 juta AND massively cut my environmental impact.\n\nThe numbers are clear — second-hand fashion isn't just trendy, it's the logical choice 💚 What's your sustainable fashion win this month?",
                'image_path' => 'placeholder.jpg',
                'users_id'   => $buyer?->id,
                'tags'       => 'sustainability,savings,eco,secondhand',
            ],
            [
                'title'      => 'PSA: How to check clothing condition before buying on ReWear 🔍',
                'content'    => "Been buying second-hand for 3 years now and I've developed a foolproof checklist. Sharing it here because I see a lot of newcomers getting confused!\n\n**Before you buy, always check:**\n\n✅ **Fabric condition** — look at stitching in the photos, ask seller to show close-ups of seams\n✅ **Pilling** — common on knitwear, can be fixed with a fabric shaver (cheap!)\n✅ **Odour description** — a good seller will mention if an item has been dry-cleaned\n✅ **Colour fading** — ask if the item has been washed many times\n✅ **Zipper & button functionality** — always ask seller to demonstrate\n✅ **Measurements** — don't trust size labels alone, always ask for actual measurements\n\nReWear's condition ratings (new_with_tags → like_new → good → fair) are great starting points, but these questions help fill in the gaps.\n\nHappy thrifting! Drop your tips below 👇",
                'image_path' => null,
                'users_id'   => $admin?->id,
                'tags'       => 'tips,guide,community,buying',
            ],
            [
                'title'      => 'My first ReWear haul — 5 items under Rp 200k total!',
                'content'    => "I was skeptical about second-hand shopping online but wow, I was completely wrong 😭\n\nJust got my first ReWear haul and everything is in PERFECT condition. Here's what I scored:\n\n🧥 Navy blazer (like new) — Rp 55.000\n👗 Floral midi dress — Rp 35.000\n👕 3x basic cotton tees — Rp 15.000 each\n\nGrand total: Rp 135.000\n\nEquivalent retail value: roughly Rp 850.000\nEstimated CO2 saved: ~42 kg\n\nThe seller packaged everything beautifully with a handwritten note about the items' history. This is what shopping SHOULD feel like — personal, intentional, and guilt-free.\n\nI'm officially converted. See you all in the app! 🌿 #FirstHaul #ReWear #ThriftWin",
                'image_path' => 'placeholder.jpg',
                'users_id'   => $buyer?->id,
                'tags'       => 'haul,firsttime,thrift,rewear',
            ],
        ];

        foreach ($posts as $postData) {
            Post::create($postData);
        }
    }
}
