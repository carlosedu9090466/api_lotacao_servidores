<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\FotoPessoa;
use App\Models\Traits;
use App\Models\ValidacaoId;
use App\Repositories\FotoPessoaRepository;
use App\Repositories\PessoaRepository;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Illuminate\Http\Request;

class FotoPessoaController extends Controller
{

    private PessoaRepository $pessoaRepository;
    private FotoPessoaRepository $fotoPessoaRepository;
    private FotoPessoa $fotoPessoa;
    private Traits $trais;
    private ValidacaoId $validacaoId;

    public function __construct(PessoaRepository $pessoaRepository, FotoPessoa $fotoPessoa, FotoPessoaRepository $fotoPessoaRepository, Traits $trais, ValidacaoId $validacaoId)
    {
        $this->pessoaRepository = $pessoaRepository;
        $this->fotoPessoaRepository = $fotoPessoaRepository;
        $this->fotoPessoa = $fotoPessoa;
        $this->trais = $trais;
        $this->validacaoId = $validacaoId;
    }


    public function upload(Request $request, $pes_id)
    {
        if (!$this->pessoaRepository->getPessoaById($pes_id)) {
            return response()->json([
                'message' => 'Essa pessoa não existe no sistema!'
            ], 404);
        }

        $arquivos = $request->allFiles();
        $fotosNormalizadas = ['fotos' => array_values($arquivos)];
    
        $validacao = $this->fotoPessoa->validarFotos($fotosNormalizadas);

        if ($validacao !== true) {
            return response()->json([
                'message' => $validacao['message'],
                'errors' => $validacao['errors']
            ], 400);
        }

        if (empty($arquivos)) {
            return response()->json([
                'message' => 'Nenhuma foto foi enviada!'
            ], 400);
        }

        $fotos = [];

        foreach ($arquivos as $foto) {

            $hash = md5($foto->getClientOriginalName() . time());

            $extensao = $foto->getClientOriginalExtension();
            $fotoPath = "pessoas/{$pes_id}/{$hash}.{$extensao}";

            Storage::disk('s3')->put($fotoPath, file_get_contents($foto));
            $fotoPessoa = $this->fotoPessoaRepository->createFotoPessoa($pes_id, $hash, $extensao);

            $fotos[] = $fotoPessoa;
        }

        return response()->json([
            'data' => $fotos,
            'message' => 'Fotos salvas com sucesso!'
        ], 201);
    }

    public function getFoto($foto_id)
    {
        $erroValidacaoID = $this->validacaoId->validarId($foto_id);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }
        $foto = $this->fotoPessoaRepository->getFotoById($foto_id);
        $caminho = "pessoas/{$foto->pes_id}/{$foto->fp_hash}.{$foto->fp_extensao}";
        $url = $this->trais->generatePresignedUrl($caminho);

        return response()->json([
            'url' => $url,
            'expira_em' => now()->addMinutes(5)->toDateTimeString()
        ]);
    }

    public function getFotosAllPessoaById($pes_id)
    {

        $erroValidacaoID = $this->validacaoId->validarId($pes_id);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $fotos = $this->fotoPessoaRepository->getFotoAllPessoaById($pes_id);

        $links = [];
        foreach ($fotos as $foto) {
            $caminho = "pessoas/{$foto->pes_id}/{$foto->fp_hash}.{$foto->fp_extensao}";

            $url = $this->trais->generatePresignedUrl($caminho);

            $links[] = $url;
        }

        return response()->json([
            'pes_id' => $pes_id,
            'links' => $links,
            'expira_em' => now()->addMinutes(5)->toDateTimeString()
        ]);
    }
}
