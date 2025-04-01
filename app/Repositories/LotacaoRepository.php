<?php

namespace App\Repositories;

use App\Models\Lotacao;
use App\Models\Pessoa;

class LotacaoRepository
{

    public function createLotacao($dados)
    {
        return Lotacao::create($dados->all());
    }

    public function existServidorLotacaoUnidade($id_unidade, $id_pessoa)
    {
        return Lotacao::where('unid_id', $id_unidade)
            ->where('pes_id', $id_pessoa)
            ->exists();
    }

    public function getAllLotacao()
    {
        return Lotacao::all();
    }

    public function getLotacaoById($id_lotacao)
    {
        return Lotacao::find($id_lotacao);
    }


    public function getEnderecoFuncionalPorNome($nome_servidor)
    {

        $enderecoFuncionalUnidade = Pessoa::where('pes_nome', 'LIKE', "%{$nome_servidor}%")
            ->whereHas('servidorEfetivo')
            ->whereHas('lotacao.unidadeLotacao.enderecos') 
            ->with('lotacao.unidadeLotacao.enderecos') 
            ->get()
            ->map(function ($pessoa) {
                return $pessoa->lotacao->unidadeLotacao->enderecos->map(function ($endereco) {
                    return [
                        'end_tipo_logradouro' => $endereco->end_tipo_logradouro,
                        'end_logradouro' => $endereco->end_logradouro,
                        'end_numero' => $endereco->end_numero,
                        'end_bairro' => $endereco->end_bairro
                    ];
                });
            });

        return $enderecoFuncionalUnidade;
    }


    public function updateLotacaoById($id_lotacao, $dadosLotacao)
    {

        $lotacao = self::getLotacaoById($id_lotacao);

        if (!$lotacao) {
            return null;
        }
        $lotacao->fill($dadosLotacao);
        $lotacao->save();

        return $lotacao;
    }

    public function deleteLotacaoById($id_cidade)
    {
        $lotacao = self::getLotacaoById($id_cidade);

        if (!$lotacao) {
            return null;
        }
        $lotacao->delete();
        return true;
    }
}
