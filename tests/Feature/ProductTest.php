<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * SHOW-1
     */
    public function test_client_can_show_a_product(){

        $product = factory(Product::class)->create();
        $response = $this->json('GET', '/api/products/'.$product->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'price'
        ]);
        $body = $response->decodeResponseJson();
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $body['id'],
                'name' => $body['name'],
                'price' => $body['price']
            ]
        );
    }

    /**
     * SHOW-2
     */
    public function test_client_error_not_found_show_a_product(){
        $product = factory(Product::class)->create();
        //when
        $response = $this->json('GET', '/api/products/200');
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
    /**
     * UPDATE-1
     */
    public function test_client_can_update_a_product(){
        $product = factory(Product::class)->create();
        
        $updateProduct = [
            'name' => 'Super Product',
            'price' => '23.30'
        ];

        $response = $this->json('PUT', '/api/products/'.$product->id, $updateProduct);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'price'
        ]);
        $body = $response->decodeResponseJson();
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $body['id'],
                'name' => $body['name'],
                'price' => $body['price']
            ]
        );
    }
    /**
     * UPDATE-2
     */
    public function test_client_error_price_isnt_numeric_update_a_product(){
        $product = factory(Product::class)->create();
        
        $updateProduct = [
            'name' => 'Super Product',
            'price' => 'Super Product'
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
                    'price'
                ]
            ]
        ]);

         // Assert the error
        $response->assertJsonFragment([
            'errors' => [
                'code' => 'ERROR-1',
                'title' => 'Unprocessable Entity',
                'message' =>[
                    'price' => [
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
    public function test_client_error_price_is_less_than_or_equal_to_zero_update_a_product(){
        $product = factory(Product::class)->create();
        
        $updateProduct = [
            'name' => 'Super Product',
            'price' => '-20'
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
                    'price'
                ]
            ]
        ]);

         // Assert the error
        $response->assertJsonFragment([
            'errors' => [
                'code' => 'ERROR-1',
                'title' => 'Unprocessable Entity',
                'message' =>[
                    'price' => [
                        "El precio del articulo es menor o igual a 0"
                    ]
                ]
            ]
        ]);
    }
    /**
     * UPDATE-4
     */
    public function test_client_error_not_found_update_a_product(){
        $product = factory(Product::class)->create();
        
        $updateProduct = [
            'name' => 'Super Product',
            'price' => '20'
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

    /**
     * LIST-1
     */
    public function test_client_can_list_products(){
        $products = factory(Product::class,2)->create()->map(function ($product) {
            return $product->only(['id', 'name', 'price']);
        });;
        $response = $this->json('GET', '/api/products/');
        $response->assertStatus(200);
        $response->assertJson($products->toArray());
        $response->assertJsonStructure([
            '*' => ['id',
            'name',
            'price']
        ]);

    }
    /**
     * LIST-2
     */
    public function test_client_empty_list_products(){
        $products = factory(Product::class,0)->create()->map(function ($product) {
            return $product->only(['id', 'name', 'price']);
        });;
        $response = $this->json('GET', '/api/products/');
        $response->assertStatus(200);
        $response->assertJson($products->toArray());
        $response->assertJsonStructure(null);

    }
    /**
     * DELETE-1
     */
    public function test_client_can_delete_a_product(){
        $product = factory(Product::class)->create();
        $response = $this->json('DELETE', '/api/products/'.$product->id);
        $response->assertStatus(204);
    }
    /**
     * DELETE-2
     */
    public function test_client_error_not_found_delete_a_product(){
        $product = factory(Product::class)->create();
        //when
        $response = $this->json('DELETE', '/api/products/200');
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
    /**
     * CREATE-1
     */
    public function test_client_can_create_a_product()
    {
        // Given
        $productData = [
            'name' => 'Super Product',
            'price' => '23.30'
        ];

        // When
        $response = $this->json('POST', '/api/products', $productData); 

        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(201);
        
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'id',
            'name',
            'price'
        ]);

        // Assert the product was created
        // with the correct data
        $response->assertJsonFragment([
            'name' => 'Super Product',
            'price' => '23.30'
        ]);
        
        $body = $response->decodeResponseJson();

        // Assert product is on the database
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $body['id'],
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
            'price' => '23.30'
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
                    'name'
                ]
            ]
        ]);

         // Assert the error
        $response->assertJsonFragment([
            'errors' => [
                'code' => 'ERROR-1',
                'title' => 'Unprocessable Entity',
                'message' =>[
                    'name' => [
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
            'name' => 'calcetas'
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
                    'price'
                ]
            ]
        ]);

         // Assert the error
        $response->assertJsonFragment([
            'errors' => [
                'code' => 'ERROR-1',
                'title' => 'Unprocessable Entity',
                'message' =>[
                    'price' => [
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
            'name' => 'Calcetas',
            'price' => 'Calcetas'
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
                    'price'
                ]
            ]
        ]);

         // Assert the error
        $response->assertJsonFragment([
            'errors' => [
                'code' => 'ERROR-1',
                'title' => 'Unprocessable Entity',
                'message' =>[
                    'price' => [
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
            'name' => 'Calcetas',
            'price' => '-20'
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
                    'price'
                ]
            ]
        ]);

         // Assert the error
        $response->assertJsonFragment([
            'errors' => [
                'code' => 'ERROR-1',
                'title' => 'Unprocessable Entity',
                'message' =>[
                    'price' => [
                        "El precio del articulo es menor o igual a 0"
                    ]
                ]
            ]
        ]);
        
    }

}
