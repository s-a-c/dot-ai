# Implementation Adjustments

Compliant with AI-GUIDELINES.md

## Status

**Document Type**: Implementation Notes  
**Created**: 2025-12-13  
**Status**: Pending - To be applied after documentation recovery complete

## Purpose

This document captures implementation adjustments identified during documentation recovery that must be applied to both documentation and implementation when development begins.

## Adjustments Required

### 1. Tool Naming

**Change**: Rename tool from "PDF Generator" to "pndcgn"

**Rationale**: Shorter, CLI-friendly name

**Impact Areas**:
- All documentation references
- Script filenames
- Directory names
- User-facing messages
- README and help text

**Examples**:
- `generate-pdfs.sh` → `pndcgn`
- "PDF Generator" → "pndcgn" in all text
- `tools/pdf-generator/` → `tools/pndcgn/` (directory structure)

### 2. Variable and Function Naming Convention

**Change**: Use `pndcgn_` prefix for all variables and functions

**Rationale**: Namespace-like organization prevents naming conflicts

**Pattern**:
```bash
# Variables
pndcgn_run_id
pndcgn_out_type
pndcgn_cache_db
pndcgn_output_dir

# Functions
pndcgn_initialize()
pndcgn_check_prerequisites()
pndcgn_generate_pdf()
pndcgn_build_filter_chain()
```

**Impact Areas**:
- All shell script code
- constants.sh variables
- Test fixtures and assertions
- Documentation code examples

### 3. Output Type as Argument

**Change**: Output type is input argument, not hardcoded

**Implementation**:
```bash
pndcgn [OPTIONS] --type TYPE [TARGET_DIR]

# Default
--type pdf  # First type to implement

# Future types (examples)
--type html
--type epub
--type markdown
```

**Variables**:
```bash
pndcgn_out_type="${1:-pdf}"  # Default to 'pdf'
```

**Impact Areas**:
- CLI argument parsing
- Output directory structure
- Filter chain selection
- Documentation: Usage guide, CLI options
- Documentation: Architecture (multi-format support)

### 4. Target/Output Parent Directory

**Change**: Output parent directory is optional argument

**Default Behavior**:
```bash
pndcgn_target_parent="${TARGET_DIR:-$PWD}"
```

**Usage Examples**:
```bash
# Default to current working directory
pndcgn

# Specify output location
pndcgn /path/to/output

# With options
pndcgn --force --type pdf /path/to/output
```

**Impact Areas**:
- CLI argument parsing
- Path resolution logic
- Documentation: Usage guide, examples

### 5. Output Directory Structure

**Change**: Output stored in structured directory with run ID

**Format**:
```bash
${pndcgn_target_parent}/pndcgn/${pndcgn_out_type}-${pndcgn_run_id}/
```

**Examples**:
```log
./pndcgn/pdf-01JEMH3FQZR8XKWP2M4N5Q6T7Y/
./pndcgn/html-01JEMH4GSZX9YLAQ3N5P6R7U8Z/
/output/pndcgn/pdf-01JEMH5HTAY0ZMBR4O6Q7S8V9A/
```

**Structure Within Run Directory**:
```log
pndcgn/pdf-01JEMH3FQZR8XKWP2M4N5Q6T7Y/
├── 100-laravel.pdf
├── 100.010-tad.pdf
├── 200-AI-GUIDELINES.pdf
└── _index.md
```

**Impact Areas**:
- Directory creation logic
- Path resolution throughout script
- Clean/drop command implementation
- Documentation: Architecture, output structure
- Documentation: Dewey Decimal (path examples)

### 6. SQLite Database Location

**Change**: Database stored in XDG-compliant location

**Path Logic**:
```bash
pndcgn_state_dir="${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn"
pndcgn_cache_db="${pndcgn_state_dir}/cache.sqlite"
```

**Directory Structure**:
```log
~/.local/state/pndcgn/
├── cache.sqlite
├── cache.sqlite-wal
└── cache.sqlite-shm
```

**Benefits**:
- XDG Base Directory compliance
- Centralized state regardless of output location
- Standard location for state data

**Impact Areas**:
- Database initialization
- Path references throughout code
- Clean/drop command (cache location)
- Documentation: Architecture (SQLite section)
- Documentation: Installation (XDG standards)

### 7. Fingerprinting System

**Change**: Implement fingerprinting for consistency validation

**Purpose**: Ensure resumption validity by detecting state changes

**Fingerprint Components**:
```bash
# Input fingerprint
pndcgn_input_fingerprint=$(
  find . -type f -name "*.md" -o -name "*.mdc" | 
  sort | 
  xargs sha256sum | 
  sha256sum | 
  cut -d' ' -f1
)

# Configuration fingerprint
pndcgn_config_fingerprint=$(
  printf "%s\n" \
    "$pndcgn_out_type" \
    "$pndcgn_target_parent" \
    "$(pandoc --version | head -1)" \
    "$(sqlite3 --version)" |
  sha256sum |
  cut -d' ' -f1
)

# Combined fingerprint
pndcgn_run_fingerprint=$(
  printf "%s%s" \
    "$pndcgn_input_fingerprint" \
    "$pndcgn_config_fingerprint" |
  sha256sum |
  cut -d' ' -f1
)
```

