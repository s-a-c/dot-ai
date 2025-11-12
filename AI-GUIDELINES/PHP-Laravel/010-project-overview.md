# Project Overview

## 1. Introduction

This is a comprehensive, open-source application built on Laravel 12.x and FilamentPHP 3.x. It's designed for Small and Medium Enterprises (SMEs) and large-scale enterprises, offering a modular plugin architecture for managing various business operations.

## 2. Core Technologies

- **Laravel 12.x**: Modern PHP framework providing the foundation
- **FilamentPHP 3.x**: Admin panel framework for building the user interface
- **PHP 8.4+**: Taking advantage of modern PHP features
- **MySQL/PostgreSQL**: Database backend options

## 3. Project Structure

### 3.1. Core Directories

- `/app` - Core application code
- `/plugins` - Modular business logic organized by domain
- `/packages` - Custom packages and third-party integrations
- `/config` - Application configuration
- `/database` - Schema and data migrations
- `/resources` - Frontend assets and views
- `/routes` - URL routing definitions
- `/tests` - Testing infrastructure

### 3.2. Plugin Architecture

This application supports a modular plugin architecture where business logic can be organized into domain-specific plugins. Plugins are typically organized under `/plugins/{vendor}/` with each plugin representing a business domain.

**Note**: For specific information about the `aureauserp/aureuserp` package and its plugin architecture, see the [AureusERP Package Overview](AureusERP/010-package-overview.md).

A generic plugin structure follows this pattern:

```log
plugins/{vendor}/{module}/
├── composer.json          ← Composer package definition
├── src/
│   ├── {Module}Plugin.php ← FilamentPHP plugin class
│   ├── Models/           ← Domain models
│   ├── Resources/        ← FilamentPHP resources
│   └── Providers/        ← Service providers
├── database/
│   ├── migrations/       ← Database schema
│   └── seeders/         ← Sample data
└── tests/               ← Plugin-specific tests
```

### 3.3. Key Plugins

**Note**: The following plugin list is example-based. For the complete list of plugins available in the `aureauserp/aureuserp` package, see the [AureusERP Package Overview](AureusERP/010-package-overview.md).

#### 3.3.1. Core Plugins

- **Analytics**: Business intelligence and reporting
- **Chatter**: Internal communication
- **Fields**: Custom field definitions
- **Security**: Authentication and authorization
- **Support**: Customer support management
- **Table View**: Enhanced data visualization

#### 3.3.2. Business Plugins

- **Accounts**: Financial accounting
- **Contacts**: Customer and contact management
- **Employees**: Human resources
- **Inventories**: Stock management
- **Invoices**: Billing and invoicing
- **Partners**: Partner relationship management
- **Payments**: Payment processing
- **Products**: Product catalog management
- **Projects**: Project management
- **Purchases**: Procurement management
- **Recruitments**: Hiring and recruitment
- **Sales**: Sales management
- **Time-off**: Leave management
- **Timesheets**: Time tracking
- **Website**: Public-facing website management

## 4. FilamentPHP Integration

The project uses FilamentPHP extensively for admin interfaces:

- Resources follow FilamentPHP conventions
- FilamentShield for permission management
- FilamentPHP form and table builders
- Dual-panel architecture (Admin/Customer)

## 5. Database Architecture

- Laravel migrations for schema changes
- Eloquent ORM for data access
- Proper relationships between entities
- Database seeders for test data

## 6. Security Framework

- Laravel security best practices
- FilamentShield for permission management
- Input validation
- Data encryption
- Laravel Sanctum for API authentication

## 7. Development Environment

### 7.1. Local Setup

```bash
composer dev
```

This runs:

- Laravel development server
- Queue worker for background jobs
- Real-time log monitoring
- Asset compilation with hot reload

### 7.2. Quality Tools

- **PHPStan Level 10 (max)**: Maximum static analysis compliance (configured as `level: max` in phpstan.neon)
- **Laravel Pint**: Code formatting and style consistency
- **Pest/PHPUnit**: Comprehensive testing framework
- **Laravel Dusk**: Browser testing for user flows

## 8. Architecture Patterns

### 8.1. Domain-Driven Design

- Clear separation between layers
- Business logic encapsulated in domain models
- Rich domain models with behavior
- Bounded contexts for different business areas

