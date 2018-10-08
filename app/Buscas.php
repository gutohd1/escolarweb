<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Buscas extends Model
{
    protected $fillable = [
        'status', 'data', 'iniciado', 'finalizado', 'exp', 'qtd'
    ];
}
