<?php

namespace App\Http\Controllers;

use App\Models\Pagination;
use App\Models\UnidadeEndereco;
use App\Models\ValidacaoId;
use App\Repositories\UnidadeEnderecoRepository;
use Illuminate\Http\Request;

class UnidadeEnderecoController extends Controller
{

    private UnidadeEnderecoRepository $unidadeEnderecoRepository;
    private UnidadeEndereco $unidadeEndereco;
    private Pagination $pagination;
    private ValidacaoId $validacaoId;

    public function __construct(UnidadeEnderecoRepository $unidadeEnderecoRepository, UnidadeEndereco $unidadeEndereco, Pagination $pagination, ValidacaoId $validacaoId)
    {
        $this->unidadeEnderecoRepository = $unidadeEnderecoRepository;
        $this->unidadeEndereco = $unidadeEndereco;
        $this->pagination = $pagination;
        $this->validacaoId = $validacaoId;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $unidadeEndereco = $this->unidadeEnderecoRepository->getAllUnidadeEnderecoCidade($perPage, $page);

        $message = $unidadeEndereco->isEmpty()
            ? 'Nenhuma unidade com endereço encontrada.'
            : 'Unidades com endereços listadas com sucesso.';

        return response()->json(
            $this->pagination->format($unidadeEndereco, $message),
            200
        );
    }

    public function store(Request $request)
    {
        $validacaoDados = $this->unidadeEndereco->validarDados($request->all());
        if ($validacaoDados) {
            return response()->json($validacaoDados, 422);
        }

        //validação caso a unidade já possua endereço a vinculado.
        if ($this->unidadeEnderecoRepository->existsUnidadeEndereco($request->unid_id)) {
            return response()->json([
                'message' => 'Esta unidade já possui um endereço cadastrado.'
            ], 422);
        }

        $unidade_endereco = $this->unidadeEnderecoRepository->createUnidadeEndereco($request);
        return response()->json([
            'data' => $unidade_endereco,
            'message' => 'A vinculação entre a Unidade e endereço foi cadastrada com sucesso!'
        ], 201);
    }

    public function update(Request $request, $id_unidade)
    {

        $erroValidacaoID = $this->validacaoId->validarId($id_unidade);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $unidadeEnderecoAtualizado = $this->unidadeEnderecoRepository->updateUnidadeEndereco($id_unidade, $request->all());
        if (!$unidadeEnderecoAtualizado) {
            return response()->json([
                'message' => 'Vinculo entre a Unidade e endereço não encontrado.'
            ], 404);
        }

        return response()->json([
            'data' => $unidadeEnderecoAtualizado,
            'message' => 'Vínculo atualizado com sucesso!'
        ]);
    }

    public function getEnderecoByUnidade($id_unidade, Request $request)
    {
        // Validação do ID
        $erroValidacaoID = $this->validacaoId->validarId($id_unidade);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $resultado = $this->unidadeEnderecoRepository->getEnderecoByIdUnidade($id_unidade, $perPage, $page);

        if (!$resultado['unidade']) {
            return response()->json(['message' => 'Unidade não encontrada'], 404);
        }

        return response()->json([
            'data' => [
                'unidade' => $resultado['unidade'],
                'enderecos' => $resultado['unidade']->enderecos,
                'meta' => [
                    'current_page' => $resultado['pagination']['current_page'],
                    'per_page' => $resultado['pagination']['per_page'],
                    'total' => $resultado['pagination']['total'],
                    'last_page' => ceil($resultado['pagination']['total'] / $resultado['pagination']['per_page'])
                ],
                'links' => [
                    'first' => url("/api/unidades/{$id_unidade}/enderecos?page=1"),
                    'last' => url("/api/unidades/{$id_unidade}/enderecos?page=" . ceil($resultado['pagination']['total'] / $resultado['pagination']['per_page'])),
                    'prev' => $page > 1 ? url("/api/unidades/{$id_unidade}/enderecos?page=" . ($page - 1)) : null,
                    'next' => $page < ceil($resultado['pagination']['total'] / $resultado['pagination']['per_page']) ?
                        url("/api/unidades/{$id_unidade}/enderecos?page=" . ($page + 1)) : null
                ]
            ],
            'message' => 'Unidade encontrada com seus endereços.'
        ], 200);
    }

}
