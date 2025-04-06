<?php

namespace App\Models;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;

class Unidade extends Model
{
    protected $table = 'unidade';
    protected  $primaryKey = 'unid_id';
    protected $dates = ['date'];
    protected $guarded = [];

    protected $fillable = ['unid_nome','unid_sigla'];



    private static function regras()
    {
        $regras = [
            'unid_nome' => 'required|min:10|max:200',
            'unid_sigla' => 'required|min:2|max:20',
        ];

        return $regras;
    }


    private static function feedback()
    {

        $feedback = [
            'required' => 'o campo :attribute deve ser preenchido.',
            'unid_nome.min' => 'o campo nome deve ter no mínino 10 caracteres.',
            'unid_nome.max' => 'o campo nome deve ter no máximo 200 caracteres.',
            'unid_sigla.min' => 'o campo nome deve ter no mínino 2 caracteres.',
            'unid_sigla.max' => 'o campo nome deve ter no máximo 20 caracteres.',
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


    // Relacionamento com endereco (N para N)
    public function enderecos()
    {
        return $this->belongsToMany(Endereco::class, 'unidade_endereco', 'unid_id', 'end_id');
    }

    public function lotacao(){
        return $this->hasOne(Lotacao::class, 'unid_id', 'unid_id');
    }


}
