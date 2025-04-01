<?php

namespace App\Http\Controllers;

use App\Models\Unidade;
use App\Repositories\UnidadeRepository;
use Illuminate\Http\Request;

class UnidadeController extends Controller
{

    private UnidadeRepository $unidadeRepository;
    private Unidade $unidade;

    public function __construct(UnidadeRepository $unidadeRepository, Unidade $unidade)
    {
        $this->unidadeRepository = $unidadeRepository;
        $this->unidade = $unidade;
    }
    
    public function index()
    {
        $unidade = $this->unidadeRepository->getAllUnidade();

        $messagem = $unidade->isEmpty() ? 'Nenhuma unidade encontrada' : 'Unidades listadas com sucesso.';

        return response()->json([
            'data' => $unidade,
            'message' => $messagem
        ], 200);
    }

   
    public function store(Request $request)
    {
        $validacaoDados = $this->unidade->validarDados($request->all()); 
        if($validacaoDados){
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
        $erroValidacaoID = $this->unidade->validarId($id);
        if($erroValidacaoID){
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
        
        $erroValidacaoID = $this->unidade->validarId($id);
        if($erroValidacaoID){
            return response()->json($erroValidacaoID, 422);
        }
  
        $unidadeAtualizada = $this->unidadeRepository->updateUnidadeById($id,$request->all());
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
        $erroValidacaoID = $this->unidade->validarId($id);
        if($erroValidacaoID){
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
