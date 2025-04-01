<?php

namespace App\Http\Controllers;

use App\Models\Pagination;
use App\Models\ServidorEfetivo;
use App\Models\ValidacaoId;
use App\Repositories\ServidorEfetivoRepository;
use Illuminate\Http\Request;

class ServidorEfetivoController extends Controller
{

    private ServidorEfetivoRepository $servidorEfetivoRepository;
    private ServidorEfetivo $servidorEfetivo;
    private ValidacaoId $validacaoId;
    private Pagination $pagination;

    public function __construct(ServidorEfetivoRepository $servidorEfetivoRepository, ServidorEfetivo $servidorEfetivo, ValidacaoId $validacaoId, Pagination $pagination)
    {
        $this->servidorEfetivoRepository = $servidorEfetivoRepository;
        $this->servidorEfetivo = $servidorEfetivo;
        $this->validacaoId = $validacaoId;
        $this->pagination = $pagination;
    }

    public function getAllServidoresEfetivos(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $servidoresEfetivos = $this->servidorEfetivoRepository->getAllServidorEfetivo($perPage, $page);

        $message = $servidoresEfetivos->isEmpty()
            ? 'Nenhum servidor efetivo encontrado.'
            : 'Servidores efetivos listados com sucesso.';

        return response()->json(
            $this->pagination->format($servidoresEfetivos, $message),
            200
        );
    }

    public function getServidoresEfetivosPorUnidade($id_unidade, Request $request)
    {
        // Validação do ID
        $erroValidacaoID = $this->validacaoId->validarId($id_unidade);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $servidoresEfetivosPorUnidade = $this->servidorEfetivoRepository
            ->getServidoresEfetivosPorUnidade($id_unidade, $perPage, $page);

        $message = $servidoresEfetivosPorUnidade->isEmpty()
            ? 'Nenhum servidor efetivo encontrado.'
            : 'Servidores efetivos listados com sucesso.';

        return response()->json(
            $this->pagination->format($servidoresEfetivosPorUnidade, $message),
            200
        );
    }




    public function store(Request $request)
    {
        $validacaoDados = $this->servidorEfetivo->validarDados($request->all());
        if ($validacaoDados) {
            return response()->json($validacaoDados, 422);
        }

        if ($this->servidorEfetivoRepository->existsServidorEfetivo($request->pes_id)) {
            return response()->json([
                'message' => 'Esta pessoa já possui o status de servidor efetivo.'
            ], 422);
        }

        if ($this->servidorEfetivoRepository->existsMatricula($request->se_matricula)) {
            return response()->json([
                'message' => 'Sitema não aceita matrícula duplicada!'
            ], 422);
        }


        $servidor_efetivo = $this->servidorEfetivoRepository->createServidorEfetivo($request);
        return response()->json([
            'data' => $servidor_efetivo,
            'message' => 'Novo servidor efetivo registrado com sucesso!'
        ], 201);
    }


    //atualizar a matricula efetiva
    public function update(Request $request, $id_pessoa)
    {

        $erroValidacaoID = $this->validacaoId->validarId($id_pessoa);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $servidorEfetivoAtualizado = $this->servidorEfetivoRepository->updateServidorEfetivo($id_pessoa, $request->all());

        if (!$servidorEfetivoAtualizado) {
            return response()->json([
                'message' => 'O servidor não foi encontrado no sistema.'
            ], 404);
        }

        return response()->json([
            'data' => $servidorEfetivoAtualizado,
            'message' => 'A matricula atualizada com sucesso!'
        ]);
    }

    public function destroy($id_pessoa)
    {

        $erroValidacaoID = $this->servidorEfetivo->validarId($id_pessoa);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $cidade = $this->servidorEfetivoRepository->deleteServidorEfetivoById($id_pessoa);
        if (!$cidade) {
            return response()->json([
                'message' => 'O servidor efetivo não foi encontrado.'
            ], 404);
        }

        return response()->json([
            'message' => 'Servidor efetivo deletado com sucesso!'
        ]);
    }
}
