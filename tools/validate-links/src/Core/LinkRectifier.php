<?php
/**
 * Link Rectifier - Automatic Link Fixing
 *
 * Provides intelligent link fixing capabilities including case correction,
 * file extension normalization, moved file detection, and anchor generation.
 *
 * @package SAC\ValidateLinks\Core
 */

declare(strict_types=1);

namespace SAC\ValidateLinks\Core;

use SAC\ValidateLinks\Utils\Logger;
use SAC\ValidateLinks\Utils\SecurityValidator;

/**
 * Automatic link fixing and remediation
 *
 * Intelligently fixes broken links by analyzing patterns, searching for
 * similar files, and applying various correction strategies.
 */
final class LinkRectifier
{
    private readonly Logger $logger;
    private readonly SecurityValidator $securityValidator;
    private readonly GitHubAnchorGenerator $anchorGenerator;
    private array $fileIndex = [];
    private array $headingsIndex = [];

    public function __construct(Logger $logger, SecurityValidator $securityValidator)
    {
        $this->logger = $logger;
        $this->securityValidator = $securityValidator;
        $this->anchorGenerator = new GitHubAnchorGenerator();
    }

    /**
     * Fix broken links in validation results
     *
     * @param array $validationResults Validation results from LinkValidator
     * @param array $config Configuration options
     * @return array Updated validation results with fixes applied
     */
    public function fixLinks(array $validationResults, array $config): array
    {
        $this->logger->info('🔧 Starting automatic link fixing...');

        // Build file and heading indexes
        $this->buildIndexes($validationResults);

        $fixesApplied = 0;
        $filesModified = [];

        foreach ($validationResults['results'] as $filePath => &$fileResult) {
            if (empty($fileResult['broken_links'])) {
                continue;
            }

            $this->logger->debug("Processing file: {$filePath}");

            // Read file content
            if (!$this->securityValidator->isFileReadable($filePath)) {
                $this->logger->warning("Cannot read file for fixing: {$filePath}");
                continue;
            }

            $content = file_get_contents($filePath);
            if ($content === false) {
                $this->logger->warning("Failed to read file content: {$filePath}");
                continue;
            }

            $originalContent = $content;
            $fileFixes = 0;

            // Process broken links in reverse order (to preserve line numbers)
            $brokenLinks = array_reverse($fileResult['broken_links'], true);
            foreach ($brokenLinks as $index => $linkInfo) {
                $fix = $this->fixLink($linkInfo, $filePath, $content);
                if ($fix !== null) {
                    $content = $fix['content'];
                    $fileResult['broken_links'][$index]['fix_applied'] = $fix['description'];
                    $fileFixes++;
                }
            }

            // Write back if changes were made
            if ($content !== $originalContent) {
                $this->writeFixedFile($filePath, $content, $config);
                $filesModified[] = $filePath;
                $fixesApplied += $fileFixes;
                $this->logger->info("Fixed {$fileFixes} links in {$filePath}");
            }
        }

        $this->logger->info("✅ Fixed {$fixesApplied} links in " . count($filesModified) . " files");

        return $validationResults;
    }

    /**
     * Fix a single broken link
     *
     * @param array $linkInfo Link information
     * @param string $filePath Current file path
     * @param string $content File content
     * @return array|null Fix information or null if no fix possible
     */
    private function fixLink(array $linkInfo, string $filePath, string $content): ?array
    {
        $url = $linkInfo['url'];
        $line = $linkInfo['line'];

        $this->logger->debug("Attempting to fix link: '{$url}' on line {$line}");

        // Try different fixing strategies
        $fix = match ($linkInfo['status']) {
            'Internal link target not found' => $this->fixInternalLink($url, $filePath, $content, $line),
            'Anchor not found in file' => $this->fixAnchorLink($url, $filePath, $content, $line),
            'Cross-reference target file not found' => $this->fixCrossReferenceLink($url, $filePath, $content, $line),
            default => null
        };

        return $fix;
    }

