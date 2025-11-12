<?php
/**
 * Application - Main Application Class
 *
 * This is the main application class for the apply-doc-standards tool.
 * It handles command-line argument parsing, configuration management,
 * and orchestrates the documentation standards application process.
 *
 * @package SAC\ApplyDocStandards\Core
 * @author StandAloneComplex <71233932+s-a-c@users.noreply.github.com>
 * @license MIT
 * @version 1.0.0
 */

declare(strict_types=1);

namespace SAC\ApplyDocStandards\Core;

use SAC\ApplyDocStandards\Utils\FileScanner;
use SAC\ApplyDocStandards\Utils\DocumentationProcessor;
use SAC\ApplyDocStandards\Utils\ConfigManager;

/**
 * Main Application Class
 *
 * Handles the overall application flow, CLI argument parsing,
 * and coordination of various components.
 */
class Application
{
    /**
     * Application version
     */
    private const VERSION = '1.0.0';

    /**
     * Configuration manager instance
     */
    private ConfigManager $config;

    /**
     * File scanner instance
     */
    private FileScanner $scanner;

    /**
     * Documentation processor instance
     */
    private DocumentationProcessor $processor;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = new ConfigManager();
        $this->scanner = new FileScanner($this->config);
        $this->processor = new DocumentationProcessor($this->config);
    }

    /**
     * Run the application
     *
     * @param array $argv Command line arguments
     * @return int Exit code
     */
    public function run(array $argv): int
    {
        try {
            $options = $this->parseArguments($argv);

            if (isset($options['help'])) {
                $this->showHelp();
                return 0;
            }

            if (isset($options['usage'])) {
                $this->showUsage();
                return 0;
            }

            if (isset($options['version'])) {
                $this->showVersion();
                return 0;
            }

            // Validate required arguments
            if (empty($options['paths'])) {
                fwrite(STDERR, "Error: No paths specified. Use --help for usage information.\n");
                return 1;
            }

            // Load configuration
            if (isset($options['config'])) {
                $this->config->loadFromFile($options['config']);
            }

            // Apply configuration overrides
            $this->applyConfigOverrides($options);

            // Process paths
            $results = $this->processPaths($options['paths'], $options);

            // Show results
            $this->showResults($results, $options);

            return $results['errors'] > 0 ? 1 : 0;

        } catch (\Throwable $e) {
            fwrite(STDERR, "Error: " . $e->getMessage() . "\n");

            if (isset($options['debug']) || getenv('DEBUG') === 'true') {
                fwrite(STDERR, "Stack trace:\n" . $e->getTraceAsString() . "\n");
            }

            return 2;
        }
    }

    /**
     * Parse command line arguments
     *
     * @param array $argv Command line arguments
     * @return array Parsed options
     */
    private function parseArguments(array $argv): array
    {
        $options = [
            'paths' => [],
            'recursive' => true,
            'dry-run' => false,
            'verbose' => false,
            'debug' => false,
            'force' => false,
        ];

        $skipNext = false;
        for ($i = 1; $i < count($argv); $i++) {
            if ($skipNext) {
                $skipNext = false;
                continue;
            }

            $arg = $argv[$i];

            switch ($arg) {
                case '--help':
                case '-h':
                    $options['help'] = true;
                    break;

                case '--usage':
                case '-u':
                    $options['usage'] = true;
                    break;

                case '--version':
                case '-v':
                    $options['version'] = true;
                    break;

                case '--recursive':
                case '-r':
                    $options['recursive'] = true;
                    break;

                case '--no-recursive':
                    $options['recursive'] = false;
                    break;

                case '--dry-run':
                case '-n':
                    $options['dry-run'] = true;
                    break;

                case '--verbose':
                    $options['verbose'] = true;
                    break;

                case '--debug':
                    $options['debug'] = true;
                    break;

                case '--force':
                case '-f':
                    $options['force'] = true;
                    break;

                case '--config':
                case '-c':
                    if (isset($argv[$i + 1])) {
                        $options['config'] = $argv[$i + 1];
                        $skipNext = true;
                    } else {
                        throw new \InvalidArgumentException("Missing configuration file path after --config");
                    }
                    break;

                case '--extensions':
                    if (isset($argv[$i + 1])) {
                        $options['extensions'] = explode(',', $argv[$i + 1]);
                        $skipNext = true;
                    } else {
                        throw new \InvalidArgumentException("Missing extensions list after --extensions");
                    }
                    break;

                default:
                    if (!str_starts_with($arg, '-')) {
                        $options['paths'][] = $arg;
                    } else {
                        fwrite(STDERR, "Warning: Unknown option '{$arg}' ignored\n");
                    }
                    break;
            }
        }

        return $options;
    }

    /**
     * Apply configuration overrides from command line
     *
     * @param array $options Parsed options
     */
    private function applyConfigOverrides(array $options): void
    {
        if (isset($options['extensions'])) {
            $this->config->set('file.extensions', $options['extensions']);
        }

        if (isset($options['dry-run'])) {
            $this->config->set('processing.dry_run', $options['dry-run']);
        }

        if (isset($options['verbose'])) {
            $this->config->set('output.verbose', $options['verbose']);
        }

        if (isset($options['force'])) {
            $this->config->set('processing.force', $options['force']);
        }
    }

    /**
     * Process the specified paths
     *
     * @param array $paths Paths to process
     * @param array $options Processing options
     * @return array Processing results
     */
    private function processPaths(array $paths, array $options): array
    {
        $results = [
            'files' => 0,
            'processed' => 0,
            'skipped' => 0,
            'errors' => 0,
            'details' => []
        ];

        foreach ($paths as $path) {
            if (!file_exists($path)) {
                fwrite(STDERR, "Error: Path '{$path}' does not exist\n");
                $results['errors']++;
                continue;
            }

            if (is_dir($path)) {
                $dirResults = $this->processDirectory($path, $options);
                $this->mergeResults($results, $dirResults);
            } else {
                $fileResults = $this->processFile($path, $options);
                $this->mergeResults($results, $fileResults);
            }
        }

        return $results;
    }

    /**
     * Process a directory
     *
     * @param string $directory Directory path
     * @param array $options Processing options
     * @return array Processing results
     */
    private function processDirectory(string $directory, array $options): array
    {
        if ($options['verbose']) {
            echo "Scanning directory: {$directory}\n";
        }

        $files = $this->scanner->scanDirectory($directory, $options['recursive']);
        $results = [
            'files' => count($files),
            'processed' => 0,
            'skipped' => 0,
            'errors' => 0,
            'details' => []
        ];

        foreach ($files as $file) {
            $fileResults = $this->processFile($file, $options);
            $this->mergeResults($results, $fileResults);
        }

        return $results;
    }

    /**
     * Process a single file
     *
     * @param string $file File path
     * @param array $options Processing options
     * @return array Processing results
     */
    private function processFile(string $file, array $options): array
    {
        $results = [
            'files' => 1,
            'processed' => 0,
            'skipped' => 0,
            'errors' => 0,
            'details' => []
        ];

        try {
            if ($options['verbose']) {
                echo "Processing file: {$file}\n";
            }

            $result = $this->processor->processFile($file, $options);

            if ($result['processed']) {
                $results['processed']++;
                if ($options['verbose'] && $result['changes']) {
                    echo "  Applied standards to: {$file}\n";
                    foreach ($result['changes'] as $change) {
                        echo "    - {$change}\n";
                    }
                }
            } else {
                $results['skipped']++;
                if ($options['verbose']) {
                    echo "  Skipped: {$file} ({$result['reason']})\n";
                }
            }

            $results['details'][$file] = $result;

        } catch (\Throwable $e) {
            $results['errors']++;
            fwrite(STDERR, "Error processing file '{$file}': " . $e->getMessage() . "\n");
            $results['details'][$file] = ['error' => $e->getMessage()];
        }

        return $results;
    }

    /**
     * Merge results from multiple processing operations
     *
     * @param array &$target Target results array (passed by reference)
     * @param array $source Source results array
     */
    private function mergeResults(array &$target, array $source): void
    {
        $target['files'] += $source['files'];
        $target['processed'] += $source['processed'];
        $target['skipped'] += $source['skipped'];
        $target['errors'] += $source['errors'];
        $target['details'] = array_merge($target['details'], $source['details']);
    }

    /**
     * Show processing results
     *
     * @param array $results Processing results
     * @param array $options Processing options
     */
    private function showResults(array $results, array $options): void
    {
        echo "\nDocumentation Standards Application Results:\n";
        echo str_repeat("=", 50) . "\n";
        echo "Files scanned:    {$results['files']}\n";
        echo "Files processed:  {$results['processed']}\n";
        echo "Files skipped:    {$results['skipped']}\n";
        echo "Errors:           {$results['errors']}\n";

        if ($results['errors'] > 0) {
            echo "\nErrors occurred during processing:\n";
            foreach ($results['details'] as $file => $detail) {
                if (isset($detail['error'])) {
                    echo "  {$file}: {$detail['error']}\n";
                }
            }
        }

        if ($options['verbose'] && $results['processed'] > 0) {
            echo "\nProcessed files:\n";
            foreach ($results['details'] as $file => $detail) {
                if (isset($detail['processed']) && $detail['processed']) {
                    echo "  {$file}\n";
                }
            }
        }

        if (isset($options['dry-run']) && $options['dry-run']) {
            echo "\nDRY RUN MODE - No files were modified\n";
        }

        echo "\n";
    }

    /**
     * Show help information
     */
    private function showHelp(): void
    {
        echo "apply-doc-standards - Apply Documentation Standards Tool v" . self::VERSION . "\n\n";
        echo "Usage: apply-doc-standards [OPTIONS] PATH...\n\n";
        echo "Arguments:\n";
        echo "  PATH                  One or more files or directories to process\n\n";
        echo "Options:\n";
        echo "  -h, --help            Show this help message\n";
        echo "  -u, --usage           Show brief usage information\n";
        echo "  -v, --version         Show version information\n";
        echo "  -r, --recursive       Process directories recursively (default)\n";
        echo "  --no-recursive        Process only specified directories, not subdirectories\n";
        echo "  -n, --dry-run         Show what would be changed without modifying files\n";
        echo "  -f, --force           Force processing even if files appear to already follow standards\n";
        echo "  --verbose             Show detailed processing information\n";
        echo "  --debug               Show debug information\n";
        echo "  -c, --config FILE     Load configuration from file\n";
        echo "  --extensions EXT      Comma-separated list of file extensions to process\n";
        echo "                        (default: md,markdown)\n\n";
        echo "Examples:\n";
        echo "  apply-doc-standards ./docs\n";
        echo "  apply-doc-standards --dry-run --verbose file1.md file2.md\n";
        echo "  apply-doc-standards --extensions md,txt --no-recursive ./docs\n";
        echo "  apply-doc-standards --config custom.json ./documentation\n\n";
        echo "Documentation Standards Applied:\n";
        echo "  - Anchored document titles using <a id=\"document-name\"></a>\n";
        echo "  - Introduction section (Section 1) with context and overview\n";
        echo "  - Unnumbered Table of Contents with collapsible structure\n";
        echo "  - Numbered content sections (2., 3., 4., etc.)\n";
        echo "  - Standard navigation footer (Previous | Next | Top)\n";
        echo "  - WCAG 2.1 AA accessibility compliance\n\n";
        echo "For more information, visit: https://github.com/s-a-c/apply-doc-standards\n";
    }

    /**
     * Show brief usage information
     */
    private function showUsage(): void
    {
        echo "Usage: apply-doc-standards [OPTIONS] PATH...\n\n";
        echo "Apply documentation standards to markdown files.\n\n";
        echo "Examples:\n";
        echo "  apply-doc-standards ./docs\n";
        echo "  apply-doc-standards --dry-run --verbose file.md\n";
        echo "  apply-doc-standards --force --recursive ./documentation\n\n";
        echo "Common options:\n";
        echo "  -h, --help            Show detailed help\n";
        echo "  -u, --usage           Show this brief usage\n";
        echo "  -n, --dry-run         Show changes without modifying files\n";
        echo "  -f, --force           Force processing even if standards already met\n";
        echo "  --verbose             Show detailed processing information\n\n";
        echo "Use --help for complete documentation and all options.\n";
    }

    /**
     * Show version information
     */
    private function showVersion(): void
    {
        echo "apply-doc-standards version " . self::VERSION . "\n";
        echo "Copyright (c) 2024 StandAloneComplex\n";
        echo "License: MIT\n";
        echo "PHP Version: " . PHP_VERSION . "\n";
    }
}
