<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('servidor_efetivo', function (Blueprint $table) {
            $table->id();
            $table->string('se_matricula', 20)->unique();
            $table->unsignedBigInteger('pes_id');

            $table->foreign('pes_id')->references('pes_id')->on('pessoa')->onDelete('cascade');
            $table->unique('pes_id'); 

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('servidor_efetivo');
    }
};
