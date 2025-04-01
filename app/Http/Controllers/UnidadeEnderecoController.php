<?php

namespace App\Http\Controllers;

use App\Models\UnidadeEndereco;
use App\Repositories\UnidadeEnderecoRepository;
use Illuminate\Http\Request;

class UnidadeEnderecoController extends Controller
{

    private UnidadeEnderecoRepository $unidadeEnderecoRepository;
    private UnidadeEndereco $unidadeEndereco;

    public function __construct(UnidadeEnderecoRepository $unidadeEnderecoRepository, UnidadeEndereco $unidadeEndereco)
    {
        $this->unidadeEnderecoRepository = $unidadeEnderecoRepository;
        $this->unidadeEndereco = $unidadeEndereco;
    }

    public function index(){

        $unidadeEndereco = $this->unidadeEnderecoRepository->getAllUnidadeEnderecoCidade();
        $messagem = $unidadeEndereco->isEmpty() ? 'Nenhuma cidade x endereço encontrada.' : 'Cidades x Endereço listadas com sucesso.';

        return response()->json([
            'data' => $unidadeEndereco,
            'message' => $messagem
        ], 200);

    }

    public function store(Request $request)
    {
        $validacaoDados = $this->unidadeEndereco->validarDados($request->all());
        if ($validacaoDados) {
            return response()->json($validacaoDados, 422);
        }

        //validação caso a unidade já possua endereço a vinculado.
        if($this->unidadeEnderecoRepository->existsUnidadeEndereco($request->unid_id)){
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

    public function update(Request $request, $id_unidade){
      
        $erroValidacaoID = $this->unidadeEndereco->validarId($id_unidade);
        if($erroValidacaoID){
            return response()->json($erroValidacaoID, 422);
        }
        
        $unidadeEnderecoAtualizado = $this->unidadeEnderecoRepository->updateUnidadeEndereco($id_unidade,$request->all());
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

    public function getEnderecoByUnidade($id_unidade){
        
        $erroValidacaoID = $this->unidadeEndereco->validarId($id_unidade);
        if($erroValidacaoID){
            return response()->json($erroValidacaoID, 422);
        }
        
        $unidade = $this->unidadeEnderecoRepository->getEnderecoByIdUnidade($id_unidade);

        if(!$unidade){
            return response()->json(['message' => 'Unidade não encontrada'], 404);
        }

        return response()->json([
            'data' => $unidade,
            'message' => 'Unidade Encontrada com o seu endereço.'
        ]);
        
    }

    //FALTA FAZER O DELETE DESSE MODEL UnidadeEndereco

}
