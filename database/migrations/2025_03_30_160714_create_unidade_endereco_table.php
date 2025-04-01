<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('unidade_endereco', function (Blueprint $table) {
            $table->id();

            // FK Unidade
            $table->unsignedBigInteger('unid_id');
            $table->foreign('unid_id')->references('unid_id')->on('unidade')->onDelete('cascade');

            // FK Endereco
            $table->unsignedBigInteger('end_id');
            $table->foreign('end_id')->references('end_id')->on('endereco')->onDelete('cascade');

            //unid_id seja único na tabela UnidadeEndereco
            $table->unique('unid_id'); 

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('unidade_endereco');
    }
};
