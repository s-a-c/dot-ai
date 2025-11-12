# AureusERP Test Templates Index

## 1. Introduction

This directory contains ready-to-use test templates specifically for AureusERP plugins. All templates use the `Webkul\` namespace convention used by the `aureauserp/aureuserp` package.

## 2. Available Templates

### 2.1. [Unit Test Template](unit-test-template.php)

Template for creating unit tests for AureusERP plugin models. Includes examples for testing model attributes, relationships, methods, validation, scopes, and events using the `Webkul\` namespace.

### 2.2. [Feature Test Template](feature-test-template.php)

Template for creating feature tests for AureusERP plugin resources. Includes examples for testing resource listing, creation, viewing, editing, deletion, validation, and filtering using FilamentPHP resources with the `Webkul\` namespace.

### 2.3. [Integration Test Template](integration-test-template.php)

Template for creating integration tests for AureusERP plugin services. Includes examples for testing service functionality, state transitions, validation, database transactions, error handling, events, and complex business logic using the `Webkul\` namespace.

## 3. Usage

### 3.1. Using Templates

1. Copy the appropriate template file
2. Replace `YourPlugin` with your actual plugin name
3. Replace `YourModel` with your actual model name
4. Update namespace references to match your plugin's namespace (always `Webkul\{Module}\`)
5. Customize test cases for your specific use case

### 3.2. Namespace Convention

All AureusERP plugins use the `Webkul\` namespace:

- **Models**: `Webkul\{Module}\Models\{ModelName}`
- **Services**: `Webkul\{Module}\Services\{ServiceName}`
- **Security Components**: `Webkul\Security\Models\{Component}`

### 3.3. Test Attributes

All templates include proper test attributes:

- `#[PluginTest('PluginName')]` - Identifies the plugin
- `#[Group('test-type')]` - Groups tests by type (unit, feature, integration)
- `#[Group('plugin-name')]` - Groups tests by plugin
- `#[CoversClass]` - Specifies which class is being tested

## 4. Integration with Main Guidelines

### 4.1. Testing Standards

These templates follow the main [Testing Standards](../../030-testing-standards.md) and should be used in conjunction with:

- **[Testing Examples](../../021-testing-examples.md)** - Generic testing examples
- **[AureusERP Testing Examples](../020-testing-examples.md)** - AureusERP-specific examples
- **[Testing Standards](../../030-testing-standards.md)** - Comprehensive testing requirements

### 4.2. Development Standards

When using these templates, also follow:

- **[Development Standards](../../020-development-standards.md)** - Code quality and architecture patterns
- **[Package Overview](../010-package-overview.md)** - AureusERP package architecture

## 5. Navigation

 [←  Testing Examples](../020-testing-examples.md) | [↑ Top](#aureuserp-test-templates-index) |  [Unit Test Template →](unit-test-template.php)
