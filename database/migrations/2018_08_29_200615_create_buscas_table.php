<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuscasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buscas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('status');
            $table->date('data');
            $table->dateTime('iniciado');
            $table->dateTime('finalizado')->nullable();
            $table->integer('exp');
            $table->integer('qtd')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buscas');
    }
}
