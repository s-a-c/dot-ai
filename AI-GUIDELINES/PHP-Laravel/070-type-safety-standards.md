# Type Safety Standards

## Table of Contents

<details>
<summary>Expand Table of Contents</summary>

- [1. Introduction](#1-introduction)
- [2. Core Type Safety Principles](#2-core-type-safety-principles)
  - [2.1. Strict Type Declarations](#21-strict-type-declarations)
  - [2.2. Zero Mixed Types Policy](#22-zero-mixed-types-policy)
  - [2.3. Complete Type Coverage](#23-complete-type-coverage)
- [3. Property Type Annotations](#3-property-type-annotations)
  - [3.1. Basic Property Types](#31-basic-property-types)
  - [3.2. Complex Array Types](#32-complex-array-types)
  - [3.3. Nullable Types](#33-nullable-types)
- [4. Method Documentation Requirements](#4-method-documentation-requirements)
  - [4.1. PHPDoc Block Standards](#41-phpdoc-block-standards)
  - [4.2. Generic Type Documentation](#42-generic-type-documentation)
  - [4.3. Array Shape Documentation](#43-array-shape-documentation)
- [5. ValueObject Standards](#5-valueobject-standards)
  - [5.1. Readonly ValueObjects](#51-readonly-valueobjects)
  - [5.2. Factory Methods with Type Safety](#52-factory-methods-with-type-safety)
  - [5.3. Array Conversion Methods](#53-array-conversion-methods)
- [6. Service Layer Type Safety](#6-service-layer-type-safety)
  - [6.1. Interface Implementation](#61-interface-implementation)
  - [6.2. Service Implementation](#62-service-implementation)
  - [6.3. Dependency Injection Type Safety](#63-dependency-injection-type-safety)
- [7. Command Type Safety](#7-command-type-safety)
  - [7.1. Command Properties](#71-command-properties)
  - [7.2. Command Method Type Safety](#72-command-method-type-safety)
- [8. Exception Handling](#8-exception-handling)
  - [8.1. Exception Type Safety](#81-exception-type-safety)
  - [8.2. Custom Exception Classes](#82-custom-exception-classes)
- [9. Array Shape Definitions](#9-array-shape-definitions)
  - [9.1. Complex Array Structures](#91-complex-array-structures)
  - [9.2. Nested Array Types](#92-nested-array-types)
- [10. Generic Types](#10-generic-types)
  - [10.1. Collection Types](#101-collection-types)
  - [10.2. Factory Methods with Generics](#102-factory-methods-with-generics)
- [11. Laravel Zero Specific Patterns](#11-laravel-zero-specific-patterns)
  - [11.1. Service Container Type Safety](#111-service-container-type-safety)
  - [11.2. Configuration Type Safety](#112-configuration-type-safety)
  - [11.3. Enum Usage Patterns](#113-enum-usage-patterns)
- [12. Best Practices](#12-best-practices)
  - [12.1. Type Safety First](#121-type-safety-first)
  - [12.2. Documentation Standards](#122-documentation-standards)
  - [12.3. Error Handling](#123-error-handling)
  - [12.4. Performance Considerations](#124-performance-considerations)
  - [12.5. Maintenance](#125-maintenance)
- [13. Common Patterns](#13-common-patterns)
  - [13.1. Type-Safe Array Processing](#131-type-safe-array-processing)
  - [13.2. Type-Safe Factory Pattern](#132-type-safe-factory-pattern)
- [14. Integration with Other Guidelines](#14-integration-with-other-guidelines)
- [15. Navigation](#15-navigation)

</details>

## 1. Introduction

This document establishes comprehensive type safety standards for maintaining **PHPStan Level 10 compliance**. These standards are based on successfully implemented patterns throughout the codebase and provide practical guidance for writing type-safe PHP code in Laravel applications.

For related information, see:

- [Static Analysis Standards](060-static-analysis-standards.md) - PHPStan Level 10 configuration and tool setup
- [Development Standards](020-development-standards.md) - Code style and architecture patterns
- [Testing Standards](030-testing-standards.md) - Type-safe testing patterns

## 2. Core Type Safety Principles

### 2.1. Strict Type Declarations

**All PHP files must start with strict type declarations:**

```php
<?php

declare(strict_types=1);
```

### 2.2. Zero Mixed Types Policy

**Avoid mixed types wherever possible. When unavoidable, handle explicitly:**

```php
// ❌ Avoid: Mixed type without validation
public function processData(mixed $data): mixed
{
    return $data['key'] ?? null;
}

// ✅ Correct: Explicit type handling
public function processData(mixed $data): array
{
    if (!is_array($data)) {
        throw new InvalidArgumentException('Data must be an array');
    }

    $result = [];
    foreach ($data as $key => $value) {
        $result[(string) $key] = $value;
    }

    return $result;
}
```

### 2.3. Complete Type Coverage

**Every property, parameter, and return value must have explicit type information:**

```php
// ✅ Complete type coverage
final readonly class ValidationResult implements Stringable
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private string $url,
        private LinkStatus $status,
        private ValidationScope $scope,
        private ?string $error = null,
        private ?int $httpStatusCode = null,
        private ?string $redirectUrl = null,
        private float $responseTime = 0.0,
        private array $metadata = [],
        private ?string $filePath = null
    ) {}
}
```

## 3. Property Type Annotations

### 3.1. Basic Property Types

```php
// ✅ Correct: Complete property type annotations
final class ValidationConfig
{
    /**
     * @var array<ValidationScope>
     */
    private array $scopes;

    /**
     * @var array<string, mixed>
     */
    private array $options;

    private int $timeout;
    private bool $followRedirects;
    private ?string $userAgent;
}
```

### 3.2. Complex Array Types

```php
// ✅ Correct: Detailed array shape definitions
final readonly class ValidationResult
{
    /**
     * @var array<array{url: string, file: string, reason: string}>
     */
    public array $broken;

    /**
     * @var array<string>
     */
    public array $files;

    /**
     * @var array<array{url: string, status: string, scope: string, is_valid: bool, response_time: float}>
     */
    public array $links;
}
```

### 3.3. Nullable Types

```php
// ✅ Correct: Explicit nullable type handling
final class LinkValidationService
{
    public ?GitHubAnchorInterface $gitHubAnchorService = null;

    private ?string $lastError = null;
    private ?int $lastHttpCode = null;
}
```

## 4. Method Documentation Requirements

### 4.1. PHPDoc Block Standards

**Every method must have a complete PHPDoc block with:**

1. **Description**: Clear explanation of method purpose
2. **Parameters**: Type and description for each parameter
3. **Return Type**: Explicit return type documentation
4. **Throws**: All possible exceptions

```php
/**
 * Validate all links in a file and return a collection of validation results.
 *
 * @param string $filePath The path to the file to validate
 * @param ValidationConfig $config The validation configuration to use
 * @return ValidationResultCollection Collection of validation results for all links found
 * @throws SecurityException If the file path fails security validation
 * @throws RuntimeException If the file cannot be read
 */
public function validateFile(string $filePath, ValidationConfig $config): ValidationResultCollection
{
    // Implementation
}
```

### 4.2. Generic Type Documentation

```php
/**
 * Validate multiple links and return a collection of validation results.
 *
 * @param array<string> $urls Array of URLs to validate
 * @param ValidationConfig $config The validation configuration to use
 * @return ValidationResultCollection Collection of validation results
 */
public function validateLinks(array $urls, ValidationConfig $config): ValidationResultCollection
{
    // Implementation
}
```

### 4.3. Array Shape Documentation

```php
/**
 * Extract links from content with detailed metadata.
 *
 * @param string $content The content to extract links from
 * @return array<array{url: string, text: string, line: int}>
 */
public function extractLinks(string $content): array
{
    // Implementation
}
```

## 5. ValueObject Standards

### 5.1. Readonly ValueObjects

**Use readonly classes for immutable data structures:**

```php
final readonly class ValidationResult implements Stringable
{
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private string $url,
        private LinkStatus $status,
        private ValidationScope $scope,
        private ?string $error = null,
        private ?int $httpStatusCode = null,
        private ?string $redirectUrl = null,
        private float $responseTime = 0.0,
        private array $metadata = [],
        private ?string $filePath = null
    ) {}

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getStatus(): LinkStatus
    {
        return $this->status;
    }
}
```

### 5.2. Factory Methods with Type Safety

```php
/**
 * Create a successful validation result.
 *
 * @param array<string, mixed> $metadata
 */
public static function success(
    string $url,
    ValidationScope $scope,
    ?int $httpStatusCode = null,
    float $responseTime = 0.0,
    array $metadata = [],
    ?string $filePath = null
): self {
    return new self(
        url: $url,
        status: LinkStatus::VALID,
        scope: $scope,
        httpStatusCode: $httpStatusCode,
        responseTime: $responseTime,
        metadata: $metadata,
        filePath: $filePath
    );
}
```

### 5.3. Array Conversion Methods

```php
/**
 * Convert to array representation.
 *
 * @return array<string, mixed>
 */
public function toArray(): array
{
    return [
        'url' => $this->url,
        'status' => $this->status->value,
        'scope' => $this->scope->value,
        'is_valid' => $this->isValid(),
        'is_broken' => $this->isBroken(),
        'error' => $this->error,
        'http_status_code' => $this->httpStatusCode,
        'redirect_url' => $this->redirectUrl,
        'response_time' => $this->responseTime,
        'severity' => $this->getSeverity(),
        'recommended_action' => $this->getRecommendedAction(),
        'metadata' => $this->metadata,
        'file_path' => $this->filePath,
    ];
}
```

## 6. Service Layer Type Safety

### 6.1. Interface Implementation

```php
interface LinkValidationInterface
{
    /**
     * Validate all links in a file and return a collection of validation results.
     *
     * @param string $filePath The path to the file to validate
     * @param ValidationConfig $config The validation configuration to use
     * @return ValidationResultCollection Collection of validation results for all links found
     * @throws SecurityException If the file path fails security validation
     * @throws RuntimeException If the file cannot be read
     */
    public function validateFile(string $filePath, ValidationConfig $config): ValidationResultCollection;

    /**
     * Validate a single link and return the validation result.
     *
     * @param string $url The URL to validate
     * @param ValidationConfig $config The validation configuration to use
     * @return ValidationResult The validation result for the link
     */
    public function validateLink(string $url, ValidationConfig $config): ValidationResult;
}
```

### 6.2. Service Implementation

```php
final class LinkValidationService implements LinkValidationInterface
{
    public ?GitHubAnchorInterface $gitHubAnchorService = null;

    public function __construct(
        private readonly SecurityValidationInterface $security
    ) {}

    public function validateFile(string $filePath, ValidationConfig $config): ValidationResultCollection
    {
        // Type-safe implementation with explicit type checking
        $results = [];

        if (!file_exists($filePath)) {
            return ValidationResultCollection::fromArray([
                ValidationResult::create(
                    url: $filePath,
                    status: LinkStatus::BROKEN,
                    message: 'File not found'
                ),
            ]);
        }

        // Continue with type-safe implementation
    }
}
```

### 6.3. Dependency Injection Type Safety

```php
final class LinkValidationService implements LinkValidationInterface
{
    public function __construct(
        private readonly SecurityValidationInterface $security,
        private readonly ?GitHubAnchorInterface $gitHubAnchorService = null
    ) {}
}
```

## 7. Command Type Safety

### 7.1. Command Properties

```php
final class ValidateCommand extends BaseValidationCommand
{
    /**
     * The signature of the command.
     */
    protected $signature = 'validate
                            {paths* : Paths to validate (files or directories)}
                            {--scope=all : Validation scope (internal, external, anchor, image, all)}
                            {--format=console : Output format (console, json, html, markdown)}';

    /**
     * The description of the command.
     */
    protected $description = 'Validate links in documentation files with comprehensive scope and format options';

    public function __construct(
        LinkValidationInterface $linkValidation,
        ReportingInterface $reporting
    ) {
        parent::__construct($linkValidation, $reporting);
    }
}
```

### 7.2. Command Method Type Safety

```php
/**
 * Execute the console command.
 */
public function handle(): int
{
    try {
        // Validate command options using enum validation
        $this->validateCommandOptions();

        return match (true) {
            $this->option('interactive') => $this->handleInteractive(),
            default => $this->handleNonInteractive(),
        };
    } catch (Throwable $e) {
        return $this->handleValidationError($e);
    }
}

/**
 * Gather paths to validate with validation.
 *
 * @return array<string>
 */
private function gatherPaths(): array
{
    $paths = [];

    do {
        $path = text(
            label: 'Enter path to validate',
            placeholder: './docs',
            required: true,
            validate: fn (string $value): ?string => is_dir($value) || is_file($value)
                ? null
                : 'Path must exist'
        );

        $paths[] = $path;

        $addMore = confirm('Add another path?', false);
    } while ($addMore);

    return $paths;
}
```

## 8. Exception Handling

### 8.1. Exception Type Safety

```php
/**
 * Handle validation errors with proper type safety.
 */
private function handleValidationError(Throwable $e): int
{
    match (true) {
        $e instanceof SecurityException => $this->error("Security violation: {$e->getMessage()}"),
        $e instanceof ValidationException => $this->error("Validation error: {$e->getMessage()}"),
        $e instanceof RuntimeException => $this->error("Runtime error: {$e->getMessage()}"),
        default => $this->error("Unexpected error: {$e->getMessage()}"),
    };

    return self::FAILURE;
}
```

### 8.2. Custom Exception Classes

```php
final class SecurityException extends ValidateLinksException
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
```

## 9. Array Shape Definitions

### 9.1. Complex Array Structures

```php
/**
 * @return array<string, array<array{url: string, text: string, line: int}>>
 */
public function categorizeLinks(array $links): array
{
    $categorized = [
        'internal' => [],
        'external' => [],
        'anchor' => [],
    ];

    foreach ($links as $link) {
        $url = $link['url'];
        $linkType = $this->classifyLink($url);

        match ($linkType) {
            LinkType::ANCHOR => $categorized['anchor'][] = $link,
            LinkType::EXTERNAL => $categorized['external'][] = $link,
            LinkType::INTERNAL, LinkType::CROSS_REFERENCE => $categorized['internal'][] = $link,
        };
    }

    return $categorized;
}
```

### 9.2. Nested Array Types

```php
/**
 * Get broken links by type (for aggregate results).
 *
 * @return array<string, array<array{url: string, file: string, reason: string}>>
 */
public function getBrokenLinksByType(): array
{
    $results = $this->metadata['results'] ?? [];
    $brokenByType = [];

    if (!is_array($results)) {
        return $brokenByType;
    }

    foreach ($results as $result) {
        if ($result instanceof self && !$result->isValid()) {
            $type = $result->getScope()->value;
            if (!isset($brokenByType[$type])) {
                $brokenByType[$type] = [];
            }
            $brokenByType[$type][] = [
                'url' => $result->getUrl(),
                'file' => 'unknown', // Would need file context
                'reason' => $result->getError() ?? 'Validation failed',
            ];
        }
    }

    return $brokenByType;
}
```

## 10. Generic Types

### 10.1. Collection Types

```php
/**
 * @template T
 */
final class ValidationResultCollection implements Countable, IteratorAggregate
{
    /**
     * @param ValidationResult[] $results
     */
    public function __construct(
        private array $results = []
    ) {}

    /**
     * @return ValidationResult[]
     */
    public function toValidationResultArray(): array
    {
        return $this->results;
    }

    /**
     * @return Iterator<int, ValidationResult>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->results);
    }
}
```

### 10.2. Factory Methods with Generics

```php
/**
 * Create collection from array of results.
 *
 * @param ValidationResult[] $results
 */
public static function fromArray(array $results): self
{
    return new self($results);
}
```

## 11. Laravel Zero Specific Patterns

### 11.1. Service Container Type Safety

```php
// ✅ Correct: Type-safe service resolution
public function __construct(
    LinkValidationInterface $linkValidation,
    ReportingInterface $reporting
) {
    parent::__construct($linkValidation, $reporting);
}

// ✅ Correct: Type-safe app() usage
$service = app(LinkValidationInterface::class);
expect($service)->toBeInstanceOf(LinkValidationInterface::class);
```

### 11.2. Configuration Type Safety

```php
/**
 * Create validation configuration from array.
 *
 * @param array<string, mixed> $options
 */
public static function create(array $options): self
{
    $scopes = $options['scopes'] ?? [ValidationScope::ALL];
    if (!is_array($scopes)) {
        $scopes = [ValidationScope::ALL];
    }

    // Ensure all scopes are ValidationScope instances
    $validScopes = [];
    foreach ($scopes as $scope) {
        if ($scope instanceof ValidationScope) {
            $validScopes[] = $scope;
        } elseif (is_string($scope)) {
            $validScopes[] = ValidationScope::from($scope);
        }
    }

    return new self(
        scopes: $validScopes,
        timeout: is_int($options['timeout'] ?? null) ? $options['timeout'] : 30,
        concurrentRequests: is_int($options['concurrent_requests'] ?? null) ? $options['concurrent_requests'] : 10,
        followRedirects: is_bool($options['follow_redirects'] ?? null) ? $options['follow_redirects'] : true,
        maxRedirects: is_int($options['max_redirects'] ?? null) ? $options['max_redirects'] : 5,
        userAgent: is_string($options['user_agent'] ?? null) ? $options['user_agent'] : 'validate-links/1.0',
        cacheResults: is_bool($options['cache_results'] ?? null) ? $options['cache_results'] : true
    );
}
```

### 11.3. Enum Usage Patterns

```php
// ✅ Correct: Type-safe enum usage
public function classifyLink(string $url): LinkType
{
    if (str_starts_with($url, '#')) {
        return LinkType::ANCHOR;
    }

    if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
        return LinkType::EXTERNAL;
    }

    // Check if it's a cross-reference (internal link with anchor)
    if (str_contains($url, '#')) {
        return LinkType::CROSS_REFERENCE;
    }

    return LinkType::INTERNAL;
}
```

## 12. Best Practices

### 12.1. Type Safety First

- Always start with the most restrictive types possible
- Use union types sparingly and only when necessary
- Prefer explicit type checking over type casting

### 12.2. Documentation Standards

- Every public method must have complete PHPDoc
- Array shapes must be explicitly defined
- Generic types should be documented where applicable

### 12.3. Error Handling

- Use typed exceptions for different error categories
- Provide meaningful error messages with context
- Handle edge cases explicitly

### 12.4. Performance Considerations

- Use readonly classes for immutable data
- Prefer typed arrays over generic arrays
- Use appropriate data structures for the use case

### 12.5. Maintenance

- Keep type annotations synchronized with implementation
- Update PHPDoc when method signatures change
- Use static analysis tools to verify type safety

## 13. Common Patterns

### 13.1. Type-Safe Array Processing

```php
/**
 * Process array data with explicit type validation.
 *
 * @param array<string, mixed> $data
 * @return array<string, string>
 */
private function processArrayData(array $data): array
{
    $result = [];

    foreach ($data as $key => $value) {
        if (!is_string($key)) {
            continue;
        }

        if (is_scalar($value)) {
            $result[$key] = (string) $value;
        } elseif (is_array($value)) {
            $result[$key] = json_encode($value) ?: '';
        } else {
            $result[$key] = '';
        }
    }

    return $result;
}
```

### 13.2. Type-Safe Factory Pattern

```php
/**
 * Create instance from mixed data with type validation.
 *
 * @param array<string, mixed> $data
 */
public static function fromArray(array $data): self
{
    $url = $data['url'] ?? '';
    $metadata = $data['metadata'] ?? [];

    return new self(
        url: is_string($url) ? $url : '',
        status: isset($data['status']) ?
            LinkStatus::from(is_string($data['status']) || is_int($data['status']) ? $data['status'] : '') :
            LinkStatus::BROKEN,
        scope: isset($data['scope']) ?
            ValidationScope::from(is_string($data['scope']) || is_int($data['scope']) ? $data['scope'] : '') :
            ValidationScope::ALL,
        error: isset($data['error']) && (is_string($data['error']) || is_null($data['error'])) ? $data['error'] : null,
        responseTime: isset($data['response_time']) && (is_float($data['response_time']) || is_int($data['response_time'])) ?
            (float) $data['response_time'] : 0.0,
        metadata: is_array($metadata) ? $metadata : []
    );
}
```

## 14. Integration with Other Guidelines

- **Static Analysis Standards**: Type safety standards are enforced by PHPStan Level 10 analysis
- **Development Standards**: Type safety complements code style and architecture patterns
- **Testing Standards**: Type-safe code enables comprehensive test coverage
- **Laravel Zero Patterns**: Specific patterns for console application type safety

## 15. Navigation

[←  Static Analysis Standards](060-static-analysis-standards.md) | [↑ Top](#type-safety-standards) |  [Laravel Zero Patterns →](080-laravel-zero-patterns.md)
