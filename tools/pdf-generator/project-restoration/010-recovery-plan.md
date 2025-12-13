# PDF Generator Documentation Recovery Plan

Compliant with AI-GUIDELINES.md

## Table of Contents

<details>
<summary>Expand Table of Contents</summary>

- [1. Problem Statement](#1-problem-statement)
- [2. Current State Analysis](#2-current-state-analysis)
  - [2.1. What Exists](#21-what-exists)
  - [2.2. What's Missing](#22-whats-missing)
  - [2.3. Key Information from Scratch Files](#23-key-information-from-scratch-files)
- [3. Documentation Standards](#3-documentation-standards)
  - [3.1. AI-GUIDELINES Compliance](#31-ai-guidelines-compliance)
  - [3.2. File Naming Convention](#32-file-naming-convention)
  - [3.3. Formatting Requirements](#33-formatting-requirements)
- [4. Proposed Document Structure](#4-proposed-document-structure)
  - [4.1. Core Documentation Files](#41-core-documentation-files)
  - [4.2. Content Sources](#42-content-sources)
  - [4.3. Directory Structure](#43-directory-structure)
- [5. Document Specifications](#5-document-specifications)
  - [5.1. 000-index.md](#51-000-indexmd)
  - [5.2. 010-overview.md](#52-010-overviewmd)
  - [5.3. 020-installation.md](#53-020-installationmd)
  - [5.4. 030-usage.md](#54-030-usagemd)
  - [5.5. 040-architecture.md](#55-040-architecturemd)
  - [5.6. 050-filters.md](#56-050-filtersmd)
  - [5.7. 060-dewey-decimal.md](#57-060-dewey-decimalmd)
  - [5.8. 070-statistics.md](#58-070-statisticsmd)
  - [5.9. 090-changelog.md](#59-090-changelogmd)
- [6. Key Sections to Document](#6-key-sections-to-document)
  - [6.1. System Architecture Components](#61-system-architecture-components)
  - [6.2. Script Features](#62-script-features)
  - [6.3. Evolution Timeline](#63-evolution-timeline)
- [7. Implementation Approach](#7-implementation-approach)
  - [7.1. Phase 1: Directory Setup](#71-phase-1-directory-setup)
  - [7.2. Phase 2: Core Documentation](#72-phase-2-core-documentation)
  - [7.3. Phase 3: Technical Documentation](#73-phase-3-technical-documentation)
  - [7.4. Phase 4: Quality Assurance](#74-phase-4-quality-assurance)
- [8. Validation Checklist](#8-validation-checklist)
  - [8.1. Content Validation](#81-content-validation)
  - [8.2. Format Validation](#82-format-validation)
  - [8.3. Technical Validation](#83-technical-validation)
- [9. Success Criteria](#9-success-criteria)
- [10. Navigation](#10-navigation)

</details>

## 1. Problem Statement

The `tools/pdf-generator/docs` directory is missing and needs to be recreated based on the comprehensive planning work documented in `dot-scratch/scratch#01.md`. This scratch file contains an extensive conversation thread (1591 lines) detailing the evolution of an advanced PDF generation system with sophisticated features including:

- SQLite-based intelligent caching
- Parallel processing capabilities
- Dynamic Pandoc filter chain
- Dewey Decimal naming convention
- Run identification and resumption support
- Comprehensive statistics and reporting

**Purpose**: This recovery activity is about **recapturing knowledge, decisions, and understanding achieved during development thus far**, not migrating existing documentation. The scratch file serves as the primary source of technical decisions and design rationale that must be preserved in proper documentation format.

## 2. Current State Analysis

### 2.1. What Exists

- `tools/pdf-generator/` directory with bin, spec, src subdirectories
- **`tools/pdf-generator/bin/pdf-generator`** - Existing script implementation (154 lines, v6)
- **`tools/pdf-generator/src/constants.sh`** - Existing constants file (31 lines)
- `dot-scratch/scratch#01.md` - comprehensive conversation history with full planning evolution
- `dot-scratch/update-prerequisites-for-idx.md` - IDX-specific prerequisite updates
- Working `dev.nix` configuration at `.idx/dev.nix` with all required packages
- `spec/` directory - shellspec test directory structure

### 2.2. What's Missing

- `tools/pdf-generator/docs/` directory
- Documentation files that should be in that directory
- Index and navigation structure

### 2.3. Key Information from All Sources

**From Scratch Files** - The scratch file reveals an iterative design process across 11 major evolution points:

1. **Evolution through multiple iterations** - Plan was refined based on:
   - Progress indicators with animated spinners (lines 37-82)
   - Dewey Decimal prefix system for flat PDF storage (lines 86-131)
   - SQLite caching for performance (lines 166-224)
   - Parallel processing recommendations (lines 133-162)
   - Filter-native approach using Pandoc ecosystem (lines 428-757)
   - Run identification and resumption capabilities (lines 1473-1591)
   - Comprehensive statistics tracking
   - `constants.sh` for ANSI codes used in error messages and outputs
   - `constants.sh` is used by both pdf-generator and shellspec tests

2. **Final system architecture includes**:
   - SQLite cache in WAL mode (`prerendered/cache.sqlite`)
   - Tables: `runs`, `generated_pdfs`, `source_files`, `dependencies`
   - Dynamic filter detection and chain building
   - Dewey Decimal naming (100, 100.010, 100.800, etc.)
   - Master index with Mermaid diagram and hyperlinked ToC
   - Run statistics and efficiency metrics
   - RUN_ID in ULID format (not timestamp-based)

3. **Prerequisites finalized for IDX**:
   - Pandoc with extensive filter suite
   - TeX Live (scheme-medium with plantuml package)
   - PlantUML executable
   - Mermaid CLI
   - SQLite
   - Python 3 with uv for pip-based filters
   - Filters: plantuml, mermaid, dbml, fignos, tablenos, secnos, imagine, include
   - shellspec for BDD/TDD testing framework (already in dev.nix and implemented in IDX)

4. **Script features** (from final iteration):
   - Argument processing: `--help`, `--force`, `--resume`
   - `--clean RUN_ID [RUN_ID...]` - Clean specific run(s), requires confirmation
   - `--drop` - Clear all output artefacts and cache, requires confirmation
   - `--dry-run` - Preview mode without generating files
   - `--verbose` - Detailed output mode
   - Run identification with ULID format RUN_ID
   - Checkpoint-based resumption
   - Statistics tracking: dirs processed/skipped, files by type, timings, efficiency
   - Rich `_index.md` with run report

### 2.4. From Existing Implementation

**bin/pdf-generator** (v6 - 154 lines):
- Already uses `pndcgn_` prefix for functions and variables
- Configuration system with TOML file (`pdf-generator.toml`)
- Argument parsing: `--init`, `--force`, `--resume`, `--clean`, `--dry-run`, `--verbose`, `--help`
- SQLite database with WAL mode
- Database tables: `runs`, `generated_pdfs`
- Run tracking with timestamp-based IDs (not yet ULID)
- Trap handlers for cleanup on interrupt
- Uses `printf` for output (not echo)
- Stub functions: `pndcgn_initialize()`, `pndcgn_start_run()`, `pndcgn_process_directories()`, `pndcgn_finish_run()`

**src/constants.sh** (31 lines):
- ANSI color codes using CSI (Control Sequence Introducer) pattern
- Defined: `RED`, `BOLD`, `RESET`, `B_RED` (Bold Red)
- Uses `$'\033['` syntax for escape sequences
- Designed for sourcing by both application and tests

**Key Observations**:
- Implementation already started with v6 tag
- Core structure exists but functions are stubs
- Naming convention (`pndcgn_`) already in use
- Configuration approach uses TOML files
- Database schema partially implemented

### 2.5. BDD/TDD Requirements

The project follows Behavior-Driven Development (BDD) and Test-Driven Development (TDD) practices:

1. **Requirements Specification**:
   - User stories showing desired and undesired behaviors
   - Clear acceptance criteria
   - Testable scenarios

2. **Testing Framework**:
   - shellspec for shell script BDD/TDD testing
   - System tests validating requirements
   - Feature/unit tests validating implementation

3. **Test Structure**:
   - System tests map to requirements documentation
   - Feature/unit tests map to implementation plan
   - Shared `constants.sh` between production and test code

## 3. Documentation Standards

### 3.1. AI-GUIDELINES Compliance

All documentation must comply with:

- **[AI-GUIDELINES.md](../../AI-GUIDELINES.md)** - Core principles and orchestration policy
- **[AI-GUIDELINES/Documentation/010-documentation-standards.md](../../AI-GUIDELINES/Documentation/010-documentation-standards.md)** - Formatting and structure
- **WCAG 2.1 AA accessibility standards** - Color contrast, alt text, semantic structure
- **Junior developer clarity** - Clear, actionable, suitable for junior developers

### 3.2. File Naming Convention

All documentation files MUST use 3-digit prefixes in multiples of 10:

**Core Documentation:**
- `000-index.md` - Documentation index
- `010-overview.md` - Project overview and core objectives
- `020-requirements.md` - BDD requirements as user stories
- `030-installation.md` - Installation and setup guide
- `040-user-guide.md` - User guide with CLI options and workflows
- `050-architecture.md` - Technical architecture
- `060-filters.md` - Pandoc filter ecosystem
- `070-dewey-decimal.md` - Dewey Decimal naming algorithm
- `080-statistics.md` - Statistics and resumption system

**Testing Documentation:**
- `100-system-test-plan.md` - System tests for documented requirements
- `110-implementation-plan.md` - Implementation plan with requirements references
- `120-feature-unit-test-plan.md` - Feature and unit tests for implementation

**Supporting Documentation:**
- `200-constants.md` - ANSI color codes and constants specification

**Tool Root Documentation:**
- `README.md` - Project README (at `tools/pdf-generator/README.md`, not in docs/)

**Development History:**
- `900-changelog.md` - Evolution history

### 3.3. Formatting Requirements

Per AI-GUIDELINES/Documentation/010-documentation-standards.md:

- **Plain H1 headings**: No HTML anchors (e.g., `# Document Title`)
- **Numbered headings**: All headings below H1 must be numbered (1, 1.1, 1.1.1)
- **Table of Contents**: Collapsible TOC in `<details>` tags immediately after title
- **Navigation footer**: Format: `[← Previous](path) | [↑ Top](#anchor) | [Next →](path)`
- **Code blocks**: All code blocks must specify language (use `log` for plain text)
- **Links**: Use markdown syntax `[text](url)`
- **Accessibility**: WCAG 2.1 AA compliance, high-contrast diagrams

## 4. Proposed Document Structure

### 4.1. Core Documentation Files

Create `tools/pdf-generator/docs/` with the following files:

**4.1.1. Index and Overview**
- `000-index.md` - Documentation index with navigation
- `010-overview.md` - Project overview, core objectives, feature summary

**4.1.2. Requirements and User Documentation**
- `020-requirements.md` - BDD requirements as user stories with acceptance criteria
- `030-installation.md` - Installation guide (IDX + manual)
- `040-user-guide.md` - Comprehensive user guide with CLI options and workflows

**4.1.3. Technical Documentation**
- `050-architecture.md` - System architecture, SQLite schema, caching
- `060-filters.md` - Pandoc filter ecosystem and integration
- `070-dewey-decimal.md` - Dewey Decimal algorithm specification
- `080-statistics.md` - Statistics tracking and resumption system

**4.1.4. Testing Documentation**
- `100-system-test-plan.md` - System tests mapped to requirements (shellspec)
- `110-implementation-plan.md` - Implementation plan with requirements references
- `120-feature-unit-test-plan.md` - Feature/unit tests for implementation (shellspec)

**4.1.5. Supporting Documentation**
- `200-constants.md` - ANSI color codes and constants specification

**4.1.6. Tool Root Documentation**
- `README.md` - Tool README at `tools/pdf-generator/README.md` (overview + key info with links to detailed docs)

**4.1.7. Development History**
- `900-changelog.md` - Evolution history extracted from scratch files

### 4.2. Content Sources

**Primary Sources**:
- `dot-scratch/scratch#01.md` lines 1-1591 (comprehensive conversation history)
- `dot-scratch/update-prerequisites-for-idx.md` (IDX-specific prerequisites)
- **`tools/pdf-generator/bin/pdf-generator`** (existing implementation, 154 lines)
- **`tools/pdf-generator/src/constants.sh`** (existing constants, 31 lines)

**Reference Sources**:
- Current `dev.nix` configuration at `.idx/dev.nix`
- `AI-GUIDELINES/Documentation/010-documentation-standards.md`

**Content Extraction Approach**:
- Line references serve as **reference points** for context and technical decisions
- **Existing code provides implementation details and actual patterns used**
- Create **fresh documentation** synthesizing information from all sources
- Do not extract verbatim; instead, create clear, structured documentation suitable for junior developers
- Preserve technical decisions and rationale from scratch file discussions
- Document actual implementation patterns from existing bin/pdf-generator script
- Use actual constants from existing src/constants.sh file

### 4.3. Directory Structure

```log
tools/pdf-generator/
├── project-restoration/
│   └── 010-recovery-plan.md           # This document
├── docs/
│   ├── 000-index.md                   # Documentation index
│   ├── 010-overview.md                # Overview and objectives
│   ├── 020-requirements.md            # BDD requirements (user stories)
│   ├── 030-installation.md            # Installation guide
│   ├── 040-user-guide.md              # User guide
│   ├── 050-architecture.md            # Technical architecture
│   ├── 060-filters.md                 # Filter ecosystem
│   ├── 070-dewey-decimal.md           # Naming algorithm
│   ├── 080-statistics.md              # Statistics system
│   ├── 100-system-test-plan.md        # System tests (shellspec)
│   ├── 110-implementation-plan.md     # Implementation plan
│   ├── 120-feature-unit-test-plan.md  # Feature/unit tests (shellspec)
│   ├── 200-constants.md               # ANSI constants specification
│   └── 900-changelog.md               # Evolution history
├── bin/
├── spec/                              # shellspec tests (exists)
├── src/                               # Source code (exists)
│   └── constants.sh                   # ANSI codes and constants
├── README.md                          # Tool README (overview + links to docs/)
└── CHANGELOG.md                       # Tool changelog
```

## 5. Document Specifications

### 5.1. 000-index.md

**Purpose**: Central documentation index with navigation to all documents

**Key Sections**:
1. Introduction
2. Quick Start
3. Documentation Structure (links to all docs)
4. For Specific Tasks (task-oriented quick reference)
5. Document Formatting Standards
6. Navigation

**Content Source**: Custom synthesis based on AI-GUIDELINES/000-index.md pattern

### 5.2. 010-overview.md

**Purpose**: High-level project overview and core objectives

**Key Sections**:
1. Introduction
2. Core Objective
3. System Features Overview
4. Design Philosophy
5. Prerequisites Summary
6. Quick Start
7. Navigation

**Content Source**: Lines 1-5, 330-333 from scratch#01.md

### 5.3. 020-requirements.md

**Purpose**: BDD requirements specification as user stories

**Key Sections**:
1. Introduction
2. Requirements Methodology (BDD)
3. User Stories
   - Story format: As a [role], I want [feature], So that [benefit]
   - Acceptance criteria
   - Examples of desired behavior
   - Examples of undesired behavior
4. Story Categories
   - Installation and setup
   - PDF generation
   - Caching and resumption
   - CLI operations
   - Output and reporting
5. Traceability Matrix (Story ID to Test ID)
6. Navigation

**Content Source**: Derived from scratch#01.md features, structured as BDD stories

### 5.4. 030-installation.md

**Purpose**: Complete installation guide for all platforms

**Key Sections**:
1. Introduction
2. IDX Platform Installation (Recommended)
   - dev.nix configuration
   - Workspace setup
3. Manual Installation
   - Pandoc
   - TeX Live
   - PlantUML
   - Mermaid CLI
   - SQLite
   - Python with uv
4. Verification Steps
5. Troubleshooting
6. Navigation

**Content Source**: Lines 336-373, 428-476, 873-976 from scratch#01.md

### 5.5. 040-user-guide.md

**Purpose**: Comprehensive user guide with CLI options, workflows, and troubleshooting

**Key Sections**:
1. Introduction
2. Getting Started
   - Quick start
   - First run walkthrough
3. Command-Line Options
   - `--help` - Display usage information
   - `--force` - Force regeneration ignoring cache
   - `--clean RUN_ID [RUN_ID...]` - Remove specific run outputs (requires confirmation)
   - `--drop` - Clear all outputs and cache (requires confirmation)
   - `--dry-run` - Preview without generating
   - `--verbose` - Detailed output
   - `--resume` - Resume interrupted run
4. Common Workflows
   - First run
   - Incremental updates
   - Force regeneration
   - Resuming interrupted runs
   - Cleaning specific runs
   - Dropping all artefacts
5. Understanding Output
   - Progress indicators (animated spinners)
   - Index file structure
   - PDF naming (Dewey Decimal)
   - Statistics report
6. ULID Run Identifiers
   - Format specification
   - Shell script implementation (no external libraries)
7. Troubleshooting
8. Navigation

**Content Source**: Lines 1260-1468, 1473-1591 from scratch#01.md

**Note**: ULID implementation will be via shell script, not external libraries

### 5.6. 050-architecture.md

**Purpose**: Technical architecture and system design

**Key Sections**:
1. Introduction
2. System Architecture Overview
3. SQLite Cache Database
   - Schema design
   - WAL mode configuration
   - Tables: runs, generated_pdfs, source_files, dependencies
4. Parallel Processing
   - Process management
   - Concurrency handling
5. Caching Strategy
   - Hash-based change detection
   - Dependency tracking
   - Resume-on-failure
6. Output Structure
7. Navigation

**Content Source**: Lines 166-224, 374-422 from scratch#01.md

### 5.7. 060-filters.md

**Purpose**: Pandoc filter ecosystem documentation

**Key Sections**:
1. Introduction
2. Filter-Native Architecture
3. Available Filters
   - Diagram filters (plantuml, mermaid, dbml)
   - Cross-reference filters (fignos, tablenos, secnos)
   - Content filters (imagine, include)
4. Dynamic Filter Chain
5. Filter Discovery Process
6. Adding New Filters
7. Navigation

**Content Source**: Lines 428-757, 762-842 from scratch#01.md

### 5.8. 070-dewey-decimal.md

**Purpose**: Dewey Decimal naming algorithm specification

**Key Sections**:
1. Introduction
2. Algorithm Overview
3. Naming Strategy
4. Hierarchical Numbering
   - Top-level directories
   - Sub-directories
   - Numbering logic
5. Sorting and Stability
6. Examples
7. Navigation

**Content Source**: Lines 86-131 from scratch#01.md

### 5.9. 080-statistics.md

**Purpose**: Statistics tracking and resumption system

**Key Sections**:
1. Introduction
2. ULID Run Identification
3. Statistics Tracked
   - Directory counts
   - File counts by type
   - Processing timings
   - Cache efficiency
4. Resumption System
   - Checkpoint mechanism
   - State persistence
   - Resume workflow
5. Index File Report
6. Navigation

**Content Source**: Lines 1473-1591 from scratch#01.md

### 5.10. 100-system-test-plan.md

**Purpose**: System-level tests validating requirements (shellspec)

**Key Sections**:
1. Introduction
2. Testing Framework (shellspec)
3. Test Structure
4. Requirements Coverage
   - Test cases mapped to requirements (traceability)
   - Desired behavior validation
   - Undesired behavior prevention
5. System Test Scenarios
   - Installation verification
   - PDF generation pipeline
   - Caching behavior
   - CLI argument handling
   - Resumption and recovery
   - Output validation
6. Test Execution
7. Navigation

**Content Source**: Derived from requirements, following BDD Given-When-Then format

### 5.11. 110-implementation-plan.md

**Purpose**: Implementation plan with requirements references

**Key Sections**:
1. Introduction
2. Requirements Traceability
3. Implementation Phases
   - Phase 1: Core infrastructure
   - Phase 2: PDF generation
   - Phase 3: Caching system
   - Phase 4: Statistics and resumption
   - Phase 5: CLI and user interface
4. Component Design
   - constants.sh specification
   - Main script structure
   - SQLite schema implementation
   - Filter chain implementation
5. Implementation Tasks
   - Task breakdown
   - Dependencies
   - Requirements mapping
6. Navigation

**Content Source**: Synthesized from scratch#01.md with requirements mapping

### 5.12. 120-feature-unit-test-plan.md

**Purpose**: Feature and unit tests for implementation (shellspec)

**Key Sections**:
1. Introduction
2. Test Philosophy (TDD/BDD)
3. Feature Tests
   - Feature-level behavior validation
   - Integration scenarios
   - Mapped to implementation plan
4. Unit Tests
   - Function-level tests
   - Edge cases
   - Error handling
5. Test Organization
   - spec/ directory structure
   - Test naming conventions
   - Shared fixtures and helpers
6. constants.sh Testing
   - ANSI code validation
   - Usage in tests
7. Test Execution
8. Navigation

**Content Source**: Derived from implementation plan, following BDD patterns

### 5.13. 200-constants.md

**Purpose**: ANSI color codes and constants specification

**Key Sections**:
1. Introduction
2. Purpose and Usage
3. ANSI Color Codes
   - Error messages
   - Success messages
   - Warning messages
   - Info messages
   - Progress indicators
4. Spinner Characters
5. Format Constants
6. Usage in Production Code
7. Usage in Tests
8. Accessibility Considerations
9. Navigation

**Content Source**: Derived from scratch#01.md progress indicator sections

### 5.14. README.md

**Location**: `tools/pdf-generator/README.md` (tool root, not in docs/)

**Purpose**: Tool README combining overview with key information and links to detailed documentation

**Key Sections**:
1. Project Overview
2. Purpose and Goals  
3. Key Features (summary)
4. Quick Start
5. Documentation (with links to docs/000-index.md)
6. Prerequisites (summary with link to docs/030-installation.md)
7. Testing (with link to docs/100-system-test-plan.md)
8. Usage Examples (with link to docs/040-user-guide.md)
9. Contributing
10. License

**Content Source**: Synthesis of all documentation for tool-level README

**Note**: This is separate from project root README update, which is a future task after tool implementation

### 5.15. 900-changelog.md

**Purpose**: Evolution history from scratch conversation

**Key Sections**:
1. Introduction
2. Version History
   - v0.1: Initial aggregation concept
   - v0.2: Progress indicators (lines 37-82)
   - v0.3: Dewey Decimal system (lines 86-131)
   - v0.4: Improvement recommendations (lines 133-162)
   - v0.5: SQLite caching (lines 166-224)
   - v0.6: Filter-native approach (lines 428-757)
   - v0.7: DBML integration (lines 762-842)
   - v0.8: Python uv package manager (lines 925-995)
   - v0.9: Complete script (lines 997-1243)
   - v1.0: Argument processing (lines 1260-1468)
   - v1.1: Statistics and resumption (lines 1473-1591)
3. Navigation

**Content Source**: Entire scratch#01.md organized chronologically

## 6. Key Sections to Document

### 6.1. System Architecture Components

From scratch#01.md conversation:

1. **SQLite Cache** (lines 166-224)
   - WAL mode for concurrent access
   - Tables: runs, generated_pdfs, source_files, dependencies
   - Hash-based change detection
   - Dependency tracking

2. **Parallel Processing** (lines 133-162)
   - Multi-core utilization
   - Process management
   - Output coordination

3. **Dynamic Filter Chain** (lines 428-757)
   - Filter discovery
   - Automatic integration
   - Extensibility

### 6.2. Script Features

From final iteration (lines 1473-1591):

1. **Argument Processing**
   - `--help`, `--force`, `--clean`, `--dry-run`, `--verbose`, `--resume`

2. **Run Identification**
   - Unique RUN_ID (ULID format)
   - Status tracking (in_progress, completed, aborted)

3. **Statistics Tracking**
   - Directories processed/skipped
   - Files by type and extension
   - Processing timings
   - Cache efficiency metrics

4. **Output Generation**
   - Master index with Mermaid diagram
   - Hyperlinked PDF list
   - Statistics report

### 6.3. Evolution Timeline

**From scratch#01.md conversation:**

1. Initial simple aggregation concept
2. Addition of progress indicators (lines 37-82)
3. Dewey Decimal naming system (lines 86-131)
4. Improvement suggestions with pros/cons (lines 133-162)
5. SQLite caching discussion (lines 166-224)
6. Filter-native strategy (lines 428-757)
7. DBML filter integration (lines 762-842)
8. Python package management with uv (lines 925-995)
9. Complete script implementation (lines 997-1243)
10. Argument processing (lines 1260-1468)
11. Statistics and resumption (lines 1473-1591)

## 7. Implementation Approach

**Recovery Strategy**: Create documentation by **dependency order**, committing after each dependency level is complete. This ensures requirements exist before tests, and core documentation exists before supporting documentation.

**Commit Strategy**: One commit per dependency level (not per document).

### 7.1. Phase 1: Directory Setup

1. Create `tools/pdf-generator/docs/` directory
2. Set up version control tracking
3. Prepare for dependency-ordered creation

### 7.2. Dependency Level 1: Foundation Documents

**Dependencies**: None (these are prerequisites for all other docs)

1. Create `000-index.md` - Documentation index and navigation
2. Create `010-overview.md` - System summary and purpose
3. Create `200-constants.md` - ANSI constants specification (needed by implementation and tests)

**Commit**: "docs: Add foundation documents (index, overview, constants)"

### 7.3. Dependency Level 2: Requirements

**Dependencies**: 010-overview.md (for context)

1. Create `020-requirements.md` - BDD user stories with acceptance criteria

**Commit**: "docs: Add requirements specification (BDD user stories)"

### 7.4. Dependency Level 3: User-Facing Documentation

**Dependencies**: 020-requirements.md (features), 200-constants.md (for examples)

1. Create `030-installation.md` - Installation guide
2. Create `040-user-guide.md` - CLI and workflow documentation

**Commit**: "docs: Add user-facing documentation (installation, user guide)"

### 7.5. Dependency Level 4: Technical Documentation

**Dependencies**: 020-requirements.md (technical requirements), 040-user-guide.md (user-facing features)

1. Create `050-architecture.md` - System architecture and design
2. Create `060-filters.md` - Filter ecosystem
3. Create `070-dewey-decimal.md` - Naming algorithm
4. Create `080-statistics.md` - Statistics and resumption

**Commit**: "docs: Add technical architecture documentation"

### 7.6. Dependency Level 5: Implementation Planning

**Dependencies**: 020-requirements.md (stories to implement), 050-architecture.md (design to implement)

1. Create `110-implementation-plan.md` - Implementation plan with requirements references

**Commit**: "docs: Add implementation plan"

### 7.7. Dependency Level 6: Testing Documentation

**Dependencies**: 020-requirements.md (for system tests), 110-implementation-plan.md (for feature/unit tests), 200-constants.md (test fixtures)

1. Create `100-system-test-plan.md` - System tests mapped to requirements
2. Create `120-feature-unit-test-plan.md` - Feature/unit tests for implementation

**Commit**: "docs: Add testing documentation (system and feature/unit tests)"

### 7.8. Dependency Level 7: Tool README and History

**Dependencies**: All docs (README summarizes and links to them), scratch file (for changelog)

1. Create `README.md` - Tool README at tool root (overview + links)
2. Create `900-changelog.md` - Evolution history

**Commit**: "docs: Add tool README and changelog"

### 7.9. Phase 8: Quality Assurance and Review

**Dependencies**: All documentation complete

1. Validate all internal links
2. Check code block language tags
3. Verify navigation footers
4. Confirm WCAG 2.1 AA compliance
5. Test against current dev.nix configuration
6. Verify shellspec test framework references
7. Validate requirements-to-tests traceability
8. Review for junior developer clarity
9. Single reviewer sign-off (user review)

**Commit**: "docs: QA fixes and final review updates"

## 8. Validation Checklist

### 8.1. Content Validation

- [ ] All content extracted from scratch#01.md accurately
- [ ] Prerequisites match current dev.nix configuration
- [ ] Code examples are properly formatted
- [ ] Technical accuracy verified
- [ ] Junior developer clarity achieved

### 8.2. Format Validation

- [ ] All files use 3-digit multiples of 10 naming
- [ ] Plain H1 headings (no HTML anchors)
- [ ] Numbered headings (1, 1.1, 1.1.1) below H1
- [ ] Collapsible TOC in all documents
- [ ] Navigation footer in all documents
- [ ] All code blocks specify language
- [ ] All links use markdown syntax

### 8.3. Technical Validation

- [ ] SQLite schema documentation accurate
- [ ] Dewey Decimal algorithm explanation correct
- [ ] Filter chain mechanism documented
- [ ] Statistics tracking specifications complete
- [ ] CLI options match script implementation
- [ ] Prerequisites list complete and accurate

## 9. Success Criteria

- Complete `tools/pdf-generator/docs/` directory created
- All 14 documentation files in docs/ present and complete
  - Core: 000, 010, 020, 030, 040, 050, 060, 070, 080 (9 docs)
  - Testing: 100, 110, 120 (3 docs)
  - Supporting: 200 (1 doc)
  - History: 900 (1 doc)
- Tool README at `tools/pdf-generator/README.md` (1 doc at tool root)
- **Total: 14 docs in docs/ + 1 README.md at tool root = 15 documents**
- BDD/TDD documentation complete with shellspec references
- Requirements-to-tests traceability established
- constants.sh specification documented
- ULID format RUN_ID documented with shell script implementation
- `--clean` and `--drop` commands properly specified
- Documentation accurately reflects scratch file content and design decisions
- Content created fresh (not verbatim extraction) using line references as context
- Prerequisites match current dev.nix (shellspec confirmed present)
- All code blocks properly formatted with language tags
- Navigation structure complete with working links
- Follows AI-GUIDELINES documentation standards
- WCAG 2.1 AA accessibility compliance
- Junior developer clarity achieved throughout
- Created in dependency order with commits per level
- Single reviewer (user) sign-off obtained

## 10. Navigation

[↑ Top](#pdf-generator-documentation-recovery-plan)