    /**
     * Fix internal link by finding similar files
     *
     * @param string $url Original URL
     * @param string $filePath Current file path
     * @param string $content File content
     * @param int $line Line number
     * @return array|null Fix information
     */
    private function fixInternalLink(string $url, string $filePath, string $content, int $line): ?array
    {
        // Extract just the filename (remove anchors)
        $targetFile = strtok($url, '#');
        $anchor = strtok('#') ?: '';

        // Strategy 1: Check for case variations
        $caseFix = $this->findCaseMatch($targetFile, dirname($filePath));
        if ($caseFix) {
            $newUrl = $caseFix . ($anchor ? "#{$anchor}" : '');
            return $this->applyFix($content, $url, $newUrl, $line, "Fixed case: '{$targetFile}' → '{$caseFix}'");
        }

        // Strategy 2: Check for extension variations (.md vs .html)
        $extFix = $this->findExtensionMatch($targetFile, dirname($filePath));
        if ($extFix) {
            $newUrl = $extFix . ($anchor ? "#{$anchor}" : '');
            return $this->applyFix($content, $url, $newUrl, $line, "Fixed extension: '{$targetFile}' → '{$extFix}'");
        }

        // Strategy 3: Find similar files using fuzzy matching
        $fuzzyFix = $this->findSimilarFile($targetFile, dirname($filePath));
        if ($fuzzyFix) {
            $newUrl = $fuzzyFix . ($anchor ? "#{$anchor}" : '');
            return $this->applyFix($content, $url, $newUrl, $line, "Fuzzy match: '{$targetFile}' → '{$fuzzyFix}'");
        }

        return null;
    }

    /**
     * Fix anchor link by finding similar headings
     *
     * @param string $url Original URL
     * @param string $filePath Current file path
     * @param string $content File content
     * @param int $line Line number
     * @return array|null Fix information
     */
    private function fixAnchorLink(string $url, string $filePath, string $content, int $line): ?array
    {
        if (!str_contains($url, '#')) {
            return null;
        }

        [$targetFile, $anchor] = explode('#', $url, 2);
        $targetFile = $targetFile ?: basename($filePath);

        // Get headings for the target file
        $headings = $this->headingsIndex[$targetFile] ?? [];

        if (empty($headings)) {
            return null;
        }

        // Strategy 1: Find exact heading match (case insensitive)
        foreach ($headings as $heading) {
            if (strcasecmp($heading['anchor'], $anchor) === 0) {
                $newUrl = ($targetFile !== basename($filePath) ? $targetFile : '') . "#{$heading['anchor']}";
                return $this->applyFix($content, $url, $newUrl, $line, "Fixed anchor case: '#{$anchor}' → '#{$heading['anchor']}'");
            }
        }

        // Strategy 2: Find similar heading using fuzzy matching
        $similarHeading = $this->findSimilarHeading($anchor, $headings);
        if ($similarHeading) {
            $newUrl = ($targetFile !== basename($filePath) ? $targetFile : '') . "#{$similarHeading['anchor']}";
            return $this->applyFix($content, $url, $newUrl, $line, "Fuzzy anchor match: '#{$anchor}' → '#{$similarHeading['anchor']}'");
        }

        return null;
    }

    /**
     * Fix cross-reference link
     *
     * @param string $url Original URL
     * @param string $filePath Current file path
     * @param string $content File content
     * @param int $line Line number
     * @return array|null Fix information
     */
    private function fixCrossReferenceLink(string $url, string $filePath, string $content, int $line): ?array
    {
        // For cross-references, try the same strategies as internal links
        return $this->fixInternalLink($url, $filePath, $content, $line);
    }

    /**
     * Apply a fix to the content
     *
     * @param string $content Original content
     * @param string $oldUrl Old URL
     * @param string $newUrl New URL
     * @param int $line Line number
     * @param string $description Fix description
     * @return array Fix information
     */
    private function applyFix(string $content, string $oldUrl, string $newUrl, int $line, string $description): array
    {
        // Replace the URL in the content
        $lines = explode("\n", $content);
        if (isset($lines[$line - 1])) {
            $lines[$line - 1] = str_replace($oldUrl, $newUrl, $lines[$line - 1]);
            $content = implode("\n", $lines);
        }

        return [
            'content' => $content,
            'description' => $description
        ];
    }

    /**
     * Find case-insensitive file match
     *
     * @param string $targetFile Target filename
     * @param string $directory Directory to search
     * @return string|null Correct case filename
     */
    private function findCaseMatch(string $targetFile, string $directory): ?string
    {
        $files = $this->fileIndex[$directory] ?? [];

        foreach ($files as $file) {
            if (strcasecmp(basename($file), $targetFile) === 0) {
                return basename($file);
            }
        }

        return null;
    }

