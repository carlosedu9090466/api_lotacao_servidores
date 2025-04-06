<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CidadeController;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\FotoPessoaController;
use App\Http\Controllers\LotacaoController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\PessoaEnderecoController;
use App\Http\Controllers\ServidorEfetivoController;
use App\Http\Controllers\ServidorTemporarioController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\UnidadeEnderecoController;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh-token', [AuthController::class, 'refreshToken']);


//PROTEÇÃO ROTAS
Route::middleware(['jwt.expiration'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    //****************ROTAS PESSOA ******************************************//
    Route::apiResource('pessoa', PessoaController::class);
    Route::post('/pessoa/endereco', [PessoaEnderecoController::class, 'store']);
    Route::put('/pessoa/{pes_id}/endereco', [PessoaEnderecoController::class, 'update']);
    Route::get('/pessoa/{pes_id}/endereco', [PessoaEnderecoController::class, 'getEnderecoByPessoaId']);
    Route::get('/pessoaAll/enderecoAll', [PessoaEnderecoController::class, 'index']);
    Route::delete('/pessoa/{pes_id}/endereco', [PessoaEnderecoController::class, 'destroy']);
    //**************** END ROTAS PESSOA ******************************************/

     
    /************FOTOS*************************************************** */

    Route::post('/pessoa/{pes_id}/fotos', [FotoPessoaController::class, 'upload']);
    Route::get('/pessoa/fotos/{foto_id}', [FotoPessoaController::class, 'getFoto']);
    Route::get('/pessoa/fotos/todas/{pes_id}',[FotoPessoaController::class, 'getFotosAllPessoaById']);

    /************END FOTOD********************************************** */


    //****************ROTAS UNIDADE ******************************************
    Route::apiResource('unidade', UnidadeController::class);
    Route::get('/unidadeAll/enderecoAll', [UnidadeEnderecoController::class, 'index']);
    //vinculando a unidade ao endereço
    Route::post('/unidade/endereco', [UnidadeEnderecoController::class, 'store']);
    Route::get('/unidade/{id}/endereco', [UnidadeEnderecoController::class, 'getEnderecoByUnidade']);
    Route::put('/unidade/{unid_id}/endereco', [UnidadeEnderecoController::class, 'update']);
    //**************** END ROTAS UNIDADE ******************************************


    //**************** ROTAS ENDERECO ******************************************
    Route::apiResource('endereco', EnderecoController::class);
    //****************END ROTAS CIDADE ******************************************

    //**************** ROTAS CIDADE ******************************************
    Route::apiResource('cidade', CidadeController::class);
    //**************** END ROTAS CIDADE ******************************************


    /***********************ROTAS SERVIDOR EFETIVO****************************************/
    Route::get('/servidor/efetivo/', [ServidorEfetivoController::class, 'getAllServidoresEfetivos']);
    Route::post('/servidor/efetivo', [ServidorEfetivoController::class, 'store']);
    Route::put('/servidor/efetivo/{pes_id}', [ServidorEfetivoController::class, 'update']);
    Route::delete('/servidor/efetivo/{pes_id}', [ServidorEfetivoController::class, 'destroy']);
    Route::get('/servidor/efetivo/unidade/{unid_id}', [ServidorEfetivoController::class, 'getServidoresEfetivosPorUnidade']);
    /**************************END ROTAS SERVIDOR EFETIVO**************************************/


    /*******************ROTA SERVIDOR TEMPORARIO*************/
    Route::get('/servidor/temporario/', [ServidorTemporarioController::class, 'getAllServidoresTemporarios']);
    Route::post('/servidor/temporario', [ServidorTemporarioController::class, 'store']);
    Route::put('/servidor/temporario/{pes_id}', [ServidorTemporarioController::class, 'update']);
    Route::delete('/servidor/temporario/{pes_id}', [ServidorTemporarioController::class, 'destroy']);
    Route::get('/servidor/temporario/unidade/{unid_id}', [ServidorTemporarioController::class, 'getServidoresTemporariosPorUnidade']);
    /**************END ROTA SERVIDOR TEMPORARIO *************/


    /*****************ROTAS DE LOTACAO**************** */
    Route::post('/lotacao', [LotacaoController::class, 'store']);
    Route::get('/lotacao', [LotacaoController::class, 'index']);
    Route::get('/lotacao/{lot_id}', [LotacaoController::class, 'show']);
    Route::put('/lotacao/{lot_id}', [LotacaoController::class, 'update']);
    Route::delete('/lotacao/{lot_id}', [LotacaoController::class, 'destroy']);

    Route::get('/lotacao/servidor/enderecoFuncional', [LotacaoController::class, 'getEnderecoFuncionalServidorEfetivoPorNome']);
    /*****************END ROTAS DE LOTACAO**************** */
   
  
});
