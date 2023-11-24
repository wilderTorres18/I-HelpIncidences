<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasTable('departments')) { return; }
        Schema::create('departments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->softDeletes();
        });

        DB::table('departments')->insert([
            ['name' => 'Ventas'], ['name' => 'Facturación'], ['name' => 'Soporte Técnico'], ['name' => 'Contabilidad'], ['name' => 'Sistemas'], ['name' => 'Inventario'] ,['name'=>'Otros']
    ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
}
