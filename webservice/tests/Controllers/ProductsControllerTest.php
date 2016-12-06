<?php

use App\Product;
use App\Category;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductsControllerTest extends ApiTestCase
{
    use DatabaseMigrations, WithoutMiddleware, Factory;

    /**
     * @test
     */
    public function can_list_products()
    {
        $category = $this->create(Category::class);

        $this->times(5)->create(Product::class, [
            'category_id' => $category->id,
        ]);

        $this->json('GET', '/api/products');

        $this->assertResponseOk();
        $this->seeJsonStructure([
            'data' => [
                '*' => ['name', 'category'],
            ],
            'meta' => ['pagination' => []],
        ]);
    }

    /**
     * @test
     */
    public function can_create_product()
    {
        $category = $this->create(Category::class);

        $this->json('POST', '/api/products', [
            'name' => 'Dummy',
            'category' => $category->id,
        ]);

        $this->assertResponseOk();
        $this->seeInDatabase('products', [
            'name' => 'Dummy',
            'category_id' => $category->id,
        ]);
    }

    /**
     * @test
     */
    public function can_get_product()
    {
        $product = $this->create(Product::class, [
            'category_id' => $this->create(Category::class)->id,
        ]);

        $this->json('GET', '/api/products/1');

        $this->assertResponseOk();
        $this->seeJsonStructure([
            'data' => [
                'name', 'category',
            ],
        ]);
    }

    /**
     * @test
     */ 
    public function can_update_product()
    {
        $category = $this->create(Category::class);
        $product = $this->create(Product::class, [
            'category_id' => $this->create(Category::class)->id,
        ]);

        $this->json('PUT', '/api/products/1', [
            'name' => 'Dummy',
            'category' => $category->id,
        ]);

        $this->assertResponseOk();
        $this->seeInDatabase('products', [
            'id' => $product->id,
            'name' => 'Dummy',
            'category_id' => $category->id,
        ]);
    }

    /**
     * @test
     */
    public function can_delete_product()
    {
        $this->create(Product::class, [
            'category_id' => $this->create(Category::class)->id,
        ]);

        $this->json('DELETE', '/api/products/1');

        $this->assertResponseOk();
        $this->dontSeeInDatabase('products', ['id' => 1]);
    }

    /**
     * @test
     * @dataProvider urlProvider
     */
    public function get_404_if_product_dont_exist($method, $url)
    {
        $this->json($method, $url, [
            'name' => 'Dummy',
            'category' => $this->create(Category::class)->id,
        ]);

        $this->assertResponseStatus(404);
        $this->seeJsonStructure([
            'messages' => [[]],
        ]);
    }

    /**
     * Url provider.
     *
     * @return array
     */
    public function urlProvider()
    {
        return [
            ['GET', '/api/products/1'],
            ['PUT', '/api/products/1'],
            ['DELETE', '/api/products/1'],
        ];
    }
}
