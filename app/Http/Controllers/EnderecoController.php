<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Repositories\EnderecoRepository;
use Illuminate\Http\Request;

class EnderecoController extends Controller
{
    private EnderecoRepository $enderecoRepository;
    private Endereco $endereco;

    public function __construct(EnderecoRepository $enderecoRepository, Endereco $endereco)
    {
        $this->enderecoRepository = $enderecoRepository;
        $this->endereco = $endereco;
    }

    public function index()
    {
        $endereco = $this->enderecoRepository->getAllEndereco();

        $messagem = $endereco->isEmpty() ? 'Nenhum endereço encontrada.' : 'Endereços listados com sucesso.';

        return response()->json([
            'data' => $endereco,
            'message' => $messagem
        ], 200);
    }



    public function store(Request $request)
    {
        $validacaoDados = $this->endereco->validarDados($request->all());
        if ($validacaoDados) {
            return response()->json($validacaoDados, 422);
        }
        $endereco = $this->enderecoRepository->createEndereco($request);
        return response()->json([
            'data' => $endereco,
            'message' => 'Endereço cadastrado com sucesso!'
        ], 201);
    }


    public function show($id)
    {
        $erroValidacaoID = $this->endereco->validarId($id);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $endereco = $this->enderecoRepository->getEnderecoById($id);
        if (!$endereco) {
            return response()->json([
                'message' => 'Endereço não encontrado.'
            ], 404);
        }

        return response()->json([
            'data' => $endereco,
            'message' => 'Endereço encontrado com sucesso.'
        ]);
    }



    public function update(Request $request, $id)
    {

        $erroValidacaoID = $this->endereco->validarId($id);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }
        $enderecoAtualizado = $this->enderecoRepository->updateEnderecoById($id, $request->all());
        if (!$enderecoAtualizado) {
            return response()->json([
                'message' => 'Endereço não encontrado.'
            ], 404);
        }

        return response()->json([
            'data' => $enderecoAtualizado,
            'message' => 'Endereço atualizado com sucesso!'
        ]);
    }

    public function destroy($id)
    {
        $erroValidacaoID = $this->endereco->validarId($id);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }
        $endereco = $this->enderecoRepository->deleteEnderecoById($id);
        if (!$endereco) {
            return response()->json([
                'message' => 'Endereço não encontrado.'
            ], 404);
        }

        return response()->json([
            'message' => 'Endereço deletado com sucesso!'
        ]);
    }
}
