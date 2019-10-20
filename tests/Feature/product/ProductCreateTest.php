<?php

namespace Tests\Feature\product;

use Tests\TestCase;
use App\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductCreateTest extends TestCase
{
    use RefreshDatabase;

     /**
     * CREATE-1
     */
    public function test_client_can_create_a_product()
    {
        // Given
        $productData = [
            "data" => [
                "type" => "products",
                "attributes" => [
                    "name" => "Super Product",
                    "price" => "23.30"
                ]
            ]
        ];

        // When
        $response = $this->json('POST', '/api/products', $productData); 

        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(201);
        
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            "data" => [
                "type",
                "id",
                "attributes" => [
                  "name",
                  "price"
                ],
                "link" => [
                  "self"
                ]
            ]
        ]);

        // Assert the product was created
        // with the correct data
        $body = $response->decodeResponseJson();

        $response->assertJsonFragment([
            "data" => [
                "type" => "products",
                "id" => $body['data']['id'],
                "attributes" => [
                  "name" => 'Super Product' ,
                  "price" =>'23.30'
                ],
                "link" => [
                  "self" => $body['data']['link']['self']
                ]
            ]
        ]);
        
        

        // Assert product is on the database
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $body['data']['id'],
                'name' => 'Super Product',
                'price' => '23.30'
            ]
        );
    }
    /**
     * CREATE-2
     */
    public function test_client_error_name_wasnt_sent_when_creating_a_product()
    {
        //Given
        $productData = [
            "data" => [
                "type" => "products",
                "attributes" => [
                    "price" => "23.30"
                ]
            ]
        ];
        // When
        $response = $this->json('POST', '/api/products', $productData); 
        //
         // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(422);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'errors'=>[
                'code',
                'title',
                'message'=>[
                    'data.attributes.name'
                ]
            ]
        ]);

         // Assert the error
        $response->assertJsonFragment([
            'errors' => [
                'code' => 'ERROR-1',
                'title' => 'Unprocessable Entity',
                'message' =>[
                    'data.attributes.name' => [
                        "El nombre del articulo no es enviado en la solicitud"
                    ]
                ]
            ]
        ]);
        
    }
    /**
     * CREATE-3
     */
    public function test_client_error_price_wasnt_sent_when_creating_a_product()
    {
        //Given
        $productData = [
            "data" => [
                "type" => "products",
                "attributes" => [
                    "name" => "Super Product",
                ]
            ]
        ];
        // When
        $response = $this->json('POST', '/api/products', $productData); 
        //
         // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(422);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'errors'=>[
                'code',
                'title',
                'message'=>[
                    'data.attributes.price'
                ]
            ]
        ]);

         // Assert the error
        $response->assertJsonFragment([
            'errors' => [
                'code' => 'ERROR-1',
                'title' => 'Unprocessable Entity',
                'message' =>[
                    'data.attributes.price' => [
                        "El precio del articulo no es enviado en la solicitud"
                    ]
                ]
            ]
        ]);
        
    }
    /**
     * CREATE-4
     */
    public function test_client_error_price_isnt_numeric_when_creating_a_product()
    {
        //Given
        $productData = [
            "data" => [
                "type" => "products",
                "attributes" => [
                    "name" => "Calcetas",
                    "price" => "Calcetas"
                ]
            ]
        ];
        // When
        $response = $this->json('POST', '/api/products', $productData); 
        //
         // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(422);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'errors'=>[
                'code',
                'title',
                'message'=>[
                    'data.attributes.price'
                ]
            ]
        ]);

         // Assert the error
        $response->assertJsonFragment([
            'errors' => [
                'code' => 'ERROR-1',
                'title' => 'Unprocessable Entity',
                'message' =>[
                    'data.attributes.price' => [
                        "El precio del articulo no es un número",
                        "El precio del articulo es menor o igual a 0"
                    ]
                ]
            ]
        ]);
        
    }
    /**
     * CREATE-5
     */
    public function test_client_error_price_is_less_than_or_equal_to_zero_when_creating_a_product()
    {
        //Given
        $productData = [
            "data" => [
                "type" => "products",
                "attributes" => [
                    "name" => "Super Product",
                    "price" => "-20"
                ]
            ]
        ];
        // When
        $response = $this->json('POST', '/api/products', $productData); 
        //
         // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(422);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'errors'=>[
                'code',
                'title',
                'message'=>[
                    'data.attributes.price'
                ]
            ]
        ]);

         // Assert the error
        $response->assertJsonFragment([
            'errors' => [
                'code' => 'ERROR-1',
                'title' => 'Unprocessable Entity',
                'message' =>[
                    'data.attributes.price' => [
                        "El precio del articulo es menor o igual a 0"
                    ]
                ]
            ]
        ]);
        
    }
}
