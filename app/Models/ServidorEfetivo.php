<?php

namespace App\Models;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;

class ServidorEfetivo extends Model
{
    protected $table = 'servidor_efetivo';

    protected $fillable = ['pes_id','se_matricula'];
    protected $guarded = [];



    private static function regras()
    {
        $regras = [
            'pes_id' => 'required|exists:pessoa,pes_id',
            'se_matricula' => 'required|max:20',
        ];

        return $regras;
    }


    private static function feedback()
    {

        $feedback = [
            'required' => 'o campo :attribute deve ser preenchido.',
            'pes_id.exists' => 'A pessoa informada não foi encontrada no sistema.',
            'se_matricula.max' => 'A matricula deve ter 20 caracteres.'
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

  
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id', 'pes_id');
    }


}
