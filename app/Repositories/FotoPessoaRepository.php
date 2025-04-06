<?php
namespace App\Repositories;
use App\Models\FotoPessoa;

class FotoPessoaRepository {

    public function createFotoPessoa($pes_id,$hash,$extensao){
        return FotoPessoa::create([
            'pes_id' => $pes_id,
            'fp_hash' => $hash,
            'fp_bucket' => env('MINIO_BUCKET'),
            'fp_data' => now(),
            'fp_extensao' => $extensao
        ]);
    }

    public function getFotoAllPessoaById($pes_id){
        return FotoPessoa::where('pes_id',$pes_id)->get();
    }

    public function getFotoById($id_foto){
        return FotoPessoa::findOrFail($id_foto);
    }

    
}

?>