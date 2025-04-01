<?php
namespace App\Repositories;
use App\Models\Endereco;

class EnderecoRepository {
    
    public function createEndereco($dados)
    {
        return Endereco::create($dados->all());
    }

    public function getEnderecoById($id_endereco)
    {
        return Endereco::find($id_endereco);
    }

    public function getAllEndereco()
    {
        return Endereco::all();
    }

    public function updateEnderecoById($id_endereco, $dadosEndereco)
    {

        $endereco = self::getEnderecoById($id_endereco);

        if (!$endereco) {
            return null;
        }
        $endereco->fill($dadosEndereco);
        $endereco->save();

        return $endereco;
    }

    public function deleteEnderecoById($id_endereco)
    {

        $endereco = self::getEnderecoById($id_endereco);

        if (!$endereco) {
            return null;
        }
        $endereco->delete();
        return true;
    }


}

?>