**Storage**: In `runs` table
```sql
CREATE TABLE runs (
  run_id TEXT PRIMARY KEY,
  fingerprint TEXT NOT NULL,
  start_time INTEGER NOT NULL,
  end_time INTEGER,
  status TEXT NOT NULL
);
```

**Impact Areas**:
- Run initialization
- Resume validation
- Database schema
- Documentation: Architecture (fingerprinting section)
- Documentation: Statistics (resumption system)

### 8. Dry-Run Finalization

**Change**: Allow finalizing a previous dry-run if fingerprint unchanged

**Usage**:
```bash
# Initial dry-run
pndcgn --dry-run
# Output: Dry-run complete. Run ID: 01JEMH3FQZR8XKWP2M4N5Q6T7Y
#         Fingerprint: a3f5b8c...
#         To finalize: pndcgn --finalize 01JEMH3FQZR8XKWP2M4N5Q6T7Y

# Finalize (if fingerprint matches)
pndcgn --finalize 01JEMH3FQZR8XKWP2M4N5Q6T7Y
```

**Logic**:
```bash
pndcgn_finalize_run() {
  local run_id="$1"
  local stored_fingerprint current_fingerprint
  
  stored_fingerprint=$(get_run_fingerprint "$run_id")
  current_fingerprint=$(calculate_fingerprint)
  
  if [[ "$stored_fingerprint" != "$current_fingerprint" ]]; then
    printf "${ERROR_COLOR}ERROR:${RESET_COLOR} " >&2
    printf "Fingerprint mismatch. Source files changed since dry-run.\n" >&2
    return 1
  fi
  
  # Convert dry-run to actual run
  update_run_status "$run_id" "in_progress"
  execute_generation "$run_id"
}
```

**Impact Areas**:
- CLI argument parsing (--finalize)
- Run status management
- Fingerprint validation
- Documentation: User guide (workflows)
- Documentation: Architecture (run management)

### 9. Output Command Usage

**Change**: Replace `echo` with `printf` throughout

**Rationale**: 
- More portable
- Better control over formatting
- Avoids echo quirks across shells

**Pattern**:
```bash
# Instead of:
echo -e "${ERROR_COLOR}ERROR:${RESET_COLOR} Message"

# Use:
printf "%bERROR:%b Message\n" "$ERROR_COLOR" "$RESET_COLOR"

# Instead of:
echo "Processing $file..."

# Use:
printf "Processing %s...\n" "$file"
```

**Impact Areas**:
- All script output statements
- Error messages
- Progress indicators
- Documentation: Code examples in all docs
- Documentation: constants.md (usage examples)

## Documentation Update Checklist

When applying these changes to documentation:

- [ ] Global find/replace: "PDF Generator" → "pndcgn"
- [ ] Global find/replace: "generate-pdfs.sh" → "pndcgn"
- [ ] Update all code examples to use `pndcgn_` prefix
- [ ] Add output type argument to CLI documentation
- [ ] Add target directory argument to CLI documentation
- [ ] Update output path examples to use new structure
- [ ] Update SQLite path to XDG location
- [ ] Add fingerprinting section to architecture docs
- [ ] Add --finalize workflow to user guide
- [ ] Replace all `echo` examples with `printf`
- [ ] Update 000-index.md title and references
- [ ] Update 010-overview.md introduction
- [ ] Update README.md (when created)

## Implementation Checklist

When implementing these changes in code:

- [ ] Rename directories and files
- [ ] Add `pndcgn_` prefix to all variables
- [ ] Add `pndcgn_` prefix to all functions
- [ ] Implement output type argument parsing
- [ ] Implement target directory argument parsing
- [ ] Update output directory creation logic
- [ ] Update all path references
- [ ] Implement XDG-compliant state directory
- [ ] Implement fingerprinting system
- [ ] Add fingerprint column to database schema
- [ ] Implement --finalize command
- [ ] Replace all echo with printf
- [ ] Update help text
- [ ] Update constants.sh with pndcgn_ prefixes
- [ ] Update all tests for new naming

## Test Updates Required

- [ ] Update test file paths
- [ ] Update function call expectations
- [ ] Update variable name assertions
- [ ] Add fingerprinting tests
- [ ] Add --finalize workflow tests
- [ ] Update output directory structure tests
- [ ] Test XDG_STATE_HOME override
- [ ] Test target directory argument
- [ ] Test output type argument

## Notes

These changes should be applied **after documentation recovery is complete** but **before implementation begins**. This ensures:

1. Documentation accurately reflects final design
2. No rework needed on implemented code
3. Tests match final implementation
4. Single, comprehensive update sweep

## Estimated Impact

**Documentation**: ~15 files requiring updates  
**Code**: Not yet written, will follow updated specs  
**Tests**: Will be written against updated specs

**Approach**: Batch update all documentation after recovery, before any code implementation.
