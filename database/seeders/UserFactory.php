<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserFactory extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 regular users
        $users = User::factory()->count(10)->create();

        // Create 2 group chats
        $groupChats = [
            Chat::create([
                'name' => 'گروه دوستان',
                'is_group' => true
            ]),
            Chat::create([
                'name' => 'گروه کاری',
                'is_group' => true
            ])
        ];

        // Add users to group chats
        foreach ($groupChats as $chat) {
            $chat->users()->attach($users->take(5)->pluck('id'));
        }

        // Create direct chats between users
        for ($i = 0; $i < count($users); $i += 2) {
            if (isset($users[$i + 1])) {
                $chat = Chat::create([
                    'is_group' => false
                ]);
                $chat->users()->attach([$users[$i]->id, $users[$i + 1]->id]);

                // Add some messages to the chat
                Message::create([
                    'chat_id' => $chat->id,
                    'user_id' => $users[$i]->id,
                    'content' => 'سلام! چطوری؟'
                ]);

                Message::create([
                    'chat_id' => $chat->id,
                    'user_id' => $users[$i + 1]->id,
                    'content' => 'ممنون، خوبم. شما چطوری؟'
                ]);
            }
        }

        // Add some messages to group chats
        foreach ($groupChats as $chat) {
            foreach ($chat->users as $user) {
                Message::create([
                    'chat_id' => $chat->id,
                    'user_id' => $user->id,
                    'content' => 'سلام به همه!'
                ]);
            }
        }
    }
}
