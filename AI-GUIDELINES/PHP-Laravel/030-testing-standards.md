# Testing Standards

## 1. Core Testing Principle

**All tests should be clear, comprehensive, and suitable for junior developers to understand, maintain, and extend.**

This principle ensures that testing is not just a quality gate but also a learning tool and documentation source for the entire team.

## 2. Testing Requirements

### 2.1. Coverage Requirements

- **90% Minimum Coverage**: All production code must achieve 90% test coverage
- **Quality over Quantity**: Focus on meaningful tests that verify behavior
- **Critical Path Coverage**: 100% coverage for critical business logic
- **Edge Case Testing**: Include tests for edge cases and error conditions

### 2.2. Test Types

#### 2.2.1. Unit Tests

- Test individual components in isolation
- Mock external dependencies
- Fast execution and focused scope
- Verify business logic and data transformations

#### 2.2.2. Feature Tests

- Test application features from user perspective
- Use Laravel's testing helpers and assertions
- Test HTTP requests and responses
- Verify complete user workflows

#### 2.2.3. Integration Tests

- Test component interactions
- Database integration testing
- External service integration
- End-to-end workflow validation

#### 2.2.4. Browser Tests

- Use Laravel Dusk for browser automation
- Test JavaScript interactions
- Verify UI behavior and accessibility
- Test responsive design and cross-browser compatibility

#### 2.2.5. Architecture Tests

- Enforce architectural boundaries
- Verify namespace organization
- Test class dependencies and relationships
- Ensure design pattern compliance

## 3. Testing Frameworks and Tools

### 3.1. Primary Testing Framework

**Pest** is the preferred testing framework:

- Modern, expressive syntax
- Excellent test organization
- Built-in support for Laravel
- Extensive plugin ecosystem

### 3.2. Supporting Tools

- **PHPUnit**: Legacy test support and compatibility
- **Laravel Dusk**: Browser testing automation
- **Faker**: Test data generation
- **Mockery**: Advanced mocking capabilities
- **Stressless**: Performance and load testing

### 3.3. Quality Assurance Tools

- **Pest Architecture Plugin**: Enforce architectural rules
- **Type Coverage Plugin**: Verify type safety in tests
- **Snapshot Plugin**: Regression testing for complex outputs
- **Laravel Test Assertions**: Additional Laravel-specific assertions
- **PHPStan Level 10 (max)**: All test files must achieve PHPStan Level 10 (max) compliance, ensuring type-safe test code that follows the same coding standards as application code

## 4. Test Structure and Organization

### 4.1. Directory Structure

```log
tests/
├── Unit/                    # Unit tests for individual components
├── Feature/                 # Feature tests for application behavior
├── Integration/             # Integration tests for component interactions
├── Browser/                 # Browser tests using Dusk
├── Architecture/            # Architecture and design pattern tests
├── Datasets/                # Test data factories and datasets
├── Helpers/                 # Custom test helpers and utilities
└── TestCase.php            # Base test class with common setup
```

### 4.2. Test Naming Conventions

- **Descriptive Names**: Use clear, descriptive test method names
- **Given-When-Then**: Structure test names to describe scenarios
- **Test Suffix**: End test method names with `test` prefix
- **Human Readable**: Names should read like documentation

```php
// Good examples
test('user_can_register_with_valid_credentials', function () {
    // Test implementation
});

test('login_fails_with_invalid_password', function () {
    // Test implementation
});

test('invoice_calculation_handles_discount_correctly', function () {
    // Test implementation
});
```

### 4.3. Test Organization

- **Arrange-Act-Assert**: Structure tests in clear phases
- **Descriptive Comments**: Add comments for complex test scenarios
- **Test Data Management**: Use factories and datasets for test data
- **Cleanup and Teardown**: Proper cleanup in test teardown

## 5. Test Data Management

### 5.1. Factories

Use Laravel factories for test data creation:

```php
// Database/Factories/UserFactory.php
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
```

