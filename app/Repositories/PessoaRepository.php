<?php
namespace App\Repositories;
use App\Models\Pessoa;


class PessoaRepository {

    public function getPessoaById($id_pessoa){
        return Pessoa::find($id_pessoa);
    }

    public function getAllPessoa($porPage, $page){
        return Pessoa::paginate($porPage, ['*'], 'page', $page);
    }

    public function createPessoa($dados){
        return Pessoa::create($dados->all());
    }

    public function updatePessoaById($id_pessoa,$dadosPessoa){

        $pessoa = self::getPessoaById($id_pessoa);
        
        if(!$pessoa){
            return null;
        }
        
        $pessoa->fill($dadosPessoa);
        $pessoa->save();
        
        return $pessoa;
    }

    public function deletePessoaById($id_pessoa){

        $pessoa = self::getPessoaById($id_pessoa);

        if(!$pessoa){
            return null;
        }

        $pessoa->delete();
        return true;
    }


}

?>