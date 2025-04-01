<?php

namespace App\Repositories;
use App\Models\Unidade;


class UnidadeRepository
{

    public function createUnidade($dados)
    {
        return Unidade::create($dados->all());
    }

    public function getUnidadeById($id_unidade)
    {
        return Unidade::find($id_unidade);
    }

    public function getAllUnidade()
    {
        return Unidade::all();
    }

    public function updateUnidadeById($id_unidade, $dadosUnidade)
    {

        $unidade = self::getUnidadeById($id_unidade);

        if (!$unidade) {
            return null;
        }
        $unidade->fill($dadosUnidade);
        $unidade->save();

        return $unidade;
    }

    public function deleteUnidadeById($id_unidade)
    {

        $unidade = self::getUnidadeById($id_unidade);

        if (!$unidade) {
            return null;
        }
        $unidade->delete();
        return true;
    }
}
