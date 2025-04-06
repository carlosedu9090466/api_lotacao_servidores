<?php

namespace App\Models;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;

class PessoaEndereco extends Model
{
    protected $table = 'pessoa_endereco';
    protected $fillable = ['pes_id', 'end_id'];

    protected $dates = ['date'];
    protected $guarded = [];


    public function pessoa(){
        return $this->belongsTo(Pessoa::class, 'pes_id','pes_id');
    }

    public function endereco(){
        return $this->belongsTo(Endereco::class, 'end_id', 'end_id');
    }


    private static function regras()
    {
        $regras = [
            'pes_id' => 'required|exists:pessoa,pes_id',
            'end_id' => 'required|exists:endereco,end_id'
        ];

        return $regras;
    }


    private static function feedback()
    {

        $feedback = [
            'required' => 'o campo :attribute deve ser preenchido.',
            'pes_id.exists' => 'A pessoa informada não foi encontrada no sistema.',
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



}
