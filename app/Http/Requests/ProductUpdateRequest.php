<?php

namespace App\Http\Requests;


use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'data.attributes.price'=>'numeric|gt:0'
        ];
    }
    
    
    public function messages()
    {
        return[
            'data.attributes.price.numeric' => 'El :attribute debe ser un número',
            'data.attributes.price.gt' => 'El :attribute es menor o igual a 0'
        ];
    }

    public function attributes()
    {
        return [
            'data.attributes.price' => 'precio del articulo'
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
