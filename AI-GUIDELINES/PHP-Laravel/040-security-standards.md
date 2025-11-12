# Security Standards

## 1. Core Security Principle

**All security implementations should be clear, verifiable, and suitable for a junior developer to understand, implement, and maintain.**

This principle ensures that security measures are not only effective but also maintainable and auditable by team members at all experience levels.

## 2. Authentication and Authorization

### 2.1. Laravel Authentication System

#### 2.1.1. Built-in Authentication
- Use Laravel's built-in authentication system as the foundation
- Implement Laravel Breeze or Jetstream for starter authentication scaffolding
- Never build custom authentication from scratch without compelling reasons
- Follow Laravel's authentication conventions and naming patterns

#### 2.1.2. Multi-Factor Authentication (MFA)
- Implement MFA for all administrative accounts
- Use Laravel Fortify for two-factor authentication features
- Support TOTP (Time-based One-Time Password) applications
- Provide backup recovery codes for account recovery

#### 2.1.3. Password Security
- Enforce strong password requirements:
  - Minimum 12 characters
  - Mix of uppercase, lowercase, numbers, and symbols
  - No common dictionary words or patterns
- Use Laravel's built-in password validation rules
- Implement password history to prevent reuse of recent passwords
- Set password expiration policies for sensitive accounts

### 2.2. FilamentShield Integration

#### 2.2.1. Role-Based Access Control (RBAC)
- Use FilamentShield for comprehensive permission management
- Define roles based on business functions, not technical access levels
- Implement principle of least privilege - grant minimum necessary permissions
- Regular audit and review of role assignments

#### 2.2.2. Permission Structure
```php
// Example permission structure
'permissions' => [
    'view_invoices',
    'create_invoices',
    'edit_invoices',
    'delete_invoices',
    'approve_invoices',
    'export_invoices'
]
```

#### 2.2.3. Resource-Level Security
- Implement resource-level permissions for FilamentPHP resources
- Use policy classes for complex authorization logic
- Ensure consistent permission checking across all admin interfaces

### 2.3. Session Management

#### 2.3.1. Session Configuration
- Use secure session configuration in production:
  - `SESSION_SECURE=true` for HTTPS-only cookies
  - `SESSION_HTTP_ONLY=true` to prevent XSS access
  - `SESSION_SAME_SITE=strict` for CSRF protection
- Set appropriate session lifetime based on security requirements
- Implement session regeneration on authentication state changes

#### 2.3.2. Session Security
- Invalidate sessions on password changes
- Implement concurrent session limits for high-security accounts
- Log and monitor suspicious session activities
- Provide users with active session management capabilities

## 3. Data Protection

### 3.1. Data Encryption

#### 3.1.1. Encryption at Rest
- Use Laravel's built-in encryption for sensitive data fields
- Implement database-level encryption for highly sensitive information
- Use encrypted database columns for PII (Personally Identifiable Information)
- Example implementation:
```php
// Model with encrypted attributes
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    protected $casts = [
        'social_security_number' => 'encrypted',
        'bank_account' => 'encrypted',
    ];
}
```

#### 3.1.2. Encryption in Transit
- Enforce HTTPS for all connections in production
- Use TLS 1.3 or higher for all external communications
- Implement certificate pinning for critical API connections
- Validate SSL certificates and reject invalid connections

### 3.2. Input Validation and Sanitization

#### 3.2.1. Request Validation
- Use Laravel's Form Request validation for all user inputs
- Implement whitelist-based validation (define what is allowed)
- Validate data types, formats, and ranges explicitly
- Example validation rules:
```php
public function rules(): array
{
    return [
        'email' => ['required', 'email:rfc,dns', 'max:255'],
        'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
        'status' => ['required', 'in:pending,approved,rejected'],
    ];
}
```

#### 3.2.2. Output Sanitization
- Use Blade templating engine's automatic escaping
- Explicitly escape data when using `{!! !!}` syntax
- Sanitize data before storing in database
- Validate and sanitize file uploads rigorously

### 3.3. Database Security

#### 3.3.1. Query Protection
- Use Eloquent ORM to prevent SQL injection attacks
- Avoid raw SQL queries; when necessary, use parameter binding
- Implement database query logging and monitoring
- Use read-only database connections for reporting queries

#### 3.3.2. Database Access Control
- Use separate database users for different application components
- Implement database-level access controls and permissions
- Regular database security audits and vulnerability assessments
- Encrypt database backups and secure backup storage

## 4. Web Application Security

### 4.1. Cross-Site Scripting (XSS) Prevention
- Use Blade templating with automatic escaping by default
- Implement Content Security Policy (CSP) headers
- Validate and sanitize all user-generated content
- Use Laravel's built-in XSS protection mechanisms

### 4.2. Cross-Site Request Forgery (CSRF) Protection
- Enable CSRF protection for all state-changing operations
- Use Laravel's built-in CSRF middleware
- Implement proper CSRF token handling in AJAX requests
- Validate CSRF tokens on all form submissions

