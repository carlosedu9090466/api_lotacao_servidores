<?php

namespace App\Models;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;

class ServidorTemporario extends Model
{
    protected $table = 'servidor_temporario';

    protected $fillable = ['pes_id','st_data_admissao','st_data_demissao'];
    protected $dates = ['date'];
    protected $guarded = [];



    private static function regras()
    {
        $regras = [
            'pes_id' => 'required|exists:pessoa,pes_id',
            'st_data_admissao' => 'required|date',
            'st_data_demissao' => 'required|date',
        ];

        return $regras;
    }


    private static function feedback()
    {

        $feedback = [
            'required' => 'o campo :attribute deve ser preenchido.',
            'pes_id.exists' => 'A pessoa informada não foi encontrada no sistema.',
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


    private static function regrasFind()
    {
        return [
            'id' => 'required|integer|min:1'
        ];
    }

    private static function feedbackFind()
    {
        return [
            'id.integer' => 'O ID deve ser um número inteiro.',
            'id.min' => 'O ID deve ser um número positivo.'
        ];
    }

    public static function validarId($id)
    {
        $data = ['id' => $id]; 
        
        $validator = Validator::make($data, self::regrasFind(), self::feedbackFind());
       
        if ($validator->fails()) {
            return [
                'error' => $validator->errors(),
                'message' => 'Erro no ID!'
            ];
        }

        return null; 
    }

   
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id', 'pes_id');
    }
}
