# Test Templates Index

## Overview

This directory contains standardized templates for creating tests in PHP/Laravel projects. These templates provide consistent starting points for different types of tests, ensuring that all tests follow the project's standards and best practices.

## Available Templates

1. [Unit Test Template](010-unit-test-template.php) - Template for creating unit tests that test individual methods or functions
2. [Feature Test Template](020-feature-test-template.php) - Template for creating feature tests that test complete user workflows
3. [Integration Test Template](030-integration-test-template.php) - Template for creating integration tests that test component interactions

## Usage Guidelines

### Choosing the Right Template

- **Unit Tests** - Use when testing individual methods, functions, or classes in isolation
- **Feature Tests** - Use when testing complete user workflows or API endpoints
- **Integration Tests** - Use when testing how multiple components work together

### Template Customization

Each template includes:
- Standard file structure and imports
- Common setup and teardown patterns
- Example test methods
- Documentation comments explaining key concepts
- Best practice examples

### Getting Started

1. Copy the appropriate template file to your test directory
2. Rename the file to match your test subject
3. Update the class name and namespace
4. Replace placeholder content with your actual test logic
5. Follow the patterns and conventions shown in the template

## Standards Compliance

All templates follow:
- Project coding standards
- PHPUnit/Pest best practices
- Laravel testing conventions
- PHP 8 attributes standards
- Documentation requirements

## Related Guidelines

- **[PHP Attributes Standards](../020-php-attributes-standards.md)** - Guidelines for PHP attributes in tests
- **[Testing Examples](../010-testing-examples.md)** - Examples using these templates
- **[Testing Standards](../../030-testing-standards.md)** - Main testing standards

## Navigation

[←  Laravel Test Data](../040-laravel-test-data.md) | [↑ Top](#test-templates-index) |  [Unit Test Template →](010-unit-test-template.php)
