# Performance Standards

## 1. Core Performance Principle

**All performance optimizations should be clear, measurable, and suitable for junior developers to understand, implement, and maintain.**

This principle ensures that performance improvements are not only effective but also sustainable and well-documented for future maintenance.

## 2. Database Optimization

### 2.1. Query Optimization

#### 2.1.1. Eloquent Optimization
- Use eager loading to prevent N+1 query problems
- Select only required columns to reduce data transfer
- Use query scopes for reusable query logic
- Implement proper indexing strategies
- Avoid N+1 queries with `with()` and `load()` methods

```php
// Good: Eager loading
$users = User::with(['posts', 'comments'])->get();

// Bad: N+1 queries
$users = User::get();
foreach ($users as $user) {
    $user->posts; // Additional query for each user
    $user->comments; // Another query for each user
}
```

#### 2.1.2. Database Indexing
- Add indexes on frequently queried columns
- Use composite indexes for multi-column queries
- Monitor query performance with Laravel Telescope
- Use `EXPLAIN` to analyze query execution plans
- Remove unused indexes to improve write performance

```php
// Migration example for indexing
Schema::table('orders', function (Blueprint $table) {
    $table->index(['user_id', 'status']);
    $table->index('created_at');
    $table->unique('order_number');
});
```

#### 2.1.3. Query Caching
- Cache frequently accessed data
- Use Laravel's cache system for expensive queries
- Implement cache invalidation strategies
- Use cache tags for organized cache management
- Set appropriate cache expiration times

```php
// Cache expensive queries
$users = Cache::remember('active_users', 3600, function () {
    return User::where('active', true)
        ->with(['profile', 'roles'])
        ->orderBy('last_login_at', 'desc')
        ->get();
});

// Cache with tags
Cache::tags(['users', 'active'])->remember('active_users_count', 3600, function () {
    return User::where('active', true)->count();
});
```

### 2.2. Database Connection Management

#### 2.2.1. Connection Pooling
- Use connection pooling for high-traffic applications
- Configure appropriate connection limits
- Monitor connection usage and performance
- Implement connection timeout settings
- Use read/write database separation when applicable

#### 2.2.2. Database Configuration
- Optimize MySQL/PostgreSQL configuration for your workload
- Use appropriate storage engines (InnoDB recommended)
- Configure proper buffer sizes and cache settings
- Enable query cache for read-heavy applications
- Monitor database performance metrics

## 3. Caching Strategies

### 3.1. Multi-Level Caching

#### 3.1.1. Application Cache
- Use Redis or Memcached for distributed caching
- Implement cache warming strategies
- Use cache hierarchies (L1: memory, L2: Redis, L3: database)
- Implement cache invalidation patterns
- Monitor cache hit rates and performance

```php
// Multi-level caching example
class ProductService
{
    public function getProduct(int $id): Product
    {
        // L1: Application cache (memory)
        $cacheKey = "product:{$id}";

        return Cache::remember($cacheKey, 3600, function () use ($id) {
            // L2: Database query
            return Product::with(['category', 'reviews'])
                ->findOrFail($id);
        });
    }
}
```

#### 3.1.2. HTTP Caching
- Implement HTTP cache headers for API responses
- Use ETags for conditional requests
- Implement proper cache-control headers
- Use CDN for static asset caching
- Implement browser caching strategies

```php
// API response caching
public function showProduct(Product $product): JsonResponse
{
    return response()->json($product)
        ->header('Cache-Control', 'public, max-age=3600')
        ->header('ETag', md5($product->updated_at->timestamp));
}
```

### 3.2. Cache Invalidation Strategies

#### 3.2.1. Tag-Based Invalidation
- Use cache tags for organized invalidation
- Implement cascade invalidation for related data
- Use event-driven cache invalidation
- Implement cache versioning strategies
- Monitor cache invalidation effectiveness

```php
// Tag-based cache invalidation
class ProductObserver
{
    public function updated(Product $product): void
    {
        Cache::tags(['products', "product:{$product->id}"])->flush();
    }

    public function deleted(Product $product): void
    {
        Cache::tags(['products', "product:{$product->id}"])->flush();
    }
}
```

#### 3.2.2. Time-Based Invalidation
- Use appropriate expiration times
- Implement cache warming strategies
- Use sliding expiration for dynamic content
- Implement cache refresh patterns
- Monitor cache staleness

## 4. Frontend Performance

