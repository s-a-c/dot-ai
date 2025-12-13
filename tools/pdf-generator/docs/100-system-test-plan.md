# System Test Plan

<details>
<summary>Table of Contents</summary>

- [1. Overview](#1-overview)
  - [1.1. Purpose](#11-purpose)
  - [1.2. Scope](#12-scope)
  - [1.3. Test Environment](#13-test-environment)
- [2. Traceability Matrix](#2-traceability-matrix)
- [3. System Tests](#3-system-tests)
  - [3.1. ST-001: Installation and Setup](#31-st-001-installation-and-setup)
  - [3.2. ST-002: Initial Configuration](#32-st-002-initial-configuration)
  - [3.3. ST-003: Basic PDF Generation](#33-st-003-basic-pdf-generation)
  - [3.4. ST-004: Multiple Output Formats](#34-st-004-multiple-output-formats)
  - [3.5. ST-005: Caching Behavior](#35-st-005-caching-behavior)
  - [3.6. ST-006: Cache Invalidation](#36-st-006-cache-invalidation)
  - [3.7. ST-007: CLI Option: Verbose Mode](#37-st-007-cli-option-verbose-mode)
  - [3.8. ST-008: CLI Option: Force Regeneration](#38-st-008-cli-option-force-regeneration)
  - [3.9. ST-009: CLI Option: Clean Cache](#39-st-009-cli-option-clean-cache)
  - [3.10. ST-010: CLI Option: Drop Database](#310-st-010-cli-option-drop-database)
  - [3.11. ST-011: Statistics Display](#311-st-011-statistics-display)
  - [3.12. ST-012: Progress Indicators](#312-st-012-progress-indicators)
  - [3.13. ST-013: Run Interruption and Resumption](#313-st-013-run-interruption-and-resumption)
  - [3.14. ST-014: ANSI Color Output](#314-st-014-ansi-color-output)
  - [3.15. ST-015: Dry-Run and Finalization](#315-st-015-dry-run-and-finalization)
- [Navigation](#navigation)

</details>

---

**Compliant with**: AI-GUIDELINES.md v1.0

## 1. Overview

### 1.1. Purpose

This system test plan validates end-to-end workflows and ensures requirements (REQ-001 through REQ-015) are satisfied. Each system test maps to one or more requirements and describes observable behavior from a user's perspective.

### 1.2. Scope

**In scope**:
- Installation and setup procedures
- All CLI operations and options
- File processing workflows
- Caching behavior
- Error handling and recovery
- User-visible output

**Out of scope**:
- Internal function implementations (covered by unit tests)
- Performance benchmarks (separate test suite)
- Security testing (separate audit)

### 1.3. Test Environment

**Requirements**:
- Unix-like OS (Linux, macOS, BSD)
- Bash 4.0+
- SQLite 3.x
- Pandoc 2.x+
- shellspec (testing framework)

**Test data**:
```log
fixtures/
├── simple.md           # Single paragraph
├── complex.md          # Headers, lists, code blocks
├── large.md            # 1000+ lines
├── invalid.md          # Malformed markdown
└── special-chars.md    # Unicode, spaces in filename
```

---

## 2. Traceability Matrix

| Test ID | Requirement(s) | Description |
|---------|---------------|-------------|
| ST-001 | REQ-001 | Installation and setup |
| ST-002 | REQ-002 | Configuration initialization |
| ST-003 | REQ-003, REQ-004 | Basic PDF generation |
| ST-004 | REQ-004 | Multiple output formats |
| ST-005 | REQ-005, REQ-006 | Caching behavior |
| ST-006 | REQ-006 | Cache invalidation |
| ST-007 | REQ-007 | Verbose mode |
| ST-008 | REQ-008 | Force regeneration |
| ST-009 | REQ-009 | Clean cache |
| ST-010 | REQ-010 | Drop database |
| ST-011 | REQ-011 | Statistics display |
| ST-012 | REQ-012 | Progress indicators |
| ST-013 | REQ-013 | Interruption and resumption |
| ST-014 | REQ-014 | ANSI color output |
| ST-015 | REQ-015 | Dry-run and finalization |

---

## 3. System Tests

### 3.1. ST-001: Installation and Setup

**Requirement**: REQ-001

**Objective**: Verify installation process creates necessary files and directories

**Preconditions**:
- Tool installed in `$PATH`
- No existing configuration or database

**Test steps**:
1. Run `pdf-generator --help`
2. Verify help text displays
3. Check `$XDG_STATE_HOME/pndcgn/` does not exist yet

**Expected behavior**:
```log
$ pdf-generator --help
Usage: pdf-generator [OPTIONS] [SOURCE_DIR] [TARGET_DIR]

Batch convert markdown files to PDFs (or other formats) using pandoc...

$ ls $XDG_STATE_HOME/pndcgn/
ls: cannot access '...': No such file or directory
```

**shellspec test**:
```bash
Describe 'ST-001: Installation'
  It 'displays help without requiring initialization'
    When run bin/pdf-generator --help
    The output should include "Usage:"
    The status should be success
  End
End
```

### 3.2. ST-002: Initial Configuration

**Requirement**: REQ-002

**Objective**: Verify `--init` creates database and configuration

**Preconditions**:
- Tool installed
- No existing configuration

**Test steps**:
1. Run `pdf-generator --init`
2. Verify database created at `$XDG_STATE_HOME/pndcgn/cache.sqlite`
3. Verify configuration file created (if applicable)
4. Check database tables exist

**Expected behavior**:
```log
$ pdf-generator --init
Initialized database: /home/user/.local/state/pndcgn/cache.sqlite
Schema created successfully

$ sqlite3 $XDG_STATE_HOME/pndcgn/cache.sqlite ".tables"
generated_pdfs  runs
```

**shellspec test**:
```bash
Describe 'ST-002: Configuration'
  It 'initializes database schema with --init'
    When run bin/pdf-generator --init
    The status should be success
    The path "${db_path}" should be file
    The result of "check_tables_exist" should be success
  End
End
```

### 3.3. ST-003: Basic PDF Generation

**Requirements**: REQ-003, REQ-004

**Objective**: Verify basic markdown to PDF conversion

**Preconditions**:
- Database initialized
- Test markdown file exists

**Test steps**:
1. Create test directory with `simple.md`
2. Run `pdf-generator /path/to/test`
3. Verify PDF created in `pndcgn/pdf-{RUN_ID}/simple.pdf`
4. Verify PDF is valid (can be opened)

**Expected behavior**:
```log
$ pdf-generator /tmp/test
Starting run: 01HN7XJKQM3R8Y2VWSDP4T6FGZ
Processing 1 file...
  simple.md → simple.pdf [OK]
Complete: 1 processed, 0 skipped, 0 failed
Output: /tmp/test/pndcgn/pdf-01HN7X.../

$ ls /tmp/test/pndcgn/pdf-*/
simple.pdf
```

**shellspec test**:
```bash
Describe 'ST-003: Basic generation'
  setup() {
    mkdir -p /tmp/test
    echo "# Test" > /tmp/test/simple.md
  }
  
  It 'generates PDF from markdown'
    When run bin/pdf-generator /tmp/test
    The status should be success
    The output should include "1 processed"
    The path "/tmp/test/pndcgn/pdf-*/simple.pdf" should be file
  End
  
  cleanup() {
    rm -rf /tmp/test
  }
End
```

### 3.4. ST-004: Multiple Output Formats

**Requirement**: REQ-004

**Objective**: Verify support for different output formats

**Preconditions**:
- Database initialized
- Test markdown exists

**Test steps**:
1. Generate PDF: `pdf-generator --type pdf /tmp/test`
2. Generate EPUB: `pdf-generator --type epub /tmp/test`
3. Generate HTML: `pdf-generator --type html /tmp/test`
4. Verify each output created in correct directory

**Expected behavior**:
```log
$ pdf-generator --type pdf /tmp/test
Output: /tmp/test/pndcgn/pdf-01HN7X.../

$ pdf-generator --type epub /tmp/test
Output: /tmp/test/pndcgn/epub-01HN7Y.../

$ pdf-generator --type html /tmp/test
Output: /tmp/test/pndcgn/html-01HN7Z.../
```

**shellspec test**:
```bash
Describe 'ST-004: Multiple formats'
  It 'generates PDF, EPUB, and HTML'
    When run bin/pdf-generator --type pdf /tmp/test
    The path "/tmp/test/pndcgn/pdf-*/simple.pdf" should be file
    
    When run bin/pdf-generator --type epub /tmp/test
    The path "/tmp/test/pndcgn/epub-*/simple.epub" should be file
    
    When run bin/pdf-generator --type html /tmp/test
    The path "/tmp/test/pndcgn/html-*/simple.html" should be file
  End
End
```

### 3.5. ST-005: Caching Behavior

**Requirements**: REQ-005, REQ-006

**Objective**: Verify unchanged files are skipped on re-run

**Preconditions**:
- Files already processed once

**Test steps**:
1. First run: `pdf-generator /tmp/test`
2. Note files processed
3. Second run (unchanged): `pdf-generator /tmp/test`
4. Verify files skipped (cached)

**Expected behavior**:
```log
$ pdf-generator /tmp/test
Processing 3 files...
Complete: 3 processed, 0 skipped

$ pdf-generator /tmp/test
Processing 3 files...
Complete: 0 processed, 3 skipped (cached)
```

**shellspec test**:
```bash
Describe 'ST-005: Caching'
  It 'skips unchanged files on second run'
    # First run
    When run bin/pdf-generator /tmp/test
    The output should include "3 processed"
    
    # Second run
    When run bin/pdf-generator /tmp/test
    The output should include "3 skipped (cached)"
  End
End
```

### 3.6. ST-006: Cache Invalidation

**Requirement**: REQ-006

**Objective**: Verify modified files are regenerated

**Preconditions**:
- Files cached from previous run

**Test steps**:
1. First run: `pdf-generator /tmp/test`
2. Modify one file
3. Second run: `pdf-generator /tmp/test`
4. Verify modified file regenerated, others skipped

**Expected behavior**:
```log
$ echo "Updated" >> /tmp/test/file1.md

$ pdf-generator /tmp/test
Processing 3 files...
  file1.md → file1.pdf [OK]
Complete: 1 processed, 2 skipped (cached)
```

**shellspec test**:
```bash
Describe 'ST-006: Cache invalidation'
  It 'regenerates modified files'
    # First run
    bin/pdf-generator /tmp/test
    
    # Modify one file
    echo "Updated" >> /tmp/test/file1.md
    
    # Second run
    When run bin/pdf-generator /tmp/test
    The output should include "1 processed"
    The output should include "2 skipped"
  End
End
```

### 3.7. ST-007: CLI Option: Verbose Mode

**Requirement**: REQ-007

**Objective**: Verify `--verbose` shows detailed output

**Test steps**:
1. Run with `--verbose`: `pdf-generator --verbose /tmp/test`
2. Verify detailed progress information displayed

**Expected behavior**:
```log
$ pdf-generator --verbose /tmp/test
[INFO] Starting run: 01HN7X...
[INFO] Source directory: /tmp/test
[INFO] Output directory: /tmp/test/pndcgn/pdf-01HN7X.../
[INFO] Discovering files...
[INFO] Found 3 markdown files
[INFO] Processing file1.md...
[INFO]   Fingerprint: 12345:1699564800:abc123
[INFO]   Cache lookup: miss
[INFO]   Invoking pandoc...
[INFO]   Output: file1.pdf
[INFO] Processing file2.md...
...
```

**shellspec test**:
```bash
Describe 'ST-007: Verbose mode'
  It 'displays detailed output with --verbose'
    When run bin/pdf-generator --verbose /tmp/test
    The output should include "[INFO]"
    The output should include "Fingerprint:"
  End
End
```

### 3.8. ST-008: CLI Option: Force Regeneration

**Requirement**: REQ-008

**Objective**: Verify `--force` ignores cache

**Test steps**:
1. First run (creates cache)
2. Second run with `--force`
3. Verify all files regenerated despite cache

**Expected behavior**:
```log
$ pdf-generator --force /tmp/test
Processing 3 files...
  file1.md → file1.pdf [OK]
  file2.md → file2.pdf [OK]
  file3.md → file3.pdf [OK]
Complete: 3 processed, 0 skipped
```

**shellspec test**:
```bash
Describe 'ST-008: Force regeneration'
  It 'ignores cache with --force'
    # First run
    bin/pdf-generator /tmp/test
    
    # Force regeneration
    When run bin/pdf-generator --force /tmp/test
    The output should include "3 processed"
    The output should not include "skipped"
  End
End
```

### 3.9. ST-009: CLI Option: Clean Cache

**Requirement**: REQ-009

**Objective**: Verify `--clean` removes old entries

**Test steps**:
1. Create multiple completed runs
2. Run `pdf-generator --clean`
3. Verify old runs removed from database

**Expected behavior**:
```log
$ pdf-generator --clean
Cleaning cache...
Removed 15 completed runs
Database size reduced from 1.2MB to 0.3MB
```

**shellspec test**:
```bash
Describe 'ST-009: Clean cache'
  It 'removes old cache entries'
    # Create multiple runs
    create_old_runs
    
    When run bin/pdf-generator --clean
    The status should be success
    The output should include "Removed"
    The result of "count_runs" should be less than "$initial_count"
  End
End
```

### 3.10. ST-010: CLI Option: Drop Database

**Requirement**: REQ-010

**Objective**: Verify `--drop` removes entire database

**Test steps**:
1. Run `pdf-generator --drop`
2. Confirm prompt
3. Verify database deleted

**Expected behavior**:
```log
$ pdf-generator --drop
WARNING: This will delete all cache data!
Continue? (y/N) y
Database dropped: /home/user/.local/state/pndcgn/cache.sqlite
```

**shellspec test**:
```bash
Describe 'ST-010: Drop database'
  It 'removes database with --drop'
    When run bin/pdf-generator --drop <<< "y"
    The status should be success
    The path "${db_path}" should not be file
  End
End
```

### 3.11. ST-011: Statistics Display

**Requirement**: REQ-011

**Objective**: Verify statistics shown after run

**Test steps**:
1. Run `pdf-generator /tmp/test`
2. Verify statistics displayed

**Expected behavior**:
```log
$ pdf-generator /tmp/test
Processing 10 files...
Complete: 5 processed, 5 skipped, 0 failed
Duration: 12.3 seconds
Output: /tmp/test/pndcgn/pdf-01HN7X.../
```

**shellspec test**:
```bash
Describe 'ST-011: Statistics'
  It 'displays run statistics'
    When run bin/pdf-generator /tmp/test
    The output should include "processed"
    The output should include "skipped"
    The output should include "Duration:"
  End
End
```

### 3.12. ST-012: Progress Indicators

**Requirement**: REQ-012

**Objective**: Verify progress displayed during processing

**Test steps**:
1. Run with many files
2. Observe progress indicators

**Expected behavior**:
```log
$ pdf-generator /tmp/large
Processing 100 files...
Progress: [===>      ] 40% (40/100)
```

**shellspec test**:
```bash
Describe 'ST-012: Progress indicators'
  Skip 'Visual progress testing'
  # Note: Progress indicators difficult to test in CI
End
```

### 3.13. ST-013: Run Interruption and Resumption

**Requirement**: REQ-013

**Objective**: Verify interrupted runs can resume

**Test steps**:
1. Start run, interrupt (Ctrl+C)
2. Resume with `--resume {RUN_ID}`
3. Verify processing continues

**Expected behavior**:
```log
$ pdf-generator /tmp/test
Processing 10 files...
  file1.md → file1.pdf [OK]
  file2.md → file2.pdf [OK]
^C Interrupted

$ pdf-generator --resume 01HN7XJKQM3R8Y2VWSDP4T6FGZ
Resuming run 01HN7X...
Already processed: 2 files
Processing remaining 8 files...
  file3.md → file3.pdf [OK]
  ...
```

**shellspec test**:
```bash
Describe 'ST-013: Resumption'
  It 'resumes interrupted run'
    # Simulate partial run
    run_id=$(start_partial_run)
    
    # Resume
    When run bin/pdf-generator --resume "${run_id}"
    The output should include "Resuming"
    The output should include "Already processed"
  End
End
```

### 3.14. ST-014: ANSI Color Output

**Requirement**: REQ-014

**Objective**: Verify colored output and NO_COLOR support

**Test steps**:
1. Run without NO_COLOR
2. Verify colors present
3. Run with NO_COLOR=1
4. Verify no colors

**Expected behavior**:
```log
$ pdf-generator /tmp/test | cat -A
^[[32m[OK]^[[0m file1.pdf

$ NO_COLOR=1 pdf-generator /tmp/test | cat -A
[OK] file1.pdf
```

**shellspec test**:
```bash
Describe 'ST-014: ANSI colors'
  It 'outputs colors by default'
    When run bin/pdf-generator /tmp/test
    The output should include "\\033["
  End
  
  It 'respects NO_COLOR'
    NO_COLOR=1
    When run bin/pdf-generator /tmp/test
    The output should not include "\\033["
  End
End
```

### 3.15. ST-015: Dry-Run and Finalization

**Requirement**: REQ-015

**Objective**: Verify dry-run workflow

**Test steps**:
1. Run `pdf-generator --dry-run /tmp/test`
2. Verify no PDFs created
3. Finalize with `--finalize {RUN_ID}`
4. Verify PDFs created

**Expected behavior**:
```log
$ pdf-generator --dry-run /tmp/test
Dry-run mode: No files will be generated
Would process:
  file1.md → file1.pdf
  file2.md → file2.pdf
  file3.md → file3.pdf
Run ID: 01HN7XJKQM3R8Y2VWSDP4T6FGZ
To finalize: pdf-generator --finalize 01HN7X...

$ pdf-generator --finalize 01HN7XJKQM3R8Y2VWSDP4T6FGZ
Finalizing run 01HN7X...
Processing 3 files...
Complete: 3 processed
```

**shellspec test**:
```bash
Describe 'ST-015: Dry-run finalization'
  It 'creates no files in dry-run'
    run_id=$(bin/pdf-generator --dry-run /tmp/test | extract_run_id)
    The path "/tmp/test/pndcgn/pdf-*/*.pdf" should not be file
  End
  
  It 'generates files when finalized'
    When run bin/pdf-generator --finalize "${run_id}"
    The path "/tmp/test/pndcgn/pdf-*/*.pdf" should be file
  End
End
```

---

## Navigation

[← Implementation Plan](110-implementation-plan.md) | [↑ Top](#system-test-plan) | [Feature/Unit Test Plan →](120-feature-unit-test-plan.md)
