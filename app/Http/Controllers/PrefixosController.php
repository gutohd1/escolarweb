<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Buscaprefixos;
use App\Buscas;
use App\Filas;
use Exception;
use Carbon\Carbon;
use Curl;

class PrefixosController extends Controller
{
    public function criarBusca($status = 1, $qtd = 685)
    {
        $busca = new Buscas;
        $busca->status = $status;
        $busca->data = Carbon::now('Europe/London');
        $busca->iniciado = Carbon::now('Europe/London');
        //$busca->finalizado = Carbon::now('Europe/London');
        $busca->exp = $qtd;
        //$busca->qtd =
        $busca->save();
        for($i =1; $i <= $qtd; $i++){
            $fila = new Filas;
            $fila->createFila($busca->id, $i);
        }
        $busca->save();
        return ['status' => 'success'];
    }

    public function statusBusca($buscaID)
    {
        $busca = Buscas::where('id', $buscaID)->first();
        if($busca){
            if($busca->status == 1){
                $fila = Filas::where('busca', $buscaID)->where('status', 2)->get();
                $porcentagem = (count($fila)*100)/$busca->exp;
                return ['status' => 'success', 'porcentagem' => round($porcentagem, 2)];
            }else{
                return ['status' => 'success', 'porcentagem' => 100];
            }
        }
        return ['status' => 'error'];
    }

    //percorre fila realizando a busca
    public function percorreFila($engine = 0)
    {
        $continua = true;
        while ($continua == true){
            $fila = new Filas;
            $fila = $fila->getActives(5);
            if(count($fila) > 0){
                foreach($fila as $key => $item){
                    $item->status = 1;
                    $item->engine = $engine;
                    $item->save();
                    $fila[$key] = $item;
                }
                foreach($fila as $key => $item){
                    $retornoBusca = $this->buscaPrefixo($item->busca, $item->prefixo);
                    if($retornoBusca){
                        $item->status = 2;
                    }else{
                        $item->status = 3;
                    }
                    $item->save();
                }
                //$continua = $this->checkIfContinue($fila->busca);
            }else{
                $continua = false;
            }
        }
    }

    //checa se a fila ainda tem itens e se deve continuar buscando
    public function checkIfContinue($buscaID)
    {
        $busca = Buscas::where('id', $buscaID)->first();
        if($busca->status == 1){
            $fila = Filas::where('busca', $buscaID)->where('status', 0)->get();
            if(count($fila) > 0){
                return true;
            }else{
                $prefixos = Buscaprefixos::where('busca', $buscaID)->get();
                if($prefixos){
                    $busca->qtd = count($prefixos);
                }
                $busca->status = 2;
                $busca->save();
            }
        }
        return false;
    }

    //busca prefixo
    private function buscaPrefixo($busca, $prefixo)
    {
        set_time_limit(0);
        $url = 'http://www.eptc.com.br/Eptc_Consultas/consulta_prefixo_escolar.asp?busca_prefixo='.$prefixo;
        $curl = Curl::to($url);
        $curl->withOption( 'USERAGENT', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13' );
        $curl->withOption('ENCODING','gzip');
        $page = htmlqp($curl->get());
        if($page->length > 0){
            $table = $page->find('form > div > table')->eq(1);
            $tr = $table->find('tr')->eq(1);
            $tds = $tr->find('td')->each(function($i, $e){return $e;});
            if(strpos($tds->eq(0)->text(), 'cadastrado') === false){
                $prefixo = Array();
                $escolasPorPref = Array();
                $prefixo = new Buscaprefixos;

                $prefixo->busca = $busca;
                $prefixo->prefixo = $tds->eq(0)->text();
                $prefixo->permissionario = $tds->eq(1)->text();

                $prefixo->telefones = implode(' ', $this->validaTelefone($tds->eq(2)->text()));
                $prefixo->data = Carbon::now('Europe/London');

                $trEscolas = $table->find('tr')->eq(2);
                $escolas = $trEscolas->find('a')->each(function($i, $e){return $e;});
                foreach($escolas as $escola){
                    $id = preg_replace("/^\D*escola=(\d+)&\D*$/", '$1', $escola->attr('href'));
                    $text = trim(str_replace("\xc2\xa0",' ',$escola->text()));   
                    $escolasPorPref[$id] = $text;

                }
                $prefixo->escolas = json_encode($escolasPorPref);
                $prefixo->save();
            }
            return true;
        }
        return false;
    }
    private function validaTelefone($telefones)
    {
        $telefones = trim($telefones);
        $telefones = str_replace(' e ', ', ', $telefones);
        $telefones = explode(', ', $telefones);
        foreach($telefones as $key => $telefone){
            $telefones[$key] = preg_replace('/[^0-9]/', '', $telefones[$key]);
        }
        return $telefones;
    } 
}