### 4.1. Asset Optimization

#### 4.1.1. CSS and JavaScript Optimization
- Minify and compress CSS and JavaScript files
- Use asset bundling and code splitting
- Implement lazy loading for non-critical resources
- Use modern JavaScript frameworks with tree shaking
- Optimize font loading strategies

```php
// Laravel Mix/Vite configuration example
// vite.config.js
export default defineConfig({
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', 'lodash'],
                    admin: ['@fortawesome/fontawesome-free'],
                }
            }
        }
    }
});
```

#### 4.1.2. Image Optimization
- Use modern image formats (WebP, AVIF)
- Implement responsive images with srcset
- Use image lazy loading
- Optimize image compression levels
- Implement CDN for image delivery

```html
<!-- Responsive image example -->
<img
    srcset="image-320w.webp 320w,
            image-768w.webp 768w,
            image-1024w.webp 1024w"
    sizes="(max-width: 768px) 100vw, 50vw"
    src="image-1024w.webp"
    alt="Description"
    loading="lazy"
>
```

### 4.2. Rendering Performance

#### 4.2.1. Server-Side Optimization
- Implement output caching for static pages
- Use Blade template caching
- Optimize database queries in views
- Implement view composers for shared data
- Use fragment caching for expensive view components

```php
// Fragment caching in Blade views
@cache('expensive_calculation', 3600)
    <div class="statistics">
        {{ $expensiveCalculation }}
    </div>
@endcache
```

#### 4.2.2. Client-Side Optimization
- Implement virtual scrolling for large lists
- Use debouncing for search inputs
- Implement pagination and infinite scroll
- Optimize DOM manipulation
- Use requestAnimationFrame for animations

## 5. Application Performance

### 5.1. Queue Management

#### 5.1.1. Background Processing
- Use Laravel queues for time-consuming tasks
- Implement proper queue priorities
- Use queue workers with appropriate configuration
- Monitor queue performance and backlog
- Implement queue retry strategies

```php
// Queue job example
class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = [60, 300, 900]; // 1min, 5min, 15min

    public function handle(PaymentGateway $gateway): void
    {
        // Process payment logic
    }

    public function failed(Throwable $exception): void
    {
        // Handle failed payment
        Log::error('Payment processing failed', [
            'payment_id' => $this->payment->id,
            'error' => $exception->getMessage()
        ]);
    }
}
```

#### 5.1.2. Queue Configuration
- Use appropriate queue drivers (Redis recommended)
- Configure queue worker processes
- Implement queue monitoring and alerting
- Use queue batching for related jobs
- Implement queue rate limiting

### 5.2. Memory Management

#### 5.2.1. Memory Optimization
- Monitor memory usage with Laravel Telescope
- Implement memory-efficient data structures
- Use generators for large datasets
- Implement proper garbage collection
- Optimize Laravel service container usage

```php
// Memory-efficient data processing
public function processLargeDataset(): void
{
    // Use generator to process large datasets
    foreach ($this->getRecordsGenerator() as $record) {
        $this->processRecord($record);

        // Force garbage collection if needed
        if (memory_get_usage() > 256 * 1024 * 1024) { // 256MB
            gc_collect_cycles();
        }
    }
}

private function getRecordsGenerator(): Generator
{
    foreach (DB::table('large_table')->cursor() as $record) {
        yield $record;
    }
}
```

#### 5.2.2. Resource Management
- Implement connection pooling for external services
- Use HTTP client pooling for API calls
- Implement proper resource cleanup
- Monitor resource utilization
- Use appropriate PHP memory limits

## 6. Monitoring and Metrics

### 6.1. Performance Monitoring

#### 6.1.1. Application Monitoring
- Use Laravel Telescope for development monitoring
- Implement APM (Application Performance Monitoring) in production
- Monitor response times and throughput
- Track database query performance
- Monitor cache hit rates and effectiveness

```php
// Custom performance monitoring middleware
class PerformanceMonitor
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $response = $next($request);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        Log::info('Request performance', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'duration' => ($endTime - $startTime) * 1000, // milliseconds
            'memory_used' => $endMemory - $startMemory,
            'status_code' => $response->getStatusCode()
        ]);

        return $response;
    }
}
```

#### 6.1.2. Database Monitoring
- Monitor slow queries and optimize them
- Track database connection usage
- Monitor query execution plans
- Track database index usage
- Monitor database lock contention

### 6.2. Performance Metrics

