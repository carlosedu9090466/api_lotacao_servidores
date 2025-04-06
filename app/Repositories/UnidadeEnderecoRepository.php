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

    public function existsUnidadeEndereco($id_unidade)
    {
        return UnidadeEndereco::where('unid_id', $id_unidade)->exists();
    }


    public function getRelacionamentoUnidadeEndereco($id_unidade)
    {
        return UnidadeEndereco::where('unid_id', $id_unidade)->first();
    }

    public function updateUnidadeEndereco($id_unidade, $dadosNovos)
    {
        $unidadeEndereco = self::getRelacionamentoUnidadeEndereco($id_unidade);

        if (!$unidadeEndereco) {
            return null;
        }

        $unidadeEndereco->fill($dadosNovos);
        $unidadeEndereco->save();

        return $unidadeEndereco;
    }


    public function getAllUnidadeEnderecoCidade($perPage = 15, $page = 1)
    {
        return Unidade::with('enderecos.cidade')
            ->orderBy('unid_nome')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getEnderecoByIdUnidade($id_unidade, $perPage = 15, $page = 1)
    {
        $unidade = Unidade::with(['enderecos' => function ($query) use ($perPage, $page) {
            $query->paginate($perPage, ['*'], 'page', $page);
        }, 'enderecos.cidade'])
            ->find($id_unidade);

        return [
            'unidade' => $unidade,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $unidade ? $unidade->enderecos()->count() : 0
            ]
        ];
    }
}
