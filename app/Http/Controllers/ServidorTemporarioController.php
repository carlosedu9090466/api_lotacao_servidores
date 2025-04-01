<?php

namespace App\Http\Controllers;

use App\Models\Pagination;
use App\Models\ServidorTemporario;
use App\Models\ValidacaoId;
use App\Repositories\ServidorTemporarioRepository;
use Illuminate\Http\Request;

class ServidorTemporarioController extends Controller
{
    private ServidorTemporarioRepository $servidorTemporarioRepository;
    private ServidorTemporario $servidorTemporario;
    private ValidacaoId $validacaoId;
    private Pagination $pagination;

    public function __construct(ServidorTemporarioRepository $servidorTemporarioRepository, ServidorTemporario $servidorTemporario, ValidacaoId $validacaoId, Pagination $pagination)
    {
        $this->servidorTemporarioRepository = $servidorTemporarioRepository;
        $this->servidorTemporario = $servidorTemporario;
        $this->validacaoId = $validacaoId;
        $this->pagination = $pagination;
    }

    //todos os temporarios vinculados
    public function getAllServidoresTemporarios(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $servidoresTemporarios = $this->servidorTemporarioRepository->getAllServidorTemporario($perPage, $page);

        $message = $servidoresTemporarios->isEmpty()
            ? 'Nenhum servidor temporário encontrado.'
            : 'Servidores temporários listados com sucesso.';

        return response()->json(
            $this->pagination->format($servidoresTemporarios, $message),
            200
        );
    }

    public function getServidoresTemporariosPorUnidade($id_unidade, Request $request)
    {
        // Validação do ID
        $erroValidacaoID = $this->validacaoId->validarId($id_unidade);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $servidoresTemporarioPorUnidade = $this->servidorTemporarioRepository
            ->getServidoresTemporariosPorUnidade($id_unidade, $perPage, $page);

        $message = $servidoresTemporarioPorUnidade->isEmpty()
            ? 'Nenhum servidor temporário encontrado.'
            : 'Servidores temporários listados com sucesso.';

        return response()->json(
            $this->pagination->format($servidoresTemporarioPorUnidade, $message),
            200
        );
    }

    public function store(Request $request)
    {
        $validacaoDados = $this->servidorTemporario->validarDados($request->all());
        if ($validacaoDados) {
            return response()->json($validacaoDados, 422);
        }

        if ($this->servidorTemporarioRepository->existsServidorTemporario($request->pes_id)) {
            return response()->json([
                'message' => 'Esta pessoa já possui o status de servidor temporario.'
            ], 422);
        }

        $servidor_temporario = $this->servidorTemporarioRepository->createServidorTemporario($request);
        return response()->json([
            'data' => $servidor_temporario,
            'message' => 'Novo servidor temporario registrado com sucesso!'
        ], 201);
    }


    public function update(Request $request, $id_pessoa)
    {

        $erroValidacaoID = $this->servidorTemporario->validarId($id_pessoa);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $validacaoDados = $this->servidorTemporario->validarDados($request->all());
        if ($validacaoDados) {
            return response()->json($validacaoDados, 422);
        }

        $servidorTemporarioAtualizado = $this->servidorTemporarioRepository->updateServidorTemporario($id_pessoa, $request->all());

        if (!$servidorTemporarioAtualizado) {
            return response()->json([
                'message' => 'O servidor não foi encontrado no sistema.'
            ], 404);
        }

        return response()->json([
            'data' => $servidorTemporarioAtualizado,
            'message' => 'As datas foram atualizadas com sucesso!'
        ]);
    }


    public function destroy($id_pessoa)
    {

        $erroValidacaoID = $this->servidorTemporario->validarId($id_pessoa);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $servidorTemporario = $this->servidorTemporarioRepository->deleteServidorTemporarioById($id_pessoa);
        if (!$servidorTemporario) {
            return response()->json([
                'message' => 'O servidor temporario não foi encontrado.'
            ], 404);
        }

        return response()->json([
            'message' => 'Servidor temporario deletado com sucesso!'
        ]);
    }
}
