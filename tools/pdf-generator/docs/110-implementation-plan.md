# Implementation Plan

<details>
<parameter name="table of Contents</summary>

- [1. Overview](#1-overview)
  - [1.1. Implementation Approach](#11-implementation-approach)
  - [1.2. Phases](#12-phases)
- [2. Phase 1: Core Foundation](#2-phase-1-core-foundation)
  - [2.1. Objectives](#21-objectives)
  - [2.2. Deliverables](#22-deliverables)
  - [2.3. Tasks](#23-tasks)
- [3. Phase 2: Database Integration](#3-phase-2-database-integration)
  - [3.1. Objectives](#31-objectives)
  - [3.2. Deliverables](#32-deliverables)
  - [3.3. Tasks](#33-tasks)
- [4. Phase 3: Processing Engine](#4-phase-3-processing-engine)
  - [4.1. Objectives](#41-objectives)
  - [4.2. Deliverables](#42-deliverables)
  - [4.3. Tasks](#43-tasks)
- [5. Phase 4: Advanced Features](#5-phase-4-advanced-features)
  - [5.1. Objectives](#51-objectives)
  - [5.2. Deliverables](#52-deliverables)
  - [5.3. Tasks](#53-tasks)
- [6. Testing Strategy](#6-testing-strategy)
  - [6.1. Test Coverage Goals](#61-test-coverage-goals)
  - [6.2. Test Types](#62-test-types)
- [7. Acceptance Criteria](#7-acceptance-criteria)
- [Navigation](#navigation)

</details>

---

**Compliant with**: AI-GUIDELINES.md v1.0

## 1. Overview

### 1.1. Implementation Approach

**Development methodology**: BDD/TDD with shellspec

**Principles**:
- Test-first development
- Incremental feature delivery
- Continuous integration
- Documentation-driven development

**Phased approach**:
1. **Phase 1**: Core foundation (CLI, utilities, constants)
2. **Phase 2**: Database integration (SQLite, caching)
3. **Phase 3**: Processing engine (Pandoc, fingerprinting)
4. **Phase 4**: Advanced features (resume, finalize, stats)

### 1.2. Phases

**Duration estimates** (person-days):
- Phase 1: 3-5 days
- Phase 2: 4-6 days
- Phase 3: 5-7 days
- Phase 4: 3-5 days
- **Total**: 15-23 days

**Milestones**:
- M1: Basic PDF generation working (end of Phase 3)
- M2: Caching operational (end of Phase 3)
- M3: Full feature set complete (end of Phase 4)
- M4: Production-ready (after QA and documentation review)

---

## 2. Phase 1: Core Foundation

### 2.1. Objectives

- Establish project structure
- Implement CLI argument parsing
- Create utility functions
- Define constants

### 2.2. Deliverables

**Files to create**:
```log
bin/pdf-generator           Main controller (stub)
src/constants.sh            ANSI constants
src/utilities.sh            ULID, paths, logging
pdf-generator.toml          Configuration template
specs/spec_helper.sh        shellspec setup
specs/utilities_spec.sh     Utility tests
```

**Functionality**:
- Parse command-line arguments
- Generate ULIDs
- Resolve paths
- Log with colors
- Load TOML configuration

### 2.3. Tasks

#### 2.3.1. Project Structure

```bash
# Create directory layout
mkdir -p bin src specs docs

# Initialize git (if standalone)
git init
git add .gitignore README.md

# Set up CI/CD
cp .github/workflows/code-quality.yml.template .github/workflows/code-quality.yml
```

#### 2.3.2. Constants Module

**File**: `src/constants.sh`

**Requirements** (from REQ-014):
- Define ANSI color constants (RED, GREEN, YELLOW, BLUE, BOLD, RESET)
- Use CSI pattern for portability
- Support NO_COLOR environment variable

**Tests**:
```bash
# specs/constants_spec.sh
Describe 'Constants'
  It 'defines RED color'
    When call printf "%s" "$PNDCGN_RED"
    The output should start with "\\033["
  End
  
  It 'respects NO_COLOR'
    NO_COLOR=1
    When run source src/constants.sh
    The variable PNDCGN_RED should equal ""
  End
End
```

#### 2.3.3. Utilities Module

**File**: `src/utilities.sh`

**Functions to implement**:
- `pndcgn_generate_ulid()` - Generate 26-char ULID
- `pndcgn_resolve_path()` - Convert to absolute path
- `pndcgn_log_info()` - Log informational message
- `pndcgn_log_error()` - Log error message
- `pndcgn_format_duration()` - Human-readable time

**Tests**:
```bash
# specs/utilities_spec.sh
Describe 'pndcgn_generate_ulid'
  It 'generates 26-character identifier'
    When call pndcgn_generate_ulid
    The output should have length 26
  End
  
  It 'starts with timestamp component'
    ulid=$(pndcgn_generate_ulid)
    first_char="${ulid:0:1}"
    The variable first_char should equal "0"
  End
End

Describe 'pndcgn_resolve_path'
  It 'converts relative to absolute'
    When call pndcgn_resolve_path "../docs"
    The output should start with "/"
  End
End
```

#### 2.3.4. CLI Argument Parsing

**File**: `bin/pdf-generator`

**Arguments to support**:
- `--init` - Initialize configuration
- `--type <format>` - Output type (default: pdf)
- `--dry-run` - Simulate without generating
- `--verbose` - Verbose output
- `--help` - Display help
- `--version` - Display version

**Tests**:
```bash
# specs/cli_spec.sh
Describe 'CLI argument parsing'
  It 'displays help with --help'
    When run bin/pdf-generator --help
    The output should include "Usage:"
    The status should be success
  End
  
  It 'sets verbose mode with --verbose'
    When run bin/pdf-generator --verbose --help
    The variable pndcgn_verbose should equal "1"
  End
End
```

---

## 3. Phase 2: Database Integration

### 3.1. Objectives

- Implement SQLite database operations
- Create schema
- Implement caching layer

### 3.2. Deliverables

**Files to create**:
```log
src/database.sh             Database operations
specs/database_spec.sh      Database tests
```

**Functionality**:
- Initialize database schema
- Start/finish runs
- Store/retrieve fingerprints
- Cache lookups
- Cleanup operations

### 3.3. Tasks

#### 3.3.1. Database Module Skeleton

**File**: `src/database.sh`

**Functions to implement**:
- `pndcgn_db_init()` - Create schema
- `pndcgn_db_start_run()` - Insert run record
- `pndcgn_db_finish_run()` - Update run status
- `pndcgn_db_get_fingerprint()` - Cache lookup
- `pndcgn_db_store_file()` - Store generated file metadata
- `pndcgn_db_clean()` - Remove old entries
- `pndcgn_db_drop()` - Drop database

#### 3.3.2. Schema Creation

**Tests** (from REQ-002):
```bash
# specs/database_spec.sh
Describe 'pndcgn_db_init'
  It 'creates runs table'
    When call pndcgn_db_init
    The result of "tables_exist" should equal "runs generated_pdfs"
  End
  
  It 'creates indexes'
    When call pndcgn_db_init
    The result of "index_exists idx_fingerprints" should be success
  End
  
  It 'enables WAL mode'
    When call pndcgn_db_init
    The result of "check_journal_mode" should equal "wal"
  End
End
```

#### 3.3.3. Run Management

**Tests** (from REQ-013, REQ-014):
```bash
Describe 'Run management'
  It 'starts new run'
    run_id=$(pndcgn_generate_ulid)
    When call pndcgn_db_start_run "${run_id}" "/src" "/out" "pdf" 0
    The status should be success
    The result of "run_exists ${run_id}" should be success
  End
  
  It 'finishes run with statistics'
    When call pndcgn_db_finish_run "${run_id}" "complete" 150 10 140 0
    The result of "get_run_status ${run_id}" should equal "complete"
  End
End
```

#### 3.3.4. Cache Operations

**Tests** (from REQ-005, REQ-006):
```bash
Describe 'Cache operations'
  It 'stores file metadata'
    When call pndcgn_db_store_file \
        "${run_id}" \
        "/src/file.md" \
        "/out/file.pdf" \
        "12345:1699564800:abc123" \
        12345 \
        1699564800
    The status should be success
  End
  
  It 'retrieves cached file by fingerprint'
    When call pndcgn_db_get_fingerprint "12345:1699564800:abc123" "pdf"
    The output should equal "/out/file.pdf"
  End
End
```

---

## 4. Phase 3: Processing Engine

### 4.1. Objectives

- Implement file discovery
- Implement fingerprinting
- Integrate Pandoc
- Implement main processing loop

### 4.2. Deliverables

**Files to create**:
```log
src/processing.sh           Processing engine
specs/processing_spec.sh    Processing tests
specs/integration_spec.sh   End-to-end tests
```

**Functionality**:
- Discover markdown files
- Compute fingerprints
- Convert files via Pandoc
- Skip cached files
- Handle errors gracefully

### 4.3. Tasks

#### 4.3.1. File Discovery

**Function**: `pndcgn_find_files()`

**Tests** (from REQ-003):
```bash
Describe 'File discovery'
  setup() {
    mkdir -p /tmp/test/sub
    touch /tmp/test/file1.md
    touch /tmp/test/sub/file2.md
    touch /tmp/test/ignore.txt
  }
  
  It 'finds markdown files recursively'
    When call pndcgn_find_files "/tmp/test"
    The line 1 of output should include "file1.md"
    The line 2 of output should include "file2.md"
    The lines of output should equal 2
  End
End
```

#### 4.3.2. Fingerprinting

**Function**: `pndcgn_compute_fingerprint()`

**Tests** (from REQ-005):
```bash
Describe 'Fingerprinting'
  It 'computes stable fingerprint'
    fp1=$(pndcgn_compute_fingerprint "/tmp/test.md")
    fp2=$(pndcgn_compute_fingerprint "/tmp/test.md")
    The variable fp1 should equal "$fp2"
  End
  
  It 'includes size, mtime, and hash'
    When call pndcgn_compute_fingerprint "/tmp/test.md"
    The output should match pattern "*:*:*"
  End
  
  It 'changes when file modified'
    fp1=$(pndcgn_compute_fingerprint "/tmp/test.md")
    echo "new content" >> /tmp/test.md
    fp2=$(pndcgn_compute_fingerprint "/tmp/test.md")
    The variable fp1 should not equal "$fp2"
  End
End
```

#### 4.3.3. Pandoc Integration

**Function**: `pndcgn_convert_file()`

**Tests** (from REQ-003, REQ-004):
```bash
Describe 'File conversion'
  It 'converts markdown to PDF'
    When call pndcgn_convert_file "/tmp/test.md" "/tmp/test.pdf" "pdf"
    The status should be success
    The path "/tmp/test.pdf" should be file
  End
  
  It 'handles conversion errors gracefully'
    printf "{{invalid}}" > /tmp/invalid.md
    When call pndcgn_convert_file "/tmp/invalid.md" "/tmp/invalid.pdf" "pdf"
    The status should be failure
    The error should include "Pandoc conversion failed"
  End
End
```

#### 4.3.4. Processing Loop

**Integration test** (from REQ-003, REQ-004, REQ-005, REQ-006):
```bash
Describe 'End-to-end processing'
  It 'processes directory of markdown files'
    When run bin/pdf-generator /tmp/test
    The status should be success
    The path "/tmp/test/pndcgn/pdf-*/file1.pdf" should be file
    The path "/tmp/test/pndcgn/pdf-*/file2.pdf" should be file
  End
  
  It 'skips cached files on second run'
    # First run
    run1_id=$(bin/pdf-generator /tmp/test | grep "Run ID:" | awk '{print $3}')
    
    # Second run (files unchanged)
    When run bin/pdf-generator /tmp/test
    The output should include "2 skipped (cached)"
  End
End
```

---

## 5. Phase 4: Advanced Features

### 5.1. Objectives

- Implement run resumption
- Implement dry-run finalization
- Implement statistics reporting
- Implement cleanup operations

### 5.2. Deliverables

**Updated files**:
```log
bin/pdf-generator           Add --resume, --finalize, --stats
src/processing.sh           Add resumption logic
src/database.sh             Add statistics queries
specs/advanced_spec.sh      Advanced feature tests
```

**Functionality**:
- Resume interrupted runs
- Finalize dry-runs
- Generate statistics
- Clean old cache entries

### 5.3. Tasks

#### 5.3.1. Run Resumption

**Tests** (from REQ-013):
```bash
Describe 'Run resumption'
  It 'resumes interrupted run'
    # Simulate interruption
    run_id=$(start_partial_run /tmp/test)  # Process 1/2 files, then interrupt
    
    # Resume
    When run bin/pdf-generator --resume "${run_id}"
    The status should be success
    The output should include "Resuming run ${run_id}"
    The output should include "Already processed: 1"
    The result of "all_files_processed ${run_id}" should be success
  End
End
```

#### 5.3.2. Dry-Run Finalization

**Tests** (from REQ-015):
```bash
Describe 'Dry-run finalization'
  It 'finalizes dry-run without changes'
    # Dry-run
    run_id=$(bin/pdf-generator --dry-run /tmp/test | extract_run_id)
    
    # Finalize
    When run bin/pdf-generator --finalize "${run_id}"
    The status should be success
    The path "/tmp/test/pndcgn/pdf-${run_id}/*" should be file
  End
  
  It 'detects fingerprint mismatch'
    # Dry-run
    run_id=$(bin/pdf-generator --dry-run /tmp/test | extract_run_id)
    
    # Modify file
    echo "new content" >> /tmp/test/file1.md
    
    # Finalize (should fail)
    When run bin/pdf-generator --finalize "${run_id}"
    The status should be failure
    The error should include "Fingerprint mismatch"
  End
End
```

#### 5.3.3. Statistics and Reporting

**Tests** (from REQ-011, REQ-012):
```bash
Describe 'Statistics reporting'
  It 'displays run statistics'
    When run bin/pdf-generator --stats "${run_id}"
    The output should include "Files processed:"
    The output should include "Files skipped:"
    The output should include "Duration:"
  End
End
```

#### 5.3.4. Cleanup Operations

**Tests** (from REQ-009, REQ-010):
```bash
Describe 'Cleanup operations'
  It 'removes old cache entries with --clean'
    When run bin/pdf-generator --clean
    The status should be success
    The output should include "Removed"
  End
  
  It 'drops entire database with --drop'
    When run bin/pdf-generator --drop
    The status should be success
    The path "${db_path}" should not be file
  End
End
```

---

## 6. Testing Strategy

### 6.1. Test Coverage Goals

**Target**: 90%+ code coverage

**Tools**:
- `shellspec` - BDD testing framework
- `kcov` - Code coverage measurement
- `shellcheck` - Static analysis

**Coverage by module**:
- Constants: 100% (simple assignments)
- Utilities: 95%+ (thorough unit tests)
- Database: 90%+ (mock SQLite interactions)
- Processing: 85%+ (complex integration)
- Main controller: 85%+ (orchestration logic)

### 6.2. Test Types

#### 6.2.1. Unit Tests

**Scope**: Individual functions in isolation

**Example**:
```bash
# Test single function
Describe 'pndcgn_generate_ulid'
  It 'generates unique identifiers'
    ulid1=$(pndcgn_generate_ulid)
    ulid2=$(pndcgn_generate_ulid)
    The variable ulid1 should not equal "$ulid2"
  End
End
```

#### 6.2.2. Integration Tests

**Scope**: Multiple modules working together

**Example**:
```bash
# Test database + processing interaction
Describe 'Cache integration'
  It 'skips files found in cache'
    # Store in cache
    pndcgn_db_store_file "${run_id}" "/src/file.md" "/out/file.pdf" "${fp}" 100 1000
    
    # Process (should skip)
    When call pndcgn_process_file "/src/file.md"
    The output should include "Skipped (cached)"
  End
End
```

#### 6.2.3. System Tests

**Scope**: End-to-end workflows

**Example**:
```bash
# Test complete workflow
Describe 'Full workflow'
  It 'processes directory with caching'
    # First run
    When run bin/pdf-generator /tmp/test
    The status should be success
    
    # Second run (cached)
    When run bin/pdf-generator /tmp/test
    The output should include "skipped (cached)"
  End
End
```

---

## 7. Acceptance Criteria

**Phase 1 complete when**:
- CLI parses all documented arguments
- ULID generation works consistently
- Path resolution handles edge cases
- Logging outputs correctly with colors
- All utility tests pass
- Code coverage ≥95%

**Phase 2 complete when**:
- Database schema creates successfully
- Runs can be started and finished
- Cache lookups work correctly
- Cleanup operations function
- All database tests pass
- Code coverage ≥90%

**Phase 3 complete when**:
- Files are discovered recursively
- Fingerprints compute correctly
- Pandoc conversions succeed
- Cached files are skipped
- Processing loop handles errors
- All processing tests pass
- Code coverage ≥85%

**Phase 4 complete when**:
- Runs can be resumed
- Dry-runs can be finalized
- Statistics display correctly
- All advanced tests pass
- Code coverage ≥85% overall

**Production-ready when**:
- All requirements (REQ-001 through REQ-015) satisfied
- All test suites pass (unit, integration, system)
- Code coverage ≥90% overall
- Documentation complete and accurate
- No critical shellcheck warnings
- Performance benchmarks met

---

## Navigation

[← Output Formats](080-output-formats.md) | [↑ Top](#implementation-plan) | [System Test Plan →](100-system-test-plan.md)
