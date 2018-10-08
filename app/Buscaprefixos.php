<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Buscaprefixos extends Model
{
    protected $fillable = [
        'prefixo', 'permissionario', 'hash', 'telefones', 'escolas',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->hash = md5($model->prefixo.$model->permissionario.$model->telefones.$model->data.$model->escolas);
        });
    }
}
