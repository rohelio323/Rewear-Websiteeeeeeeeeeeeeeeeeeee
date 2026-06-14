<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\PostVote;
use App\Models\User;

class PostVoteSeeder extends Seeder
{
    
    public function run(): void
    {
        $seller = User::where('email', 'seller@rewear.com')->first();
        $buyer  = User::where('email', 'buyer@rewear.com')->first();
        $admin  = User::where('email', 'admin@rewear.com')->first();

        
        $voteMap = [
            'Day 1 of #ThriftOnly30' => [
                ['user' => $buyer, 'value' =>  1],
                ['user' => $admin, 'value' =>  1],
            ],
            '#OneItemFiveWays' => [
                ['user' => $seller, 'value' =>  1],
                ['user' => $admin,  'value' =>  1],
            ],
            'Full #ZeroWasteOOTD' => [
                ['user' => $buyer, 'value' =>  1],
                ['user' => $admin, 'value' =>  1],
            ],
            'How I saved Rp 2 juta' => [
                ['user' => $seller, 'value' =>  1],
                ['user' => $admin,  'value' => -1],
            ],
            'PSA: How to check clothing condition' => [
                ['user' => $seller, 'value' =>  1],
                ['user' => $buyer,  'value' =>  1],
            ],
            'My first ReWear haul' => [
                ['user' => $seller, 'value' =>  1],
            ],
        ];

        foreach ($voteMap as $titleSubstring => $votes) {
            $post = Post::where('title', 'like', "%{$titleSubstring}%")->first();

            if (! $post) {
                $this->command->warn("PostVoteSeeder: could not find post matching \"{$titleSubstring}\" — skipping.");
                continue;
            }

            foreach ($votes as $vote) {
                if (! $vote['user']) {
                    continue;
                }

                // firstOrCreate prevents duplicate-key errors if the seeder is run twice
                PostVote::firstOrCreate(
                    ['post_id' => $post->post_id, 'user_id' => $vote['user']->id],
                    ['value'   => $vote['value']]
                );
            }

            // Recalculate the denormalised cache from the source of truth
            $post->recalculateScore();
        }
    }
}
