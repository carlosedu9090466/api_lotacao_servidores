<?php

namespace App\Models;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    protected $table = 'cidade';
    protected  $primaryKey = 'cid_id';
    protected $dates = ['date'];
    protected $guarded = [];

    protected $fillable = ['cid_nome','cid_uf'];


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


    private static function regras()
    {
        $regras = [
            'cid_nome' => 'required|min:10|max:200',
            'cid_uf' => 'required|max:2',
        ];

        return $regras;
    }


    private static function feedback()
    {

        $feedback = [
            'required' => 'o campo :attribute deve ser preenchido.',
            'cid_nome.min' => 'o campo nome deve ter no mínino 10 caracteres.',
            'cid_nome.max' => 'o campo nome deve ter no máximo 200 caracteres.',
            'cid_uf.max' => 'o campo nome deve ter no máximo 2 caracteres.',
        ];

        return $feedback;
    }

    public static function validarDados($dados_pessoa){

        $validator = Validator::make($dados_pessoa, self::regras(), self::feedback());

        if ($validator->fails()) {
            return [
                'error' => $validator->errors(),
                'message' => 'Erro na validação dos dados!'
            ];
        }

        return null; 

    }

    //relacionamento entre a cidade e endereco. 1 cidade -> N endereco.
     
    //tabela Endereco = FK; tabela Cidade = PK
    public function enderecos()
    {
        return $this->hasMany(Endereco::class, 'cid_id', 'cid_id');
    }

}
