<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('servidor_temporario', function (Blueprint $table) {
            $table->id();
            $table->date('st_data_admissao');
            $table->date('st_data_demissao');
            $table->unsignedBigInteger('pes_id');
            
            $table->foreign('pes_id')->references('pes_id')->on('pessoa')->onDelete('cascade');
            $table->unique('pes_id'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servidor_temporario');
    }
};
