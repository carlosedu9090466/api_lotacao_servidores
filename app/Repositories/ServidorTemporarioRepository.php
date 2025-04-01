<?php

namespace App\Repositories;

use App\Models\Pessoa;
use App\Models\ServidorTemporario;

class ServidorTemporarioRepository
{

    public function createServidorTemporario($dados)
    {
        return ServidorTemporario::create($dados->all());
    }

    public function existsServidorTemporario($id_pessoa)
    {
        return ServidorTemporario::where('pes_id', $id_pessoa)->exists();
    }

    public function getServidorTemporario($id_pessoa)
    {
        return ServidorTemporario::where('pes_id', $id_pessoa)->first();
    }

    public function getAllServidorTemporario()
    {
        $todosServidoresTemporarios = Pessoa::whereHas('servidorTemporario')
        ->with(['lotacao.unidadeLotacao', 'servidorTemporario'])
        ->get()
        ->map(function ($pessoa) {
            return [
                'nome' => $pessoa->pes_nome,
                'idade' => Pessoa::calcularIdade($pessoa->pes_data_nascimento),
                'unidade' => $pessoa->lotacao->unidadeLotacao->unid_nome ?? 'Sem unidade definida',
                //'matricula' => $pessoa->servidorEfetivo->se_matricula,
                'fotografia' => $pessoa->fotografia ?? null
            ];
        });

        return $todosServidoresTemporarios;
    }

    public function getServidoresTemporariosPorUnidade($unid_id)
    {
        $servidoresTemporarios = Pessoa::whereHas('lotacao', function ($query) use ($unid_id) {
            $query->where('unid_id', $unid_id);
        })
        ->whereHas('servidorTemporario')
        ->with(['lotacao.unidadeLotacao', 'servidorTemporario'])
        ->get()
        ->map(function ($pessoa) {
            return [
                'nome' => $pessoa->pes_nome,
                'idade' => Pessoa::calcularIdade($pessoa->pes_data_nascimento),
                'unidade' => $pessoa->lotacao->unidadeLotacao->unid_nome ?? null,
                //'matricula' => $pessoa->servidorEfetivo->se_matricula,
                'fotografia' => $pessoa->fotografia ?? null
            ];
        });

        return $servidoresTemporarios;

    }


    public function updateServidorTemporario($id_pessoa, $dadosNovos)
    {

        $servidorTemporario = self::getServidorTemporario($id_pessoa);

        if (!$servidorTemporario) {
            return null;
        }

        $servidorTemporario->fill($dadosNovos);
        $servidorTemporario->save();
        return $servidorTemporario;
    }

    public function deleteServidorTemporarioById($id_pessoa)
    {

        $servidorTemporario = self::getServidorTemporario($id_pessoa);

        if (!$servidorTemporario) {
            return null;
        }
        $servidorTemporario->delete();
        return true;
    }
}
