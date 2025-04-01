<?php
namespace App\Repositories;

use App\Models\Pessoa;
use App\Models\PessoaEndereco;


class PessoaEnderecoRepository {

    public function createPessoaEndereco($dados)
    {
        return PessoaEndereco::create($dados->all());
    }

    public function existsPessoaEndereco($id_pessoa){
        return PessoaEndereco::where('pes_id',$id_pessoa)->exists();
    }


    public function getRelacionamentoPessoaEndereco($id_pessoa){
        return PessoaEndereco::where('pes_id',$id_pessoa)->first();
    }


    public function getEnderecoByIdPessoa($id_pessoa)
    {
        return Pessoa::with('pessoaEndereco.cidade')->find($id_pessoa);
    }

    public function getAllPessoaEndereco(){
        return Pessoa::with('pessoaEndereco.cidade')
                ->orderBy('pes_id')
                ->get();
    }


    public function updatePessoaEndereco($id_pessoa,$dadosNovos){

        $pessoaEndereco = self::getRelacionamentoPessoaEndereco($id_pessoa);

        if(!$pessoaEndereco){
            return null;
        }

        $pessoaEndereco->fill($dadosNovos);
        $pessoaEndereco->save();
        return $pessoaEndereco;

    }


    public function deletePessoaEndereco($id_pessoa)
    {

        $pessoaEndereco = self::getRelacionamentoPessoaEndereco($id_pessoa);

        if (!$pessoaEndereco) {
            return null;
        }
        $pessoaEndereco->delete();
        return true;
    }

    


}

?>