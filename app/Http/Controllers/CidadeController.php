<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Pagination;
use App\Models\ValidacaoId;
use App\Repositories\CidadeRepository;
use Illuminate\Http\Request;

class CidadeController extends Controller
{

    private CidadeRepository $cidadeRepository;
    private Cidade $cidade;
    private Pagination $pagination;
    private ValidacaoId $validacaoId;

    public function __construct(CidadeRepository $cidadeRepository, Pagination $pagination, ValidacaoId $validacaoId, Cidade $cidade)
    {
        $this->cidadeRepository = $cidadeRepository;
        $this->pagination = $pagination;
        $this->cidade = $cidade;
        $this->validacaoId = $validacaoId;
    }

    public function index(Request $request)
    {
        $cidades = $this->cidadeRepository->getAllCidade(
            $request->get('per_page', 10),
            $request->get('page', 1)
        );

        return response()->json(
            $this->pagination->format(
                $cidades,
                $cidades->isEmpty() ? 'Nenhum endereço encontrado.' : 'Endereços listados com sucesso.'
            ),
            200
        );
    }



    public function store(Request $request)
    {
        $validacaoDados = $this->cidade->validarDados($request->all());
        if ($validacaoDados) {
            return response()->json($validacaoDados, 422);
        }
        $cidade = $this->cidadeRepository->createCidade($request);
        return response()->json([
            'data' => $cidade,
            'message' => 'Cidade cadastrada com sucesso!'
        ], 201);
    }


    public function show($id)
    {
        $erroValidacaoID = $this->validacaoId->validarId($id);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $cidade = $this->cidadeRepository->getCidadeById($id);
        if (!$cidade) {
            return response()->json([
                'message' => 'Cidade não encontrada.'
            ], 404);
        }

        return response()->json([
            'data' => $cidade,
            'message' => 'Cidade encontrada com sucesso.'
        ]);
    }



    public function update(Request $request, $id)
    {

        $erroValidacaoID = $this->validacaoId->validarId($id);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $cidadeAtualizada = $this->cidadeRepository->updateCidadeById($id, $request->all());
        if (!$cidadeAtualizada) {
            return response()->json([
                'message' => 'Cidade não encontrada.'
            ], 404);
        }

        return response()->json([
            'data' => $cidadeAtualizada,
            'message' => 'Cidade atualizada com sucesso!'
        ]);
    }

    public function destroy($id)
    {
        $erroValidacaoID = $this->validacaoId->validarId($id);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }
        $cidade = $this->cidadeRepository->deleteCidadeById($id);
        if (!$cidade) {
            return response()->json([
                'message' => 'Cidade não encontrada.'
            ], 404);
        }

        return response()->json([
            'message' => 'Cidade deletada com sucesso!'
        ]);
    }
}
