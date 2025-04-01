<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\FotoPessoa;
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

    public function __construct(PessoaRepository $pessoaRepository, FotoPessoa $fotoPessoa, FotoPessoaRepository $fotoPessoaRepository)
    {
        $this->pessoaRepository = $pessoaRepository;
        $this->fotoPessoaRepository = $fotoPessoaRepository;
        $this->fotoPessoa = $fotoPessoa;
    }


    public function upload(Request $request, $pes_id)
    {
        if (!$this->pessoaRepository->getPessoaById($pes_id)) {
            return response()->json([
                'message' => 'Essa pessoa não existe no sistema!'
            ], 404);
        }
        $arquivos = $request->allFiles();

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
        $extensao = 'png';
        $caminho = "pessoas/{$foto->pes_id}/{$foto->fp_hash}.{$extensao}";

        $customS3 = new S3Client([
            'version' => 'latest',
            'region' => config('filesystems.disks.s3.region'),
            'endpoint' => config('filesystems.disks.s3.url'), // Já usa o endpoint público
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
            'use_path_style_endpoint' => true,
        ]);
    
        $comando = $customS3->getCommand('GetObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $caminho
        ]);
    
        $request = $customS3->createPresignedRequest($comando, '+5 minutes');
        $url = (string) $request->getUri();
    
        return response()->json([
            'url' => $url,
            'expira_em' => now()->addMinutes(5)->toDateTimeString()
        ]);
    }

    public function getFotosAllPessoaById($pes_id)
    {

        $fotos = FotoPessoa::where('pes_id', $pes_id)->get();
        $extensao = 'png';
        $links = [];

        foreach ($fotos as $foto) {
            $caminho = "pessoas/{$foto->pes_id}/{$foto->fp_hash}.{$extensao}";

            $customS3 = new S3Client([
                'version' => 'latest',
                'region' => config('filesystems.disks.s3.region'),
                'endpoint' => config('filesystems.disks.s3.url'), // Já usa o endpoint público
                'credentials' => [
                    'key' => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ],
                'use_path_style_endpoint' => true,
            ]);
        
            $comando = $customS3->getCommand('GetObject', [
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'Key' => $caminho
            ]);
        
            $request = $customS3->createPresignedRequest($comando, '+5 minutes');
            $url = (string) $request->getUri();
            $links[] = $url;
        }

        return response()->json([
            'pes_id' => $pes_id,
            'links' => $links,
        ]);
    }
}
