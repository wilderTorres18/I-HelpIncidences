<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasTable('categories')) { return; }
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', '50');
            $table->string('color', '20')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('categories')->insert([
            ['name'=>'Interfaz de usuario', 'color' =>'#9DAAF2'],
            ['name'=>'Conexión a Base de datos', 'color'=>'#A7BC5B'],
            ['name'=>'Cálculos', 'color'=>'#161F6D'],
            ['name'=>'Reportes y documentos', 'color'=>'#F1B814'],
            ['name'=>'Gestión de usuarios y permisos', 'color'=>'#9CF6FB'],
            ['name'=>'Procesamiento de Pagos', 'color'=>'#ABF62D'],
            ['name'=>'Notificaciones y Alertas', 'color'=>'#D76F30'],
            ['name'=>'Compatibilidad', 'color'=>'#D48166'],
            ['name'=>'Instalación y configuración', 'color'=>'#6BB77B'],
            ['name'=>'Otros', 'color'=>'#425664'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
}