#### 6.2.1. Key Performance Indicators
- **Response Time**: Average response time < 200ms
- **Throughput**: Requests per second capacity
- **Error Rate**: < 0.1% error rate
- **Database Query Time**: < 50ms average
- **Cache Hit Rate**: > 90% for frequently accessed data

#### 6.2.2. Alerting Thresholds
- Response time > 500ms
- Error rate > 1%
- Database query time > 100ms
- Memory usage > 80%
- Queue backlog > 1000 jobs

## 7. Scalability Considerations

### 7.1. Horizontal Scaling

#### 7.1.1. Load Balancing
- Implement load balancing for web servers
- Use database read replicas for read-heavy applications
- Implement session storage in Redis for distributed sessions
- Use CDN for static asset delivery
- Implement auto-scaling based on load

#### 7.1.2. Microservices Considerations
- Consider microservices for large applications
- Implement service discovery and registration
- Use API gateways for service communication
- Implement distributed tracing
- Use circuit breakers for fault tolerance

### 7.2. Performance Testing

#### 7.2.1. Load Testing
- Use tools like JMeter or k6 for load testing
- Test peak load scenarios
- Perform stress testing to find breaking points
- Test scalability with increasing load
- Monitor performance during tests

```php
// Performance test example
test('api_handles_concurrent_requests', function () {
    $response = stressless()
        ->get('/api/products')
        ->concurrent(50)
        ->for(30)->seconds()
        ->assertSuccessful();

    expect($response->response->getAverageResponseTime())
        ->toBeLessThan(500); // milliseconds
});
```

#### 7.2.2. Benchmarking
- Establish performance baselines
- Compare performance before and after optimizations
- Benchmark database query performance
- Test caching effectiveness
- Monitor performance regression

## 8. Performance Optimization Checklist

### 8.1. Database Optimization
- [ ] Implement proper indexing strategy
- [ ] Use eager loading to prevent N+1 queries
- [ ] Cache frequently accessed data
- [ ] Monitor and optimize slow queries
- [ ] Use read replicas for read-heavy operations

### 8.2. Caching Implementation
- [ ] Implement multi-level caching strategy
- [ ] Use appropriate cache expiration times
- [ ] Implement cache invalidation strategies
- [ ] Monitor cache hit rates
- [ ] Use CDN for static assets

### 8.3. Frontend Optimization
- [ ] Minify and compress assets
- [ ] Implement lazy loading
- [ ] Optimize images and use modern formats
- [ ] Use responsive images
- [ ] Implement browser caching

### 8.4. Application Performance
- [ ] Use queues for background processing
- [ ] Implement proper memory management
- [ ] Monitor resource utilization
- [ ] Implement performance monitoring
- [ ] Set up alerting for performance issues

## 9. See Also

### Related Guidelines
- **[Project Overview](010-project-overview.md)** - Understanding performance requirements
- **[Development Standards](020-development-standards.md)** - Performance-conscious coding practices
- **[Testing Standards](030-testing-standards.md)** - Performance testing requirements
- **[Security Standards](040-security-standards.md)** - Security-performance considerations

### Performance Decision Guide for Junior Developers

#### "I need to optimize a slow database query - where do I start?"
1. **Analysis**: Use Laravel Telescope or EXPLAIN to analyze the query
2. **Indexing**: Check if proper indexes exist on queried columns
3. **Eager Loading**: Prevent N+1 queries with proper eager loading
4. **Caching**: Cache frequently accessed query results
5. **Monitoring**: Monitor query performance after optimization

#### "My application is slow - what should I check first?"
- **Database Queries**: Check for slow queries and N+1 problems
- **Caching**: Verify caching is implemented and effective
- **Asset Loading**: Check if assets are optimized and cached
- **Memory Usage**: Monitor memory usage and optimize if needed
- **Queue Processing**: Check if background jobs are processing efficiently

#### "How do I implement effective caching?"
- **Identify**: Find frequently accessed, rarely changed data
- **Strategy**: Choose appropriate caching strategy (time-based, tag-based)
- **Implementation**: Use Laravel's cache system with proper expiration
- **Invalidation**: Implement proper cache invalidation strategies
- **Monitoring**: Monitor cache hit rates and effectiveness

## 10. Navigation

[←  Security Standards](040-security-standards.md) | [↑ Top](#performance-standards) |  [Static Analysis Standards →](060-static-analysis-standards.md)