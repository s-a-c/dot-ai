# AureusERP Package Guidelines Index

## 1. Introduction

This section contains comprehensive guidelines for working with the `aureauserp/aureuserp` Composer package, which is built on Laravel 12.x and FilamentPHP 3.x. The package uses the `Webkul\` namespace for all its components and provides a modular plugin architecture for managing various business operations.

## 2. Package Overview

### 2.1. [Package Overview](010-package-overview.md)

Comprehensive overview of the AureusERP package architecture, plugin system, and integration with Laravel and FilamentPHP. Includes plugin path structure, business plugins, and FilamentPHP integration specifics.

### 2.2. [Testing Examples](020-testing-examples.md)

Comprehensive testing examples specific to AureusERP plugins, including unit tests, feature tests, integration tests, and security tests using the `Webkul\` namespace.

### 2.3. [Test Templates](030-templates/)

Ready-to-use test templates for AureusERP plugins, including unit test templates, feature test templates, and integration test templates with proper `Webkul\` namespace examples.

## 3. Key Concepts

### 3.1. Package Information

- **Composer Package**: `aureauserp/aureuserp`
- **Namespace**: `Webkul\` (maintained from original package structure)
- **Framework**: Laravel 12.x + FilamentPHP 3.x
- **Plugin Architecture**: Modular business logic organized by domain

### 3.2. Plugin Structure

Plugins are organized under `/plugins/{vendor}/` with each plugin representing a business domain. The AureusERP package uses the `Webkul\` namespace for all plugin classes, models, services, and resources.

### 3.3. Business Plugins

The package includes numerous business plugins such as:
- Products (Product catalog management)
- Security (Authentication and authorization)
- Accounts (Financial accounting)
- Contacts (Customer and contact management)
- And many more (see Package Overview for complete list)

## 4. Integration with Main Guidelines

### 4.1. Development Standards

When working with AureusERP plugins, follow the main [Development Standards](../020-development-standards.md) while using the `Webkul\` namespace conventions documented here.

### 4.2. Testing Standards

All AureusERP plugin tests should follow the main [Testing Standards](../030-testing-standards.md) and use the examples and templates provided in this section.

### 4.3. Security Standards

Apply the main [Security Standards](../040-security-standards.md) when implementing security features in AureusERP plugins, particularly for authentication and authorization using the Security plugin.

## 5. Quick Reference

### 5.1. For New Developers

1. **Start with Package Overview** to understand the AureusERP architecture
2. **Review Testing Examples** to see how to test AureusERP plugins
3. **Use Test Templates** when creating new plugin tests
4. **Follow Main Development Standards** for code quality and patterns

### 5.2. For Specific Tasks

- **Creating New Plugin Tests**: Use templates from `030-templates/` directory
- **Understanding Plugin Structure**: See Package Overview section 3.2
- **Testing Existing Plugins**: Follow examples in Testing Examples document
- **Namespace Conventions**: Always use `Webkul\` namespace for AureusERP components

## 6. Navigation

 [←  PHP-Laravel Index](../000-index.md) | [↑ Top](#aureuserp-package-guidelines-index) |  [Package Overview →](010-package-overview.md)
