<?php

namespace App\Http\Controllers;

use App\Models\Lotacao;
use App\Models\ValidacaoId;
use App\Repositories\LotacaoRepository;
use Illuminate\Http\Request;
use App\Models\Pagination;

class LotacaoController extends Controller
{

    private LotacaoRepository $lotacaoRepository;
    private Lotacao $lotacao;
    private ValidacaoId $validacaoId;
    private Pagination $pagination;

    public function __construct(LotacaoRepository $lotacaoRepository, Lotacao $lotacao, ValidacaoId $validacaoId,  Pagination $pagination)
    {
        $this->lotacaoRepository = $lotacaoRepository;
        $this->lotacao = $lotacao;
        $this->validacaoId = $validacaoId;
        $this->pagination = $pagination;
    }

    public function index(Request $request)
    {
        $lotacoes = $this->lotacaoRepository->getAllLotacao(
            $request->get('per_page', 10),
            $request->get('page', 1)
        );

        return response()->json(
            $this->pagination->format(
                $lotacoes,
                $lotacoes->isEmpty() ? 'Nenhum endereço encontrado.' : 'Endereços listados com sucesso.'
            ),
            200
        );
    }

    public function getEnderecoFuncionalServidorEfetivoPorNome(Request $request){

            
        $resultado = $this->lotacaoRepository->getEnderecoFuncionalPorNome(
            $request->pes_nome,
            $request->get('per_page', 15),
            $request->get('page', 1)
        );
    
        if ($resultado['collection']->isEmpty()) {
            return response()->json([
                'message' => 'Não foi encontrado nenhuma informação no sistema.'
            ], 422);
        }
    
        return response()->json(
            $this->pagination->format(
                $resultado['collection'],
                'Endereço encontrado com sucesso!'
            ),
            200
        );

        $enderecoFuncional = $this->lotacaoRepository->getEnderecoFuncionalPorNome($request->pes_nome);

        if(!$enderecoFuncional){
            return response()->json([
                'message' => 'Não foi encontrado nenhuma informação no sistema.'
            ], 422);
        }

        return response()->json([
            'data' => $enderecoFuncional,
            'message' => 'Endereço encontrado com sucesso!'
        ], 201);

    }


    public function store(Request $request)
    {
        $validacaoDados = $this->lotacao->validarDados($request->all());
        if ($validacaoDados) {
            return response()->json($validacaoDados, 422);
        }

        if($this->lotacaoRepository->existServidorLotacaoUnidade($request->unid_id, $request->pes_id)){
            return response()->json([
                'message' => 'Este Servidor já se encontra lotado nessa unidade.'
            ], 422);
        }

        $lotacao = $this->lotacaoRepository->createLotacao($request);
        return response()->json([
            'data' => $lotacao,
            'message' => 'Lotacão cadastrada com sucesso!'
        ], 201);
    }


    public function show($id)
    {
        $erroValidacaoID = $this->validacaoId->validarId($id);
        if ($erroValidacaoID) {
            return response()->json($erroValidacaoID, 422);
        }

        $unidade = $this->lotacaoRepository->getLotacaoById($id);
        if (!$unidade) {
            return response()->json([
                'message' => 'Lotação não encontrada.'
            ], 404);
        }

        return response()->json([
            'data' => $unidade,
            'message' => 'Lotacão encontrada com sucesso.'
        ]);
    }

    public function update(Request $request, $id)
    {
        
        $erroValidacaoID = $this->validacaoId->validarId($id);
        if($erroValidacaoID){
            return response()->json($erroValidacaoID, 422);
        }

        $lotacaoAtualizada = $this->lotacaoRepository->updateLotacaoById($id,$request->all());
        
        if (!$lotacaoAtualizada) {
            return response()->json([
                'message' => 'Lotação não encontrada.'
            ], 404);
        }
        
        return response()->json([
            'data' => $lotacaoAtualizada,
            'message' => 'Lotação atualizada com sucesso!'
        ]);

    }

    public function destroy($id)
    {
        $erroValidacaoID = $this->validacaoId->validarId($id);

        if($erroValidacaoID){
            return response()->json($erroValidacaoID, 422);
        }
        $lotacao = $this->lotacaoRepository->deleteLotacaoById($id);
        if (!$lotacao) {
            return response()->json([
                'message' => 'Lotação não encontrada.'
            ], 404);
        }

        return response()->json([
            'message' => 'Lotação deletada com sucesso!'
        ]);

    }

}
