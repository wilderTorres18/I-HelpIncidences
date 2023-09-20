<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Department;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::limit(20)->get();
        $departments = Department::limit(10)->get();
        $categories = Category::limit(10)->get();
        $statuses = Status::limit(10)->get();
        $priorities = Priority::limit(10)->get();
        DB::table('tickets')->truncate();
        Ticket::factory(20)->create()->each(function ($ticket) use($users, $departments, $categories, $statuses, $priorities){
            $ticket->update(['uid' => floor(rand(500,999)*10000) + $ticket->id]);
            $ticket->update(['status_id' => $statuses->random()->id]);
            $ticket->update(['user_id' => $users->random()->id]);
            $ticket->update(['created_by' => $users->random()->id]);
            $ticket->update(['priority_id' => $priorities->random()->id]);
            $ticket->update(['department_id' => $departments->random()->id]);
            $ticket->update(['category_id' => $categories->random()->id]);
            $ticket->update(['assigned_to' => $users->random()->id]);
        });
    }
}