### 5.2. Datasets

Use datasets for parameterized tests:

```php
// Datasets/ValidEmailProviders.php
dataset('valid_email_providers', [
    ['gmail.com'],
    ['yahoo.com'],
    ['outlook.com'],
    ['company.com'],
]);

// Usage in tests
test('email_validation_accepts_valid_providers', function (string $provider) {
    $email = 'test@' . $provider;
    expect($email)->toBeValidEmail();
})->with('valid_email_providers');
```

### 5.3. Test Scenarios

Create reusable test scenarios:

```php
// Traits/CreatesUserScenarios.php
trait CreatesUserScenarios
{
    protected function createAdminUser(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    protected function createRegularUser(): User
    {
        return User::factory()->create([
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
    }
}
```

## 6. Assertions and Expectations

### 6.1. Pest Expectations

Use Pest's fluent expectation syntax:

```php
test('user_creation_works_correctly', function () {
    $user = User::factory()->create();

    expect($user)
        ->toBeInstanceOf(User::class)
        ->name->toBeString()
        ->email->toBeValidEmail()
        ->id->toBePositiveInteger();
});
```

### 6.2. Laravel Assertions

Use Laravel's testing assertions:

```php
test('user_registration_creates_user_successfully', function () {
    $response = $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response
        ->assertRedirect('/dashboard')
        ->assertSessionHas('success', 'Registration successful!');

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});
```

### 6.3. Custom Assertions

Create custom assertions for domain-specific validation:

```php
// Tests/Concerns/HasInvoiceAssertions.php
trait HasInvoiceAssertions
{
    protected function assertInvoiceCalculatesCorrectly(Invoice $invoice, float $expectedTotal): void
    {
        $calculatedTotal = $invoice->calculateTotal();

        expect($calculatedTotal)
            ->toBeFloat()
            ->toEqual($expectedTotal);
    }

    protected function assertInvoiceHasValidItems(Invoice $invoice): void
    {
        expect($invoice->items)
            ->toBeArray()
            ->not->toBeEmpty()
            ->each(function ($item) {
                expect($item)
                    ->toHaveKeys(['name', 'quantity', 'price'])
                    ->quantity->toBeGreaterThan(0)
                    ->price->toBeGreaterThan(0);
            });
    }
}
```

## 7. Mocking and Test Doubles

### 7.1. When to Mock

- **External Services**: Mock APIs and external dependencies
- **Time-dependent Logic**: Mock time for consistent tests
- **Random Data**: Use deterministic values instead of random data
- **Expensive Operations**: Mock database calls or file operations

### 7.2. Mocking Examples

```php
test('invoice_notification_uses_correct_service', function () {
    // Arrange
    $notificationService = Mockery::mock(NotificationService::class);
    $notificationService
        ->shouldReceive('sendInvoiceNotification')
        ->once()
        ->with(Mockery::type(Invoice::class), Mockery::type('string'));

    // Act
    $invoice = Invoice::factory()->create();
    $invoice->sendNotification($notificationService);

    // Assert - handled by mock expectations
});
```

### 7.3. Test Doubles and Fakes

Use Laravel's built-in fakes:

```php
test('email_is_queued_for_invoice_sending', function () {
    // Arrange
    Mail::fake();
    Event::fake();

    $invoice = Invoice::factory()->create();

    // Act
    $invoice->sendEmailNotification();

    // Assert
    Mail::assertQueued(InvoiceEmail::class, function ($mail) use ($invoice) {
        return $mail->hasTo($invoice->customer->email);
    });
});
```

## 8. Database Testing

### 8.1. Database Transactions

Use database transactions for test isolation:

```php
// TestCase.php
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Additional setup if needed
    }
}
```

### 8.2. Database Assertions

Use Laravel's database assertions:

```php
test('order_creation_updates_inventory', function () {
    $product = Product::factory()->create(['stock' => 100]);

    $this->post('/orders', [
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    $this->assertDatabaseHas('orders', [
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 95, // 100 - 5
    ]);
});
```

