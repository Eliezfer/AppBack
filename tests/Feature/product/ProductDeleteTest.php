<?php

namespace Tests\Feature\product;

use Tests\TestCase;
use App\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductDeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * DELETE-1
     */
    public function test_client_can_delete_a_product()
    {
        $product = factory(Product::class)->create();
        $response = $this->json('DELETE', '/api/products/'.$product->id);
        $response->assertStatus(204);
    }
    /**
     * DELETE-2
     */
    public function test_client_error_not_found_delete_a_product()
    {
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
}
