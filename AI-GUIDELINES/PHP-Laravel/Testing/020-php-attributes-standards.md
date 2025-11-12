# PHP Attributes Standards for Tests

## 1. Introduction

This document defines the standards for using PHP 8 attributes in tests. PHP attributes (introduced in PHP 8.0) provide a powerful way to add metadata to classes, methods, properties, and parameters, which can be used for test categorization, grouping, and other metadata.

## 2. Standard Attribute Types

### 2.1. Test Grouping Attributes

#### `#[Group]`

The `Group` attribute is used to categorize tests into logical groups that can be run together.

```php
use PHPUnit\Framework\Attributes\Group;

#[Group('api')]
class ApiTest extends TestCase
{
    // This entire class belongs to the 'api' group

    #[Group('authentication')]
    public function test_user_can_login()
    {
        // This test belongs to both 'api' and 'authentication' groups
    }
}
```

#### `#[CoversClass]`

The `CoversClass` attribute indicates which class is being tested, used for code coverage reporting.

```php
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Invoice::class)]
class InvoiceTest extends TestCase
{
    // Tests in this class cover the Invoice class
}
```

#### `#[UsesClass]`

The `UsesClass` attribute indicates which additional classes are used by the test.

```php
use PHPUnit\Framework\Attributes\UsesClass;

#[UsesClass(InvoiceCalculator::class)]
class InvoiceTest extends TestCase
{
    // Tests in this class use the InvoiceCalculator class
}
```

### 2.2. Test Behavior Attributes

#### `#[DataProvider]`

The `DataProvider` attribute specifies a method that provides test data.

```php
use PHPUnit\Framework\Attributes\DataProvider;

class CalculatorTest extends TestCase
{
    #[DataProvider('additionProvider')]
    public function test_addition($a, $b, $expected)
    {
        $this->assertEquals($expected, $a + $b);
    }

    public static function additionProvider()
    {
        return [
            [1, 2, 3],
            [0, 0, 0],
            [-1, 1, 0]
        ];
    }
}
```

#### `#[Depends]`

The `Depends` attribute indicates that a test depends on another test.

```php
use PHPUnit\Framework\Attributes\Depends;

class DependencyTest extends TestCase
{
    public function test_first()
    {
        $this->assertTrue(true);
        return 'value';
    }

    #[Depends('test_first')]
    public function test_second($value)
    {
        $this->assertEquals('value', $value);
    }
}
```

### 2.3. Test Configuration Attributes

#### `#[RunTestsInSeparateProcesses]`

The `RunTestsInSeparateProcesses` attribute indicates that all tests in a class should be run in separate PHP processes.

```php
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
class IsolatedTest extends TestCase
{
    // All tests in this class will run in separate processes
}
```

#### `#[RunInSeparateProcess]`

The `RunInSeparateProcess` attribute indicates that a specific test should be run in a separate PHP process.

```php
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class IsolatedTest extends TestCase
{
    #[RunInSeparateProcess]
    public function test_in_isolation()
    {
        // This test will run in a separate process
    }
}
```

### 2.4. Custom Project Attributes

#### `#[PluginTest]`

The `PluginTest` attribute indicates which plugin is being tested.

```php
use App\Tests\Attributes\PluginTest;

#[PluginTest('Invoices')]
class InvoiceTest extends TestCase
{
    // Tests in this class are for the Invoices plugin
}
```

#### `#[RequiresDatabase]`

The `RequiresDatabase` attribute indicates that a test requires a database connection.

```php
use App\Tests\Attributes\RequiresDatabase;

#[RequiresDatabase]
class DatabaseTest extends TestCase
{
    // Tests in this class require a database connection
}
```

## 3. Naming Conventions for Custom Attributes

When creating custom attributes for tests, follow these naming conventions:

1. Use PascalCase for attribute class names
2. Use descriptive names that clearly indicate the purpose of the attribute
3. Suffix the class name with "Attribute" (optional but recommended)
4. Place custom attributes in the `App\Tests\Attributes` namespace

Example:

```php
namespace App\Tests\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class RequiresDatabaseAttribute
{
    public function __construct(
        public readonly bool $refreshAfterTest = true
    ) {}
}
```

## 4. Applying Attributes

### 4.1. Class-Level Attributes

Class-level attributes apply to all test methods in the class:

```php
#[Group('api')]
#[CoversClass(Invoice::class)]
#[PluginTest('Invoices')]
class InvoiceApiTest extends TestCase
{
    // All tests in this class belong to the 'api' group,
    // cover the Invoice class, and are for the Invoices plugin
}
```

### 4.2. Method-Level Attributes

Method-level attributes apply only to the specific test method:

```php
class InvoiceTest extends TestCase
{
    #[Group('creation')]
    #[DataProvider('validInvoiceData')]
    public function test_create_invoice($data)
    {
        // This test belongs to the 'creation' group
        // and uses data from the validInvoiceData provider
    }
}
```

## 5. Best Practices

### 5.1. Attribute Organization

1. Order attributes from most general to most specific
2. Place test grouping attributes first, followed by behavior attributes, then configuration attributes
3. Keep related attributes together

Example:

```php
#[Group('api')]
#[Group('invoices')]
#[CoversClass(Invoice::class)]
#[UsesClass(InvoiceCalculator::class)]
#[RequiresDatabase]
class InvoiceApiTest extends TestCase
{
    #[Group('creation')]
    #[DataProvider('validInvoiceData')]
    #[Performance]
    public function test_create_invoice_performance($data)
    {
        // Test implementation
    }
}
```

### 5.2. Documentation

1. Document the purpose of custom attributes in PHPDoc blocks
2. Explain any non-obvious attribute parameters
3. Include examples of attribute usage in documentation

## 6. Standard Categories

The following standard categories should be used with the `#[Group]` attribute. See [Test Categories](../../Testing/020-test-categories.md) for general categorization principles.

### 6.1. Technical Categories

- `api`: Tests involving API endpoints
- `database`: Tests with database interactions
- `ui`: Tests involving UI components
- `performance`: Tests measuring performance
- `slow`: Tests that take longer than average to run
- `security`: Tests related to security features
- `validation`: Tests for data validation
- `error-handling`: Tests for error handling

### 6.2. Domain-Specific Categories

- `invoices`: Tests related to invoice functionality
- `payments`: Tests related to payment functionality
- `products`: Tests related to product functionality
- `accounts`: Tests related to account functionality
- `users`: Tests related to user management
- `permissions`: Tests related to permissions and access control

## 7. Running Tests by Category

To run tests by category using Pest:

```bash
./vendor/bin/pest --group=api
./vendor/bin/pest --group=invoices
./vendor/bin/pest --group="api,database"
```

## 8. Related Guidelines

- **[Test Categories](../../Testing/020-test-categories.md)** - General test categorization principles
- **[Testing Examples](010-testing-examples.md)** - Examples using PHP attributes
- **[Testing Standards](../030-testing-standards.md)** - Main testing standards

## 9. Navigation

[←  Testing Examples](010-testing-examples.md) | [↑ Top](#php-attributes-standards-for-tests) |  [Laravel Test Helpers →](030-laravel-test-helpers.md)