### 8.3. Seeding and Fixtures

Use database seeds for consistent test data:

```php
test('admin_can_view_all_users', function () {
    $this->seed(UserSeeder::class);

    $admin = User::where('role', 'admin')->first();
    $response = $this->actingAs($admin)->get('/admin/users');

    $response->assertOk();
    $response->assertSee('All Users');
});
```

## 9. State Testing Type Safety

### 9.1. Spatie Model States Testing

When testing with Spatie Model States:

```php
test('order_transitions_from_pending_to_processing', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    // Correct: Use state instance
    $order->transitionTo(OrderStatus::Processing);

    expect($order->status)->toBe(OrderStatus::Processing);
});

test('order_cannot_transition_from_completed_to_pending', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Completed]);

    // Test that invalid transitions are prevented
    expect(fn() => $order->transitionTo(OrderStatus::Pending))
        ->toThrow(InvalidStateTransition::class);
});
```

### 9.2. Type Safety Requirements

- **State Instances**: Always use state instances, not strings or enums
- **Available Methods**: Verify methods exist on state classes before use
- **Type Checking**: Use proper type checking for state-related logic
- **Documentation**: Document state transitions and business rules

## 10. Performance Testing

### 10.1. Load Testing

Use Stressless for performance testing:

```php
test('api_can_handle_concurrent_requests', function () {
    $response = stressless()
        ->get('/api/products')
        ->concurrent(20)
        ->for(10)->seconds()
        ->assertSuccessful();

    expect($response->response->getAverageResponseTime())
        ->toBeLessThan(500); // milliseconds
});
```

### 10.2. Memory Testing

Test memory usage for intensive operations:

```php
test('large_data_processing_does_not_exceed_memory_limit', function () {
    $initialMemory = memory_get_usage();

    // Process large dataset
    $processor = new LargeDataProcessor();
    $processor->process(10000);

    $finalMemory = memory_get_usage();
    $memoryUsed = $finalMemory - $initialMemory;

    expect($memoryUsed)->toBeLessThan(50 * 1024 * 1024); // 50MB
});
```

## 11. Browser Testing with Dusk

### 11.1. Browser Test Structure

```php
// Browser/UserRegistrationTest.php
class UserRegistrationTest extends DuskTestCase
{
    public function test_user_can_register_account(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertSee('Register')
                ->type('name', 'John Doe')
                ->type('email', 'john@example.com')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->click('@register-button')
                ->waitForLocation('/dashboard')
                ->assertSee('Welcome, John Doe')
                ->assertAuthenticatedAs(User::where('email', 'john@example.com')->first());
        });
    }
}
```

### 11.2. JavaScript Testing

Test JavaScript interactions and AJAX requests:

```php
public function test_dynamic_form_validation_works(): void
{
    $this->browse(function (Browser $browser) {
        $browser->visit('/contact')
            ->type('email', 'invalid-email')
            ->waitFor('.error-message')
            ->assertSee('Please enter a valid email address')
            ->type('email', 'valid@example.com')
            ->waitUntilMissing('.error-message')
            ->assertDontSee('Please enter a valid email address');
    });
}
```

## 12. Quality Assurance and CI/CD

### 12.1. Automated Testing in CI/CD

Include comprehensive testing in CI/CD pipeline:

```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.4
          extensions: dom, curl, libxml, mbstring, zip, pcntl, pdo, sqlite, pdo_sqlite, bcmath, soap, intl, gd, exif, iconv, imagick

      - name: Copy Environment File
        run: cp .env.testing .env

      - name: Install Dependencies
        run: composer install --no-progress --prefer-dist --optimize-autoloader

      - name: Generate Application Key
        run: php artisan key:generate

      - name: Run Tests
        run: vendor/bin/pest --coverage --min=90

      - name: Upload Coverage
        uses: codecov/codecov-action@v3
```

### 12.2. Quality Gates

Enforce quality gates for test requirements:

