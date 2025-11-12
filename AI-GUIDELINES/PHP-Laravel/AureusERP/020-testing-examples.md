# AureusERP Testing Examples

This document provides comprehensive testing examples specific to AureusERP plugins. All examples use the `Webkul\` namespace convention used by the `aureauserp/aureuserp` package.

These examples can be used as templates when writing tests for AureusERP plugins. For generic testing patterns and standards, see the main [Testing Examples](../021-testing-examples.md) and [Testing Standards](../030-testing-standards.md).

## 1. Unit Tests

Unit tests focus on testing individual components in isolation.

### 1.1. Model Unit Test

```php
<?php
/**
 * Product Model Unit Tests
 *
 * This file contains unit tests for the Product model in the Products plugin.
 */

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Description;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Tests\Attributes\PluginTest;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\Category;

/**
 * Test Product model attributes and relationships.
 * This test verifies that the Product model's attributes are correctly set
 * and that its relationships with other models are properly defined.
 */
#[Test]
#[Group('unit')]
#[Group('products')]
#[Group('database')]
#[PluginTest('Products')]
#[CoversClass(Product::class)]
#[Description('Test Product model attributes and relationships')]
function product_model_attributes_and_relationships()
{
    // Arrange
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'sku' => 'TEST-001',
        'price' => 100.00,
    ]);

    // Assert
    expect($product->name)->toBe('Test Product');
    expect($product->sku)->toBe('TEST-001');
    expect($product->price)->toBe(100.00);
    expect($product->categories())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
}
```

### 1.2. Service Unit Test

```php
<?php
/**
 * Product Service Unit Tests
 *
 * This file contains unit tests for the ProductService in the Products plugin.
 */

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Tests\Attributes\PluginTest;
use Webkul\Product\Services\ProductService;
use Webkul\Product\Models\Product;
use Webkul\Product\Repositories\ProductRepository;
use Mockery;

/**
 * Test ProductService create method.
 */
#[Test]
#[Group('unit')]
#[Group('products')]
#[CoversClass(ProductService::class)]
function product_service_create_method()
{
    // Arrange
    $repository = Mockery::mock(ProductRepository::class);
    $productData = ['name' => 'Test Product', 'price' => 100.00];
    $product = new Product($productData);

    $repository->shouldReceive('create')->once()->with($productData)->andReturn($product);

    $service = new ProductService($repository);

    // Act
    $result = $service->create($productData);

    // Assert
    expect($result)->toBeInstanceOf(Product::class);
    expect($result->name)->toBe('Test Product');
}
```

## 2. Feature Tests

Feature tests focus on testing features from an HTTP or command-line perspective.

### 2.1. API Feature Test

```php
<?php
/**
 * Product API Feature Tests
 */

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Tests\Attributes\PluginTest;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;

/**
 * Test product listing API endpoint.
 */
#[Test]
#[Group('feature')]
#[Group('products')]
#[Group('api')]
#[Group('database')]
#[PluginTest('Products')]
function product_listing_api_endpoint()
{
    // Arrange
    $user = User::factory()->create();
    Product::factory()->count(3)->create();

    // Act
    actingAs($user);
    $response = getJson('/api/products');

    // Assert
    $response->assertStatus(200);
    $response->assertJsonCount(3, 'data');
}
```

### 2.2. Controller Feature Test

```php
<?php
/**
 * Product Controller Feature Tests
 */

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Tests\Attributes\PluginTest;
use Webkul\Security\Models\User;

/**
 * Test product creation form submission.
 */
#[Test]
#[Group('feature')]
#[Group('products')]
#[Group('database')]
#[PluginTest('Products')]
function product_creation_form_submission()
{
    // Arrange
    $user = User::factory()->create();
    $productData = ['name' => 'Test Product', 'sku' => 'TEST-001', 'price' => 100.00];

    // Act
    actingAs($user);
    $response = post(route('admin.products.store'), $productData);

    // Assert
    $response->assertRedirect(route('admin.products.index'));
    $this->assertDatabaseHas('products', ['sku' => 'TEST-001']);
}
```

## 3. Integration Tests

Integration tests focus on testing the interactions between different components.

### 3.1. Service Integration Test

```php
<?php
/**
 * Product Service Integration Tests
 */

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Tests\Attributes\PluginTest;
use Webkul\Product\Services\ProductService;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\Category;

/**
 * Test product creation with categories.
 */
#[Test]
#[Group('integration')]
#[Group('products')]
#[Group('database')]
#[PluginTest('Products')]
function product_creation_with_categories()
{
    // Arrange
    $categories = Category::factory()->count(2)->create();
    $productData = [
        'name' => 'Test Product',
        'price' => 100.00,
        'category_ids' => $categories->pluck('id')->toArray(),
    ];

    $service = app(ProductService::class);

    // Act
    $product = $service->create($productData);

    // Assert
    expect($product->categories)->toHaveCount(2);
}
```

## 4. Tests by Category

### 4.1. Security Test Example

```php
<?php
/**
 * Product Security Tests
 */

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Tests\Attributes\PluginTest;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\Permission;

/**
 * Test product API authorization.
 */
#[Test]
#[Group('feature')]
#[Group('products')]
#[Group('api')]
#[Group('security')]
#[PluginTest('Products')]
function product_api_authorization()
{
    // Arrange
    $product = Product::factory()->create();
    $viewPermission = Permission::factory()->create(['name' => 'view-products']);
    $viewerRole = Role::factory()->create(['name' => 'product-viewer']);
    $viewerRole->permissions()->attach($viewPermission);

    $viewer = User::factory()->create();
    $viewer->roles()->attach($viewerRole);

    $unauthorized = User::factory()->create();

    // Act & Assert: Unauthorized user
    actingAs($unauthorized);
    getJson("/api/products/{$product->id}")->assertStatus(403);

    // Act & Assert: Authorized user
    actingAs($viewer);
    getJson("/api/products/{$product->id}")->assertStatus(200);
}
```

## 5. Notes

### 5.1. Namespace Usage

All AureusERP plugin tests must use the `Webkul\` namespace:

- **Models**: `Webkul\{Module}\Models\{ModelName}`
- **Services**: `Webkul\{Module}\Services\{ServiceName}`
- **Security Components**: `Webkul\Security\Models\{Component}`

### 5.2. Test Attributes

- Use `#[PluginTest('PluginName')]` to identify plugin-specific tests
- Group tests appropriately with `#[Group('unit')]`, `#[Group('feature')]`, etc.
- Use `#[CoversClass]` to specify which class is being tested

### 5.3. Cross-References

For generic testing patterns that apply to all Laravel applications (not just AureusERP), see:

- **[Testing Examples](../021-testing-examples.md)** - Generic testing examples
- **[Testing Standards](../030-testing-standards.md)** - Comprehensive testing requirements
- **[Test Templates](030-templates/)** - Ready-to-use test templates for AureusERP

## 6. Navigation

 [←  Package Overview](010-package-overview.md) | [↑ Top](#aureuserp-testing-examples) |  [Test Templates →](030-templates/000-index.md)
