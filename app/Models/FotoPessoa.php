<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class FotoPessoa extends Model
{
    protected $table = 'foto_pessoa';
    protected $primaryKey = 'fp_id';
    protected $fillable = ['pes_id', 'fp_hash', 'fp_bucket', 'fp_data'];



    private static function regrasFotos()
    {
        return [
            'fotos.*' => 'required|image|mimes:jpeg,png,jpg|max:2048', // 2MB máximo
            'fotos' => 'array|max:5' // Limite de 5 fotos
        ];
    }

    private static function feedbackFotos()
    {
        return [
            'fotos.*.required' => 'Cada foto é obrigatória.',
            'fotos.*.image' => 'O arquivo deve ser uma imagem válida (JPEG, PNG, JPG).',
            'fotos.*.mimes' => 'Apenas formatos JPEG, PNG ou JPG são permitidos.',
            'fotos.*.max' => 'O tamanho máximo por foto é 2MB.',
            'fotos.max' => 'Máximo de 5 fotos por upload.'
        ];
    }

    public static function validarFotos(array $dadosFotos)
    {
        $validator = Validator::make(
            ['fotos' => $dadosFotos['fotos'] ?? []], // Extrai apenas 'fotos'
            self::regrasFotos(),
            self::feedbackFotos()
        );

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Erro na validação das fotos.'
            ];
        }

        return null;
    }

    public static function getFileExtension($file)
    {
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            return strtolower($file->extension());
        }
        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        return strtolower($extension ?: 'jpg'); 
    }



    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id');
    }
}
