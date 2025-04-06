<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class FotoPessoa extends Model
{
    protected $table = 'foto_pessoa';
    protected $primaryKey = 'fp_id';
    protected $fillable = ['pes_id', 'fp_hash', 'fp_bucket', 'fp_data', 'fp_extensao'];



    public static function validarFotos(array $dadosFotos)
    {
        $fotos = $dadosFotos['fotos'] ?? [];

        if (!is_array($fotos)) {
            $fotos = [$fotos];
        }

        $validator = Validator::make(
            ['fotos' => $fotos],
            [
                'fotos' => 'required|array|min:1|max:5',
                'fotos.*' => 'required|image|mimes:jpeg,png,jpg|max:7048',
            ],
            [
                'fotos.required' => 'É necessário enviar pelo menos uma foto.',
                'fotos.array' => 'As fotos devem estar em um array.',
                'fotos.min' => 'Envie pelo menos uma foto.',
                'fotos.max' => 'Máximo de 5 fotos por upload.',
                'fotos.*.required' => 'Cada foto é obrigatória.',
                'fotos.*.image' => 'O arquivo deve ser uma imagem válida (JPEG, PNG, JPG).',
                'fotos.*.mimes' => 'Apenas formatos JPEG, PNG ou JPG são permitidos.',
                'fotos.*.max' => 'O tamanho máximo por foto é 7MB.',
            ]
        );

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Erro na validação das fotos.'
            ];
        }

        return true;
    }

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id');
    }
}
