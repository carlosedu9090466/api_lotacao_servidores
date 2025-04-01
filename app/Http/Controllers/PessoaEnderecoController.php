<?php

namespace App\Http\Controllers;

use App\Models\PessoaEndereco;
use App\Repositories\PessoaEnderecoRepository;
use Illuminate\Http\Request;

class PessoaEnderecoController extends Controller
{

    private PessoaEnderecoRepository $pessoaEnderecoRepository;
    private PessoaEndereco $pessoaEndereco;

    public function __construct(PessoaEnderecoRepository $pessoaEnderecoRepository, PessoaEndereco $pessoaEndereco)
    {
        $this->pessoaEnderecoRepository = $pessoaEnderecoRepository;
        $this->pessoaEndereco = $pessoaEndereco;
    }

    public function index(){

        $pessoaEndereco = $this->pessoaEnderecoRepository->getAllPessoaEndereco();
        $messagem = $pessoaEndereco->isEmpty() ? 'Nenhuma pessoa x endereço encontrada.' : 'Pessoas x Endereço listadas com sucesso.';

        return response()->json([
            'data' => $pessoaEndereco,
            'message' => $messagem
        ], 200);

    }

    
    public function store(Request $request)
    {
        $validacaoDados = $this->pessoaEndereco->validarDados($request->all());
        if ($validacaoDados) {
            return response()->json($validacaoDados, 422);
        }

        if($this->pessoaEnderecoRepository->existsPessoaEndereco($request->pes_id)){
            return response()->json([
                'message' => 'Esta pessoa já possui um endereço cadastrado.'
            ], 422);
        }

        $pessoa_endereco = $this->pessoaEnderecoRepository->createPessoaEndereco($request);
        return response()->json([
            'data' => $pessoa_endereco,
            'message' => 'A vinculação entre a pessoa e endereço foi cadastrada com sucesso!'
        ], 201);
    }

    public function update(Request $request, $id_pessoa){
      
        $erroValidacaoID = $this->pessoaEndereco->validarId($id_pessoa);
        if($erroValidacaoID){
            return response()->json($erroValidacaoID, 422);
        }
        $pessoaEnderecoAtualizado = $this->pessoaEnderecoRepository->updatePessoaEndereco($id_pessoa,$request->all());

        if (!$pessoaEnderecoAtualizado) {
            return response()->json([
                'message' => 'Vinculo entre a pessoa e endereço não encontrado.'
            ], 404);
        }
        
        return response()->json([
            'data' => $pessoaEnderecoAtualizado,
            'message' => 'Vínculo atualizado com sucesso!'
        ]);

    }


    public function getEnderecoByPessoaId($id_pessoa){
        
        $erroValidacaoID = $this->pessoaEndereco->validarId($id_pessoa);
        if($erroValidacaoID){
            return response()->json($erroValidacaoID, 422);
        }
        
        $pessoa = $this->pessoaEnderecoRepository->getEnderecoByIdPessoa($id_pessoa);
       
        if(!$pessoa){
            return response()->json(['message' => 'pessoa não encontrada'], 404);
        }
    
        return response()->json([
            'data' => $pessoa,
            'message' => 'Pessoa Encontrada com o seu endereço.'
        ]);
        
    }


    public function destroy($id_pessoa){

        $erroValidacaoID = $this->pessoaEndereco->validarId($id_pessoa);
        if($erroValidacaoID){
            return response()->json($erroValidacaoID, 422);
        }

        $pessoaEndereco = $this->pessoaEnderecoRepository->deletePessoaEndereco($id_pessoa);
        if (!$pessoaEndereco) {
            return response()->json([
                'message' => 'A pessoa não foi encontrada no sistema.'
            ], 404);
        }

        return response()->json([
            'message' => 'O endereço da pessoa foi deletado com sucesso!'
        ]);

    }


}
