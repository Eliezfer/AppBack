<?php

namespace Tests\Feature\product;

use Tests\TestCase;
use App\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductShowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * SHOW-1
     */
    public function test_client_can_show_a_product()
    {

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
    public function test_client_error_not_found_show_a_product()
    {
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
}
