<?php
namespace App\Repositories;
use App\Models\FotoPessoa;

class FotoPessoaRepository {

    public function createFotoPessoa($pes_id,$hash){
        return FotoPessoa::create([
            'pes_id' => $pes_id,
            'fp_hash' => $hash,
            'fp_bucket' => env('MINIO_BUCKET'),
            'fp_data' => now()
        ]);
    }

    public function getFotoPessoaById($foto_id){
        FotoPessoa::findOrFail($foto_id)->toArray();
    }
    
}

?>