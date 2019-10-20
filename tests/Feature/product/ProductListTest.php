<?php

namespace Tests\Feature\product;

use Tests\TestCase;
use App\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * LIST-1
     */
    public function test_client_can_list_products()
    {
        $products = factory(Product::class,2)->create()->map(function ($product) {
            return $product->only(['id', 'name', 'price']);
        });;
        $response = $this->json('GET', '/api/products/');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "data" => [
                '*' => ["type",
                "id",
                "attributes" => [
                  "name",
                  "price"
                ],
                "link" => [
                  "self"
                ]]
            ]
        ]);

    }
    /**
     * LIST-2
     */
    public function test_client_empty_list_products()
    {
        $products = factory(Product::class,0)->create()->map(function ($product) {
            return $product->only(['id', 'name', 'price']);
        });;
        $response = $this->json('GET', '/api/products/');
        $response->assertStatus(200);
        $response->assertJson($products->toArray());
        $response->assertJsonStructure(null);

    }
}
