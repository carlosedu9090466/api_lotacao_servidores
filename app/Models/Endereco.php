<?php

namespace App\Models;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{


    protected $table = 'endereco';
    protected $primaryKey = 'end_id';
    protected $dates = ['date'];
    protected $guarded = [];

    protected $fillable = [
        'end_tipo_logradouro',
        'end_logradouro',
        'end_numero',
        'end_bairro',
        'cid_id'
    ];


    private static function regras()
    {
        $regras = [
            'end_tipo_logradouro' => 'required|string|max:50',
            'end_logradouro' => 'required|string|max:200',
            'end_numero' => 'required|integer',
            'end_bairro' => 'required|string|max:200',
            'cid_id' => 'required|exists:cidade,cid_id'
        ];

        return $regras;
    }


    private static function feedback()
    {

        $feedback = [
            'required' => 'o campo :attribute deve ser preenchido.',
            'end_logradouro.max' => 'o campo nome deve ter no máximo 200 caracteres.',
            'end_tipo_logradouro.max' => 'o campo nome deve ter no máximo 50 caracteres.',
            'end_bairro.max' => 'o campo nome deve ter no máximo 200 caracteres.',
            'cid_uf.max' => 'o campo nome deve ter no máximo 2 caracteres.',
            'cid_id.exists' => 'A cidade informada não foi encontrada no sistema.'
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



    // 1 endereco -> 1 cidade
    // FK = tabela Endereco; PK = tabela Cidade
    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'cid_id', 'cid_id');
    }

    // Relacionamento com unidade (N para N)
    public function unidades()
    {
        return $this->belongsToMany(Unidade::class, 'unidade_endereco', 'end_id', 'unid_id');
    }

    public function enderecoPessoa(){
        return $this->belongsToMany(Pessoa::class, 'pessoa_endereco', 'end_id', 'pes_id');
    }

}
