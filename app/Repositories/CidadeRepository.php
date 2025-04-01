<?php
namespace App\Repositories;
use App\Models\Cidade;

class CidadeRepository {

    public function createCidade($dados)
    {
        return Cidade::create($dados->all());
    }

    public function getCidadeById($id_cidade)
    {
        return Cidade::find($id_cidade);
    }

    public function getAllCidade()
    {
        return Cidade::all();
    }

    public function updateCidadeById($id_cidade, $dadosCidade)
    {

        $cidade = self::getCidadeById($id_cidade);

        if (!$cidade) {
            return null;
        }
        $cidade->fill($dadosCidade);
        $cidade->save();

        return $cidade;
    }

    public function deleteCidadeById($id_cidade)
    {

        $cidade = self::getCidadeById($id_cidade);

        if (!$cidade) {
            return null;
        }
        $cidade->delete();
        return true;
    }


}

?>