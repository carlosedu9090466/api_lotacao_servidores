<?php

namespace App\Models;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;

class ValidacaoId extends Model
{
    
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
}
