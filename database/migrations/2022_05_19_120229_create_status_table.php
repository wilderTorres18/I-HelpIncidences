<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasTable('status')) { return; }
        Schema::create('status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('slug', 50)->default(null)->nullable();
            $table->softDeletes();
        });

        DB::table('status')->insert([
            ['name'=>'Pendiente','slug'=>'pending'],
            ['name'=>'Procesando','slug'=>'processing'],
            ['name'=>'Completado','slug'=>'closed'],
            ['name'=>'Procesamiento de retraso','slug'=>'delay_processing'],
            ['name'=>'Esperando por confirmación','slug'=>'waiting_for_confirmation'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('status');
    }
}
