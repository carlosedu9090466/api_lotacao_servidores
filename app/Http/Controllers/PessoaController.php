<?php

namespace App\Http\Controllers;

use App\Models\Pagination;
use App\Models\Pessoa;
use App\Models\ValidacaoId;
use App\Repositories\PessoaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PessoaController extends Controller
{

    private PessoaRepository $pessoaRepository;
    private Pessoa $pessoa;
    private Pagination $pagination;
    private ValidacaoId $validacaoId;

    public function __construct(PessoaRepository $pessoaRepository, Pessoa $pessoa,  Pagination $pagination,ValidacaoId $validacaoId)
    {
        $this->pessoaRepository = $pessoaRepository;
        $this->pessoa = $pessoa;
        $this->pagination = $pagination;
        $this->validacaoId = $validacaoId;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $pessoas = $this->pessoaRepository->getAllPessoa($perPage, $page);

        $message = $pessoas->isEmpty() ? 'Nenhuma pessoa encontrada.' : 'Pessoas listadas com sucesso.';

        return response()->json(
            $this->pagination->format($pessoas, $message),
            200
        );
    }

    // Criar algum servidor 
    public function store(Request $request)
    {
        $validacaoDados = $this->pessoa->validarDados($request->all());
        if ($validacaoDados) {
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
        $erroValidacaoID = $this->validacaoId->validarId($id);
        if ($erroValidacaoID) {
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

        $erroValidacaoID = $this->validacaoId->validarId($id);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $pessoaAtualizada = $this->pessoaRepository->updatePessoaById($id, $request->all());
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
        $erroValidacaoID = $this->validacaoId->validarId($id);
        if ($erroValidacaoID) {
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
