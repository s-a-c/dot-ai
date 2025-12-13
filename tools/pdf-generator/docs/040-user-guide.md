# User Guide

<details>
<summary>Table of Contents</summary>

- [1. Quick Start](#1-quick-start)
  - [1.1. Basic Usage](#11-basic-usage)
  - [1.2. Common Options](#12-common-options)
- [2. Command-Line Interface](#2-command-line-interface)
  - [2.1. Synopsis](#21-synopsis)
  - [2.2. Arguments](#22-arguments)
  - [2.3. Options](#23-options)
- [3. Usage Patterns](#3-usage-patterns)
  - [3.1. Single Directory](#31-single-directory)
  - [3.2. Multiple Directories](#32-multiple-directories)
  - [3.3. Dry-Run and Finalize](#33-dry-run-and-finalize)
  - [3.4. Different Output Types](#34-different-output-types)
- [4. Run Management](#4-run-management)
  - [4.1. Understanding Run IDs](#41-understanding-run-ids)
  - [4.2. Resuming Runs](#42-resuming-runs)
  - [4.3. Cleaning Up](#43-cleaning-up)
- [5. Advanced Features](#5-advanced-features)
  - [5.1. Caching and Fingerprinting](#51-caching-and-fingerprinting)
  - [5.2. Parallel Processing](#52-parallel-processing)
  - [5.3. Statistics and Reporting](#53-statistics-and-reporting)
- [6. Examples](#6-examples)
  - [6.1. Basic Examples](#61-basic-examples)
  - [6.2. Advanced Examples](#62-advanced-examples)
- [7. Troubleshooting](#7-troubleshooting)
  - [7.1. Common Errors](#71-common-errors)
  - [7.2. Performance Issues](#72-performance-issues)
- [Navigation](#navigation)

</details>

---

**Compliant with**: AI-GUIDELINES.md v1.0

## 1. Quick Start

### 1.1. Basic Usage

**Generate PDFs from markdown files**:
```bash
# Generate PDFs for all markdown files in current directory
pdf-generator .

# Generate PDFs for specific directory
pdf-generator /path/to/markdown/files

# Generate with verbose output
pdf-generator --verbose /path/to/markdown/files
```

**Output location**:
```bash
# Output goes to ./pndcgn/pdf-{RUN_ID}/
ls pndcgn/pdf-*/
# file1.pdf  file2.pdf  file3.pdf
```

### 1.2. Common Options

```bash
# Dry-run (no actual PDFs created)
pdf-generator --dry-run source_dir

# Different output format
pdf-generator --type epub source_dir

# Custom output directory
pdf-generator source_dir /custom/output/path

# Force regeneration (ignore cache)
pdf-generator --force source_dir

# Resume interrupted run
pdf-generator --resume {RUN_ID}
```

---

## 2. Command-Line Interface

### 2.1. Synopsis

```bash
pdf-generator [OPTIONS] [SOURCE_DIR] [TARGET_DIR]
```

**Description**:
Batch convert markdown files to PDFs (or other formats) using pandoc, with intelligent caching via SQLite and support for resumable runs.

### 2.2. Arguments

**SOURCE_DIR** (optional, default: `.`):
- Directory containing source markdown files
- Scanned recursively for `.md` files
- Can be absolute or relative path

```bash
# Current directory
pdf-generator

# Specific directory
pdf-generator ~/Documents/notes

# Absolute path
pdf-generator /var/data/markdown
```

**TARGET_DIR** (optional, default: `$PWD`):
- Parent directory for output
- Output created at `${TARGET_DIR}/pndcgn/${TYPE}-${RUN_ID}/`
- Must be writable

```bash
# Output to current directory (default)
pdf-generator source_dir

# Output to specific location
pdf-generator source_dir /tmp/output
```

### 2.3. Options

**Initialization and Setup**:

`--init`
- Initialize configuration and database schema
- Creates `pdf-generator.toml` if missing
- Sets up SQLite tables

```bash
pdf-generator --init
```

**Output Control**:

`--type <FORMAT>` (default: `pdf`)
- Output format: `pdf`, `epub`, `html`, `docx`, etc.
- Must be supported by pandoc
- Changes output directory name

```bash
pdf-generator --type epub source_dir
pdf-generator --type html source_dir
```

**Run Management**:

`--dry-run`
- Simulate processing without creating files
- Shows what would be processed
- Generates RUN_ID for later finalization

```bash
# Preview processing
pdf-generator --dry-run source_dir
# Output: Run ID: 01HN7XJKQM3R8Y2VWSDP4T6FGZ
```

`--finalize <RUN_ID>`
- Complete a previous dry-run
- Uses cached fingerprints
- Only processes files that haven't changed

```bash
# After dry-run, finalize the run
pdf-generator --finalize 01HN7XJKQM3R8Y2VWSDP4T6FGZ
```

`--resume <RUN_ID>`
- Resume interrupted run
- Skips already-processed files
- Uses existing output directory

```bash
# Resume after interruption
pdf-generator --resume 01HN7XJKQM3R8Y2VWSDP4T6FGZ
```

**Cache Control**:

`--force`
- Ignore cache, regenerate all files
- Updates all fingerprints
- Useful after pandoc updates

```bash
pdf-generator --force source_dir
```

`--clean`
- Remove cache entries for completed runs
- Keeps database schema
- Frees disk space

```bash
pdf-generator --clean
```

`--drop`
- Drop entire database and recreate schema
- Nuclear option for corruption issues
- Requires confirmation

```bash
pdf-generator --drop
```

**Output Control**:

`--verbose`
- Show detailed progress information
- Display file-by-file processing
- Include timing information

```bash
pdf-generator --verbose source_dir
```

`--help`
- Display usage information
- Show all available options
- Include examples

```bash
pdf-generator --help
```

`--version`
- Display version information

```bash
pdf-generator --version
# pndcgn v6
```

---

## 3. Usage Patterns

### 3.1. Single Directory

**Process all markdown in directory**:
```bash
# Basic usage
pdf-generator ~/Documents/notes

# Output structure
ls ~/Documents/notes/pndcgn/pdf-*/
# note1.pdf  note2.pdf  note3.pdf
```

**With options**:
```bash
# Verbose output
pdf-generator --verbose ~/Documents/notes

# Different format
pdf-generator --type epub ~/Documents/notes

# Force regeneration
pdf-generator --force ~/Documents/notes
```

### 3.2. Multiple Directories

**Process multiple directories separately**:
```bash
# First directory
pdf-generator ~/notes
# Run ID: 01HN7XJKQM3R8Y2VWSDP4T6FGZ

# Second directory (different run)
pdf-generator ~/docs
# Run ID: 01HN7XJKQM3R8Y2VWSDP4T6FH0
```

**Batch processing with script**:
```bash
# Process multiple directories
for dir in ~/notes ~/docs ~/articles; do
    printf "Processing %s...\\n" "${dir}"
    pdf-generator "${dir}"
done
```

### 3.3. Dry-Run and Finalize

**Preview before processing**:
```bash
# Step 1: Dry-run to see what would be processed
pdf-generator --dry-run ~/large-collection
# Output:
# Run ID: 01HN7XJKQM3R8Y2VWSDP4T6FGZ
# Would process:
#   file1.md → file1.pdf
#   file2.md → file2.pdf
#   (123 files total)

# Step 2: Review the list, then finalize
pdf-generator --finalize 01HN7XJKQM3R8Y2VWSDP4T6FGZ
# Processing...
# Complete: 123 PDFs generated
```

**Benefit**:
- Preview large batches before processing
- Verify source files are correct
- Avoid wasted processing time

### 3.4. Different Output Types

**Generate multiple formats**:
```bash
# PDFs (default)
pdf-generator source_dir
# Output: pndcgn/pdf-{RUN_ID}/

# EPUBs
pdf-generator --type epub source_dir
# Output: pndcgn/epub-{RUN_ID}/

# HTML
pdf-generator --type html source_dir
# Output: pndcgn/html-{RUN_ID}/

# DOCX
pdf-generator --type docx source_dir
# Output: pndcgn/docx-{RUN_ID}/
```

**Custom output location**:
```bash
# PDFs to /tmp
pdf-generator source_dir /tmp
# Output: /tmp/pndcgn/pdf-{RUN_ID}/

# EPUBs to network drive
pdf-generator --type epub source_dir /mnt/network
# Output: /mnt/network/pndcgn/epub-{RUN_ID}/
```

---

## 4. Run Management

### 4.1. Understanding Run IDs

**What is a Run ID?**
- Unique identifier for each processing run
- Format: ULID (Universally Unique Lexicographically Sortable Identifier)
- Example: `01HN7XJKQM3R8Y2VWSDP4T6FGZ`

**Properties**:
- Sortable by creation time
- 26 characters (Base32 encoded)
- Contains timestamp + randomness

**Usage**:
```bash
# Run ID shown at start
pdf-generator source_dir
# Starting run: 01HN7XJKQM3R8Y2VWSDP4T6FGZ

# Use for resume
pdf-generator --resume 01HN7XJKQM3R8Y2VWSDP4T6FGZ

# Use for finalize
pdf-generator --finalize 01HN7XJKQM3R8Y2VWSDP4T6FGZ
```

### 4.2. Resuming Runs

**When to resume**:
- Process was interrupted (Ctrl+C, system crash)
- Network mount became unavailable
- Out of disk space (after freeing space)

**How to resume**:
```bash
# Step 1: Identify the run ID
# (shown in original output, or query database)

# Step 2: Resume
pdf-generator --resume 01HN7XJKQM3R8Y2VWSDP4T6FGZ
# Resuming run 01HN7XJKQM3R8Y2VWSDP4T6FGZ
# Already processed: 45 files
# Remaining: 78 files
# Processing...
```

**What gets skipped**:
- Files with matching fingerprints in cache
- Files with output already in target directory
- Files marked as processed in database

### 4.3. Cleaning Up

**Remove old cache entries**:
```bash
# Clean completed runs (keeps schema)
pdf-generator --clean
# Removed 15 completed runs from cache
```

**Drop entire database**:
```bash
# Nuclear option (use with caution)
pdf-generator --drop
# WARNING: This will delete all cache data!
# Continue? (y/N) y
# Database dropped and recreated
```

**Manual cleanup**:
```bash
# Remove specific run's output
rm -rf pndcgn/pdf-01HN7XJKQM3R8Y2VWSDP4T6FGZ/

# Remove all output
rm -rf pndcgn/

# Remove database manually
rm -f "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn/cache.sqlite"
```

---

## 5. Advanced Features

### 5.1. Caching and Fingerprinting

**How caching works**:
1. Before processing, compute fingerprint of source file
2. Check SQLite cache for matching fingerprint
3. If match found and output exists, skip processing
4. If no match, process file and store new fingerprint

**Fingerprint contents**:
- File size
- Modification time
- SHA256 hash of content (first 64KB)

**Force cache bypass**:
```bash
# Regenerate all files
pdf-generator --force source_dir
```

**Cache benefits**:
- Skip unchanged files on re-runs
- Fast incremental updates
- Avoids redundant processing

### 5.2. Parallel Processing

**Default behavior**:
- Tool processes files sequentially by default
- Parallel processing planned for future versions

**Current limitations**:
- Single-threaded processing
- SQLite WAL mode enabled for future parallelism
- Configuration includes `parallel_jobs` (not yet used)

**Future enhancements**:
```bash
# Planned (not yet implemented)
pdf-generator --jobs 4 source_dir
```

### 5.3. Statistics and Reporting

**Run statistics** (stored in database):
- Start time
- End time
- Total files processed
- Files skipped (cached)
- Errors encountered

**Query statistics manually**:
```bash
# List all runs
sqlite3 "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn/cache.sqlite" \
    "SELECT run_id, start_time, status FROM runs ORDER BY start_time DESC LIMIT 10;"

# Count processed files for run
sqlite3 "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn/cache.sqlite" \
    "SELECT COUNT(*) FROM generated_pdfs WHERE run_id = '01HN7XJKQM3R8Y2VWSDP4T6FGZ';"
```

**Future reporting features**:
```bash
# Planned (not yet implemented)
pdf-generator --stats 01HN7XJKQM3R8Y2VWSDP4T6FGZ
```

---

## 6. Examples

### 6.1. Basic Examples

**Example 1: Convert notes to PDFs**:
```bash
# Source: ~/notes/ with markdown files
cd ~/notes
pdf-generator .

# Output: ~/notes/pndcgn/pdf-{RUN_ID}/
```

**Example 2: Generate EPUBs for e-reader**:
```bash
# Convert markdown books to EPUB
pdf-generator --type epub ~/books/markdown
```

**Example 3: Preview large batch**:
```bash
# Dry-run first
pdf-generator --dry-run ~/archive/docs
# Review output, then finalize
pdf-generator --finalize {RUN_ID}
```

### 6.2. Advanced Examples

**Example 4: Incremental updates**:
```bash
# First run: process all files
pdf-generator ~/project/docs
# 250 files processed

# Edit a few files...
vim ~/project/docs/chapter1.md

# Second run: only process changed files
pdf-generator ~/project/docs
# 1 file processed, 249 skipped (cached)
```

**Example 5: Multiple formats from same source**:
```bash
# Generate PDFs
pdf-generator ~/manuscript

# Generate EPUBs (using cache)
pdf-generator --type epub ~/manuscript

# Generate HTML (using cache)
pdf-generator --type html ~/manuscript
```

**Example 6: Network mount with resume**:
```bash
# Start processing on network mount
pdf-generator /mnt/nas/docs /mnt/nas/output
# Run ID: 01HN7XJKQM3R8Y2VWSDP4T6FGZ
# Processing...
# ^C (interrupted due to network issue)

# Fix network, then resume
pdf-generator --resume 01HN7XJKQM3R8Y2VWSDP4T6FGZ
# Continuing...
```

**Example 7: Scripted batch processing**:
```bash
#!/usr/bin/env bash
# Process multiple projects

projects=(
    "~/work/project-a/docs"
    "~/work/project-b/docs"
    "~/personal/notes"
)

for project in "${projects[@]}"; do
    printf "Processing: %s\\n" "${project}"
    
    # Generate PDFs
    pdf-generator "${project}"
    
    # Generate EPUBs
    pdf-generator --type epub "${project}"
    
    printf "\\n"
done

printf "All projects processed\\n"
```

---

## 7. Troubleshooting

### 7.1. Common Errors

**Error: "No markdown files found"**
```bash
# Cause: SOURCE_DIR has no .md files
# Solution: Verify directory and file extensions
find source_dir -name "*.md"
```

**Error: "Database is locked"**
```bash
# Cause: Multiple processes accessing database
# Solution: Wait for other process, or checkpoint
sqlite3 "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn/cache.sqlite" \
    "PRAGMA wal_checkpoint(RESTART);"
```

**Error: "Permission denied" on output**
```bash
# Cause: No write permission in target directory
# Solution: Change target or fix permissions
chmod u+w target_dir
# Or specify different target
pdf-generator source_dir /tmp
```

**Error: "Pandoc conversion failed"**
```bash
# Cause: Invalid markdown or pandoc issue
# Solution: Test pandoc directly on problem file
pandoc problem.md -o test.pdf
# Review pandoc error messages
```

### 7.2. Performance Issues

**Issue: Slow processing**
```bash
# Check file count
find source_dir -name "*.md" | wc -l

# Use verbose mode to identify bottlenecks
pdf-generator --verbose source_dir
```

**Issue: Large cache database**
```bash
# Check database size
du -h "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn/cache.sqlite"

# Clean old runs
pdf-generator --clean

# Vacuum database
sqlite3 "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn/cache.sqlite" "VACUUM;"
```

**Issue: Out of disk space**
```bash
# Check available space
df -h .

# Remove old output directories
rm -rf pndcgn/pdf-{old-run-id}/

# Or specify target on different mount
pdf-generator source_dir /mnt/large-disk
```

---

## Navigation

[← Installation](030-installation.md) | [↑ Top](#user-guide) | [Technical Specification →](050-technical-specification.md)
