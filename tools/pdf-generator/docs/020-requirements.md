# Requirements Specification

Compliant with AI-GUIDELINES.md

## Table of Contents

<details>
<summary>Expand Table of Contents</summary>

- [1. Introduction](#1-introduction)
- [2. Requirements Methodology](#2-requirements-methodology)
- [3. User Stories](#3-user-stories)
  - [3.1. Installation and Setup](#31-installation-and-setup)
  - [3.2. PDF Generation](#32-pdf-generation)
  - [3.3. Caching and Performance](#33-caching-and-performance)
  - [3.4. CLI Operations](#34-cli-operations)
  - [3.5. Output and Reporting](#35-output-and-reporting)
  - [3.6. Run Management](#36-run-management)
- [4. Traceability Matrix](#4-traceability-matrix)
- [5. Navigation](#5-navigation)

</details>

## 1. Introduction

This document specifies requirements for the PDF Generator tool using Behavior-Driven Development (BDD) methodology. Requirements are expressed as user stories with clear acceptance criteria, examples of desired behavior, and examples of undesired behavior.

**Purpose**: Define what the system must do from the user's perspective, providing a foundation for system tests and implementation.

**Audience**: Developers, testers, and stakeholders who need to understand system requirements and validate behavior.

## 2. Requirements Methodology

### 2.1. Story Format

All requirements follow the standard BDD user story format:

```log
As a [role]
I want [feature]
So that [benefit]
```

Each story includes:
- **Story ID**: Unique identifier (REQ-XXX)
- **Acceptance Criteria**: Specific, testable conditions
- **Desired Behavior**: Examples of correct system behavior
- **Undesired Behavior**: Examples of incorrect behavior to prevent

### 2.2. Testing Approach

- System tests validate requirements → [100-system-test-plan.md](100-system-test-plan.md)
- Feature/unit tests validate implementation → [120-feature-unit-test-plan.md](120-feature-unit-test-plan.md)
- Tests use shellspec framework with Given-When-Then format

## 3. User Stories

### 3.1. Installation and Setup

#### REQ-001: Prerequisites Verification

**Story**:
```
As a user
I want the tool to verify all prerequisites before running
So that I get clear error messages if something is missing
```

**Acceptance Criteria**:
- Tool checks for pandoc, sqlite3, uv before execution
- Clear error message identifies missing prerequisite
- Exit code 1 if prerequisites missing
- Succeeds silently if all prerequisites present

**Desired Behavior**:
- `pndcgn --help` shows usage even if prerequisites missing
- `pndcgn` checks prerequisites and reports: "ERROR: Prerequisite not found: 'pandoc'"
- Error message suggests installation method

**Undesired Behavior**:
- Tool crashes with cryptic error when pandoc missing
- Tool partially executes before discovering missing dependency
- Generic "command not found" without helpful context

#### REQ-002: Configuration Initialization

**Story**:
```
As a user
I want to generate a default configuration file
So that I can customize tool behavior without editing code
```

**Acceptance Criteria**:
- `pndcgn --init` creates `pdf-generator.toml`
- Configuration file includes sensible defaults
- Tool exits after creating config
- Error if config file already exists

**Desired Behavior**:
- `pndcgn --init` creates config with output_root, default format
- Confirmation message: "Created default configuration at: pdf-generator.toml"
- Existing config not overwritten without confirmation

**Undesired Behavior**:
- Config creation fails silently
- Existing config overwritten without warning
- Invalid TOML syntax in generated file

### 3.2. PDF Generation

#### REQ-003: Basic PDF Generation

**Story**:
```
As a documentation maintainer
I want to generate PDFs from project source files
So that I have consolidated, professional documentation
```

**Acceptance Criteria**:
- Tool traverses project directories
- Processes markdown, code, and diagram files
- Generates PDFs with Dewey Decimal naming
- Creates master index with hyperlinks

**Desired Behavior**:
- `pndcgn` generates PDFs in structured output directory
- PDFs named: `100-laravel.pdf`, `100.010-tad.pdf`
- `_index.md` contains Mermaid diagram and PDF links
- Progress indicators show processing status

**Undesired Behavior**:
- PDFs generated with random or inconsistent naming
- No progress feedback during long operations
- Incomplete generation without error message

#### REQ-004: Multi-Format Support

**Story**:
```
As a documentation maintainer
I want to specify output format
So that I can generate HTML, EPUB, or other formats
```

**Acceptance Criteria**:
- `--type` argument specifies output format
- PDF is default format
- Output directory reflects format: `pndcgn/pdf-{run_id}/`
- Filter chain adapts to output format

**Desired Behavior**:
- `pndcgn` defaults to PDF output
- `pndcgn --type html` generates HTML files
- Future: `--type epub`, `--type markdown`

**Undesired Behavior**:
- Invalid format type causes crash
- PDF-specific logic runs for HTML output
- Format not reflected in output path

### 3.3. Caching and Performance

#### REQ-005: Intelligent Caching

**Story**:
```
As a documentation maintainer
I want the tool to cache unchanged content
So that regeneration is fast
```

**Acceptance Criteria**:
- SQLite database stores file hashes
- Unchanged files not reprocessed
- Cache hit/miss ratio tracked
- Subsequent runs dramatically faster

**Desired Behavior**:
- First run: processes all files, caches results
- Second run (no changes): skips processing, uses cache
- Output shows: "Skipping (cached): 100-laravel.pdf"
- Stats show cache efficiency

**Undesired Behavior**:
- Every run reprocesses all files
- Cache never invalidated when files change
- Stale content in generated PDFs

#### REQ-006: Dependency Tracking

**Story**:
```
As a documentation maintainer
I want automatic dependency tracking
So that changing a diagram regenerates dependent PDFs
```

**Acceptance Criteria**:
- Database tracks file dependencies
- Change in included file triggers regeneration
- Diagram changes trigger dependent PDF regeneration

**Desired Behavior**:
- Markdown file includes PlantUML diagram
- Diagram changes → markdown PDF regenerated
- Direct markdown changes also trigger regeneration

**Undesired Behavior**:
- Diagram updated but dependent PDF not regenerated
- All PDFs regenerated when one diagram changes

### 3.4. CLI Operations

#### REQ-007: Force Regeneration

**Story**:
```
As a documentation maintainer
I want to force complete regeneration
So that I can ensure a clean rebuild
```

**Acceptance Criteria**:
- `--force` flag ignores cache
- All PDFs regenerated regardless of changes
- Cache updated with new results

**Desired Behavior**:
- `pndcgn --force` regenerates all PDFs
- Progress shows all items processing (none skipped)
- New run ID created

**Undesired Behavior**:
- Force flag partially ignored
- Cache cleared but not rebuilt
- Unexpected cache corruption

#### REQ-008: Dry Run Preview

**Story**:
```
As a documentation maintainer
I want to preview what would be generated
So that I can verify before actual generation
```

**Acceptance Criteria**:
- `--dry-run` shows what would be processed
- No actual PDFs generated
- Run ID and fingerprint provided
- Can finalize dry-run later

**Desired Behavior**:
- `pndcgn --dry-run` shows processing plan
- Output: "Would process: docs/architecture"
- Provides run ID for later finalization

**Undesired Behavior**:
- Dry-run actually generates files
- No useful preview information
- Cannot finalize dry-run

#### REQ-009: Clean Specific Runs

**Story**:
```
As a documentation maintainer
I want to remove specific run outputs
So that I can clean up space
```

**Acceptance Criteria**:
- `--clean RUN_ID [RUN_ID...]` removes specified runs
- Confirmation required before deletion
- Database records updated
- Multiple run IDs supported

**Desired Behavior**:
- `pndcgn --clean 01JEMH3F...` prompts for confirmation
- After confirmation: deletes output directory
- Database marked as cleaned

**Undesired Behavior**:
- Deletion without confirmation
- Wrong run deleted
- Database inconsistency after deletion

#### REQ-010: Drop All Outputs

**Story**:
```
As a documentation maintainer
I want to clear all outputs and cache
So that I can start fresh
```

**Acceptance Criteria**:
- `--drop` removes all output directories and cache
- Confirmation required
- Fresh state after completion

**Desired Behavior**:
- `pndcgn --drop` prompts: "Delete all outputs? (y/N)"
- After confirmation: removes output parent and state directory
- Confirmation message: "All outputs cleared"

**Undesired Behavior**:
- Deletion without confirmation
- Partial deletion leaving inconsistent state
- Source files accidentally deleted

### 3.5. Output and Reporting

#### REQ-011: Progress Indicators

**Story**:
```
As a documentation maintainer
I want clear progress feedback
So that I know the tool is working and how long to wait
```

**Acceptance Criteria**:
- Animated spinner shows activity
- Overall progress with percentage
- Per-folder progress during processing
- Clear completion message

**Desired Behavior**:
- Output shows: `[—] Overall Progress: 42/100 (42%)`
- Spinner animates through: —, \, |, /
- Color-coded status messages

**Undesired Behavior**:
- Silent execution with no feedback
- Progress stuck or incorrect
- Spinner floods console

#### REQ-012: Statistics Reporting

**Story**:
```
As a documentation maintainer
I want statistics about the generation run
So that I can understand performance and coverage
```

**Acceptance Criteria**:
- Run duration tracked
- Files processed by type
- Cache efficiency reported
- Statistics in index file

**Desired Behavior**:
- Index shows: runtime, files processed, cache hits
- Per-run statistics stored in database
- Can query historical runs

**Undesired Behavior**:
- No statistics provided
- Inaccurate counts
- Statistics not persisted

### 3.6. Run Management

#### REQ-013: Run Resumption

**Story**:
```
As a documentation maintainer
I want to resume an interrupted run
So that I don't lose progress from crashes
```

**Acceptance Criteria**:
- `--resume` continues from last checkpoint
- Detects incomplete runs automatically
- Prompts to resume or start new
- Fingerprint validation ensures consistency

**Desired Behavior**:
- Tool interrupted → next run detects incomplete
- Prompt: "Resume incomplete run? (y/N)"
- If resumed: continues from last processed directory

**Undesired Behavior**:
- Always starts from beginning
- No detection of incomplete runs
- Resume uses stale data

#### REQ-014: Fingerprinting

**Story**:
```
As a documentation maintainer
I want state validation through fingerprinting
So that resumption is safe and consistent
```

**Acceptance Criteria**:
- Input fingerprint calculated from source files
- Config fingerprint from tool settings
- Combined fingerprint stored with run
- Fingerprint mismatch prevents invalid resumption

**Desired Behavior**:
- Fingerprint calculated at run start
- Stored in database with run
- Resume validates fingerprint matches
- Mismatch: "ERROR: Fingerprint mismatch. Source files changed."

**Undesired Behavior**:
- No fingerprint validation
- Resume with changed sources
- Inconsistent output

#### REQ-015: Dry-Run Finalization

**Story**:
```
As a documentation maintainer  
I want to finalize a previous dry-run
So that I can review before committing to generation
```

**Acceptance Criteria**:
- `--finalize RUN_ID` converts dry-run to actual
- Fingerprint validation ensures consistency
- Error if fingerprint changed
- Actual generation proceeds if valid

**Desired Behavior**:
- `pndcgn --dry-run` provides run ID
- Later: `pndcgn --finalize 01JEMH3F...`
- If fingerprint matches: generation proceeds
- If not: error with explanation

**Undesired Behavior**:
- Finalize without fingerprint check
- Cannot finalize dry-runs
- Finalize uses stale plan

## 4. Traceability Matrix

| Story ID | Title | System Test |
|----------|-------|-------------|
| REQ-001 | Prerequisites Verification | TEST-001 |
| REQ-002 | Configuration Initialization | TEST-002 |
| REQ-003 | Basic PDF Generation | TEST-003 |
| REQ-004 | Multi-Format Support | TEST-004 |
| REQ-005 | Intelligent Caching | TEST-005 |
| REQ-006 | Dependency Tracking | TEST-006 |
| REQ-007 | Force Regeneration | TEST-007 |
| REQ-008 | Dry Run Preview | TEST-008 |
| REQ-009 | Clean Specific Runs | TEST-009 |
| REQ-010 | Drop All Outputs | TEST-010 |
| REQ-011 | Progress Indicators | TEST-011 |
| REQ-012 | Statistics Reporting | TEST-012 |
| REQ-013 | Run Resumption | TEST-013 |
| REQ-014 | Fingerprinting | TEST-014 |
| REQ-015 | Dry-Run Finalization | TEST-015 |

**Note**: Test IDs map to shellspec tests in [100-system-test-plan.md](100-system-test-plan.md)

## 5. Navigation

[← Overview](010-overview.md) | [↑ Top](#requirements-specification) | [Next: Installation →](030-installation.md)
