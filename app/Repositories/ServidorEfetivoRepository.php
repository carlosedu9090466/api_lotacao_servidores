<?php

namespace App\Repositories;

use App\Models\Pessoa;
use App\Models\ServidorEfetivo;

class ServidorEfetivoRepository
{

    public function createServidorEfetivo($dados)
    {
        return ServidorEfetivo::create($dados->all());
    }

    public function existsServidorEfetivo($id_pessoa)
    {
        return ServidorEfetivo::where('pes_id', $id_pessoa)->exists();
    }

    public function existsMatricula($id_matricula)
    {
        return ServidorEfetivo::where('se_matricula', $id_matricula)->exists();
    }


    public function getServidorEfetivo($id_pessoa)
    {
        return ServidorEfetivo::where('pes_id', $id_pessoa)->first();
    }

    public function getAllServidorEfetivo($perPage = 15, $page = 1)
    {
        return Pessoa::whereHas('servidorEfetivo')
            ->with(['lotacao.unidadeLotacao', 'servidorEfetivo'])
            ->orderBy('pes_nome') // Adicionando ordenação por nome
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(function ($pessoa) {
                return [
                    'nome' => $pessoa->pes_nome,
                    'idade' => Pessoa::calcularIdade($pessoa->pes_data_nascimento),
                    'unidade' => $pessoa->lotacao->unidadeLotacao->unid_nome ?? 'Sem unidade definida',
                    'fotografia' => $pessoa->fotografia ?? null
                ];
            });
    }

    public function getServidoresEfetivosPorUnidade($unid_id, $perPage = 15, $page = 1)
{
    return Pessoa::whereHas('lotacao', function ($query) use ($unid_id) {
            $query->where('unid_id', $unid_id);
        })
        ->whereHas('servidorEfetivo')
        ->with(['lotacao.unidadeLotacao', 'servidorEfetivo'])
        ->orderBy('pes_nome') // Adicionando ordenação por nome
        ->paginate($perPage, ['*'], 'page', $page)
        ->through(function ($pessoa) {
            return [
                'nome' => $pessoa->pes_nome,
                'idade' => Pessoa::calcularIdade($pessoa->pes_data_nascimento),
                'unidade' => $pessoa->lotacao->unidadeLotacao->unid_nome ?? null,
                'fotografia' => $pessoa->fotografia ?? null
            ];
        });
}



    public function updateServidorEfetivo($id_pessoa, $dadosNovos)
    {

        $servidorEfetivo = self::getServidorEfetivo($id_pessoa);

        if (!$servidorEfetivo) {
            return null;
        }

        $servidorEfetivo->fill($dadosNovos);
        $servidorEfetivo->save();
        return $servidorEfetivo;
    }

    public function deleteServidorEfetivoById($id_pessoa)
    {

        $servidorEfetivo = self::getServidorEfetivo($id_pessoa);

        if (!$servidorEfetivo) {
            return null;
        }
        $servidorEfetivo->delete();
        return true;
    }
}
