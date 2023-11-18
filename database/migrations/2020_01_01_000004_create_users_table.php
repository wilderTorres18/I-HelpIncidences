<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasTable('users')) { return; }
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id')->default(null)->nullable()->index();
            $table->string('title', 100)->default('Engineer')->nullable();
            $table->string('locale', 5)->default('es');
//            $table->integer('country_id')->default(null)->nullable()->index();
            $table->string('address', 200)->default(null)->nullable();
            $table->string('city', 50)->default(null)->nullable();
            $table->string('first_name', 25);
            $table->string('last_name', 25);
            $table->string('position', 50)->default(null)->nullable();
            $table->string('email', 50)->unique();
            $table->string('phone', 20)->default(null)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('photo_path', 100)->nullable();
            $table->integer('organization_id')->nullable()->index();
            $table->boolean('state')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
        DB::table('users')->insert([
            [
                'role_id'=>1,
                'city'=>'Piura',
                'first_name'=>'Admin',
                'last_name'=>'innovo',
                'email'=>'admin@incidencias.com',
                'phone'=>'999999999',
                'password'=>'123422',
                'organization_id'=>1
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