- **Coverage Threshold**: Minimum 90% code coverage
- **Test Success Rate**: 100% tests must pass
- **Performance Benchmarks**: Response time limits
- **Security Tests**: All security tests must pass

## 13. Test Documentation and Examples

### 13.1. Test Documentation

Document complex test scenarios and business logic:

```php
/**
 * Test that invoice calculation correctly handles:
 * - Multiple line items with different tax rates
 * - Discount application before tax
 * - Rounding to 2 decimal places
 * - Edge cases with zero amounts
 */
test('invoice_calculation_handles_complex_scenarios', function () {
    // Complex invoice scenario
});
```

### 13.2. Example Tests

Provide example tests for common patterns:

```php
// Example: Testing a service class
test('invoice_service_creates_invoice_with_correct_calculations', function () {
    // Arrange
    $service = new InvoiceService();
    $data = [
        'customer_id' => 1,
        'items' => [
            ['name' => 'Product A', 'quantity' => 2, 'price' => 100.00],
            ['name' => 'Product B', 'quantity' => 1, 'price' => 50.00],
        ],
        'discount' => 10.00,
    ];

    // Act
    $invoice = $service->createInvoice($data);

    // Assert
    expect($invoice)
        ->toBeInstanceOf(Invoice::class)
        ->subtotal->toBe(250.00) // (2 * 100) + (1 * 50)
        ->discount->toBe(10.00)
        ->total->toBe(240.00); // 250 - 10
});
```

## 14. Troubleshooting and Best Practices

### 14.1. Common Testing Issues

**Flaky Tests**:

- Identify and fix race conditions
- Use proper waiting mechanisms
- Isolate tests from external dependencies
- Use deterministic test data

**Slow Tests**:

- Optimize database queries in tests
- Use in-memory database when possible
- Mock expensive operations
- Parallelize test execution

**Complex Test Setup**:

- Use traits for reusable setup logic
- Create helper methods for common scenarios
- Use factories and datasets effectively
- Document complex setup procedures

### 14.2. Best Practices

- **Test Independence**: Tests should not depend on each other
- **Clear Assertions**: Use descriptive assertion messages
- **Test Data Management**: Use factories and datasets
- **Regular Maintenance**: Keep tests updated with code changes
- **Documentation**: Document complex test scenarios

## 15. Related Testing Guidelines

### 15.1. General Testing Principles

For general testing principles applicable across all frameworks:

- **[Testing Philosophy](../../Testing/010-testing-philosophy.md)** - Core testing principles and philosophies
- **[Test Categories](../../Testing/020-test-categories.md)** - General test categorization concepts
- **[Test Coverage](../../Testing/030-test-coverage.md)** - General coverage requirements and measurement
- **[Test Data Principles](../../Testing/040-test-data-principles.md)** - General test data management principles
- **[Test Helpers Patterns](../../Testing/050-test-helpers-patterns.md)** - General helper/utility patterns
- **[Test Linting Principles](../../Testing/060-test-linting-principles.md)** - General linting principles

### 15.2. PHP-Laravel Specific Resources

For PHP/Laravel-specific testing implementation:

- **[Testing Examples](Testing/010-testing-examples.md)** - Comprehensive PHP/Laravel test examples
- **[PHP Attributes Standards](Testing/020-php-attributes-standards.md)** - PHP 8 attributes for tests
- **[Laravel Test Helpers](Testing/030-laravel-test-helpers.md)** - Laravel-specific helpers and utilities
- **[Laravel Test Data](Testing/040-laravel-test-data.md)** - Laravel factories, seeders, and test data patterns
- **[Test Templates](Testing/050-test-templates/000-index.md)** - Standardized test templates
- **[Test Linting Rules](Testing/060-test-linting-rules.md)** - PHPStan/PHP-specific linting rules

## 16. Navigation

[←  Development Standards](020-development-standards.md) | [↑ Top](#testing-standards) |  [Security Standards →](040-security-standards.md)
