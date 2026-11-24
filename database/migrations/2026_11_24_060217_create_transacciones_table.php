<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id();

            // restrictOnDelete: no se puede eliminar una cuenta o categoría con transacciones
            $table->foreignId('cuenta_id')
                ->constrained('cuentas')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('categoria_id')
                ->constrained('categorias')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->enum('tipo', ['ingreso', 'gasto']);
            $table->decimal('monto', 18, 2);
            $table->date('fecha');
            $table->string('descripcion', 255)->nullable();
            $table->string('creado_por', 150)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transacciones');
    }
};
