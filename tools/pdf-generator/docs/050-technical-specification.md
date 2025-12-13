# Technical Specification

<details>
<summary>Table of Contents</summary>

- [1. System Architecture](#1-system-architecture)
  - [1.1. High-Level Overview](#11-high-level-overview)
  - [1.2. Component Diagram](#12-component-diagram)
  - [1.3. Data Flow](#13-data-flow)
- [2. Core Components](#2-core-components)
  - [2.1. Main Controller](#21-main-controller)
  - [2.2. Database Manager](#22-database-manager)
  - [2.3. Processing Engine](#23-processing-engine)
  - [2.4. Utilities](#24-utilities)
- [3. Data Structures](#3-data-structures)
  - [3.1. Database Schema](#31-database-schema)
  - [3.2. Run State](#32-run-state)
  - [3.3. Fingerprints](#33-fingerprints)
- [4. Algorithms](#4-algorithms)
  - [4.1. Fingerprint Generation](#41-fingerprint-generation)
  - [4.2. Cache Lookup](#42-cache-lookup)
  - [4.3. Run Resumption](#43-run-resumption)
- [5. External Interfaces](#5-external-interfaces)
  - [5.1. Pandoc Integration](#51-pandoc-integration)
  - [5.2. SQLite Integration](#52-sqlite-integration)
  - [5.3. File System](#53-file-system)
- [6. Configuration System](#6-configuration-system)
  - [6.1. TOML Configuration](#61-toml-configuration)
  - [6.2. Environment Variables](#62-environment-variables)
  - [6.3. Precedence Rules](#63-precedence-rules)
- [7. Error Handling](#7-error-handling)
  - [7.1. Error Categories](#71-error-categories)
  - [7.2. Recovery Strategies](#72-recovery-strategies)
- [Navigation](#navigation)

</details>

---

**Compliant with**: AI-GUIDELINES.md v1.0

## 1. System Architecture

### 1.1. High-Level Overview

**pndcgn** is a batch document conversion tool built on Unix principles:
- Single-purpose: Convert markdown to various formats via pandoc
- Composable: Works as part of larger pipelines
- Stateful: Maintains SQLite cache for idempotency and resumability

**Key architectural decisions**:
- **Shell-based**: Portable across Unix systems, minimal dependencies
- **SQLite for state**: Robust, ACID-compliant, supports concurrent access
- **Fingerprint-based caching**: Skip unchanged files, fast incremental updates
- **ULID run identifiers**: Sortable, unique, timestamp-embedded

### 1.2. Component Diagram

```log
┌──────────────────────────────────────────────────────────────┐
│                        pdf-generator                          │
│                      (Main Controller)                        │
└───────┬──────────────────┬──────────────────┬────────────────┘
        │                  │                  │
        ▼                  ▼                  ▼
┌───────────────┐ ┌────────────────┐ ┌──────────────────┐
│   Constants   │ │   Database     │ │   Processing     │
│  (constants.  │ │  (database.sh) │ │  (processing.sh) │
│     sh)       │ │                │ │                  │
└───────────────┘ └────────┬───────┘ └──────┬───────────┘
                           │                │
                           ▼                ▼
                  ┌────────────────┐ ┌──────────────┐
                  │   SQLite DB    │ │   Pandoc     │
                  │ (cache.sqlite) │ │              │
                  └────────────────┘ └──────────────┘
                           │                │
                           ▼                ▼
                  ┌────────────────────────────┐
                  │      File System           │
                  │  (Input MD, Output PDFs)   │
                  └────────────────────────────┘
```

### 1.3. Data Flow

**Normal run** (no cache):
```log
1. User invokes: pdf-generator source_dir
2. Main controller parses arguments
3. Generate new RUN_ID (ULID)
4. Initialize run in database
5. Discover markdown files (find)
6. For each file:
   a. Compute fingerprint
   b. Check cache (no match)
   c. Invoke pandoc
   d. Store output path and fingerprint
7. Mark run complete
8. Display statistics
```

**Cached run** (files unchanged):
```log
1. User invokes: pdf-generator source_dir
2. Main controller parses arguments
3. Generate new RUN_ID
4. Initialize run in database
5. Discover markdown files
6. For each file:
   a. Compute fingerprint
   b. Check cache (match found!)
   c. Verify output exists
   d. Skip processing, log cache hit
7. Mark run complete
8. Display statistics (X skipped)
```

**Resume run**:
```log
1. User invokes: pdf-generator --resume {RUN_ID}
2. Load run state from database
3. Discover markdown files
4. For each file:
   a. Check if already processed in this run
   b. If yes, skip
   c. If no, process normally
5. Update run status
6. Display statistics
```

---

## 2. Core Components

### 2.1. Main Controller

**File**: `bin/pdf-generator`

**Responsibilities**:
- Parse command-line arguments
- Load configuration (TOML + environment)
- Initialize database connection
- Orchestrate processing workflow
- Handle errors and cleanup

**Key functions**:
```bash
main()                     # Entry point
pndcgn_parse_args()       # Argument parsing
pndcgn_load_config()      # Configuration loading
pndcgn_initialize()       # Database setup
pndcgn_start_run()        # Begin processing run
pndcgn_process_files()    # Main processing loop
pndcgn_finish_run()       # Finalization and stats
```

### 2.2. Database Manager

**File**: `src/database.sh`

**Responsibilities**:
- SQLite connection management
- Schema creation and migration
- CRUD operations for runs and files
- Transaction management
- WAL checkpoint coordination

**Key functions**:
```bash
pndcgn_db_init()          # Create schema
pndcgn_db_start_run()     # Insert run record
pndcgn_db_get_fingerprint() # Cache lookup
pndcgn_db_store_pdf()     # Store generated file
pndcgn_db_finish_run()    # Mark run complete
pndcgn_db_clean()         # Remove old entries
pndcgn_db_drop()          # Nuclear option
```

### 2.3. Processing Engine

**File**: `src/processing.sh`

**Responsibilities**:
- File discovery (find markdown files)
- Fingerprint computation
- Pandoc invocation
- Output validation
- Progress reporting

**Key functions**:
```bash
pndcgn_find_files()       # Discover markdown files
pndcgn_compute_fingerprint() # Generate fingerprint
pndcgn_convert_file()     # Invoke pandoc
pndcgn_verify_output()    # Check conversion success
pndcgn_report_progress()  # Display progress
```

### 2.4. Utilities

**File**: `src/utilities.sh`

**Responsibilities**:
- ULID generation
- Path manipulation
- String formatting
- Date/time operations
- Logging helpers

**Key functions**:
```bash
pndcgn_generate_ulid()    # Create run ID
pndcgn_resolve_path()     # Canonicalize paths
pndcgn_log_info()         # Logging with colors
pndcgn_log_error()        # Error logging
pndcgn_format_duration()  # Human-readable time
```

---

## 3. Data Structures

### 3.1. Database Schema

**Table: runs**
```sql
CREATE TABLE IF NOT EXISTS runs (
    run_id TEXT PRIMARY KEY,      -- ULID
    start_time INTEGER NOT NULL,  -- Unix timestamp
    end_time INTEGER,              -- Unix timestamp (NULL if incomplete)
    source_dir TEXT NOT NULL,      -- Input directory
    target_dir TEXT NOT NULL,      -- Output parent directory
    output_type TEXT NOT NULL,     -- pdf, epub, html, etc.
    status TEXT NOT NULL,          -- running, complete, failed, interrupted
    dry_run INTEGER NOT NULL,      -- 0 or 1
    files_total INTEGER DEFAULT 0,
    files_processed INTEGER DEFAULT 0,
    files_skipped INTEGER DEFAULT 0,
    files_failed INTEGER DEFAULT 0
);
```

**Table: generated_pdfs**
```sql
CREATE TABLE IF NOT EXISTS generated_pdfs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    run_id TEXT NOT NULL,
    source_path TEXT NOT NULL,     -- Absolute path to .md file
    output_path TEXT NOT NULL,     -- Absolute path to output file
    fingerprint TEXT NOT NULL,     -- SHA256-based fingerprint
    file_size INTEGER NOT NULL,    -- Source file size (bytes)
    mtime INTEGER NOT NULL,        -- Source modification time
    generated_at INTEGER NOT NULL, -- Unix timestamp
    FOREIGN KEY (run_id) REFERENCES runs(run_id) ON DELETE CASCADE
);
```

**Indexes**:
```sql
CREATE INDEX IF NOT EXISTS idx_fingerprints 
    ON generated_pdfs(fingerprint);
CREATE INDEX IF NOT EXISTS idx_run_id 
    ON generated_pdfs(run_id);
CREATE INDEX IF NOT EXISTS idx_source_path 
    ON generated_pdfs(source_path);
```

### 3.2. Run State

**In-memory structure** (bash associative array):
```bash
declare -A pndcgn_run_state=(
    [run_id]="01HN7XJKQM3R8Y2VWSDP4T6FGZ"
    [source_dir]="/path/to/source"
    [target_dir]="/path/to/target"
    [output_type]="pdf"
    [output_dir]="/path/to/target/pndcgn/pdf-01HN7X..."
    [dry_run]="0"
    [verbose]="1"
    [force]="0"
    [files_total]="150"
    [files_processed]="42"
    [files_skipped]="108"
    [files_failed]="0"
)
```

### 3.3. Fingerprints

**Fingerprint format**:
```log
{size}:{mtime}:{sha256_partial}
```

**Example**:
```log
12345:1699564800:a3f5b9c2d8e1f4a7b6c3d2e9f1a8b4c7
```

**Components**:
- `size`: File size in bytes
- `mtime`: Modification time (Unix timestamp)
- `sha256_partial`: First 32 hex chars of SHA256 hash

**Rationale**:
- **Fast**: Only hash first 64KB of file
- **Sufficient**: Extremely low collision probability
- **Informative**: Includes size and mtime for quick checks

---

## 4. Algorithms

### 4.1. Fingerprint Generation

```bash
pndcgn_compute_fingerprint() {
    local file="$1"
    local size mtime sha256_partial fingerprint
    
    # Get file size and modification time
    size=$(stat -f%z "${file}" 2>/dev/null || stat -c%s "${file}")
    mtime=$(stat -f%m "${file}" 2>/dev/null || stat -c%Y "${file}")
    
    # Compute SHA256 of first 64KB
    sha256_partial=$(head -c 65536 "${file}" | sha256sum | cut -d' ' -f1)
    sha256_partial="${sha256_partial:0:32}"  # First 32 hex chars
    
    # Combine into fingerprint
    fingerprint="${size}:${mtime}:${sha256_partial}"
    
    printf "%s" "${fingerprint}"
}
```

**Time complexity**: O(1) - only reads first 64KB regardless of file size

### 4.2. Cache Lookup

```bash
pndcgn_cache_lookup() {
    local fingerprint="$1"
    local output_type="$2"
    local cached_output
    
    # Query database for matching fingerprint
    cached_output=$(sqlite3 "${PNDCGN_DB}" \
        "SELECT output_path FROM generated_pdfs
         WHERE fingerprint = '${fingerprint}'
         AND output_path LIKE '%.${output_type}'
         ORDER BY generated_at DESC
         LIMIT 1;")
    
    # Verify output file still exists
    if [[ -n "${cached_output}" && -f "${cached_output}" ]]; then
        printf "%s" "${cached_output}"
        return 0
    fi
    
    return 1
}
```

**Time complexity**: O(log n) - indexed query on fingerprint

### 4.3. Run Resumption

```bash
pndcgn_resume_run() {
    local run_id="$1"
    local processed_files
    
    # Load run state from database
    pndcgn_db_load_run_state "${run_id}"
    
    # Get list of already-processed files
    processed_files=$(sqlite3 "${PNDCGN_DB}" \
        "SELECT source_path FROM generated_pdfs
         WHERE run_id = '${run_id}';")
    
    # Process remaining files
    while IFS= read -r source_file; do
        # Skip if already processed
        if printf "%s" "${processed_files}" | grep -qF "${source_file}"; then
            continue
        fi
        
        # Process normally
        pndcgn_convert_file "${source_file}"
    done < <(pndcgn_find_files "${source_dir}")
}
```

---

## 5. External Interfaces

### 5.1. Pandoc Integration

**Invocation**:
```bash
pandoc \
    --from markdown \
    --to "${output_type}" \
    --output "${output_path}" \
    --standalone \
    "${source_path}"
```

**Error handling**:
- Non-zero exit code: Conversion failed
- Missing output file: Pandoc didn't write output
- Stderr captured and logged

**Supported formats** (subset):
- `pdf` - Portable Document Format
- `epub` - Electronic Publication
- `html` - HTML5
- `docx` - Microsoft Word
- `odt` - OpenDocument Text
- `rtf` - Rich Text Format

### 5.2. SQLite Integration

**Connection string**:
```bash
PNDCGN_DB="${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn/cache.sqlite"
```

**WAL mode**:
```sql
PRAGMA journal_mode=WAL;
PRAGMA synchronous=NORMAL;
```

**Benefits**:
- Concurrent reads during writes
- Better performance
- Crash-safe

**Checkpointing**:
```bash
# Manual checkpoint (on --clean)
sqlite3 "${PNDCGN_DB}" "PRAGMA wal_checkpoint(RESTART);"
```

### 5.3. File System

**Directory creation**:
```bash
# Output directory
mkdir -p "${target_dir}/pndcgn/${output_type}-${run_id}"

# Database directory
mkdir -p "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn"
```

**File discovery**:
```bash
# Find all markdown files recursively
find "${source_dir}" -type f -name "*.md" -print0
```

**Path handling**:
- All paths converted to absolute
- Symlinks resolved with `realpath` or `readlink -f`
- Spaces in filenames handled with null-terminated strings (`-print0`, `read -r -d ''`)

---

## 6. Configuration System

### 6.1. TOML Configuration

**File**: `pdf-generator.toml` (in tool directory or `$XDG_CONFIG_HOME`)

**Schema**:
```toml
[general]
output_type = "pdf"
cache_location = "~/.local/state/pndcgn"

[processing]
parallel_jobs = 4     # Not yet implemented
verbose = false
dry_run = false

[database]
wal_mode = true
checkpoint_interval = 1000
```

**Parsing**: Simple line-by-line parsing with regex
```bash
output_type=$(grep '^output_type' pdf-generator.toml | cut -d'=' -f2 | tr -d ' "')
```

### 6.2. Environment Variables

**Supported variables**:
```bash
PNDCGN_CACHE_DIR          # Override cache location
PNDCGN_VERBOSE            # Enable verbose mode
PNDCGN_DRY_RUN            # Enable dry-run mode
XDG_STATE_HOME            # XDG base directory
```

**Usage**:
```bash
# Override cache location
export PNDCGN_CACHE_DIR="/custom/cache"
pdf-generator source_dir
```

### 6.3. Precedence Rules

**Highest to lowest**:
1. Command-line arguments (`--type pdf`)
2. Environment variables (`PNDCGN_VERBOSE=1`)
3. TOML configuration file (`output_type = "pdf"`)
4. Built-in defaults (hardcoded in script)

---

## 7. Error Handling

### 7.1. Error Categories

**Fatal errors** (exit immediately):
- Database connection failure
- Invalid run ID for `--resume` or `--finalize`
- Missing required dependencies (pandoc, sqlite3)
- Source directory doesn't exist

**Recoverable errors** (log and continue):
- Individual file conversion failure
- Output directory creation failure (try alternative)
- Fingerprint computation failure (skip file)

**Warnings** (log but don't affect processing):
- Cache entry without corresponding output file
- Stale database entries
- Configuration file parse errors (use defaults)

### 7.2. Recovery Strategies

**Database corruption**:
```bash
# Attempt to recover
sqlite3 "${PNDCGN_DB}" ".recover" > recovered.sql
sqlite3 recovered.db < recovered.sql

# If recovery fails, drop and recreate
pdf-generator --drop
```

**Interrupted runs**:
```bash
# Resume automatically
pdf-generator --resume {RUN_ID}
```

**Disk full**:
```bash
# Clean up cache
pdf-generator --clean

# Or specify different output location
pdf-generator source_dir /mnt/external
```

**Pandoc crashes**:
- Individual file failures logged
- Processing continues with remaining files
- Run marked as "partial" if any failures
- Use `--verbose` to identify problem files

---

## Navigation

[← User Guide](040-user-guide.md) | [↑ Top](#technical-specification) | [Database Schema →](060-database-schema.md)
