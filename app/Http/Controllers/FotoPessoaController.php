<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\FotoPessoa;
use App\Repositories\FotoPessoaRepository;
use App\Repositories\PessoaRepository;
use Illuminate\Http\Request;

class FotoPessoaController extends Controller
{

    private PessoaRepository $pessoaRepository;
    private FotoPessoaRepository $fotoPessoaRepository;
    private FotoPessoa $fotoPessoa;

    public function __construct(PessoaRepository $pessoaRepository, FotoPessoa $fotoPessoa, FotoPessoaRepository $fotoPessoaRepository)
    {
        $this->pessoaRepository = $pessoaRepository;
        $this->fotoPessoaRepository = $fotoPessoaRepository;
        $this->fotoPessoa = $fotoPessoa;
    }


    public function upload(Request $request, $pes_id)
    {
        // Verifica se a pessoa existe
        if (!$this->pessoaRepository->getPessoaById($pes_id)) {
            return response()->json([
                'message' => 'Essa pessoa não existe no sistema!'
            ], 404);
        }

        // Obtém todos os arquivos da requisição
        $arquivos = $request->allFiles();

        // Verifica se há arquivos enviados
        if (empty($arquivos)) {
            return response()->json([
                'message' => 'Nenhuma foto foi enviada!'
            ], 400);
        }

        $fotos = [];

        foreach ($arquivos as $foto) {
            // Gera hash único para o nome do arquivo
            $hash = md5($foto->getClientOriginalName() . time());
            $extensao = $foto->getClientOriginalExtension();
            $caminho = "pessoas/{$pes_id}/{$hash}.{$extensao}";

            // Salva no MinIO (S3)
            Storage::disk('s3')->putFileAs("pessoas/{$pes_id}", $foto, "{$hash}.{$extensao}");

            // Registra a foto no banco de dados
            $fotoPessoa = $this->fotoPessoaRepository->createFotoPessoa($pes_id, $hash);

            $fotos[] = $fotoPessoa;
        }

        return response()->json([
            'data' => $fotos,
            'message' => 'Fotos salvas com sucesso!'
        ], 201);
    }

    public function getFoto($foto_id)
    {
        $foto = FotoPessoa::findOrFail($foto_id);

        // Obtém o caminho correto do arquivo no MinIO (verifique se 'fp_hash' é o nome correto)
        $caminho = "pessoas/{$foto->pes_id}/{$foto->fp_hash}";

        // Gera uma URL temporária válida no MinIO (S3)
        $url = Storage::disk('s3')->temporaryUrl("laravel/{$caminho}", now()->addMinutes(5));

        return response()->json([
            'url' => $url,
            'expira_em' => now()->addMinutes(5)->toDateTimeString()
        ]);
    }
}
