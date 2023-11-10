<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('languages')->truncate();
        DB::table('languages')->insert(['name' => 'Spanish', 'code' => 'es']);
        DB::table('languages')->insert(['name' => 'English', 'code' => 'en']);
        DB::table('languages')->insert(['name' => 'German', 'code' => 'de']);
        DB::table('languages')->insert(['name' => 'Chinese', 'code' => 'cn']);
        DB::table('languages')->insert(['name' => 'Bengali', 'code' => 'bd']);
        DB::table('languages')->insert(['name' => 'Dutch', 'code' => 'nl']);
        DB::table('languages')->insert(['name' => 'Italian', 'code' => 'it']);
        DB::table('languages')->insert(['name' => 'Arabic', 'code' => 'sa']);
        DB::table('languages')->insert(['name' => 'Swedish', 'code' => 'se']);
        DB::table('languages')->insert(['name' => 'Portuguese', 'code' => 'pt']);
    }
}
