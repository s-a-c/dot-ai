<?php
/**
 * FileScanner - File Scanning and Filtering Class
 *
 * Handles scanning directories for files that match specific criteria
 * and applying filters based on extensions, patterns, and other rules.
 *
 * @package SAC\ApplyDocStandards\Utils
 * @author StandAloneComplex <71233932+s-a-c@users.noreply.github.com>
 * @license MIT
 * @version 1.0.0
 */

declare(strict_types=1);

namespace SAC\ApplyDocStandards\Utils;

/**
 * File Scanner Class
 *
 * Scans directories and filters files based on configuration settings.
 * Supports recursive scanning, extension filtering, and pattern exclusion.
 */
class FileScanner
{
    /**
     * Configuration manager instance
     */
    private ConfigManager $config;

    /**
     * Constructor
     *
     * @param ConfigManager $config Configuration manager
     */
    public function __construct(ConfigManager $config)
    {
        $this->config = $config;
    }

    /**
     * Scan a directory for files matching the criteria
     *
     * @param string $directory Directory path to scan
     * @param bool $recursive Whether to scan recursively
     * @return array Array of file paths
     * @throws \RuntimeException If directory cannot be read
     */
    public function scanDirectory(string $directory, bool $recursive = true): array
    {
        if (!is_dir($directory)) {
            throw new \RuntimeException("Directory does not exist: {$directory}");
        }

        if (!is_readable($directory)) {
            throw new \RuntimeException("Directory is not readable: {$directory}");
        }

        $files = [];
        $excludePatterns = $this->config->getExcludePatterns();
        $supportedExtensions = $this->config->getSupportedExtensions();
        $maxFileSize = $this->config->getMaxFileSize();

        try {
            $iterator = $this->createIterator($directory, $recursive);

            foreach ($iterator as $file) {
                if (!$file->isFile()) {
                    continue;
                }

                $filePath = $file->getPathname();

                // Apply exclusion patterns
                if ($this->isExcluded($filePath, $excludePatterns)) {
                    continue;
                }

                // Check file extension
                if (!$this->hasSupportedExtension($filePath, $supportedExtensions)) {
                    continue;
                }

                // Check file size
                if ($file->getSize() > $maxFileSize) {
                    if ($this->config->isVerbose()) {
                        fwrite(STDERR, "Warning: File too large, skipping: {$filePath}\n");
                    }
                    continue;
                }

                // Check if file is readable
                if (!$file->isReadable()) {
                    if ($this->config->isVerbose()) {
                        fwrite(STDERR, "Warning: File not readable, skipping: {$filePath}\n");
                    }
                    continue;
                }

                $files[] = $filePath;
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException("Error scanning directory '{$directory}': " . $e->getMessage());
        }

        sort($files);
        return $files;
    }

    /**
     * Scan multiple directories
     *
     * @param array $directories Array of directory paths
     * @param bool $recursive Whether to scan recursively
     * @return array Array of all file paths
     */
    public function scanDirectories(array $directories, bool $recursive = true): array
    {
        $allFiles = [];

        foreach ($directories as $directory) {
            try {
                $files = $this->scanDirectory($directory, $recursive);
                $allFiles = array_merge($allFiles, $files);
            } catch (\Throwable $e) {
                fwrite(STDERR, "Error scanning directory '{$directory}': " . $e->getMessage() . "\n");
            }
        }

        // Remove duplicates and sort
        $allFiles = array_unique($allFiles);
        sort($allFiles);

        return $allFiles;
    }

    /**
     * Filter an array of files based on criteria
     *
     * @param array $files Array of file paths
     * @return array Filtered array of file paths
     */
    public function filterFiles(array $files): array
    {
        $excludePatterns = $this->config->getExcludePatterns();
        $supportedExtensions = $this->config->getSupportedExtensions();
        $maxFileSize = $this->config->getMaxFileSize();

        $filteredFiles = [];

        foreach ($files as $file) {
            if (!file_exists($file) || !is_file($file)) {
                continue;
            }

            // Apply exclusion patterns
            if ($this->isExcluded($file, $excludePatterns)) {
                continue;
            }

            // Check file extension
            if (!$this->hasSupportedExtension($file, $supportedExtensions)) {
                continue;
            }

            // Check file size
            $fileSize = filesize($file);
            if ($fileSize === false || $fileSize > $maxFileSize) {
                if ($this->config->isVerbose() && $fileSize !== false) {
                    fwrite(STDERR, "Warning: File too large, skipping: {$file}\n");
                }
                continue;
            }

            // Check if file is readable
            if (!is_readable($file)) {
                if ($this->config->isVerbose()) {
                    fwrite(STDERR, "Warning: File not readable, skipping: {$file}\n");
                }
                continue;
            }

            $filteredFiles[] = $file;
        }

        sort($filteredFiles);
        return $filteredFiles;
    }

    /**
     * Create directory iterator
     *
     * @param string $directory Directory path
     * @param bool $recursive Whether to iterate recursively
     * @return \Iterator Directory iterator
     */
    private function createIterator(string $directory, bool $recursive): \Iterator
    {
        if ($recursive) {
            return new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $directory,
                    \RecursiveDirectoryIterator::SKIP_DOTS | \RecursiveDirectoryIterator::FOLLOW_SYMLINKS
                ),
                \RecursiveIteratorIterator::SELF_FIRST
            );
        } else {
            return new \DirectoryIterator($directory);
        }
    }

    /**
     * Check if a file path matches any exclusion pattern
     *
     * @param string $filePath File path to check
     * @param array $excludePatterns Array of exclusion patterns
     * @return bool True if file should be excluded
     */
    private function isExcluded(string $filePath, array $excludePatterns): bool
    {
        foreach ($excludePatterns as $pattern) {
            if ($this->matchesPattern($filePath, $pattern)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if a file has a supported extension
     *
     * @param string $filePath File path to check
     * @param array $supportedExtensions Array of supported extensions
     * @return bool True if file has supported extension
     */
    private function hasSupportedExtension(string $filePath, array $supportedExtensions): bool
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return in_array($extension, $supportedExtensions, true);
    }

    /**
     * Check if a file path matches a pattern
     *
     * @param string $filePath File path to check
     * @param string $pattern Pattern to match
     * @return bool True if pattern matches
     */
    private function matchesPattern(string $filePath, string $pattern): bool
    {
        // Convert backslashes to forward slashes for consistency
        $normalizedPath = str_replace('\\', '/', $filePath);
        $normalizedPattern = str_replace('\\', '/', $pattern);

        // Simple substring match for directories
        if (str_ends_with($normalizedPattern, '/')) {
            return str_contains($normalizedPath, $normalizedPattern);
        }

        // Filename match
        if (!str_contains($normalizedPattern, '/')) {
            $filename = basename($normalizedPath);
            return $filename === $normalizedPattern;
        }

        // Wildcard matching
        if (str_contains($normalizedPattern, '*') || str_contains($normalizedPattern, '?')) {
            $regex = '/^' . str_replace(
                ['/', '*', '?'],
                ['\\/', '.*', '.'],
                preg_quote($normalizedPattern, '/')
            ) . '$/i';
            return preg_match($regex, $normalizedPath) === 1;
        }

        // Exact path match
        return str_ends_with($normalizedPath, $normalizedPattern) ||
               str_contains($normalizedPath, '/' . $normalizedPattern);
    }

    /**
     * Get file information
     *
     * @param string $filePath File path
     * @return array File information
     */
    public function getFileInfo(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("File does not exist: {$filePath}");
        }

        $stat = stat($filePath);
        if ($stat === false) {
            throw new \RuntimeException("Cannot get file information: {$filePath}");
        }

        return [
            'path' => $filePath,
            'basename' => basename($filePath),
            'dirname' => dirname($filePath),
            'extension' => strtolower(pathinfo($filePath, PATHINFO_EXTENSION)),
            'size' => $stat['size'],
            'modified' => $stat['mtime'],
            'readable' => is_readable($filePath),
            'writable' => is_writable($filePath),
            'mime_type' => $this->getMimeType($filePath),
        ];
    }

    /**
     * Get MIME type of a file
     *
     * @param string $filePath File path
     * @return string MIME type
     */
    private function getMimeType(string $filePath): string
    {
        if (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filePath);
            finfo_close($finfo);
            return $mimeType ?: 'application/octet-stream';
        }

        // Fallback to extension-based detection
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return match ($extension) {
            'md', 'markdown' => 'text/markdown',
            'txt' => 'text/plain',
            'html', 'htm' => 'text/html',
            'json' => 'application/json',
            'xml' => 'text/xml',
            'css' => 'text/css',
            'js' => 'application/javascript',
            default => 'application/octet-stream',
        };
    }

    /**
     * Check if a file is likely a markdown file
     *
     * @param string $filePath File path
     * @return bool True if file appears to be markdown
     */
    public function isMarkdownFile(string $filePath): bool
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return in_array($extension, ['md', 'markdown'], true);
    }

    /**
     * Get statistics about scanned files
     *
     * @param array $files Array of file paths
     * @return array Statistics
     */
    public function getStatistics(array $files): array
    {
        $stats = [
            'total_files' => count($files),
            'total_size' => 0,
            'extensions' => [],
            'largest_file' => null,
            'smallest_file' => null,
            'oldest_file' => null,
            'newest_file' => null,
        ];

        $largestSize = 0;
        $smallestSize = PHP_INT_MAX;
        $oldestTime = PHP_INT_MAX;
        $newestTime = 0;

        foreach ($files as $file) {
            try {
                $info = $this->getFileInfo($file);

                $stats['total_size'] += $info['size'];

                // Extension statistics
                $ext = $info['extension'];
                if (!isset($stats['extensions'][$ext])) {
                    $stats['extensions'][$ext] = 0;
                }
                $stats['extensions'][$ext]++;

                // Largest file
                if ($info['size'] > $largestSize) {
                    $largestSize = $info['size'];
                    $stats['largest_file'] = $file;
                }

                // Smallest file
                if ($info['size'] < $smallestSize) {
                    $smallestSize = $info['size'];
                    $stats['smallest_file'] = $file;
                }

                // Oldest file
                if ($info['modified'] < $oldestTime) {
                    $oldestTime = $info['modified'];
                    $stats['oldest_file'] = $file;
                }

                // Newest file
                if ($info['modified'] > $newestTime) {
                    $newestTime = $info['modified'];
                    $stats['newest_file'] = $file;
                }

            } catch (\Throwable $e) {
                // Skip files that can't be processed
                continue;
            }
        }

        // Format size for human readability
        $stats['total_size_formatted'] = $this->formatBytes($stats['total_size']);

        return $stats;
    }

    /**
     * Format bytes for human readability
     *
     * @param int $bytes Number of bytes
     * @return string Formatted string
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
