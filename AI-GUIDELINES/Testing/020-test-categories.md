# Test Categories

## 1. Introduction

This document defines general test categorization concepts and schemes for organizing tests. Test categorization helps organize tests, make them easier to run selectively, and improve test maintainability.

## 2. Category Types

A comprehensive test categorization scheme typically includes four types of categories:

1. **Test Type Categories**: Define the type of test (unit, feature, integration)
2. **Technical Categories**: Define the technical area being tested (database, API, UI, etc.)
3. **Domain Categories**: Define the business domain or functionality being tested
4. **Cross-Cutting Categories**: Define aspects that cut across different domains (security, validation, etc.)

## 3. Test Type Categories (Required)

Every test should be categorized by its type. Common test type categories include:

- **unit**: Tests for individual components in isolation
- **feature**: Tests for features from a user perspective
- **integration**: Tests for interactions between components
- **arch**: Tests for architectural constraints and rules
- **e2e**: End-to-end tests for complete user scenarios
- **performance**: Tests for performance under load
- **smoke**: Basic smoke tests for critical functionality

## 4. Technical Categories (Optional)

Technical categories define the technical area being tested:

- **database**: Tests involving database operations
- **api**: Tests for API endpoints
- **ui**: Tests for user interface components
- **cli**: Tests for command-line interfaces
- **events**: Tests for event handling
- **cache**: Tests for caching functionality
- **queue**: Tests for queue functionality
- **mail**: Tests for email functionality
- **notification**: Tests for notification functionality
- **storage**: Tests for file storage functionality
- **auth**: Tests for authentication functionality
- **network**: Tests for network operations

## 5. Domain Categories (Optional)

Domain categories define the business domain or functionality being tested. These vary by application but may include:

- **billing**: Tests for billing functionality
- **reporting**: Tests for reporting functionality
- **tax**: Tests for tax calculation functionality
- **shipping**: Tests for shipping functionality
- **pricing**: Tests for pricing functionality
- **inventory**: Tests for inventory management functionality
- **user-management**: Tests for user management functionality
- **workflow**: Tests for workflow functionality
- **import-export**: Tests for import/export functionality
- **integration-external**: Tests for integration with external systems

## 6. Cross-Cutting Categories (Optional)

Cross-cutting categories define aspects that cut across different domains:

- **security**: Tests for security features and vulnerabilities
- **validation**: Tests for input validation
- **error-handling**: Tests for error handling
- **performance**: Tests for performance characteristics
- **accessibility**: Tests for accessibility features
- **localization**: Tests for localization and internationalization
- **compatibility**: Tests for compatibility with different environments
- **regression**: Tests for regression issues
- **edge-case**: Tests for edge cases and boundary conditions
- **critical-path**: Tests for critical business paths

## 7. Category Combinations

Categories can be combined to provide more specific categorization. For example:

- A test might be categorized as: unit + database + validation
- This indicates it's a unit test that involves database operations and focuses on validation

## 8. Running Tests by Category

Tests should be runnable by category to enable selective testing:

**Benefits**:
- Run only relevant tests during development
- Focus on specific areas when debugging
- Run fast tests separately from slow tests
- Organize CI/CD pipelines by test type

**Implementation**:
- Use framework-specific mechanisms (attributes, tags, groups)
- Provide command-line options for category selection
- Create convenient scripts for common category combinations

## 9. Best Practices

### 9.1. Consistency

- Use the same categories for similar tests
- Document category usage in project guidelines
- Review categories periodically for consistency

### 9.2. Appropriate Use

- Apply only categories that are relevant to the test
- Don't over-categorize tests
- Use categories that provide value for test organization

### 9.3. Required Categories

- Always include a test type category
- Include domain categories for domain-specific tests
- Use technical categories when they add value

### 9.4. Descriptive Names

- Use descriptive test names that complement the categories
- Categories should enhance, not replace, descriptive naming
- Document category meanings in project documentation

### 9.5. Maintenance

- Periodically review and update categories as the application evolves
- Remove unused categories
- Add new categories when needed, but document them

## 10. Adding New Categories

When adding new categories:

1. **Document the Category**: Define what the category means and when to use it
2. **Update Guidelines**: Add the category to this document
3. **Update Tooling**: Add support for the category in test runners and scripts
4. **Communicate**: Inform the team about the new category
5. **Review**: Periodically review whether the category is being used appropriately

## 11. Framework-Specific Implementation

While these categorization concepts are universal, the implementation varies by framework:

- **PHP/PHPUnit/Pest**: Use PHP attributes like `#[Group('category')]`
- **JavaScript/Jest**: Use test tags or `describe` blocks
- **Python/pytest**: Use markers
- **Ruby/RSpec**: Use tags or metadata

For framework-specific implementation details, see:
- **[PHP-Laravel Testing Guidelines](../PHP-Laravel/Testing/000-index.md)** - PHP/Laravel-specific category implementation

## 12. Related Guidelines

- **[Testing Philosophy](010-testing-philosophy.md)** - Core testing principles
- **[Test Coverage](030-test-coverage.md)** - Coverage requirements
- **[PHP-Laravel Testing Guidelines](../PHP-Laravel/Testing/000-index.md)** - Framework-specific implementation

## 13. Navigation

[←  Testing Philosophy](010-testing-philosophy.md) | [↑ Top](#test-categories) |  [Test Coverage →](030-test-coverage.md)
