<?php

namespace App\Models;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;

class Lotacao extends Model
{
    protected $table = 'lotacao';
    protected  $primaryKey = 'lot_id';
    protected $dates = ['date'];

    protected $fillable = [
        'pes_id',
        'unid_id',
        'lot_data_lotacao',
        'lot_data_remocao',
        'lot_portaria'
    ];
    protected $guarded = [];


    private static function regras()
    {
        $regras = [
            'pes_id' => 'required|exists:pessoa,pes_id',
            'unid_id' => 'required|exists:unidade,unid_id',
            'lot_data_lotacao' => 'required|date|before_or_equal:today',
            'lot_data_remocao' => ['required','date','after_or_equal:lot_data_lotacao'],
            'lot_portaria' => 'required|min:10|max:100'
        ];

        return $regras;
    }


    private static function feedback()
    {

        $feedback = [
            'required' => 'o campo :attribute deve ser preenchido.',
            'pes_id.exists' => 'A pessoa informada não foi encontrada no sistema.',
            'unid_id.exists' => 'A unidade informado não foi encontrado no sistema.',
            'lot_data_lotacao.before_or_equal' => 'A data de lotação não pode ser futura.',
            'lot_data_remocao.after_or_equal' => 'A data de remoção não pode ser anterior à data de lotação.',
            'lot_portaria.min' => 'o campo nome deve ter no mínino 10 caracteres.',
            'lot_portaria.max' => 'o campo nome deve ter no máximo 100 caracteres.',
        ];

        return $feedback;
    }

    public static function validarDados($dadosLotacao)
    {

        $validator = Validator::make($dadosLotacao, self::regras(), self::feedback());

        if ($validator->fails()) {
            return [
                'error' => $validator->errors(),
                'message' => 'Erro na validação dos dados!'
            ];
        }

        return null;
    }


    // falta a lotação - model, controller e os repository
    // uma pessoa pertence a uma lotacao
    public function pessoaLotacao(){
        return $this->belongsTo(Pessoa::class, 'pes_id', 'pes_id');
    }

    public function unidadeLotacao(){
        return $this->belongsTo(Unidade::class,'unid_id','unid_id');
    }


}
