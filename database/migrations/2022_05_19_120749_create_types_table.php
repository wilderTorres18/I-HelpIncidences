<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('types')) { return; }
        Schema::create('types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->softDeletes();
        });

        DB::table('types')->insert([
            ['name' => 'Error en la aplicación'], ['name' => 'Problema de rendimiento'],
            ['name' => 'Problemas con exportación '], ['name' => 'Problemas de acceso'],
            ['name' => 'Consulta general'] ,['name' => 'Otros']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('types');
    }
}
