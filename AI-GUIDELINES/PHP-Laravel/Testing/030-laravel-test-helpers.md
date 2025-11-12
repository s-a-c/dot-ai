# Laravel Test Helpers

## 1. Introduction

This document provides documentation for Laravel-specific test helpers and utilities. These helpers make it easier to write tests by providing common functionality and reducing boilerplate code.

## 2. Base TestCase Class

The `TestCase` class (`tests/TestCase.php`) is the base class for all tests in Laravel. It extends Laravel's `TestCase` class and provides additional functionality.

### 2.1. Setup and Teardown

```php
protected function setUp(): void
{
    parent::setUp();

    // Clear the cache before each test
    Cache::flush();
}

protected function tearDown(): void
{
    // Clean up any test files
    $this->cleanupTestFiles();

    parent::tearDown();
}
```

### 2.2. Database Helpers

```php
protected function useRefreshDatabase(): void
{
    $this->refreshDatabase();
}

protected function useInMemoryDatabase(): void
{
    $this->app['config']->set('database.default', 'sqlite');
    $this->app['config']->set('database.connections.sqlite', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
    ]);
}
```

## 3. Laravel Testing Traits

### 3.1. RefreshDatabase Trait

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    // Tests will automatically refresh the database
}
```

### 3.2. DatabaseTransactions Trait

```php
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductTest extends TestCase
{
    use DatabaseTransactions;

    // Tests will run in transactions
}
```

## 4. Laravel Testing Utilities

### 4.1. Acting As User

```php
$user = User::factory()->create();
$this->actingAs($user);

// Or with Pest
actingAs($user);
```

### 4.2. HTTP Testing

```php
// GET request
$response = $this->get('/products');

// POST request
$response = $this->postJson('/api/products', $data);

// Assertions
$response->assertStatus(200);
$response->assertJsonStructure(['data']);
```

### 4.3. Custom Assertion Helpers

Custom assertion methods can be added to the base TestCase class:

```php
/**
 * Assert that a JSON response has a given structure.
 */
protected function assertJsonStructure(\Illuminate\Testing\TestResponse $response, array $structure): void
{
    $response->assertJsonStructure($structure);
}

/**
 * Assert that a model has the expected attributes.
 */
protected function assertModelHasAttributes(\Illuminate\Database\Eloquent\Model $model, array $attributes): void
{
    foreach ($attributes as $key => $value) {
        $this->assertEquals($value, $model->{$key}, "Model attribute {$key} does not match expected value.");
    }
}

/**
 * Assert that a model has the expected relationships.
 */
protected function assertModelHasRelationships(\Illuminate\Database\Eloquent\Model $model, array $relationships): void
{
    foreach ($relationships as $relationship => $type) {
        $this->assertTrue(method_exists($model, $relationship), "Model does not have relationship {$relationship}.");
        $this->assertInstanceOf($type, $model->{$relationship}(), "Relationship {$relationship} is not of type {$type}.");
    }
}
```

## 5. Testing Traits

Laravel provides several testing traits that can be used in tests:

### 5.1. Database Traits

```php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductTest extends TestCase
{
    use RefreshDatabase; // Refreshes database for each test
    // OR
    use DatabaseTransactions; // Uses transactions that roll back
}
```

### 5.2. Custom Testing Traits

Custom traits can provide domain-specific testing functionality:

```php
use Tests\Traits\ApiTestingTrait;

class ApiTest extends TestCase
{
    use ApiTestingTrait;

    public function test_api_endpoint()
    {
        $response = $this->getJson('api/products');
        $this->assertSuccessful($response);
        $this->assertPaginated($response);
    }
}
```

### 4.4. Database Assertions

```php
$this->assertDatabaseHas('products', [
    'name' => 'Test Product',
]);

$this->assertDatabaseMissing('products', [
    'name' => 'Deleted Product',
]);
```

## 6. Related Guidelines

- **[Test Helpers Patterns](../../Testing/050-test-helpers-patterns.md)** - General helper patterns
- **[Laravel Test Data](040-laravel-test-data.md)** - Test data management
- **[Testing Examples](010-testing-examples.md)** - Usage examples

## 7. Navigation

[←  PHP Attributes Standards](020-php-attributes-standards.md) | [↑ Top](#laravel-test-helpers) |  [Laravel Test Data →](040-laravel-test-data.md)
