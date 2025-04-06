<?php

namespace App\Http\Controllers;

use App\Models\Pagination;
use App\Models\Unidade;
use App\Models\ValidacaoId;
use App\Repositories\UnidadeRepository;
use Illuminate\Http\Request;

class UnidadeController extends Controller
{

    private UnidadeRepository $unidadeRepository;
    private Unidade $unidade;
    private ValidacaoId $validacaoId;
    private Pagination $pagination;

    public function __construct(UnidadeRepository $unidadeRepository, Unidade $unidade, Pagination $pagination, ValidacaoId $validacaoId)
    {
        $this->unidadeRepository = $unidadeRepository;
        $this->unidade = $unidade;
        $this->pagination = $pagination;
        $this->validacaoId = $validacaoId;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $unidades = $this->unidadeRepository->getAllUnidade($perPage, $page);

        $message = $unidades->isEmpty()
            ? 'Nenhuma unidade encontrada.'
            : 'Unidades listadas com sucesso.';

        return response()->json(
            $this->pagination->format($unidades, $message),
            200
        );
    }


    public function store(Request $request)
    {
        $validacaoDados = $this->unidade->validarDados($request->all());
        if ($validacaoDados) {
            return response()->json($validacaoDados, 422);
        }
        $unidade = $this->unidadeRepository->createUnidade($request);
        return response()->json([
            'data' => $unidade,
            'message' => 'Unidade cadastrada com sucesso!'
        ], 201);
    }


    public function show($id)
    {
        $erroValidacaoID = $this->validacaoId->validarId($id);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $unidade = $this->unidadeRepository->getUnidadeById($id);
        if (!$unidade) {
            return response()->json([
                'message' => 'Unidade não encontrada.'
            ], 404);
        }

        return response()->json([
            'data' => $unidade,
            'message' => 'Unidade encontrada com sucesso.'
        ]);
    }



    public function update(Request $request, $id)
    {

        $erroValidacaoID = $this->validacaoId->validarId($id);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $unidadeAtualizada = $this->unidadeRepository->updateUnidadeById($id, $request->all());
        if (!$unidadeAtualizada) {
            return response()->json([
                'message' => 'Unidade não encontrada.'
            ], 404);
        }

        return response()->json([
            'data' => $unidadeAtualizada,
            'message' => 'Unidade atualizada com sucesso!'
        ]);
    }

    public function destroy($id)
    {
        $erroValidacaoID = $this->validacaoId->validarId($id);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $unidade = $this->unidadeRepository->deleteUnidadeById($id);
        if (!$unidade) {
            return response()->json([
                'message' => 'Unidade não encontrada.'
            ], 404);
        }

        return response()->json([
            'message' => 'Unidade deletada com sucesso!'
        ]);
    }
}
