<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuscaprefixosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buscaprefixos', function (Blueprint $table) {
            $table->integer('busca');
            $table->string('prefixo', 3);
            $table->string('permissionario', 100);
            $table->string('telefones', 100);
            $table->date('data');
            $table->mediumText('escolas');
            $table->string('hash', 100);
            $table->timestamps();
            $table->primary(['prefixo', 'data']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buscaprefixos');
    }
}
