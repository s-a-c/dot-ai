# Testing Guidelines Index

## Overview

This directory contains general testing principles and guidelines applicable across all programming languages and frameworks. These guidelines focus on universal testing concepts, philosophies, and best practices.

## General Testing Principles

### 1. [Testing Philosophy](010-testing-philosophy.md)
Core testing principles and philosophies that guide testing practices, including test behavior vs implementation, test independence, and maintainability.

### 2. [Test Categories](020-test-categories.md)
General test categorization concepts and schemes for organizing tests, including test type categories, technical categories, domain categories, and cross-cutting concerns.

### 3. [Test Coverage](030-test-coverage.md)
General test coverage requirements, measurement principles, and best practices for maintaining comprehensive test coverage.

### 4. [Test Data Principles](040-test-data-principles.md)
General principles for managing test data, including isolation, relevance, minimalism, consistency, and reproducibility.

### 5. [Test Helpers Patterns](050-test-helpers-patterns.md)
General patterns and best practices for creating test helpers and utilities to reduce boilerplate and improve test maintainability.

### 6. [Test Linting Principles](060-test-linting-principles.md)
General principles for enforcing test code quality through linting, including style standards and documentation requirements.

## Integration with Framework-Specific Guidelines

These general principles are complemented by framework-specific testing guidelines:

- **PHP/Laravel**: See [PHP-Laravel Testing Guidelines](../PHP-Laravel/Testing/000-index.md) for Pest, PHPUnit, Laravel factories, and PHP 8 attributes
- **JavaScript/TypeScript**: See [JavaScript-TypeScript Index](../JavaScript-TypeScript/000-index.md) for testing guidelines
- **Shell/CLI**: See [Shell-CLI Index](../Shell-CLI/000-index.md) for shell testing standards

## Quick Reference

### For New Developers
1. Start with [Testing Philosophy](010-testing-philosophy.md) to understand core principles
2. Review [Test Categories](020-test-categories.md) to understand test organization
3. Read [Test Data Principles](040-test-data-principles.md) for data management
4. Check framework-specific guidelines for implementation details

### For Specific Tasks
- **Writing Tests**: Follow Testing Philosophy and Test Categories principles
- **Test Data**: Apply Test Data Principles for data management
- **Test Helpers**: Review Test Helpers Patterns for reusable utilities
- **Code Quality**: Follow Test Linting Principles for code standards

## Related Guidelines

- **[Documentation Standards](../Documentation/010-documentation-standards.md)** - Documentation standards for test documentation
- **[PHP-Laravel Testing Standards](../PHP-Laravel/030-testing-standards.md)** - Main entry point for PHP/Laravel testing
- **[PHP-Laravel Testing Guidelines](../PHP-Laravel/Testing/000-index.md)** - Laravel-specific testing patterns and templates

## Navigation

[←  AI Guidelines Index](../000-index.md) | [↑ Top](#testing-guidelines-index) |  [Testing Philosophy →](010-testing-philosophy.md)
