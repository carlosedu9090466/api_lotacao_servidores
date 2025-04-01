<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Repositories\CidadeRepository;
use Illuminate\Http\Request;

class CidadeController extends Controller
{

    private CidadeRepository $cidadeRepository;
    private Cidade $cidade;

    public function __construct(CidadeRepository $cidadeRepository, Cidade $cidade)
    {
        $this->cidadeRepository = $cidadeRepository;
        $this->cidade = $cidade;
    }
   
    public function index()
    {
        $cidade = $this->cidadeRepository->getAllCidade();

        $messagem = $cidade->isEmpty() ? 'Nenhuma cidade encontrada.' : 'Cidades listadas com sucesso.';

        return response()->json([
            'data' => $cidade,
            'message' => $messagem
        ], 200);
    }


    
    public function store(Request $request)
    {
        $validacaoDados = $this->cidade->validarDados($request->all()); 
        if($validacaoDados){
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
        $erroValidacaoID = $this->cidade->validarId($id);
        if($erroValidacaoID){
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
        
        $erroValidacaoID = $this->cidade->validarId($id);
        if($erroValidacaoID){
            return response()->json($erroValidacaoID, 422);
        }
     
        $cidadeAtualizada = $this->cidadeRepository->updateCidadeById($id,$request->all());
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
        $erroValidacaoID = $this->cidade->validarId($id);
        if($erroValidacaoID){
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
