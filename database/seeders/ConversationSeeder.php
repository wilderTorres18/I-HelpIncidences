<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Conversation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('conversations')->truncate();
        $contacts = Contact::limit(25)->get();
        Conversation::factory(20)->create()->each(function ($ticket) use($contacts){
            $ticket->update(['contact_id' => $contacts->random()->id]);
        });
    }
}