    /**
     * Find file with different extension
     *
     * @param string $targetFile Target filename
     * @param string $directory Directory to search
     * @return string|null Filename with correct extension
     */
    private function findExtensionMatch(string $targetFile, string $directory): ?string
    {
        $baseName = pathinfo($targetFile, PATHINFO_FILENAME);
        $files = $this->fileIndex[$directory] ?? [];

        foreach ($files as $file) {
            $fileBaseName = pathinfo($file, PATHINFO_FILENAME);
            if (strcasecmp($fileBaseName, $baseName) === 0) {
                return basename($file);
            }
        }

        return null;
    }

    /**
     * Find similar file using fuzzy matching
     *
     * @param string $targetFile Target filename
     * @param string $directory Directory to search
     * @return string|null Most similar filename
     */
    private function findSimilarFile(string $targetFile, string $directory): ?string
    {
        $files = $this->fileIndex[$directory] ?? [];
        $bestMatch = null;
        $bestScore = 0;

        foreach ($files as $file) {
            $score = $this->calculateSimilarity($targetFile, basename($file));
            if ($score > $bestScore && $score > 0.6) { // 60% similarity threshold
                $bestMatch = basename($file);
                $bestScore = $score;
            }
        }

        return $bestMatch;
    }

    /**
     * Find similar heading using fuzzy matching
     *
     * @param string $anchor Target anchor
     * @param array $headings Available headings
     * @return array|null Most similar heading
     */
    private function findSimilarHeading(string $anchor, array $headings): ?array
    {
        $bestMatch = null;
        $bestScore = 0;

        foreach ($headings as $heading) {
            $score = $this->calculateSimilarity($anchor, $heading['anchor']);
            if ($score > $bestScore && $score > 0.6) { // 60% similarity threshold
                $bestMatch = $heading;
                $bestScore = $score;
            }
        }

        return $bestMatch;
    }

    /**
     * Calculate similarity between two strings
     *
     * @param string $str1 First string
     * @param string $str2 Second string
     * @return float Similarity score (0-1)
     */
    private function calculateSimilarity(string $str1, string $str2): float
    {
        // Convert to lowercase for comparison
        $str1 = strtolower($str1);
        $str2 = strtolower($str2);

        // Remove common separators and normalize
        $normalize = fn($s) => str_replace(['-', '_', ' ', '.'], '', $s);
        $str1 = $normalize($str1);
        $str2 = $normalize($str2);

        // Levenshtein distance
        $maxLen = max(strlen($str1), strlen($str2));
        if ($maxLen === 0) {
            return 1.0;
        }

        $distance = levenshtein($str1, $str2);
        return 1.0 - ($distance / $maxLen);
    }

    /**
     * Write fixed file with backup
     *
     * @param string $filePath File path
     * @param string $content Fixed content
     * @param array $config Configuration
     */
    private function writeFixedFile(string $filePath, string $content, array $config): void
    {
        // Create backup if requested
        if ($config['create_backup'] ?? true) {
            $backupPath = $filePath . '.backup.' . date('Y-m-d-H-i-s');
            if (!copy($filePath, $backupPath)) {
                $this->logger->warning("Failed to create backup: {$backupPath}");
            } else {
                $this->logger->debug("Created backup: {$backupPath}");
            }
        }

        // Write fixed content
        if (file_put_contents($filePath, $content) === false) {
            throw new \RuntimeException("Failed to write fixed file: {$filePath}");
        }
    }

    /**
     * Build file and heading indexes for efficient searching
     *
     * @param array $validationResults Validation results
     */
    private function buildIndexes(array $validationResults): void
    {
        $this->fileIndex = [];
        $this->headingsIndex = [];

        foreach ($validationResults['results'] as $filePath => $fileResult) {
            // Index files by directory
            $directory = dirname($filePath);
            if (!isset($this->fileIndex[$directory])) {
                $this->fileIndex[$directory] = [];
            }
            $this->fileIndex[$directory][] = basename($filePath);

            // Index headings
            if ($this->securityValidator->isFileReadable($filePath)) {
                $content = file_get_contents($filePath);
                if ($content !== false) {
                    $this->headingsIndex[basename($filePath)] = $this->extractHeadings($content);
                }
            }
        }
    }

    /**
     * Extract headings from content
     *
     * @param string $content File content
     * @return array Array of headings with their anchors
     */
    private function extractHeadings(string $content): array
    {
        $headings = [];
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            if (preg_match('/^(#{1,6})\s+(.+)$/', $line, $matches)) {
                $level = strlen($matches[1]);
                $text = trim($matches[2]);
                $anchor = $this->anchorGenerator->generate($text);

                $headings[] = [
                    'level' => $level,
                    'text' => $text,
                    'anchor' => $anchor
                ];
            }
        }

        return $headings;
    }
}
