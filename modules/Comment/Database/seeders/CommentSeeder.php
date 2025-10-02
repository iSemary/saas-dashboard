<?php

namespace Modules\Comment\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Comment\Entities\Comment;
use Modules\Comment\Entities\CommentReaction;
use Modules\Comment\Entities\CommentAttachment;
use App\Models\User;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users for relationships
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping comment seeding.');
            return;
        }

        // Sample comments for different object types
        $comments = [
            [
                'comment' => 'This is a sample comment for testing purposes. It demonstrates how comments work in the system.',
                'user_id' => $users->random()->id,
                'object_id' => 1,
                'object_model' => 'Modules\\Ticket\\Entities\\Ticket',
                'seen' => false,
                'metadata' => json_encode(['source' => 'web']),
            ],
            [
                'comment' => 'Great work on this! I think we should also consider adding some additional features.',
                'user_id' => $users->random()->id,
                'object_id' => 1,
                'object_model' => 'Modules\\Ticket\\Entities\\Ticket',
                'seen' => true,
                'metadata' => json_encode(['source' => 'web']),
            ],
            [
                'comment' => 'I have some concerns about this approach. Let me explain my thoughts...',
                'user_id' => $users->random()->id,
                'object_id' => 2,
                'object_model' => 'Modules\\Ticket\\Entities\\Ticket',
                'seen' => false,
                'metadata' => json_encode(['source' => 'mobile']),
            ],
            [
                'comment' => 'This looks good to me. When can we expect this to be implemented?',
                'user_id' => $users->random()->id,
                'object_id' => 2,
                'object_model' => 'Modules\\Ticket\\Entities\\Ticket',
                'seen' => true,
                'metadata' => json_encode(['source' => 'web']),
            ],
            [
                'comment' => 'I found a potential issue with the current implementation. Here are the details...',
                'user_id' => $users->random()->id,
                'object_id' => 3,
                'object_model' => 'Modules\\Ticket\\Entities\\Ticket',
                'seen' => false,
                'metadata' => json_encode(['source' => 'api']),
            ],
        ];

        foreach ($comments as $commentData) {
            $comment = Comment::create($commentData);
            
            // Add some reactions to comments
            $this->addRandomReactions($comment, $users);
        }

        // Create some reply comments
        $this->createReplies($users);
        
        $this->command->info('✅ Comment seeding completed successfully.');
    }

    /**
     * Add random reactions to a comment
     */
    private function addRandomReactions(Comment $comment, $users)
    {
        $reactionTypes = array_keys(CommentReaction::getReactionTypes());
        $numReactions = rand(0, 3); // 0 to 3 reactions per comment
        
        for ($i = 0; $i < $numReactions; $i++) {
            $user = $users->random();
            $reactionType = $reactionTypes[array_rand($reactionTypes)];
            
            // Avoid duplicate reactions from same user
            $existingReaction = CommentReaction::where('comment_id', $comment->id)
                ->where('user_id', $user->id)
                ->first();
                
            if (!$existingReaction) {
                CommentReaction::create([
                    'comment_id' => $comment->id,
                    'user_id' => $user->id,
                    'reaction_type' => $reactionType,
                ]);
            }
        }
    }

    /**
     * Create reply comments
     */
    private function createReplies($users)
    {
        $parentComments = Comment::whereNull('parent_id')->get();
        
        $replies = [
            'Thanks for the feedback! I\'ll look into this.',
            'I agree with your points. Let\'s discuss this further.',
            'Good catch! I\'ll fix this issue.',
            'That\'s a great suggestion. I\'ll implement it.',
            'I need more information about this. Can you provide details?',
            'This has been resolved. Please check and confirm.',
            'I\'ve updated the implementation based on your feedback.',
            'Let me know if you need any clarification.',
        ];

        foreach ($parentComments as $parentComment) {
            // 50% chance of having a reply
            if (rand(0, 1)) {
                $reply = Comment::create([
                    'parent_id' => $parentComment->id,
                    'comment' => $replies[array_rand($replies)],
                    'user_id' => $users->random()->id,
                    'object_id' => $parentComment->object_id,
                    'object_model' => $parentComment->object_model,
                    'seen' => rand(0, 1),
                    'metadata' => json_encode(['source' => 'web']),
                ]);
                
                // Add reactions to replies too
                $this->addRandomReactions($reply, $users);
            }
        }
    }
}