### 8.2. Plugin Architecture Benefits

- **Modularity**: Independent development and deployment
- **Scalability**: Easy addition of new business domains
- **Maintainability**: Isolated code for different features
- **Extensibility**: Plugin system for custom functionality

## 9. Technology Integration

### 9.1. Modern PHP Features

- **PHP 8.4 Attributes**: Modern metadata declaration
- **Union Types**: Flexible type declarations
- **Match Expressions**: Improved conditional logic
- **Constructor Property Promotion**: Simplified class definitions

### 9.2. Laravel 12 Features

- **Improved Queue System**: Enhanced background processing
- **Advanced Cache**: Multi-layer caching strategies
- **Modern Routing**: Optimized route handling
- **Enhanced Security**: Built-in security improvements

## 10. Performance Considerations

### 10.1. Database Optimization

- Query optimization and indexing strategies
- Relationship optimization with eager loading
- Database connection pooling
- Read/write database separation

### 10.2. Caching Strategy

- Multi-level caching (application, database, CDN)
- Cache invalidation strategies
- Real-time cache warming
- Performance monitoring and metrics

## 11. Security Implementation

### 11.1. Authentication & Authorization

- Laravel Fortify for authentication
- FilamentShield for role-based permissions
- Multi-factor authentication support
- Session management and security

### 11.2. Data Protection

- Encryption for sensitive data
- Input validation and sanitization
- SQL injection prevention
- XSS protection mechanisms

## 12. Testing Strategy

### 12.1. Test Coverage Requirements

- **90% Minimum Coverage**: Comprehensive test suite
- **Multiple Test Types**: Unit, feature, integration, browser tests
- **Architecture Tests**: Enforce architectural boundaries
- **Performance Tests**: Load and stress testing

### 12.2. Quality Assurance

- Automated testing in CI/CD pipeline
- Code quality checks with PHPStan
- Security vulnerability scanning
- Performance regression testing

## 13. Deployment and Operations

### 13.1. Deployment Strategy

- Container-based deployment
- Zero-downtime deployments
- Database migration management
- Asset optimization and CDN integration

### 13.2. Monitoring and Observability

- Application performance monitoring
- Error tracking and alerting
- Log aggregation and analysis
- Health check endpoints

## 14. See Also

### Related Guidelines

- **[Development Standards](020-development-standards.md)** - Code quality and architecture patterns
- **[Security Standards](040-security-standards.md)** - Comprehensive security implementation guide
- **[Testing Standards](030-testing-standards.md)** - Testing requirements for plugins
- **[Performance Standards](050-performance-standards.md)** - Performance optimization techniques

### Quick Decision Guide for New Developers

#### "I need to create a new plugin - where do I start?"

1. **First**: Review this project overview to understand the architecture
2. **Then**: Check [Development Standards](020-development-standards.md) for coding patterns
3. **Next**: Follow [Testing Standards](030-testing-standards.md) for test implementation
4. **Finally**: Document using [Documentation Standards](../Documentation/010-documentation-standards.md)

#### "I need to understand the technology stack"

- **Laravel 12**: See [Development Standards](020-development-standards.md) section 3.4
- **FilamentPHP 3**: Review section 4 above and [Development Standards](020-development-standards.md) section 3.2.3
- **Security**: See [Security Standards](040-security-standards.md) for comprehensive security implementation
- **Performance**: See [Performance Standards](050-performance-standards.md) for optimization techniques

#### "I need to work with existing plugins"

- **Plugin Structure**: Review section 3.2 above for directory organization
- **AureusERP Package**: See [AureusERP Package Overview](AureusERP/010-package-overview.md) for package-specific plugin information
- **Business Plugins**: See section 3.3.2 for example business domains, or [AureusERP Package Overview](AureusERP/010-package-overview.md) for complete plugin list
- **Testing**: Use [Comprehensive Testing Guide](060-testing-comprehensive/000-index.md) for plugin-specific testing approaches, or [AureusERP Testing Examples](AureusERP/020-testing-examples.md) for package-specific examples

## 15. Navigation

 [←  PHP-Laravel Index](000-index.md) | [↑ Top](#project-overview) |  [Development Standards →*](020-development-standards.md)
