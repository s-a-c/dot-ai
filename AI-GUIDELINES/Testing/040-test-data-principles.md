# Test Data Principles

## 1. Introduction

This document outlines general principles for managing test data. Proper test data is crucial for effective testing, and understanding the principles of test data management helps ensure tests are reliable and maintainable.

## 2. General Test Data Principles

### 2.1. Isolation

Test data should be isolated from other tests to prevent interference.

**Requirements**:
- Each test should have its own data
- Tests should not depend on data from other tests
- Tests should clean up after themselves
- Use transactions or test databases when possible

### 2.2. Relevance

Test data should be relevant to the test case and represent realistic scenarios.

**Guidelines**:
- Use data that matches real-world scenarios
- Include edge cases and boundary conditions
- Test with both valid and invalid data
- Represent realistic data relationships

### 2.3. Minimalism

Use the minimum amount of test data necessary to test the functionality.

**Principles**:
- Create only the data needed for the test
- Avoid creating unnecessary test objects
- Use minimal data sets for faster tests
- Focus on essential attributes

### 2.4. Consistency

Test data should be consistent across related tests.

**Benefits**:
- Easier to understand test behavior
- Predictable test results
- Easier maintenance
- Clearer test intent

### 2.5. Reproducibility

Test data should be reproducible to ensure tests can be run repeatedly with the same results.

**Requirements**:
- Use fixed seeds for random data
- Avoid time-dependent data when possible
- Use deterministic data generation
- Document any non-deterministic aspects

## 3. Test Data Sources

Tests can use several sources for test data:

### 3.1. Factories

Factories are the preferred way to create test data. They provide:
- Consistent way to create objects
- Default values that can be overridden
- Reusable data generation logic
- Support for relationships and states

### 3.2. Seeders

Seeders can be used to populate the database with a standard set of data for testing:
- Useful for tests requiring specific data sets
- Provides consistent baseline data
- Can be used for integration tests
- Should be idempotent

### 3.3. Test Helpers

Test helpers provide methods for generating test data:
- Random data generation
- File creation utilities
- Model creation shortcuts
- Data transformation utilities

**Example Helper Methods**:
```php
// Generate random data
$email = TestHelpers::randomEmail();
$date = TestHelpers::randomDate();

// Create a test file
$file = TestHelpers::createTestFile('test.txt');

// Get random models
$users = TestHelpers::getRandomModels(User::class, 5);
```

### 3.4. In-Memory Data

For unit tests that don't require database interaction:
- Use plain objects or arrays
- Create mock objects
- Use in-memory data structures
- Avoid external dependencies

## 4. Test Data Requirements by Test Type

### 4.1. Unit Tests

Unit tests should use minimal test data focused on the specific component being tested:

- **Models/Objects**: Create only attributes needed for the test
- **Services**: Use mock objects for dependencies
- **Utilities**: Use minimal test data sets
- **Focus**: Test the component's logic, not data setup

### 4.2. Integration Tests

Integration tests should use test data that exercises the interactions between components:

- **Component Interactions**: Create data that tests interactions
- **Workflow Tests**: Use data that exercises complete workflows
- **Relationships**: Include related data to test relationships
- **Complex Scenarios**: Use data that represents complex scenarios

### 4.3. Feature Tests

Feature tests should use more comprehensive test data that represents realistic scenarios:

- **Controllers/Endpoints**: Create data that represents user scenarios
- **Forms**: Use data that represents form submissions
- **User Workflows**: Include data for complete user workflows
- **Realistic Scenarios**: Use data that matches real-world usage

## 5. Test Data Assumptions

When writing tests, common assumptions about test data include:

### 5.1. Database State

- Database is empty at the start of each test unless explicitly seeded
- Tests run in transactions that are rolled back
- Tests do not interfere with each other's data

### 5.2. Factory Definitions

- Factory definitions are up-to-date and create valid objects
- Factories produce consistent data
- Factory states are properly defined

### 5.3. Relationships

- Object relationships are properly defined
- Related objects can be created as needed
- Relationship data is consistent

### 5.4. Isolation

- Tests do not interfere with each other
- Test data is properly cleaned up
- External resources are reset between tests

## 6. Test Data Cleanup

Test data should be cleaned up after tests to prevent interference:

### 6.1. Automatic Cleanup

- Use database transactions for automatic rollback
- Use test database that is reset between runs
- Use framework-specific cleanup mechanisms

### 6.2. Manual Cleanup

- Clean up files created during tests
- Clear cache after tests that modify cached data
- Reset external resources
- Remove temporary data

### 6.3. Cleanup Best Practices

- Clean up in teardown methods
- Use try-finally blocks for guaranteed cleanup
- Document cleanup requirements
- Test cleanup doesn't interfere with other tests

## 7. Troubleshooting Test Data Issues

Common test data issues and how to resolve them:

### 7.1. Test Interference

If tests are interfering with each other:
- Ensure tests use isolated data
- Use transactions or test databases
- Clean up after each test
- Verify test isolation

### 7.2. Invalid Data

If tests are failing due to invalid data:
- Check factory definitions
- Verify data creation logic
- Ensure data meets validation requirements
- Review data assumptions

### 7.3. Missing Relationships

If tests are failing due to missing relationships:
- Ensure factory definitions create related objects
- Verify relationship setup
- Check relationship data requirements
- Review relationship assumptions

### 7.4. Inconsistent Results

If tests are producing inconsistent results:
- Use fixed seeds for random data
- Avoid time-dependent data
- Ensure deterministic data generation
- Document any non-deterministic aspects

## 8. Framework-Specific Implementation

Test data management implementation varies by framework:

- **PHP/Laravel**: Factories, seeders, model factories
- **JavaScript**: Factories, fixtures, test data builders
- **Python**: Fixtures, factories, test data factories
- **Ruby**: Factories, fixtures, builders

For framework-specific implementation details, see:
- **[PHP-Laravel Testing Guidelines](../PHP-Laravel/Testing/000-index.md)** - Laravel factories, seeders, and test data patterns

## 9. Related Guidelines

- **[Testing Philosophy](010-testing-philosophy.md)** - Core testing principles
- **[Test Helpers Patterns](050-test-helpers-patterns.md)** - Helper utilities for test data
- **[PHP-Laravel Testing Guidelines](../PHP-Laravel/Testing/000-index.md)** - Framework-specific implementation

## 10. Navigation

[←  Test Coverage](030-test-coverage.md) | [↑ Top](#test-data-principles) |  [Test Helpers Patterns →](050-test-helpers-patterns.md)
