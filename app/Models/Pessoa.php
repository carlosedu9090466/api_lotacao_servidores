<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Pessoa extends Model
{
    protected $table = 'pessoa';
    protected  $primaryKey = 'pes_id';
    protected $dates = ['date'];

    protected $fillable = [
        'pes_nome',
        'pes_data_nascimento',
        'pes_sexo',
        'pes_mae',
        'pes_pai'
    ];

    protected $guarded = [];


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
            'pes_nome' => 'required|min:10|max:80',
            'pes_data_nascimento' => 'required|date|before_or_equal:today',
            'pes_sexo' => 'required|in:Masculino,Feminino',
            'pes_mae' => 'nullable|string|max:80',
            'pes_pai' => 'nullable|string|max:80',
        ];

        return $regras;
    }


    private static function feedback()
    {

        $feedback = [
            'required' => 'o campo :attribute deve ser preenchido.',
            'pes_nome.min' => 'o campo nome deve ter no mínino 10 caracteres.',
            'pes_nome.max' => 'o campo nome deve ter no máximo 80 caracteres.',
            'pes_sexo.in' => 'O sexo deve ser "Masculino" ou "Feminino".',
            'data_nascimento.before_or_equal' => 'A data de nascimento não pode ser futura.'
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

    public function pessoaEndereco()
    {
        return $this->belongsToMany(Endereco::class, 'pessoa_endereco', 'pes_id', 'end_id');
    }

    // pessoa pertece a uma lotacao
    public function lotacao()
    {
        return $this->hasOne(Lotacao::class, 'pes_id', 'pes_id');
    }

    public function servidorEfetivo()
    {
        return $this->hasOne(ServidorEfetivo::class, 'pes_id', 'pes_id');
    }

    public function servidorTemporario()
    {
        return $this->hasOne(ServidorTemporario::class, 'pes_id', 'pes_id');
    }

    public static function calcularIdade($data_nascimento)
    {
        return \Carbon\Carbon::parse($data_nascimento)->age;
    }
}
