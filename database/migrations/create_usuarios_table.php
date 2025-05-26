<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            
            // FK a roles
            $table->unsignedBigInteger('id_rol');
            
            // Otros campos
            $table->string('direccion')->unique();  // correo electrónico
            $table->string('clave');               // contraseña
            

            // Constraint
            $table
              ->foreign('id_rol')
              ->references('id')
              ->on('rols')
              ->onDelete('cascade')
              ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['id_rol']);
        });
        Schema::dropIfExists('usuarios');
    }
};

