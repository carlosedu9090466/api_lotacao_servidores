<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\Pagination;
use App\Models\ValidacaoId;
use App\Repositories\EnderecoRepository;
use Illuminate\Http\Request;

class EnderecoController extends Controller
{
    private EnderecoRepository $enderecoRepository;
    private Endereco $endereco;
    private Pagination $pagination;
    private ValidacaoId $validacaoId;

    public function __construct(EnderecoRepository $enderecoRepository, Endereco $endereco, Pagination $pagination, ValidacaoId $validacaoId)
    {
        $this->enderecoRepository = $enderecoRepository;
        $this->endereco = $endereco;
        $this->pagination = $pagination;
        $this->validacaoId = $validacaoId;
    }

    public function index(Request $request)
    {

        $enderecos = $this->enderecoRepository->getAllEndereco(
            $request->get('per_page', 10),
            $request->get('page', 1)
        );

        return response()->json(
            $this->pagination->format(
                $enderecos,
                $enderecos->isEmpty() ? 'Nenhum endereço encontrado.' : 'Endereços listados com sucesso.'
            ),
            200
        );
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
        $erroValidacaoID = $this->validacaoId->validarId($id);
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

        $erroValidacaoID = $this->validacaoId->validarId($id);
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
