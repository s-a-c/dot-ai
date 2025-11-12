# AureusERP Package Overview

## 1. Introduction

The `aureauserp/aureuserp` package is a comprehensive, open-source Laravel 12.x and FilamentPHP 3.x application designed for Small and Medium Enterprises (SMEs) and large-scale enterprises. It offers a modular plugin architecture for managing various business operations.

**Important**: This package uses the `Webkul\` namespace for all its components. This namespace is maintained from the original package structure and should be used consistently throughout all plugin code, tests, and documentation.

## 2. Package Information

### 2.1. Composer Package

- **Package Name**: `aureauserp/aureuserp`
- **Namespace**: `Webkul\`
- **Framework**: Laravel 12.x
- **Admin Panel**: FilamentPHP 3.x
- **PHP Version**: PHP 8.4+

### 2.2. Core Technologies

- **Laravel 12.x**: Modern PHP framework providing the foundation
- **FilamentPHP 3.x**: Admin panel framework for building the user interface
- **PHP 8.4+**: Taking advantage of modern PHP features
- **MySQL/PostgreSQL**: Database backend options

## 3. Plugin Architecture

### 3.1. Plugin Directory Structure

Plugins can be organized under `/plugins/{vendor}/` with each plugin representing a business domain. The actual path structure may vary based on the package installation, but typically follows this pattern:

```log
plugins/{vendor}/{module}/
├── composer.json          ← Composer package definition
├── src/
│   ├── {Module}Plugin.php ← FilamentPHP plugin class
│   ├── Models/           ← Domain models (Webkul\ namespace)
│   ├── Resources/        ← FilamentPHP resources
│   └── Providers/        ← Service providers
├── database/
│   ├── migrations/       ← Database schema
│   └── seeders/         ← Sample data
└── tests/               ← Plugin-specific tests
```

**Note**: The actual plugin path structure depends on the package installation. Common paths include `/plugins/webkul/` or `/plugins/aureuserp/` depending on the package configuration. Always verify the actual path structure in your specific installation.

### 3.2. Namespace Convention

All AureusERP plugins use the `Webkul\` namespace:

- **Models**: `Webkul\{Module}\Models\{ModelName}`
- **Services**: `Webkul\{Module}\Services\{ServiceName}`
- **Repositories**: `Webkul\{Module}\Repositories\{RepositoryName}`
- **Resources**: `Webkul\{Module}\Resources\{ResourceName}`
- **Plugins**: `Webkul\{Module}\{Module}Plugin`

Example:
- Product model: `Webkul\Product\Models\Product`
- Security user model: `Webkul\Security\Models\User`
- Product service: `Webkul\Product\Services\ProductService`

### 3.3. Plugin Organization

Plugins are organized by business domain, with each plugin representing a distinct business capability. This modular approach allows for:

- **Independent Development**: Plugins can be developed and maintained separately
- **Scalability**: Easy addition of new business domains
- **Maintainability**: Isolated code for different features
- **Extensibility**: Plugin system for custom functionality

## 4. Key Plugins

### 4.1. Core Plugins

- **Analytics**: Business intelligence and reporting
- **Chatter**: Internal communication
- **Fields**: Custom field definitions
- **Security**: Authentication and authorization (uses `Webkul\Security\` namespace)
- **Support**: Customer support management
- **Table View**: Enhanced data visualization

### 4.2. Business Plugins

- **Accounts**: Financial accounting (`Webkul\Accounts\`)
- **Contacts**: Customer and contact management (`Webkul\Contacts\`)
- **Employees**: Human resources (`Webkul\Employees\`)
- **Inventories**: Stock management (`Webkul\Inventories\`)
- **Invoices**: Billing and invoicing (`Webkul\Invoices\`)
- **Partners**: Partner relationship management (`Webkul\Partners\`)
- **Payments**: Payment processing (`Webkul\Payments\`)
- **Products**: Product catalog management (`Webkul\Product\`)
- **Projects**: Project management (`Webkul\Projects\`)
- **Purchases**: Procurement management (`Webkul\Purchases\`)
- **Recruitments**: Hiring and recruitment (`Webkul\Recruitments\`)
- **Sales**: Sales management (`Webkul\Sales\`)
- **Time-off**: Leave management (`Webkul\TimeOff\`)
- **Timesheets**: Time tracking (`Webkul\Timesheets\`)
- **Website**: Public-facing website management (`Webkul\Website\`)

## 5. FilamentPHP Integration

The AureusERP package uses FilamentPHP extensively for admin interfaces:

- **Resources**: Follow FilamentPHP conventions with `Webkul\` namespace
- **FilamentShield**: Permission management integrated with Security plugin
- **Form and Table Builders**: FilamentPHP form and table builders
- **Dual-panel Architecture**: Admin/Customer panel separation

### 5.1. FilamentPHP Resource Structure

FilamentPHP resources in AureusERP plugins follow this structure:

```php
namespace Webkul\{Module}\Resources;