### 4.3. Security Headers
Implement comprehensive security headers:
```php
// Example security headers configuration
'headers' => [
    'X-Content-Type-Options' => 'nosniff',
    'X-Frame-Options' => 'DENY',
    'X-XSS-Protection' => '1; mode=block',
    'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
    'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'",
    'Referrer-Policy' => 'strict-origin-when-cross-origin'
]
```

## 5. API Security

### 5.1. Laravel Sanctum Implementation
- Use Laravel Sanctum for API authentication
- Implement proper token scoping and permissions
- Set appropriate token expiration times
- Provide token revocation capabilities

### 5.2. Rate Limiting
- Implement rate limiting for all API endpoints
- Use different rate limits based on authentication status
- Implement progressive rate limiting for suspicious activities
- Log and monitor rate limit violations

### 5.3. API Input Validation
- Validate all API inputs using Form Request classes
- Implement API versioning for backward compatibility
- Use proper HTTP status codes for all responses
- Implement comprehensive API error handling

## 6. File Upload Security

### 6.1. File Validation
- Validate file types using MIME type checking
- Implement file size limits appropriate for use case
- Scan uploaded files for malware using antivirus integration
- Store uploaded files outside the web root directory

### 6.2. File Storage Security
- Use Laravel's filesystem abstraction for file operations
- Implement proper file access controls and permissions
- Generate unique, non-guessable filenames for uploads
- Implement file integrity checking using checksums

## 7. Logging and Monitoring

### 7.1. Security Event Logging
- Log all authentication attempts (successful and failed)
- Log authorization failures and permission violations
- Log sensitive data access and modifications
- Implement structured logging for security events

### 7.2. Monitoring and Alerting
- Set up real-time alerts for security incidents
- Monitor for unusual access patterns and behaviors
- Implement automated response to detected threats
- Regular security log analysis and reporting

## 8. Compliance and Auditing

### 8.1. Data Privacy Compliance
- Implement GDPR compliance measures where applicable
- Provide data export and deletion capabilities
- Maintain audit trails for data processing activities
- Implement privacy-by-design principles

### 8.2. Security Auditing
- Conduct regular security code reviews
- Perform penetration testing on critical applications
- Maintain security documentation and procedures
- Regular security training for development team

## 9. Incident Response

### 9.1. Security Incident Procedures
- Establish clear incident response procedures
- Maintain incident response team contact information
- Implement automated incident detection and notification
- Regular incident response drills and training

### 9.2. Recovery and Remediation
- Develop and test disaster recovery procedures
- Implement secure backup and restore processes
- Maintain business continuity plans
- Post-incident analysis and improvement processes

## 10. Security Testing

### 10.1. Automated Security Testing
- Include security tests in CI/CD pipeline
- Use static analysis tools for vulnerability detection
- Implement dependency scanning for known vulnerabilities
- Regular automated security assessments

### 10.2. Manual Security Testing
- Conduct regular penetration testing
- Perform manual code security reviews
- Test authentication and authorization mechanisms
- Validate input validation and output sanitization

## 11. See Also

### Related Guidelines
- **[Project Overview](010-project-overview.md)** - Understanding project security context
- **[Development Standards](020-development-standards.md)** - Secure coding practices and standards
- **[Testing Standards](030-testing-standards.md)** - Security testing requirements
- **[Performance Standards](050-performance-standards.md)** - Security performance considerations

### Security Decision Guide for Junior Developers

#### "I'm implementing authentication - what security measures should I use?"
1. **Authentication**: Follow section 2.1 authentication and authorization standards
2. **Password Security**: Apply section 2.1.3 password policies and hashing
3. **Session Management**: Use section 2.3 secure session handling
4. **Multi-Factor**: Implement section 2.1.2 MFA where appropriate

#### "I'm handling user data - what protection is required?"
- **Input Validation**: Follow section 3.2 comprehensive input validation
- **Data Encryption**: Apply section 3.1 encryption for sensitive data
- **Database Security**: Use section 3.3 database protection measures
- **Privacy Compliance**: Implement section 8.1 data privacy requirements

#### "I'm building an API - what security standards apply?"
- **Authentication**: Use section 5.1 Laravel Sanctum implementation
- **Rate Limiting**: Apply section 5.2 API rate limiting strategies
- **Input Validation**: Follow section 5.3 API input validation requirements
- **Security Headers**: Implement section 4.3 comprehensive security headers

#### "I need to handle file uploads - what security measures are needed?"
- **File Validation**: Follow section 6.1 comprehensive file validation
- **Storage Security**: Apply section 6.2 secure file storage practices
- **Malware Protection**: Implement antivirus scanning for uploads
- **Access Controls**: Use proper file permissions and access restrictions

## 12. Navigation

[←  Testing Standards](030-testing-standards.md) | [↑ Top](#security-standards) |  [Performance Standards →](050-performance-standards.md)