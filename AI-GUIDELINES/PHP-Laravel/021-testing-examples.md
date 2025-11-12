# PHP & Laravel Testing Examples

This document provides generic examples for each test type and category in the testing framework. These examples can be used as templates when writing new tests.

**Note**: For package-specific examples using the `Webkul\` namespace (AureusERP package), see [AureusERP Testing Examples](AureusERP/020-testing-examples.md).

## 1. Unit Tests

Unit tests focus on testing individual components in isolation.

### 1.1. Model Unit Test

```php
<?php
/**
 * Example Model Unit Tests
 *
 * This file contains unit tests for an example model.
 */

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Description;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Tests\Attributes\PluginTest;
use App\Models\ExampleModel;
use App\Models\RelatedModel;

/**
 * Test model attributes and relationships.
 * This test verifies that the model's attributes are correctly set
 * and that its relationships with other models are properly defined.
 */
#[Test]
#[Group('unit')]
#[Group('database')]
#[CoversClass(ExampleModel::class)]
#[Description('Test model attributes and relationships')]
function model_attributes_and_relationships()
{
    // Arrange
    $model = ExampleModel::factory()->create([
        'name' => 'Test Model',
        'code' => 'TEST-001',
        'value' => 100.00,
    ]);

    // Assert
    expect($model->name)->toBe('Test Model');
    expect($model->code)->toBe('TEST-001');
    expect($model->value)->toBe(100.00);
    expect($model->relatedModels())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
}
```

### 1.2. Service Unit Test

```php
<?php
/**
 * Example Service Unit Tests
 *
 * This file contains unit tests for an example service.
 */

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Services\ExampleService;
use App\Models\ExampleModel;
use App\Repositories\ExampleRepository;
use Mockery;

/**
 * Test service create method.
 */
#[Test]
#[Group('unit')]
#[CoversClass(ExampleService::class)]
function service_create_method()
{
    // Arrange
    $repository = Mockery::mock(ExampleRepository::class);
    $modelData = ['name' => 'Test Model', 'value' => 100.00];
    $model = new ExampleModel($modelData);

    $repository->shouldReceive('create')->once()->with($modelData)->andReturn($model);

    $service = new ExampleService($repository);

    // Act
    $result = $service->create($modelData);

    // Assert
    expect($result)->toBeInstanceOf(ExampleModel::class);
    expect($result->name)->toBe('Test Model');
}
```

## 2. Feature Tests

Feature tests focus on testing features from an HTTP or command-line perspective.

### 2.1. API Feature Test

```php
<?php
/**
 * Example API Feature Tests
 */

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Models\ExampleModel;
use App\Models\User;

/**
 * Test API listing endpoint.
 */
#[Test]
#[Group('feature')]
#[Group('api')]
#[Group('database')]
function api_listing_endpoint()
{
    // Arrange
    $user = User::factory()->create();
    ExampleModel::factory()->count(3)->create();

    // Act
    actingAs($user);
    $response = getJson('/api/examples');

    // Assert
    $response->assertStatus(200);
    $response->assertJsonCount(3, 'data');
}
```

### 2.2. Controller Feature Test

```php
<?php
/**
 * Example Controller Feature Tests
 */

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Models\User;

/**
 * Test form submission.
 */
#[Test]
#[Group('feature')]
#[Group('database')]
function form_submission()
{
    // Arrange
    $user = User::factory()->create();
    $modelData = ['name' => 'Test Model', 'code' => 'TEST-001', 'value' => 100.00];

    // Act
    actingAs($user);
    $response = post(route('examples.store'), $modelData);

    // Assert
    $response->assertRedirect(route('examples.index'));
    $this->assertDatabaseHas('examples', ['code' => 'TEST-001']);
}
```

## 3. Integration Tests

Integration tests focus on testing the interactions between different components.

### 3.1. Service Integration Test

```php
<?php
/**
 * Example Service Integration Tests
 */

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Services\ExampleService;
use App\Models\ExampleModel;
use App\Models\RelatedModel;

/**
 * Test creation with relationships.
 */
#[Test]
#[Group('integration')]
#[Group('database')]
function creation_with_relationships()
{
    // Arrange
    $relatedModels = RelatedModel::factory()->count(2)->create();
    $modelData = [
        'name' => 'Test Model',
        'value' => 100.00,
        'related_ids' => $relatedModels->pluck('id')->toArray(),
    ];

    $service = app(ExampleService::class);

    // Act
    $model = $service->create($modelData);

    // Assert
    expect($model->relatedModels)->toHaveCount(2);
}
```

## 4. Tests by Category

### 4.1. Security Test Example

```php
<?php
/**
 * Example Security Tests
 */

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use App\Models\ExampleModel;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Test API authorization.
 */
#[Test]
#[Group('feature')]
#[Group('api')]
#[Group('security')]
function api_authorization()
{
    // Arrange
    $model = ExampleModel::factory()->create();
    $viewPermission = Permission::create(['name' => 'view-examples']);
    $viewerRole = Role::create(['name' => 'viewer']);
    $viewerRole->givePermissionTo($viewPermission);

    $viewer = User::factory()->create();
    $viewer->assignRole($viewerRole);

    $unauthorized = User::factory()->create();

    // Act & Assert: Unauthorized user
    actingAs($unauthorized);
    getJson("/api/examples/{$model->id}")->assertStatus(403);

    // Act & Assert: Authorized user
    actingAs($viewer);
    getJson("/api/examples/{$model->id}")->assertStatus(200);
}
```

## 5. Package-Specific Examples

For package-specific testing examples using the `Webkul\` namespace (AureusERP package), see:

- **[AureusERP Testing Examples](AureusERP/020-testing-examples.md)** - Comprehensive AureusERP-specific testing examples
- **[AureusERP Test Templates](AureusERP/030-templates/)** - Ready-to-use test templates for AureusERP plugins

## 6. Navigation

 [←  Development Standards](020-development-standards.md) | [↑ Top](#php--laravel-testing-examples) |  [Testing Standards →](030-testing-standards.md)
