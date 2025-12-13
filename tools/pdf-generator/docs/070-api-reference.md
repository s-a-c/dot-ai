# API Reference

<details>
<summary>Table of Contents</summary>

- [1. Overview](#1-overview)
  - [1.1. Module Structure](#11-module-structure)
  - [1.2. Naming Conventions](#12-naming-conventions)
- [2. Main Controller (pdf-generator)](#2-main-controller-pdf-generator)
  - [2.1. Entry Point](#21-entry-point)
  - [2.2. Initialization](#22-initialization)
  - [2.3. Configuration](#23-configuration)
- [3. Database Module (database.sh)](#3-database-module-databasesh)
  - [3.1. Connection Management](#31-connection-management)
  - [3.2. Run Operations](#32-run-operations)
  - [3.3. File Operations](#33-file-operations)
- [4. Processing Module (processing.sh)](#4-processing-module-processingsh)
  - [4.1. File Discovery](#41-file-discovery)
  - [4.2. Conversion](#42-conversion)
  - [4.3. Fingerprinting](#43-fingerprinting)
- [5. Utilities Module (utilities.sh)](#5-utilities-module-utilitiessh)
  - [5.1. ULID Generation](#51-ulid-generation)
  - [5.2. Path Operations](#52-path-operations)
  - [5.3. Logging](#53-logging)
- [6. Constants Module (constants.sh)](#6-constants-module-constantssh)
  - [6.1. ANSI Colors](#61-ansi-colors)
  - [6.2. Exit Codes](#62-exit-codes)
- [Navigation](#navigation)

</details>

---

**Compliant with**: AI-GUIDELINES.md v1.0

## 1. Overview

### 1.1. Module Structure

**pndcgn** is organized into focused modules:

```log
bin/pdf-generator         Main controller (orchestration)
src/constants.sh          ANSI color constants
src/database.sh           SQLite operations
src/processing.sh         Conversion logic
src/utilities.sh          Helper functions
```

**Module loading**:
```bash
# Source modules in order
source "${PNDCGN_ROOT}/src/constants.sh"
source "${PNDCGN_ROOT}/src/utilities.sh"
source "${PNDCGN_ROOT}/src/database.sh"
source "${PNDCGN_ROOT}/src/processing.sh"
```

### 1.2. Naming Conventions

**Function naming**:
- Prefix: `pndcgn_` (namespace)
- Module hint: `pndcgn_db_*` (database), `pndcgn_log_*` (logging)
- Verb-noun pattern: `pndcgn_compute_fingerprint`, `pndcgn_store_file`

**Variable naming**:
- Lowercase with underscores: `source_dir`, `run_id`
- Constants: Uppercase: `PNDCGN_DB`, `PNDCGN_ROOT`
- Prefix for globals: `pndcgn_*`

---

## 2. Main Controller (pdf-generator)

### 2.1. Entry Point

#### `main()`

**Purpose**: Script entry point, orchestrates entire workflow

**Signature**:
```bash
main()
```

**Flow**:
1. Parse command-line arguments
2. Load configuration
3. Initialize database
4. Execute requested operation
5. Handle errors and cleanup

**Example**:
```bash
# Called automatically at script end
main
```

### 2.2. Initialization

#### `pndcgn_initialize()`

**Purpose**: Initialize database and verify prerequisites

**Signature**:
```bash
pndcgn_initialize()
```

**Returns**: 0 on success, non-zero on failure

**Side effects**:
- Creates database if missing
- Creates database directory
- Verifies required binaries (pandoc, sqlite3)

**Example**:
```bash
if ! pndcgn_initialize; then
    printf "Initialization failed\\n" >&2
    exit 1
fi
```

### 2.3. Configuration

#### `pndcgn_load_config()`

**Purpose**: Load configuration from TOML file and environment

**Signature**:
```bash
pndcgn_load_config()
```

**Returns**: 0 always (uses defaults on failure)

**Sets variables**:
- `pndcgn_output_type`
- `pndcgn_cache_dir`
- `pndcgn_verbose`
- `pndcgn_dry_run`

**Precedence**: CLI args > env vars > TOML > defaults

**Example**:
```bash
pndcgn_load_config
printf "Output type: %s\\n" "${pndcgn_output_type}"
```

---

## 3. Database Module (database.sh)

### 3.1. Connection Management

#### `pndcgn_db_init()`

**Purpose**: Initialize database schema

**Signature**:
```bash
pndcgn_db_init()
```

**Returns**: 0 on success, 1 on failure

**Side effects**:
- Creates `runs` table
- Creates `generated_pdfs` table
- Creates indexes
- Enables WAL mode

**Example**:
```bash
if pndcgn_db_init; then
    printf "Database initialized\\n"
fi
```

### 3.2. Run Operations

#### `pndcgn_db_start_run()`

**Purpose**: Insert new run record

**Signature**:
```bash
pndcgn_db_start_run <run_id> <source_dir> <target_dir> <output_type> <dry_run>
```

**Parameters**:
- `run_id` - ULID identifier
- `source_dir` - Absolute path to source
- `target_dir` - Absolute path to target
- `output_type` - Output format (pdf, epub, etc.)
- `dry_run` - 0 or 1

**Returns**: 0 on success, 1 on failure

**Example**:
```bash
run_id=$(pndcgn_generate_ulid)
pndcgn_db_start_run "${run_id}" "/home/user/docs" "/home/user/output" "pdf" 0
```

#### `pndcgn_db_finish_run()`

**Purpose**: Mark run as complete

**Signature**:
```bash
pndcgn_db_finish_run <run_id> <status> <files_total> <files_processed> <files_skipped> <files_failed>
```

**Parameters**:
- `run_id` - Run identifier
- `status` - `complete`, `failed`, `interrupted`, or `partial`
- `files_total` - Total files discovered
- `files_processed` - Files converted
- `files_skipped` - Files skipped (cached)
- `files_failed` - Files that failed

**Returns**: 0 on success, 1 on failure

**Example**:
```bash
pndcgn_db_finish_run "${run_id}" "complete" 150 10 140 0
```

### 3.3. File Operations

#### `pndcgn_db_get_fingerprint()`

**Purpose**: Look up cached file by fingerprint

**Signature**:
```bash
pndcgn_db_get_fingerprint <fingerprint> <output_type>
```

**Parameters**:
- `fingerprint` - Content fingerprint
- `output_type` - Desired output format

**Returns**: 0 if found, 1 if not found

**Stdout**: Output path if found

**Example**:
```bash
cached=$(pndcgn_db_get_fingerprint "${fingerprint}" "pdf")
if [[ -n "${cached}" && -f "${cached}" ]]; then
    printf "Cache hit: %s\\n" "${cached}"
fi
```

#### `pndcgn_db_store_file()`

**Purpose**: Store generated file metadata

**Signature**:
```bash
pndcgn_db_store_file <run_id> <source_path> <output_path> <fingerprint> <file_size> <mtime>
```

**Parameters**:
- `run_id` - Run identifier
- `source_path` - Absolute path to source
- `output_path` - Absolute path to output
- `fingerprint` - Content fingerprint
- `file_size` - Source size (bytes)
- `mtime` - Source mtime (Unix timestamp)

**Returns**: 0 on success, 1 on failure

**Example**:
```bash
pndcgn_db_store_file \
    "${run_id}" \
    "/home/user/docs/notes.md" \
    "/home/user/output/pndcgn/pdf-${run_id}/notes.pdf" \
    "12345:1699564800:a3f5b9c2d8e1f4a7b6c3d2e9f1a8b4c7" \
    12345 \
    1699564800
```

---

## 4. Processing Module (processing.sh)

### 4.1. File Discovery

#### `pndcgn_find_files()`

**Purpose**: Discover markdown files in directory

**Signature**:
```bash
pndcgn_find_files <directory>
```

**Parameters**:
- `directory` - Directory to search (recursively)

**Returns**: 0 always

**Stdout**: Null-terminated list of absolute paths

**Example**:
```bash
while IFS= read -r -d '' file; do
    printf "Found: %s\\n" "${file}"
done < <(pndcgn_find_files "/home/user/docs")
```

### 4.2. Conversion

#### `pndcgn_convert_file()`

**Purpose**: Convert single file using pandoc

**Signature**:
```bash
pndcgn_convert_file <source_path> <output_path> <output_type>
```

**Parameters**:
- `source_path` - Absolute path to source .md
- `output_path` - Absolute path for output
- `output_type` - Output format (pdf, epub, etc.)

**Returns**: 0 on success, 1 on failure

**Side effects**:
- Creates output directory if needed
- Invokes pandoc
- Logs conversion result

**Example**:
```bash
if pndcgn_convert_file "notes.md" "notes.pdf" "pdf"; then
    printf "Conversion successful\\n"
fi
```

### 4.3. Fingerprinting

#### `pndcgn_compute_fingerprint()`

**Purpose**: Compute content fingerprint

**Signature**:
```bash
pndcgn_compute_fingerprint <file_path>
```

**Parameters**:
- `file_path` - Path to file

**Returns**: 0 always

**Stdout**: Fingerprint string (format: `size:mtime:sha256_partial`)

**Example**:
```bash
fingerprint=$(pndcgn_compute_fingerprint "/home/user/docs/notes.md")
printf "Fingerprint: %s\\n" "${fingerprint}"
# Output: 12345:1699564800:a3f5b9c2d8e1f4a7b6c3d2e9f1a8b4c7
```

---

## 5. Utilities Module (utilities.sh)

### 5.1. ULID Generation

#### `pndcgn_generate_ulid()`

**Purpose**: Generate ULID identifier

**Signature**:
```bash
pndcgn_generate_ulid()
```

**Returns**: 0 always

**Stdout**: 26-character ULID string

**Example**:
```bash
run_id=$(pndcgn_generate_ulid)
printf "Run ID: %s\\n" "${run_id}"
# Output: 01HN7XJKQM3R8Y2VWSDP4T6FGZ
```

### 5.2. Path Operations

#### `pndcgn_resolve_path()`

**Purpose**: Convert to absolute path

**Signature**:
```bash
pndcgn_resolve_path <path>
```

**Parameters**:
- `path` - Relative or absolute path

**Returns**: 0 on success, 1 if path doesn't exist

**Stdout**: Absolute path

**Example**:
```bash
abs_path=$(pndcgn_resolve_path "../docs/notes.md")
printf "Absolute: %s\\n" "${abs_path}"
# Output: /home/user/docs/notes.md
```

### 5.3. Logging

#### `pndcgn_log_info()`

**Purpose**: Log informational message

**Signature**:
```bash
pndcgn_log_info <message>
```

**Parameters**:
- `message` - Message to log

**Returns**: 0 always

**Side effects**: Writes to stdout with color

**Example**:
```bash
pndcgn_log_info "Processing file: notes.md"
```

#### `pndcgn_log_error()`

**Purpose**: Log error message

**Signature**:
```bash
pndcgn_log_error <message>
```

**Parameters**:
- `message` - Error message

**Returns**: 0 always

**Side effects**: Writes to stderr with color

**Example**:
```bash
pndcgn_log_error "Conversion failed for notes.md"
```

---

## 6. Constants Module (constants.sh)

### 6.1. ANSI Colors

**Available constants**:
```bash
PNDCGN_RED        # Red text
PNDCGN_GREEN      # Green text
PNDCGN_YELLOW     # Yellow text
PNDCGN_BLUE       # Blue text
PNDCGN_BOLD       # Bold text
PNDCGN_RESET      # Reset formatting
```

**Usage**:
```bash
printf "%sError:%s Something went wrong\\n" "${PNDCGN_RED}" "${PNDCGN_RESET}"
```

### 6.2. Exit Codes

**Standard codes**:
```bash
0   # Success
1   # General error
2   # Invalid arguments
3   # Missing dependency
4   # Database error
5   # File operation error
```

---

## Navigation

[← Database Schema](060-database-schema.md) | [↑ Top](#api-reference) | [Output Formats →](080-output-formats.md)
