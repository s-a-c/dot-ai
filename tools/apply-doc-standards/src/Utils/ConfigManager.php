<?php
/**
 * ConfigManager - Configuration Management Class
 *
 * Handles loading, managing, and providing access to configuration
 * settings for the documentation standards application tool.
 *
 * @package SAC\ApplyDocStandards\Utils
 * @author StandAloneComplex <71233932+s-a-c@users.noreply.github.com>
 * @license MIT
 * @version 1.0.0
 */

declare(strict_types=1);

namespace SAC\ApplyDocStandards\Utils;

/**
 * Configuration Manager Class
 *
 * Manages application configuration with support for file-based
 * configuration, runtime overrides, and default values.
 */
class ConfigManager
{
    /**
     * Default configuration settings
     */
    private const DEFAULT_CONFIG = [
        'file' => [
            'extensions' => ['md', 'markdown'],
            'exclude_patterns' => [
                'vendor/',
                'node_modules/',
                '.git/',
                '__MACOSX/',
                '.DS_Store',
                'Thumbs.db'
            ],
            'max_file_size' => 10485760, // 10MB
        ],
        'processing' => [
            'dry_run' => false,
            'force' => false,
            'backup' => true,
            'encoding' => 'UTF-8',
            'line_endings' => "\n",
        ],
        'standards' => [
            'require_introduction' => true,
            'require_toc' => true,
            'require_navigation' => true,
            'toc_collapsible' => true,
            'anchor_prefix' => '',
            'section_numbering' => true,
            'navigation_format' => 'Previous: {previous} | Next: {next} | Top',
        ],
        'content' => [
            'min_content_length' => 100,
            'max_title_length' => 100,
            'toc_min_sections' => 2,
            'navigation_top_link' => '#top',
            'introduction_section' => '1',
        ],
        'output' => [
            'verbose' => false,
            'show_changes' => true,
            'show_skipped' => false,
            'color_output' => true,
        ],
        'validation' => [
            'check_links' => false,
            'check_structure' => true,
            'check_accessibility' => true,
        ],
    ];

    /**
     * Current configuration settings
     */
    private array $config = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = self::DEFAULT_CONFIG;
    }

    /**
     * Load configuration from a file
     *
     * @param string $configFile Path to configuration file
     * @throws \RuntimeException If file cannot be read or parsed
     */
    public function loadFromFile(string $configFile): void
    {
        if (!file_exists($configFile)) {
            throw new \RuntimeException("Configuration file not found: {$configFile}");
        }

        if (!is_readable($configFile)) {
            throw new \RuntimeException("Configuration file is not readable: {$configFile}");
        }

        $content = file_get_contents($configFile);
        if ($content === false) {
            throw new \RuntimeException("Failed to read configuration file: {$configFile}");
        }

        $config = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(
                "Invalid JSON in configuration file: " . json_last_error_msg()
            );
        }

        $this->mergeConfig($config);
    }

    /**
     * Get a configuration value
     *
     * @param string $key Configuration key (supports dot notation)
     * @param mixed $default Default value if key not found
     * @return mixed Configuration value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Set a configuration value
     *
     * @param string $key Configuration key (supports dot notation)
     * @param mixed $value Value to set
     */
    public function set(string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $k) {
            if (!is_array($config)) {
                $config = [];
            }
            if (!array_key_exists($k, $config)) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }

    /**
     * Check if a configuration key exists
     *
     * @param string $key Configuration key (supports dot notation)
     * @return bool True if key exists
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Get all configuration settings
     *
     * @return array Complete configuration array
     */
    public function getAll(): array
    {
        return $this->config;
    }

    /**
     * Reset configuration to defaults
     */
    public function reset(): void
    {
        $this->config = self::DEFAULT_CONFIG;
    }

    /**
     * Validate configuration settings
     *
     * @return array Array of validation errors (empty if valid)
     */
    public function validate(): array
    {
        $errors = [];

        // Validate file extensions
        $extensions = $this->get('file.extensions');
        if (!is_array($extensions) || empty($extensions)) {
            $errors[] = 'file.extensions must be a non-empty array';
        } else {
            foreach ($extensions as $ext) {
                if (!is_string($ext) || empty(trim($ext))) {
                    $errors[] = 'File extensions must be non-empty strings';
                    break;
                }
            }
        }

        // Validate file size
        $maxSize = $this->get('file.max_file_size');
        if (!is_int($maxSize) || $maxSize <= 0) {
            $errors[] = 'file.max_file_size must be a positive integer';
        }

        // Validate processing settings
        $dryRun = $this->get('processing.dry_run');
        if (!is_bool($dryRun)) {
            $errors[] = 'processing.dry_run must be a boolean';
        }

        $force = $this->get('processing.force');
        if (!is_bool($force)) {
            $errors[] = 'processing.force must be a boolean';
        }

        // Validate content settings
        $minLength = $this->get('content.min_content_length');
        if (!is_int($minLength) || $minLength < 0) {
            $errors[] = 'content.min_content_length must be a non-negative integer';
        }

        $maxLength = $this->get('content.max_title_length');
        if (!is_int($maxLength) || $maxLength <= 0) {
            $errors[] = 'content.max_title_length must be a positive integer';
        }

        // Validate output settings
        $verbose = $this->get('output.verbose');
        if (!is_bool($verbose)) {
            $errors[] = 'output.verbose must be a boolean';
        }

        return $errors;
    }

    /**
     * Export configuration to array
     *
     * @return array Configuration array
     */
    public function export(): array
    {
        return $this->config;
    }

    /**
     * Import configuration from array
     *
     * @param array $config Configuration array to import
     */
    public function import(array $config): void
    {
        $this->config = [];
        $this->mergeConfig($config);
    }

    /**
     * Merge configuration array with existing configuration
     *
     * @param array $config Configuration array to merge
     */
    private function mergeConfig(array $config): void
    {
        $this->config = array_merge_recursive($this->config, $config);
    }

    /**
     * Get configuration for a specific section
     *
     * @param string $section Section name
     * @return array Section configuration
     */
    public function getSection(string $section): array
    {
        return $this->get($section, []);
    }

    /**
     * Check if processing should be forced
     *
     * @return bool True if force processing is enabled
     */
    public function shouldForce(): bool
    {
        return $this->get('processing.force', false);
    }

    /**
     * Check if running in dry-run mode
     *
     * @return bool True if dry-run mode is enabled
     */
    public function isDryRun(): bool
    {
        return $this->get('processing.dry_run', false);
    }

    /**
     * Check if verbose output is enabled
     *
     * @return bool True if verbose output is enabled
     */
    public function isVerbose(): bool
    {
        return $this->get('output.verbose', false);
    }

    /**
     * Get supported file extensions
     *
     * @return array Array of file extensions
     */
    public function getSupportedExtensions(): array
    {
        return $this->get('file.extensions', ['md', 'markdown']);
    }

    /**
     * Get exclude patterns
     *
     * @return array Array of exclude patterns
     */
    public function getExcludePatterns(): array
    {
        return $this->get('file.exclude_patterns', []);
    }

    /**
     * Get maximum file size
     *
     * @return int Maximum file size in bytes
     */
    public function getMaxFileSize(): int
    {
        return $this->get('file.max_file_size', 10485760);
    }
}
