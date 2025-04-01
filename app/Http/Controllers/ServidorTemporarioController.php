<?php

namespace App\Http\Controllers;

use App\Models\ServidorTemporario;
use App\Models\ValidacaoId;
use App\Repositories\ServidorTemporarioRepository;
use Illuminate\Http\Request;

class ServidorTemporarioController extends Controller
{
    private ServidorTemporarioRepository $servidorTemporarioRepository;
    private ServidorTemporario $servidorTemporario;
    private ValidacaoId $validacaoId;

    public function __construct(ServidorTemporarioRepository $servidorTemporarioRepository, ServidorTemporario $servidorTemporario, ValidacaoId $validacaoId)
    {
        $this->servidorTemporarioRepository = $servidorTemporarioRepository;
        $this->servidorTemporario = $servidorTemporario;
        $this->validacaoId = $validacaoId;
    }
    
    //todos os temporarios vinculados
    public function getAllServidoresTemporarios(){
      
        $servidoresTemporarios = $this->servidorTemporarioRepository->getAllServidorTemporario();

        $messagem = $servidoresTemporarios->isEmpty() ? 'Nenhuma servidor temporario encontrado.' : 'Servidores temporario listadas com sucesso.';

        return response()->json([
            'data' => $servidoresTemporarios,
            'message' => $messagem
        ], 200);

    }

    public function getServidoresTemporariosPorUnidade($id_unidade){

        $erroValidacaoID = $this->validacaoId->validarId($id_unidade);
        if($erroValidacaoID){
            return response()->json($erroValidacaoID, 422);
        }
        $servidoresTemporarioPorUnidade = $this->servidorTemporarioRepository->getServidoresTemporariosPorUnidade($id_unidade);
        
        $messagem = $servidoresTemporarioPorUnidade->isEmpty() ? 'Nenhuma servidor temporario encontrado.' : 'Servidores temporarios listadas com sucesso.';

        return response()->json([
            'data' => $servidoresTemporarioPorUnidade,
            'message' => $messagem
        ], 200);

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


    public function update(Request $request, $id_pessoa){
      
        $erroValidacaoID = $this->servidorTemporario->validarId($id_pessoa);
        if($erroValidacaoID){
            return response()->json($erroValidacaoID, 422);
        }
        
        $validacaoDados = $this->servidorTemporario->validarDados($request->all());
        if ($validacaoDados) {
            return response()->json($validacaoDados, 422);
        }
      
        $servidorTemporarioAtualizado = $this->servidorTemporarioRepository->updateServidorTemporario($id_pessoa,$request->all());

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


    public function destroy($id_pessoa){

        $erroValidacaoID = $this->servidorTemporario->validarId($id_pessoa);
        if($erroValidacaoID){
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