use Filament\Resources\Resource;
use Webkul\{Module}\Models\{ModelName};

class {ModelName}Resource extends Resource
{
    // Resource implementation
}
```

## 6. Database Architecture

- **Laravel Migrations**: Schema changes follow Laravel conventions
- **Eloquent ORM**: Data access using Eloquent models with `Webkul\` namespace
- **Relationships**: Proper relationships between entities across plugins
- **Database Seeders**: Test data seeders for each plugin

## 7. Security Framework

- **Laravel Security**: Best practices from Laravel framework
- **FilamentShield**: Permission management via Security plugin
- **Input Validation**: Comprehensive validation using Form Requests
- **Data Encryption**: Sensitive data encryption
- **Laravel Sanctum**: API authentication

### 7.1. Security Plugin

The Security plugin (`Webkul\Security\`) provides:

- **User Management**: `Webkul\Security\Models\User`
- **Role Management**: `Webkul\Security\Models\Role`
- **Permission Management**: `Webkul\Security\Models\Permission`
- **Authentication**: Integration with Laravel Fortify
- **Authorization**: Role-based access control via FilamentShield

## 8. Testing Strategy

### 8.1. Test Organization

Tests for AureusERP plugins should:

- Use the `Webkul\` namespace in all test files
- Follow Pest testing framework conventions
- Include comprehensive test coverage (90% minimum)
- Use proper test attributes and groups

### 8.2. Test Examples

See [Testing Examples](020-testing-examples.md) for comprehensive examples of testing AureusERP plugins.

### 8.3. Test Templates

Ready-to-use test templates are available in the [Test Templates](030-templates/) directory.

## 9. Development Workflow

### 9.1. Plugin Development

When developing new plugins or modifying existing ones:

1. **Use `Webkul\` Namespace**: All classes must use the `Webkul\{Module}\` namespace
2. **Follow Laravel Conventions**: Adhere to Laravel 12.x patterns and conventions
3. **Implement FilamentPHP Resources**: Use FilamentPHP for admin interfaces
4. **Write Tests**: Comprehensive test coverage using Pest
5. **Document Code**: Follow PHPDoc standards and inline documentation

### 9.2. Code Quality

- **PHPStan Level 10**: Maximum static analysis compliance
- **Laravel Pint**: Code formatting and style consistency
- **Pest/PHPUnit**: Comprehensive testing framework
- **Type Safety**: Explicit type declarations and return types

## 10. Integration with Main Application

When integrating AureusERP plugins into a main application:

1. **Composer Installation**: Install via `composer require aureauserp/aureuserp`
2. **Service Provider Registration**: Register plugin service providers
3. **Configuration**: Configure plugin settings as needed
4. **Database Migrations**: Run plugin migrations
5. **Asset Compilation**: Compile plugin assets if needed

## 11. See Also

### Related Guidelines

- **[Testing Examples](020-testing-examples.md)** - Comprehensive testing examples for AureusERP plugins
- **[Test Templates](030-templates/)** - Ready-to-use test templates
- **[Development Standards](../020-development-standards.md)** - Code quality and architecture patterns
- **[Testing Standards](../030-testing-standards.md)** - Testing requirements for plugins
- **[Security Standards](../040-security-standards.md)** - Comprehensive security implementation guide

## 12. Navigation

 [←  AureusERP Index](000-index.md) | [↑ Top](#aureuserp-package-overview) |  [Testing Examples →](020-testing-examples.md)
