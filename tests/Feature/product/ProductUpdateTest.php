<?php

namespace Tests\Feature\product;
use App\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * UPDATE-1
     */
    public function test_client_can_update_a_product()
    {
        $product = factory(Product::class)->create();
        
        $updateProduct = [
                "data" => [
                    "type" => "products",
                    "attributes" => [
                        "name" => "Super Product",
                        "price" => "23.30"
                    ]
                ]
        ];

        $response = $this->json('PUT', '/api/products/'.$product->id, $updateProduct);
        $response->assertStatus(200);
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
        $body = $response->decodeResponseJson();
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $body['data']['id'],
                'name' => $body['data']['attributes']['name'],
                'price' =>  $body['data']['attributes']['price']
            ]
        );
    }

    /**
     * UPDATE-2
     */
    public function test_client_error_price_isnt_numeric_update_a_product()
    {
        $product = factory(Product::class)->create();
        
        $updateProduct = [
            "data" => [
                "type" => "products",
                "attributes" => [
                    'name' => 'Super Product',
                    'price' => 'Super Product'
                ]
            ]
        ];

        //when
        $response = $this->json('PUT', '/api/products/'.$product->id, $updateProduct);
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
                        "El precio del articulo debe ser un número",
                        "El precio del articulo es menor o igual a 0"
                    ]
                ]
            ]
        ]);
    }

    /**
     * UPDATE-3
     */
    public function test_client_error_price_is_less_than_or_equal_to_zero_update_a_product()
    {
        $product = factory(Product::class)->create();
        
        $updateProduct = [
            "data" => [
                "type" => "products",
                "attributes" => [
                    'name' => 'Super Product',
                    'price' => '-20'
                ]
            ]
        ];
        //when
        $response = $this->json('PUT', '/api/products/'.$product->id, $updateProduct);
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

    /**
     * UPDATE-4
     */
    public function test_client_error_not_found_update_a_product()
    {
        $product = factory(Product::class)->create();
        
        $updateProduct = [
            "data" => [
                "type" => "products",
                "attributes" => [
                    'name' => 'Super Product',
                    'price' => '20'
                ]
            ]
        ];

        //when
        $response = $this->json('PUT', '/api/products/200', $updateProduct);
         // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(404);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'errors'=>[
                'code',
                'title',
                'message'
            ]
        ]);

         // Assert the error
        $response->assertJsonFragment([
            'errors' => [
                'code' => 'ERROR-2',
                'title' => 'Not Found',
                'message' => "No hay un producto con ese ID"
            ]
        ]);
    }
}
