<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Repositories\PessoaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PessoaController extends Controller
{

    private PessoaRepository $pessoaRepository;
    private Pessoa $pessoa;

    public function __construct(PessoaRepository $pessoaRepository, Pessoa $pessoa)
    {
        $this->pessoaRepository = $pessoaRepository;
        $this->pessoa = $pessoa;
    }

    public function index()
    {
        $pessoas = $this->pessoaRepository->getAllPessoa();

        $messagem = $pessoas->isEmpty() ? 'Nenhuma pessoa encontrada' : 'Pessoas listadas com sucesso.';

        return response()->json([
            'data' => $pessoas,
            'message' => $messagem
        ], 200);
    }

    // Criar algum servidor 
    public function store(Request $request)
    {
        $validacaoDados = $this->pessoa->validarDados($request->all()); 
        if($validacaoDados){
            return response()->json($validacaoDados, 422);
        }

        $pessoa = $this->pessoaRepository->createPessoa($request);
        return response()->json([
            'data' => $pessoa,
            'message' => 'Pessoa cadastrada com sucesso!'
        ], 201);
    }

    //exibir algum servidor específico
    public function show($id)
    {
        $erroValidacaoID = $this->pessoa->validarId($id);
        if($erroValidacaoID){
            return response()->json($erroValidacaoID, 422);
        }

        $pessoa = $this->pessoaRepository->getPessoaById($id);
        if (!$pessoa) {
            return response()->json([
                'message' => 'Pessoa não encontrada.'
            ], 404);
        }

        return response()->json([
            'data' => $pessoa,
            'message' => 'Pessoa encontrada com sucesso.'
        ]);
    }


    public function update(Request $request, $id)
    {
        
        $erroValidacaoID = $this->pessoa->validarId($id);
        if($erroValidacaoID){
            return response()->json($erroValidacaoID, 422);
        }
        
        $pessoaAtualizada = $this->pessoaRepository->updatePessoaById($id,$request->all());
        if (!$pessoaAtualizada) {
            return response()->json([
                'message' => 'Pessoa não encontrada.'
            ], 404);
        }
        
        return response()->json([
            'data' => $pessoaAtualizada,
            'message' => 'Pessoa atualizada com sucesso!'
        ]);

    }


    public function destroy($id)
    {
        $erroValidacaoID = $this->pessoa->validarId($id);
        if($erroValidacaoID){
            return response()->json($erroValidacaoID, 422);
        }

        $pessoa = $this->pessoaRepository->deletePessoaById($id);
        if (!$pessoa) {
            return response()->json([
                'message' => 'Pessoa não encontrada.'
            ], 404);
        }

        return response()->json([
            'message' => 'Pessoa deletada com sucesso!'
        ]);

    }
}
