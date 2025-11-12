# Test Helpers Patterns

## 1. Introduction

This document provides general patterns and best practices for creating test helpers and utilities. Test helpers reduce boilerplate code, improve test maintainability, and make tests more readable.

## 2. Purpose of Test Helpers

Test helpers serve several purposes:

- **Reduce Boilerplate**: Eliminate repetitive code in tests
- **Improve Readability**: Make tests more concise and understandable
- **Ensure Consistency**: Provide consistent patterns across tests
- **Centralize Logic**: Keep test setup logic in one place
- **Facilitate Maintenance**: Make it easier to update test patterns

## 3. Types of Test Helpers

### 3.1. Base Test Case Classes

Base test case classes provide common functionality for all tests:

- **Setup and Teardown**: Common initialization and cleanup
- **Shared Utilities**: Common helper methods
- **Configuration**: Test environment configuration
- **Assertions**: Custom assertion methods

### 3.2. Testing Traits

Traits provide specific functionality for different types of tests:

- **API Testing**: Helpers for API endpoint testing
- **Database Testing**: Helpers for database operations
- **Authentication Testing**: Helpers for authentication scenarios
- **File Testing**: Helpers for file operations

### 3.3. Helper Classes

Helper classes provide static methods for common testing tasks:

- **Data Generation**: Generate test data
- **File Operations**: Create and manage test files
- **Model Creation**: Create test models/objects
- **Utility Functions**: Common utility functions

### 3.4. Factory Helpers

Factory helpers make it easier to create test data:

- **Model Factories**: Create test models
- **Data Factories**: Generate test data
- **Relationship Helpers**: Create related objects
- **State Helpers**: Create objects in specific states

## 4. Helper Patterns

### 4.1. Setup and Teardown Helpers

Helpers for test setup and teardown:

**Patterns**:
- Centralized setup in base classes
- Reusable teardown methods
- Resource cleanup utilities
- Environment configuration

### 4.2. Data Creation Helpers

Helpers for creating test data:

**Patterns**:
- Factory methods for common objects
- Builder patterns for complex objects
- Fluent interfaces for data creation
- Default value providers

### 4.3. Assertion Helpers

Custom assertion helpers:

**Patterns**:
- Domain-specific assertions
- Complex condition assertions
- Response validation helpers
- Database assertion helpers

### 4.4. Mocking Helpers

Helpers for creating mocks and stubs:

**Patterns**:
- Mock factory methods
- Common mock configurations
- Stub creation utilities
- Mock verification helpers

## 5. Best Practices

### 5.1. Use the Right Helper

Choose the appropriate helper for the task:
- Use base classes for common functionality
- Use traits for specific test types
- Use helper classes for utility functions
- Use factories for data creation

### 5.2. Keep Helpers Focused

Each helper should have a single responsibility:
- Don't mix concerns in helpers
- Keep helpers focused on specific tasks
- Split complex helpers into smaller ones
- Document helper purposes clearly

### 5.3. Document Helpers

Document helpers to make them easy to understand:
- Explain what the helper does
- Document parameters and return values
- Provide usage examples
- Keep documentation up to date

### 5.4. Test Your Helpers

Write tests for helpers to ensure they work correctly:
- Test helper functionality
- Test edge cases
- Test error handling
- Maintain helper test coverage

### 5.5. Use Descriptive Names

Use descriptive names for helpers:
- Make helper names clear and descriptive
- Use consistent naming conventions
- Indicate helper purpose in the name
- Follow framework naming conventions

### 5.6. Avoid Duplication

If you find yourself writing the same code in multiple tests:
- Create a helper for it
- Reuse existing helpers
- Refactor common patterns
- Document helper usage

### 5.7. Keep Helpers Simple

Helpers should be simple and easy to understand:
- Avoid complex logic in helpers
- Keep helpers focused and simple
- Refactor complex helpers
- Use clear, straightforward implementations

### 5.8. Use Type Hints

Use type hints to make helpers clear:
- Indicate parameter types
- Specify return types
- Use type hints for better IDE support
- Improve code documentation

### 5.9. Use Default Values

Use default values for parameters to make helpers flexible:
- Provide sensible defaults
- Allow overrides when needed
- Make helpers easy to use
- Reduce required parameters

### 5.10. Use Method Chaining

Use method chaining for fluent interfaces:
- Enable readable helper usage
- Support fluent test setup
- Improve test readability
- Follow fluent interface patterns

## 6. Helper Organization

### 6.1. Directory Structure

Organize helpers in a clear directory structure:

```
tests/
├── Helpers/           # Helper classes
├── Traits/            # Testing traits
├── Factories/         # Test data factories
└── TestCase.php       # Base test case class
```

### 6.2. Naming Conventions

Follow consistent naming conventions:
- Helper classes: `*Helper` or `*TestHelper`
- Traits: `*TestingTrait` or `*TestTrait`
- Factory classes: `*Factory` or `*TestFactory`
- Base classes: `TestCase` or `BaseTestCase`

## 7. Creating Custom Helpers

When creating custom helpers:

1. **Identify the Need**: Identify repetitive patterns in tests
2. **Design the Helper**: Design a focused, reusable helper
3. **Implement the Helper**: Implement with clear, simple logic
4. **Document the Helper**: Document usage and examples
5. **Test the Helper**: Write tests for the helper
6. **Refactor Tests**: Update tests to use the new helper

## 8. Framework-Specific Implementation

Helper implementation varies by framework:

- **PHP/Laravel**: Base TestCase, traits, helper classes, factories
- **JavaScript**: Test utilities, helper functions, factories
- **Python**: Fixtures, helper functions, factories
- **Ruby**: Helpers, factories, fixtures

For framework-specific implementation details, see:
- **[PHP-Laravel Testing Guidelines](../PHP-Laravel/Testing/000-index.md)** - Laravel-specific helpers and utilities

## 9. Related Guidelines

- **[Testing Philosophy](010-testing-philosophy.md)** - Core testing principles
- **[Test Data Principles](040-test-data-principles.md)** - Test data management
- **[PHP-Laravel Testing Guidelines](../PHP-Laravel/Testing/000-index.md)** - Framework-specific implementation

## 10. Navigation

[←  Test Data Principles](040-test-data-principles.md) | [↑ Top](#test-helpers-patterns) |  [Test Linting Principles →](060-test-linting-principles.md)
