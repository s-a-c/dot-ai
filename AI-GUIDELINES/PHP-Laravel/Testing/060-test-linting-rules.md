# Test Linting Rules

## 1. Introduction

This document describes PHPStan/PHP-specific linting rules used to enforce test style standards. These rules check that test functions have the required PHP attributes and PHPDoc blocks.

## 2. PHPStan Rules for Tests

### 2.1. TestClassAttributesRule

This rule checks that test functions have the following PHP attributes:

- `#[Group]` - To categorize the test
- `#[CoversClass]` - To indicate which class is being tested
- `#[PluginTest]` - To indicate which plugin is being tested (if applicable)

### 2.2. TestFunctionDocBlockRule

This rule checks that test functions have a PHPDoc block that describes:

- What is being tested
- The expected outcome
- Any special setup or conditions

The PHPDoc block should be comprehensive (at least 50 characters long).

## 3. Example Compliant Test

```php
/**
 * Test Invoice model attributes and relationships
 *
 * This test verifies that the Invoice model's attributes are correctly set
 * and that its relationships with other models are properly defined.
 */
#[Test]
#[Group('unit')]
#[Group('invoices')]
#[CoversClass(Invoice::class)]
function invoice_model_attributes_and_relationships()
{
    // Test implementation
}
```

## 4. Usage

To run the linting rules on the test files, use PHPStan with test-specific configuration:

```bash
# Run PHPStan on tests with custom rules
./vendor/bin/phpstan analyse tests/ --level=10 --configuration=phpstan-tests.neon

# Or use a convenience script (if available)
./lint-tests.sh
```

## 5. Integration with CI/CD

The linting rules can be integrated into the CI/CD pipeline:

```yaml
- name: Lint Tests
  run: ./vendor/bin/phpstan analyse tests/ --level=10 --configuration=phpstan-tests.neon
```

## 6. Fixing Issues

If the linting rules report issues with your test files, you can fix them by:

1. Adding the required PHP attributes to the test functions
2. Adding or improving the PHPDoc blocks for the test functions
3. Ensuring all test functions follow the required structure

## 7. Related Guidelines

- **[Test Linting Principles](../../Testing/060-test-linting-principles.md)** - General linting principles
- **[Static Analysis Standards](../060-static-analysis-standards.md)** - PHPStan configuration
- **[PHP Attributes Standards](020-php-attributes-standards.md)** - Attribute requirements

## 8. Navigation

[←  Test Templates](050-test-templates/000-index.md) | [↑ Top](#test-linting-rules) |  [Testing Index →](000-index.md)
