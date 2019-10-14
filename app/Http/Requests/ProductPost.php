<?php

namespace App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class ProductPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'name' => 'required',
            'price'=>'required|numeric|gt:0'
        ];
    }
    
    
    public function messages()
    {
        return[
            'name.required' => 'El :attribute no es enviado en la solicitud',
            'price.required' => 'El :attribute no es enviado en la solicitud',
            'price.numeric' => 'El :attribute no es un número',
            'price.gt' => 'El :attribute es menor o igual a 0'
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'nombre del articulo',
            'price' => 'precio del articulo'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        $json = [
            'code' => 'ERROR-1',
            'title' =>'Unprocessable Entity',
            'message' => $errors
        ];

        throw new HttpResponseException(
            response()->json(['errors' =>$json],
             JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
