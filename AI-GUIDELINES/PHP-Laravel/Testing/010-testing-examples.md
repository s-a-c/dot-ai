# Testing Examples

## 1. Introduction

This document provides comprehensive PHP/Laravel test examples for unit, feature, and integration tests using Pest and PHPUnit. These examples demonstrate Laravel-specific testing patterns and best practices.

## 2. Unit Tests

Unit tests focus on testing individual components in isolation.

### 2.1. Model Unit Test

```php
<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Models\Product;

#[Test]
#[Group('unit')]
#[Group('products')]
#[CoversClass(Product::class)]
function product_model_attributes_and_relationships()
{
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'sku' => 'TEST-001',
        'price' => 100.00,
    ]);

    expect($product->name)->toBe('Test Product');
    expect($product->sku)->toBe('TEST-001');
    expect($product->price)->toBe(100.00);
}
```

### 2.2. Service Unit Test

```php
<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Services\ProductService;
use App\Models\Product;
use Mockery;

#[Test]
#[Group('unit')]
#[Group('products')]
function product_service_create_method()
{
    $repository = Mockery::mock(ProductRepository::class);

    $productData = [
        'name' => 'Test Product',
        'sku' => 'TEST-001',
        'price' => 100.00,
    ];

    $product = new Product($productData);

    $repository->shouldReceive('create')
        ->once()
        ->with($productData)
        ->andReturn($product);

    $service = new ProductService($repository);
    $result = $service->create($productData);

    expect($result)->toBeInstanceOf(Product::class);
    expect($result->name)->toBe('Test Product');
}
```

## 3. Feature Tests

Feature tests focus on testing features from an HTTP perspective.

### 3.1. API Feature Test

```php
<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Models\Product;
use App\Models\User;

#[Test]
#[Group('feature')]
#[Group('api')]
#[Group('products')]
function product_listing_api_endpoint()
{
    $user = User::factory()->create();
    $products = Product::factory()->count(3)->create();

    $response = $this->actingAs($user)
        ->getJson('/api/products');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
}
```

### 3.2. Controller Feature Test

```php
<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Models\Product;
use App\Models\User;

#[Test]
#[Group('feature')]
#[Group('products')]
function product_creation_works()
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/products', [
            'name' => 'New Product',
            'sku' => 'NEW-001',
            'price' => 50.00,
        ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('products', [
        'name' => 'New Product',
        'sku' => 'NEW-001',
    ]);
}
```

## 4. Integration Tests

Integration tests focus on testing how different components work together.

### 4.1. Service Integration Test

```php
<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Services\ProductService;
use App\Models\Product;
use App\Models\Category;

#[Test]
#[Group('integration')]
#[Group('products')]
function product_service_creates_product_with_categories()
{
    $categories = Category::factory()->count(2)->create();
    $categoryIds = $categories->pluck('id')->toArray();

    $service = app(ProductService::class);
    $product = $service->create([
        'name' => 'Test Product',
        'category_ids' => $categoryIds,
    ]);

    expect($product->categories)->toHaveCount(2);
}
```

## 5. Best Practices in Examples

These examples demonstrate:

- **Proper Attribute Usage**: Using PHP 8 attributes for categorization
- **Factory Usage**: Using Laravel factories for test data
- **Mocking**: Using Mockery for dependencies
- **Assertions**: Using Pest's expressive assertions
- **Type Safety**: Using strict types and type hints

## 6. Related Guidelines

- **[Testing Philosophy](../../Testing/010-testing-philosophy.md)** - Core testing principles
- **[PHP Attributes Standards](020-php-attributes-standards.md)** - Attribute usage
- **[Test Templates](050-test-templates/000-index.md)** - Test templates
- **[Testing Standards](../030-testing-standards.md)** - Main testing standards

## 7. Navigation

[←  Testing Index](000-index.md) | [↑ Top](#testing-examples) |  [PHP Attributes Standards →](020-php-attributes-standards.md)
