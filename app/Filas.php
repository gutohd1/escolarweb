<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Filas extends Model
{
	protected $fillable = [
        'busca', 'status', 'engine', 'prefixo'
    ];

    public function createFila($busca, $prefixo)
    {
    	$fila = new $this;
    	$fila->busca = $busca;
    	$fila->status = 0;
    	$fila->prefixo = $prefixo;
    	$fila->save();
    }
    public function getActives($qtd = 1)
    {
    	$fila = new $this;
        $fila = $fila->where('status', 0);
        if($qtd == 1){
            $fila = $fila->first();
        }else{
            $fila = $fila->limit($qtd)->get();
        }
    	return $fila;;
    }
}
