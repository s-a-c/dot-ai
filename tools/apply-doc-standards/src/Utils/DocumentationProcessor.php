<?php
/**
 * DocumentationProcessor - Documentation Standards Application Class
 *
 * Core class responsible for analyzing, processing, and applying documentation
 * standards to markdown files. Handles structure validation, TOC generation,
 * anchor creation, navigation setup, and content organization.
 *
 * @package SAC\ApplyDocStandards\Utils
 * @author StandAloneComplex <71233932+s-a-c@users.noreply.github.com>
 * @license MIT
 * @version 1.0.0
 */

declare(strict_types=1);

namespace SAC\ApplyDocStandards\Utils;

/**
 * Documentation Processor Class
 *
 * Applies documentation standards to files including TOC generation,
 * anchor creation, navigation setup, and structure validation.
 */
class DocumentationProcessor
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
     * Process a single file and apply documentation standards
     *
     * @param string $filePath Path to the file to process
     * @param array $options Processing options
     * @return array Processing result
     */
    public function processFile(string $filePath, array $options): array
    {
        $result = [
            'processed' => false,
            'changes' => [],
            'reason' => '',
            'original_content' => '',
            'new_content' => '',
        ];

        try {
            // Read file content
            $content = file_get_contents($filePath);
            if ($content === false) {
                throw new \RuntimeException("Failed to read file: {$filePath}");
            }

            $result['original_content'] = $content;

            // Check if file should be processed
            if (!$this->shouldProcessFile($content, $options)) {
                $result['reason'] = 'Already follows standards or does not need processing';
                return $result;
            }

            // Process the content
            $newContent = $this->processContent($content, $filePath);
            $result['new_content'] = $newContent;

            // Determine what changed
            $changes = $this->detectChanges($content, $newContent);
            $result['changes'] = $changes;

            if (empty($changes)) {
                $result['reason'] = 'No changes needed';
                return $result;
            }

            // Write the file if not in dry-run mode
            if (!$this->config->isDryRun()) {
                $this->writeFile($filePath, $newContent);
            }

            $result['processed'] = true;

        } catch (\Throwable $e) {
            throw new \RuntimeException("Error processing file '{$filePath}': " . $e->getMessage());
        }

        return $result;
    }

    /**
     * Check if a file should be processed
     *
     * @param string $content File content
     * @param array $options Processing options
     * @return bool True if file should be processed
     */
    private function shouldProcessFile(string $content, array $options): bool
    {
        // Skip empty files
        if (empty(trim($content))) {
            return false;
        }

        // Skip if force is not enabled and file already follows standards
        if (!$this->config->shouldForce() && $this->alreadyFollowsStandards($content)) {
            return false;
        }

        // Check minimum content length
        $minLength = $this->config->get('content.min_content_length', 100);
        if (strlen($content) < $minLength) {
            return false;
        }

        return true;
    }

    /**
     * Check if content already follows documentation standards
     *
     * @param string $content Content to check
     * @return bool True if content follows standards
     */
    private function alreadyFollowsStandards(string $content): bool
    {
        $lines = explode("\n", $content);
        $sections = $this->extractSections($lines);

        $hasAnchoredTitle = false;
        $hasTocAfterTitle = false;
        $hasIntroduction = false;
        $hasNavigation = false;
        $hasTopLink = false;
        $titleLineIndex = -1;
        $tocLineIndex = -1;

        // Check for anchored title
        foreach ($lines as $index => $line) {
            if (preg_match('/^#\s+<a\s+id="[^"]+"><\/a>/', $line)) {
                $hasAnchoredTitle = true;
                $titleLineIndex = $index;
                break;
            }
        }

        // Check what sections exist
        $hasIntro = $this->hasSection($sections, 'introduction');
        $hasToc = $this->hasTocBetweenTitleAndIntro($lines, $titleLineIndex);
        $hasNavigation = $this->hasNavigationWithTopLink($lines);

        // Determine what's required
        $requiredElements = 0;
        if ($this->config->get('standards.require_introduction', true)) $requiredElements++;
        if ($this->config->get('standards.require_toc', true)) $requiredElements++;
        if ($this->config->get('standards.require_navigation', true)) $requiredElements++;

        $presentElements = 0;
        if ($hasAnchoredTitle) $presentElements++;
        if ($hasToc) $presentElements++;
        if ($hasIntro) $presentElements++;
        if ($hasNavigation) $presentElements++;

        return $presentElements >= $requiredElements;
    }

    /**
     * Process content and apply documentation standards
     *
     * @param string $content Original content
     * @param string $filePath File path
     * @return string Processed content
     */
    private function processContent(string $content, string $filePath): string
    {
        $lines = explode("\n", $content);

        // Remove existing TOC
        $linesWithoutToc = [];
        $inToc = false;
        foreach ($lines as $line) {
            if (preg_match('/^##\s+Table of Contents/i', $line)) {
                $inToc = true;
                continue;
            }

            if ($inToc) {
                if (preg_match('/^##\s+/', $line)) {
                    $inToc = false;
                    $linesWithoutToc[] = $line;
                } elseif (preg_match('/<\/details>/i', $line)) {
                    $inToc = false;
                }
                continue;
            }
            $linesWithoutToc[] = $line;
        }
        $lines = $linesWithoutToc;

        $sections = $this->extractSections($lines);

        // Extract or generate document title
        $title = $this->extractDocumentTitle($sections, $filePath);
        $documentId = $this->generateDocumentId($title, $filePath);

        // Check what's already present in the original content
        $hasIntro = $this->hasSection($sections, 'introduction');
        $hasNavigation = $this->hasNavigationWithTopLink($lines);

        // Build new content structure
        $processedLines[] = "# <a id=\"{$documentId}\"></a>{$title}";
        $processedLines[] = "";

        // Create or reposition the Table of Contents
        $processedLines = $this->moveOrGenerateToc($processedLines, $sections);

        // Add introduction section if required and not present
        if ($this->config->get('standards.require_introduction', true) && !$hasIntro) {
            $processedLines = array_merge($processedLines, $this->createIntroductionSection($sections));
        }

        // Add existing content sections (preserving structure)
        $contentLines = $this->processContentSections($sections);
        $processedLines = array_merge($processedLines, $contentLines);

        // Add navigation footer if required and not present with proper Top link
        if ($this->config->get('standards.require_navigation', true) && !$hasNavigation) {
            $cleanedLines = $this->removeExistingNavigation($processedLines);
            $cleanedLines = array_merge($cleanedLines, $this->createNavigationSection($documentId));
            $processedLines = $cleanedLines;
        }

        // Final cleaning of the entire output
        $finalLines = [];
        $consecutiveEmpty = 0;
        $lastLineWasHeading = false;

        foreach ($processedLines as $line) {
            $isEmpty = empty(trim($line));
            $isHeading = preg_match('/^#{1,6}\s+/', $line);

            // Handle heading spacing - ensure single blank line before and after
            if ($isHeading) {
                // Add blank line before heading if last line wasn't empty and wasn't a heading
                if (!empty($finalLines) && !empty(trim(end($finalLines))) && !$lastLineWasHeading) {
                    $finalLines[] = "";
                }
                $finalLines[] = $line;
                $finalLines[] = ""; // Always add blank line after heading
                $lastLineWasHeading = true;
                $consecutiveEmpty = 1;
                continue;
            }

            if ($isEmpty) {
                $consecutiveEmpty++;
                // Only allow one consecutive empty line (except after headings where we already added one)
                if ($consecutiveEmpty === 1 && !$lastLineWasHeading) {
                    $finalLines[] = $line;
                }
            } else {
                $finalLines[] = $line;
                $consecutiveEmpty = 0;
                $lastLineWasHeading = false;
            }
        }

        // Remove trailing empty lines
        while (!empty($finalLines) && empty(trim(end($finalLines)))) {
            array_pop($finalLines);
        }

        // Ensure document ends with single blank line
        if (!empty($finalLines) && !empty(trim(end($finalLines)))) {
            $finalLines[] = "";
        }

        return implode("\n", $finalLines);
    }

    /**
     * Create or reposition the Table of Contents.
     *
     * @param array $lines The current lines of the document being processed.
     * @param array $sections The extracted sections of the document.
     * @return array The modified lines with the TOC in the correct position.
     */
    private function moveOrGenerateToc(array $lines, array $sections): array
    {
        // Find the H1 heading
        $h1Index = -1;
        foreach ($lines as $index => $line) {
            if (preg_match('/^#\s+/', $line)) {
                $h1Index = $index;
                break;
            }
        }

        if ($h1Index === -1) {
            // No H1 found, so we can't place the TOC correctly.
            return $lines;
        }

        // Remove existing TOC
        $linesWithoutToc = [];
        $inToc = false;
        foreach ($lines as $line) {
            if (preg_match('/^##\s+Table of Contents/i', $line)) {
                $inToc = true;
                continue;
            }

            if ($inToc) {
                if (preg_match('/^##\s+/', $line)) {
                    $inToc = false;
                    $linesWithoutToc[] = $line;
                } elseif (preg_match('/<\/details>/i', $line)) {
                    $inToc = false;
                }
                continue;
            }
            $linesWithoutToc[] = $line;
        }
        $lines = $linesWithoutToc;


        // Generate new TOC
        $tocLines = [];
        if ($this->config->get('standards.require_toc', true)) {
            $tocSections = $this->getTocSections($sections);
            if (count($tocSections) >= $this->config->get('content.toc_min_sections', 2)) {
                $tocLines = $this->createTocSection($tocSections);
            }
        }

        // Insert TOC after H1
        $newLines = array_slice($lines, 0, $h1Index + 1);
        $newLines = array_merge($newLines, $tocLines);
        $newLines = array_merge($newLines, array_slice($lines, $h1Index + 1));

        return $newLines;
    }


    /**
     * Extract sections from content lines
     *
     * @param array $lines Content lines
     * @return array Extracted sections
     */
    private function extractSections(array $lines): array
    {
        $sections = [];
        $currentSection = null;
        $currentContent = [];
        $seenTitles = []; // Track to avoid duplicates
        $inCodeBlock = false;

        foreach ($lines as $line) {
            // Track code blocks
            if (preg_match('/^```/', $line)) {
                $inCodeBlock = !$inCodeBlock;
                // Preserve code blocks in section content
                if ($currentSection !== null) {
                    $currentContent[] = $line;
                }
                continue;
            }

            // Add content when inside code block (but don't process as section)
            if ($inCodeBlock && $currentSection !== null) {
                $currentContent[] = $line;
                continue;
            }

            if (preg_match('/^(#{1,6})\s+(.+)/', $line, $matches)) {
                $title = trim($matches[2]);
                $level = strlen($matches[1]);

                // Create normalized title for deduplication
                $normalizedTitle = strtolower(preg_replace('/^\d+\.\s*/', '', $title));
                $normalizedTitle = preg_replace('/[^a-z0-9\s]/', '', $normalizedTitle);
                $normalizedTitle = preg_replace('/\s+/', ' ', $normalizedTitle);
                $normalizedTitle = trim($normalizedTitle);

                // Skip sections that are clearly examples
                if (str_contains($normalizedTitle, 'example') ||
                    str_contains($normalizedTitle, 'format example') ||
                    str_contains($normalizedTitle, 'toc format example')) {
                    if ($currentSection !== null) {
                        $currentContent[] = $line;
                    }
                    $currentContent = [];
                    continue;
                }

                // Skip if we've already seen this title (except for H1 title)
                if ($level > 1 && isset($seenTitles[$normalizedTitle])) {
                    // Skip this section and its content
                    $currentContent = [];
                    continue;
                }

                $seenTitles[$normalizedTitle] = true;

                // Save previous section
                if ($currentSection !== null) {
                    $sections[] = [
                        'level' => $currentSection['level'],
                        'title' => $currentSection['title'],
                        'content' => $this->cleanContent($currentContent)
                    ];
                }

                // Start new section
                $currentSection = [
                    'level' => $level,
                    'title' => $title
                ];
                $currentContent = [];
            } else {
                // Only add content if we're not skipping a duplicate section
                if ($currentSection !== null) {
                    $currentContent[] = $line;
                }
            }
        }

        // Save last section
        if ($currentSection !== null) {
            $sections[] = [
                'level' => $currentSection['level'],
                'title' => $currentSection['title'],
                'content' => $this->cleanContent($currentContent)
            ];
        }

        return $sections;
    }

    /**
     * Clean content by removing excessive blank lines
     *
     * @param array $content Content lines
     * @return array Cleaned content lines
     */
    private function cleanContent(array $content): array
    {
        $cleaned = [];
        $consecutiveEmpty = 0;

        foreach ($content as $line) {
            $isEmpty = empty(trim($line));

            if ($isEmpty) {
                $consecutiveEmpty++;
                // Only allow one consecutive empty line
                if ($consecutiveEmpty === 1) {
                    $cleaned[] = $line;
                }
            } else {
                $cleaned[] = $line;
                $consecutiveEmpty = 0;
            }
        }

        // Remove trailing empty lines
        while (!empty($cleaned) && empty(trim(end($cleaned)))) {
            array_pop($cleaned);
        }

        return $cleaned;
    }

    /**
     * Check if a section has similar content to existing sections
     *
     * @param string $title Section title
     * @param array $content Section content
     * @param array $existingSections Existing sections
     * @return bool True if similar content found
     */
    private function hasSimilarContent(string $title, array $content, array $existingSections): bool
    {
        $currentContent = implode(' ', array_map('trim', $content));
        $currentContent = strtolower(preg_replace('/\s+/', ' ', $currentContent));

        if (empty(trim($currentContent))) {
            return false;
        }

        // Check for obvious example content patterns
        if (str_contains($currentContent, 'introduction content here') ||
            str_contains($currentContent, 'content referenced in toc') ||
            str_contains($currentContent, 'more content') ||
            str_contains($currentContent, 'document-name') ||
            str_contains($currentContent, 'document name') ||
            str_contains($currentContent, 'previous doc') ||
            str_contains($currentContent, 'next doc')) {
            return true; // This is example content
        }

        // Simple similarity check for exact duplicates (since we skip code-fences)
        foreach ($existingSections as $section) {
            $existingContent = implode(' ', array_map('trim', $section['content']));
            $existingContent = strtolower(preg_replace('/\s+/', ' ', $existingContent));

            if (empty(trim($existingContent))) {
                continue;
            }

            // Calculate similarity ratio
            similar_text($currentContent, $existingContent, $percent);

            // If content is more than 90% similar, consider it a duplicate
            if ($percent > 90) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract document title from sections
     *
     * @param array $sections Document sections
     * @param string $filePath File path
     * @return string Document title
     */
    private function extractDocumentTitle(array $sections, string $filePath): string
    {
        // Look for first level 1 heading
        foreach ($sections as $section) {
            if ($section['level'] === 1) {
                // Remove existing anchor if present
                $title = preg_replace('/<a\s+id="[^"]+"><\/a>\s*/', '', $section['title']);
                return trim($title);
            }
        }

        // Fallback to filename
        return pathinfo($filePath, PATHINFO_FILENAME);
    }

    /**
     * Generate document ID from title and file path
     *
     * @param string $title Document title
     * @param string $filePath File path
     * @return string Document ID
     */
    private function generateDocumentId(string $title, string $filePath): string
    {
        $id = strtolower($title);
        $id = preg_replace('/[^a-z0-9\s-]/', '', $id);
        $id = preg_replace('/\s+/', '-', $id);
        $id = trim($id, '-');

        // Fallback to filename if title becomes empty
        if (empty($id)) {
            $id = pathinfo($filePath, PATHINFO_FILENAME);
            $id = strtolower($id);
            $id = preg_replace('/[^a-z0-9\s-]/', '', $id);
            $id = preg_replace('/\s+/', '-', $id);
            $id = trim($id, '-');
        }

        return $id ?: 'document';
    }

    /**
     * Create introduction section
     *
     * @param array $sections Original sections
     * @return array Introduction section lines
     */
    private function createIntroductionSection(array $sections): array
    {
        $lines = [];
        $lines[] = "## 1. Introduction";
        $lines[] = "";

        // Look for existing introduction content
        $introContent = "";
        foreach ($sections as $section) {
            if (preg_match('/^(1\.|introduction)/i', $section['title'])) {
                $introContent = implode("\n", array_filter($section['content'], fn($line) => !empty(trim($line))));
                break;
            }
        }

        if (empty($introContent)) {
            $lines[] = "These documentation standards provide comprehensive guidelines for creating clear, accessible, and maintainable documentation. All documentation MUST be clear, actionable, and suitable for a junior developer to understand and implement. This means using simple language, providing concrete examples, defining technical terms, and explaining the \"why\" behind decisions.";
            $lines[] = "";
        } else {
            $lines[] = $introContent;
            $lines[] = "";
        }

        return $lines;
    }

    /**
     * Get sections for table of contents
     *
     * @param array $sections Document sections
     * @return array TOC sections
     */
    private function getTocSections(array $sections): array
    {
        $tocSections = [];
        $sectionNumber = 2; // Start with section 2 (introduction is always section 1)

        // Always add introduction first
        $tocSections[] = [
            'level' => 2,
            'title' => '1. Introduction',
            'content' => [],
            'section_number' => 1
        ];

        foreach ($sections as $section) {
            // Skip title, TOC, and navigation sections
            if (preg_match('/^(table of contents|navigation)/i', $section['title']) || $section['level'] === 1) {
                continue;
            }

            // Handle introduction section
            if (preg_match('/^(1\.|introduction)/i', $section['title'])) {
                continue;
            }

            // Add section number if not already present
            if (!preg_match('/^\d+\./', $section['title'])) {
                $section['title'] = $sectionNumber . '. ' . $section['title'];
                $section['section_number'] = $sectionNumber;
                $sectionNumber++;
            }

            $tocSections[] = $section;
        }

        return $tocSections;
    }

    /**
     * Create table of contents section
     *
     * @param array $sections Sections for TOC
     * @return array TOC section lines
     */
    private function createTocSection(array $sections): array
    {
        $lines = [];
        $lines[] = "## Table of Contents";
        $lines[] = "";

        if ($this->config->get('standards.toc_collapsible', true)) {
            $lines[] = "<details>";
            $lines[] = "<summary>Expand Table of Contents</summary>";
            $lines[] = "";
        }

        foreach ($sections as $section) {
            $title = $section['title'];
            $anchor = $this->generateAnchor($title);
            $indent = str_repeat('  ', max(0, $section['level'] - 2));
            $lines[] = "{$indent}- [{$title}](#{$anchor})";
        }

        if ($this->config->get('standards.toc_collapsible', true)) {
            $lines[] = "";
            $lines[] = "</details>";
        }

        $lines[] = "";

        return $lines;
    }

    /**
     * Process content sections with numbering
     *
     * @param array $sections Document sections
     * @return array Processed section lines
     */
    private function processContentSections(array $sections): array
    {
        $lines = [];
        $sectionNumber = 2; // Start with section 2 (introduction is always section 1)

        $processedSections = [];
        foreach ($sections as $section) {
            // Skip title, TOC, and navigation sections
            if (preg_match('/^(table of contents|navigation)/i', $section['title']) || $section['level'] === 1) {
                continue;
            }

            $title = $section['title'];
            $normalizedTitle = strtolower(preg_replace('/^\d+\.\s*/', '', $title));
            $normalizedTitle = preg_replace('/[^a-z0-9\s]/', '', $normalizedTitle);
            $normalizedTitle = preg_replace('/\s+/', ' ', $normalizedTitle);
            $normalizedTitle = trim($normalizedTitle);

            // Skip if already processed
            if (isset($processedSections[$normalizedTitle])) {
                continue;
            }

            // Handle introduction section
            if (preg_match('/^(introduction)/i', $title) && !preg_match('/^\d+\./', $title)) {
                $title = '1. ' . $title;
            }

            $processedSections[$normalizedTitle] = true;

            // Add section number if not already present
            if (!preg_match('/^\d+\./', $title) && $this->config->get('standards.section_numbering', true)) {
                $title = $sectionNumber . '. ' . $title;
                $sectionNumber++;
            }

            $lines[] = "## {$title}";
            $lines[] = "";

            // Add section content (cleaned)
            $cleanedContent = $this->cleanContent($section['content']);
            foreach ($cleanedContent as $contentLine) {
                $lines[] = $contentLine;
            }

            $lines[] = "";
        }

        return $lines;
    }

    /**
     * Add navigation footer if required and not present with proper Top link
     *
     * @param string $documentId Document ID for the Top link
     * @return array Navigation section lines
     */
    private function createNavigationSection(string $documentId): array
    {
        $lines = [];
        $lines[] = "## Navigation";
        $lines[] = "";
        $lines[] = "**Previous:** [Previous Doc](path) | **Next:** [Next Doc](path) | **Top**(#{$documentId})";
        $lines[] = "";

        return $lines;
    }



    /**
     * Remove existing navigation sections from lines
     *
     * @param array $lines Array of lines
     * @return array Cleaned lines without navigation sections
     */
    private function removeExistingNavigation(array $lines): array
    {
        $cleanedLines = [];
        $inNavigation = false;

        foreach ($lines as $line) {
            if (preg_match('/^##\s+Navigation/i', $line)) {
                $inNavigation = true;
                continue;
            }

            if ($inNavigation) {
                if (preg_match('/^##\s+/', $line)) {
                    $inNavigation = false;
                } else {
                    continue;
                }
            }

            // Check if this line contains navigation content without the heading
            if (str_contains($line, 'Previous:') && str_contains($line, 'Next:') && str_contains($line, 'Top')) {
                continue; // Skip standalone navigation lines
            }

            $cleanedLines[] = $line;
        }

        return $cleanedLines;
    }



    /**
     * Check if a section exists in the sections array
     *
     * @param array $sections Sections array
     * @param string $sectionName Section name to check
     * @return bool True if section exists
     */
    private function hasSection(array $sections, string $sectionName): bool
    {
        foreach ($sections as $section) {
            $normalizedTitle = strtolower(preg_replace('/^\d+\.\s*/', '', $section['title']));
            $normalizedTitle = preg_replace('/[^a-z0-9\s]/', '', $normalizedTitle);
            $normalizedTitle = preg_replace('/\s+/', ' ', $normalizedTitle);
            $normalizedTitle = trim($normalizedTitle);

            $normalizedSearch = strtolower(preg_replace('/[^a-z0-9\s]/', '', $sectionName));
            $normalizedSearch = preg_replace('/\s+/', ' ', $normalizedSearch);
            $normalizedSearch = trim($normalizedSearch);

            if ($normalizedTitle === $normalizedSearch) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if TOC exists between title and introduction
     *
     * @param array $lines File lines
     * @param int $titleLineIndex Index of the title line
     * @return bool True if TOC exists in correct position
     */
    private function hasTocBetweenTitleAndIntro(array $lines, int $titleLineIndex): bool
    {
        // Check if there's a TOC heading at the expected position (title + blank line)
        $expectedTocIndex = $titleLineIndex + 2;

        if ($expectedTocIndex >= count($lines)) {
            return false;
        }

        $expectedTocLine = trim($lines[$expectedTocIndex]);

        return $expectedTocLine === '## Table of Contents';
    }

    /**
     * Check if navigation exists with proper Top link
     *
     * @param array $lines File lines
     * @return bool True if navigation with Top link exists
     */
    private function hasNavigationWithTopLink(array $lines): bool
    {
        foreach ($lines as $line) {
            if (str_contains($line, 'Previous:') && str_contains($line, 'Next:') && str_contains($line, 'Top')) {
                if (preg_match('/\*\*Top\*\*\(#[^)]+\)/', $line)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Generate anchor from title
     *
     * @param string $title Section title
     * @return string Anchor
     */
    private function generateAnchor(string $title): string
    {
        $anchor = strtolower($title);
        $anchor = preg_replace('/[^a-z0-9\s-]/', '', $anchor);
        $anchor = preg_replace('/\s+/', '-', $anchor);
        $anchor = trim($anchor, '-');

        return $anchor ?: 'section';
    }

    /**
     * Detect changes between original and new content
     *
     * @param string $original Original content
     * @param string $new New content
     * @return array Array of change descriptions
     */
    private function detectChanges(string $original, string $new): array
    {
        $changes = [];

        if ($original !== $new) {
            $changes[] = 'Applied documentation standards structure';

            // Check for specific changes
            if (!preg_match('/<a\s+id="[^"]+"><\/a>/', $original) && preg_match('/<a\s+id="[^"]+"><\/a>/', $new)) {
                $changes[] = 'Added anchored document title';
            }

            if (!preg_match('/##\s+1\.\s+/', $original) && preg_match('/##\s+1\.\s+/', $new)) {
                $changes[] = 'Added introduction section';
            }

            if (!preg_match('/##\s+Table\s+of\s+Contents/', $original) && preg_match('/##\s+Table\s+of\s+Contents/', $new)) {
                $changes[] = 'Added table of contents';
            }

            if (!preg_match('/##\s+Navigation/', $original) && preg_match('/##\s+Navigation/', $new)) {
                $changes[] = 'Added navigation footer';
            }
        }

        return $changes;
    }

    /**
     * Write content to file
     *
     * @param string $filePath File path
     * @param string $content Content to write
     * @throws \RuntimeException If file cannot be written
     */
    private function writeFile(string $filePath, string $content): void
    {
        // Create backup if enabled
        if ($this->config->get('processing.backup', true)) {
            $backupPath = $filePath . '.bak.' . date('Y-m-d-H-i-s');
            if (!copy($filePath, $backupPath)) {
                fwrite(STDERR, "Warning: Could not create backup file: {$backupPath}\n");
            }
        }

        // Write new content
        if (file_put_contents($filePath, $content) === false) {
            throw new \RuntimeException("Failed to write to file: {$filePath}");
        }
    }
}
