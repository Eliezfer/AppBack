<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_client_can_delete_a_product(){
        $product = factory(Product::class)->create();
        $response = $this->json('DELETE', '/api/products/'.$product->id);
        $response->assertStatus(204);
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
}
