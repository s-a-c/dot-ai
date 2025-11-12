# PHP-Laravel Testing Guidelines Index

## Overview

This directory contains PHP and Laravel-specific testing patterns, examples, and templates. These guidelines complement the general [Testing Guidelines](../../Testing/000-index.md) with framework-specific implementation details.

## PHP-Laravel Testing Resources

### 1. [Testing Examples](010-testing-examples.md)
Comprehensive PHP/Laravel test examples for unit, feature, and integration tests using Pest and PHPUnit.

### 2. [PHP Attributes Standards](020-php-attributes-standards.md)
Standards for using PHP 8 attributes in tests, including `#[Group]`, `#[Test]`, `#[CoversClass]`, and custom attributes.

### 3. [Laravel Test Helpers](030-laravel-test-helpers.md)
Laravel-specific helpers and utilities, including base TestCase classes, testing traits, and Laravel testing utilities.

### 4. [Laravel Test Data](040-laravel-test-data.md)
Laravel factories, seeders, and test data patterns for creating and managing test data in Laravel applications.

### 5. [Test Templates](050-test-templates/000-index.md)
Standardized test templates for unit, feature, and integration tests in PHP/Laravel.

### 6. [Test Linting Rules](060-test-linting-rules.md)
PHPStan/PHP-specific linting rules for enforcing test code quality and style standards.

## Integration with General Testing Guidelines

These Laravel-specific guidelines implement the general principles from [Testing Guidelines](../../Testing/000-index.md):

- **[Testing Philosophy](../../Testing/010-testing-philosophy.md)** - General testing principles (implemented with Pest/PHPUnit)
- **[Test Categories](../../Testing/020-test-categories.md)** - Test categorization (implemented with PHP attributes)
- **[Test Coverage](../../Testing/030-test-coverage.md)** - Coverage requirements (implemented with Pest/PHPUnit coverage)
- **[Test Data Principles](../../Testing/040-test-data-principles.md)** - Test data management (implemented with Laravel factories)
- **[Test Helpers Patterns](../../Testing/050-test-helpers-patterns.md)** - Helper patterns (implemented with Laravel traits and helpers)
- **[Test Linting Principles](../../Testing/060-test-linting-principles.md)** - Linting principles (implemented with PHPStan)

## Quick Reference

### For New Developers
1. Review [Testing Philosophy](../../Testing/010-testing-philosophy.md) for general principles
2. Read [Testing Examples](010-testing-examples.md) for Laravel-specific examples
3. Check [PHP Attributes Standards](020-php-attributes-standards.md) for test categorization
4. Review [Laravel Test Data](040-laravel-test-data.md) for data management
5. Use [Test Templates](050-test-templates/000-index.md) when creating new tests

### For Specific Tasks
- **Writing Tests**: Use [Testing Examples](010-testing-examples.md) and [Test Templates](050-test-templates/000-index.md)
- **Test Data**: Follow [Laravel Test Data](040-laravel-test-data.md) patterns
- **Test Helpers**: Use [Laravel Test Helpers](030-laravel-test-helpers.md)
- **Code Quality**: Follow [Test Linting Rules](060-test-linting-rules.md)

## Related Guidelines

- **[Testing Standards](../030-testing-standards.md)** - Main entry point for PHP/Laravel testing standards
- **[General Testing Guidelines](../../Testing/000-index.md)** - General testing principles
- **[Development Standards](../020-development-standards.md)** - General development standards
- **[Static Analysis Standards](../060-static-analysis-standards.md)** - PHPStan configuration

## Navigation

[←  PHP-Laravel Index](../000-index.md) | [↑ Top](#php-laravel-testing-guidelines-index) |  [Testing Examples →](010-testing-examples.md)
