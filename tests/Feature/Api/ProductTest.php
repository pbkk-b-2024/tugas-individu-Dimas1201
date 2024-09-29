<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected $owner;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user with the role of Owner
        $this->owner = User::factory()->create([
            'password' => bcrypt('Owner'), // Set the password
        ]);
    }

    /** @test */
    public function can_create_product()
    {
        // Authenticate as the owner
        $response = $this->actingAs($this->owner)
            ->postJson('/api/products', [
                'product_name' => 'Product Name',
                'description' => 'Product Description',
                'price' => 99.99,
            ]);

        $response->assertStatus(201)
                 ->assertJson(['product_name' => 'Product Name']);
    }

    /** @test */
    public function can_get_products()
    {
        // Create some products
        $this->actingAs($this->owner); // Authenticate as the owner
        $products = Product::factory()->count(6)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonCount(6); // Adjust this if you want to check a specific count
    }

    /** @test */
    public function can_update_product()
    {
        $this->actingAs($this->owner); // Authenticate as the owner
        $product = Product::factory()->create();

        $response = $this->putJson('/api/products/' . $product->id, [
            'product_name' => 'Updated Name',
            'description' => 'Updated Description',
            'price' => 79.99,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['product_name' => 'Updated Name']);
    }

    /** @test */
    public function can_delete_product()
    {
        $this->actingAs($this->owner); // Authenticate as the owner
        $product = Product::factory()->create();

        $response = $this->deleteJson('/api/products/' . $product->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}