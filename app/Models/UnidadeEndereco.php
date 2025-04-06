<?php

namespace App\Models;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;

class UnidadeEndereco extends Model
{
    //
    protected $table = 'unidade_endereco';
    protected $fillable = ['unid_id', 'end_id'];

    protected $dates = ['date'];
    protected $guarded = [];
    

    private static function regras()
    {
        $regras = [
            'unid_id' => 'required|exists:unidade,unid_id',
             'end_id' => 'required|exists:endereco,end_id'
        ];

        return $regras;
    }


    private static function feedback()
    {

        $feedback = [
            'required' => 'o campo :attribute deve ser preenchido.',
            'unid_id.exists' => 'A cidade informada não foi encontrada no sistema.',
            'end_id.exists' => 'O endereço informado não foi encontrado no sistema.'
        ];

        return $feedback;
    }

    public static function validarDados($dados_pessoa)
    {

        $validator = Validator::make($dados_pessoa, self::regras(), self::feedback());

        if ($validator->fails()) {
            return [
                'error' => $validator->errors(),
                'message' => 'Erro na validação dos dados!'
            ];
        }

        return null;
    }


    // Uma unidade_endereco pertence a uma unidade
    public function unidade()
    {
        return $this->belongsTo(Unidade::class, 'unid_id', 'unid_id');
    }

       // Uma unidade_endereco pertence a um endereço
    public function endereco()
    {
        return $this->belongsTo(Endereco::class, 'end_id', 'end_id');
    }


}
