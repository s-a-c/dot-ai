# Laravel Test Data

## 1. Introduction

This document outlines Laravel-specific patterns for managing test data, including factories, seeders, and test data creation patterns.

## 2. Laravel Factories

Model factories are the preferred way to create test data in Laravel.

### 2.1. Basic Factory Usage

```php
// Create a user with default values
$user = User::factory()->create();

// Create a user with specific values
$user = User::factory()->create([
    'name' => 'Test User',
    'email' => 'test@example.com',
]);

// Create multiple users
$users = User::factory()->count(3)->create();

// Create a user with a specific state
$admin = User::factory()->admin()->create();
```

### 2.2. Factory States

```php
// In factory definition
public function admin()
{
    return $this->state(function (array $attributes) {
        return [
            'is_admin' => true,
        ];
    });
}

// Usage
$admin = User::factory()->admin()->create();
```

### 2.3. Factory Relationships

```php
// Create a user with a profile
$user = User::factory()
    ->has(Profile::factory(), 'profile')
    ->create();

// Create a product with categories
$product = Product::factory()
    ->hasAttached(Category::factory()->count(2))
    ->create();
```

## 3. Database Seeders

Database seeders can be used to populate the database with a standard set of data for testing.

```php
// Run a specific seeder
$this->seed(UserSeeder::class);

// Run all seeders
$this->seed();
```

## 4. Test Data Helpers

### 4.1. Using Test Helpers

```php
use Tests\Helpers\TestHelpers;

// Generate random data
$email = TestHelpers::randomEmail();
$date = TestHelpers::randomDate();

// Create a test file
$file = TestHelpers::createTestFile('test.txt');
```

## 5. Best Practices

1. **Use Factories**: Prefer factories over manual model creation
2. **Factory States**: Use states for common variations
3. **Relationships**: Define relationships in factories
4. **Isolation**: Ensure test data is isolated
5. **Cleanup**: Use transactions or RefreshDatabase trait

## 6. Related Guidelines

- **[Test Data Principles](../../Testing/040-test-data-principles.md)** - General test data principles
- **[Laravel Test Helpers](030-laravel-test-helpers.md)** - Helper utilities
- **[Testing Examples](010-testing-examples.md)** - Usage examples

## 7. Navigation

[←  Laravel Test Helpers](030-laravel-test-helpers.md) | [↑ Top](#laravel-test-data) |  [Test Templates →](050-test-templates/000-index.md)
