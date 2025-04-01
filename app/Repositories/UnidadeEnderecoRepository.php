<?php

namespace App\Repositories;

use App\Models\Unidade;
use App\Models\UnidadeEndereco;


class UnidadeEnderecoRepository
{

    public function createUnidadeEndereco($dados)
    {
        return UnidadeEndereco::create($dados->all());
    }

    public function existsUnidadeEndereco($id_unidade){
        return UnidadeEndereco::where('unid_id',$id_unidade)->exists();
    }


    public function getRelacionamentoUnidadeEndereco($id_unidade){
        return UnidadeEndereco::where('unid_id',$id_unidade)->first();
    }

    public function updateUnidadeEndereco($id_unidade,$dadosNovos){
        $unidadeEndereco = self::getRelacionamentoUnidadeEndereco($id_unidade);

        if(!$unidadeEndereco){
            return null;
        }

        $unidadeEndereco->fill($dadosNovos);
        $unidadeEndereco->save();

        return $unidadeEndereco;

    }


    public function getAllUnidadeEnderecoCidade(){
        return Unidade::with('enderecos.cidade')->get(); 
    }

    public function getEnderecoByIdUnidade($id_unidade)
    {
        return Unidade::with('enderecos.cidade')->find($id_unidade);
    }
}
