# Installation

<details>
<summary>Table of Contents</summary>

- [1. Prerequisites](#1-prerequisites)
  - [1.1. System Requirements](#11-system-requirements)
  - [1.2. External Dependencies](#12-external-dependencies)
  - [1.3. Optional Dependencies](#13-optional-dependencies)
- [2. Installation Steps](#2-installation-steps)
  - [2.1. Clone or Extract Tool](#21-clone-or-extract-tool)
  - [2.2. Initialize Configuration](#22-initialize-configuration)
  - [2.3. Verify Installation](#23-verify-installation)
- [3. Directory Structure](#3-directory-structure)
  - [3.1. Tool Layout](#31-tool-layout)
  - [3.2. Data Directories](#32-data-directories)
- [4. Configuration](#4-configuration)
  - [4.1. TOML Configuration](#41-toml-configuration)
  - [4.2. Environment Variables](#42-environment-variables)
- [5. Verification](#5-verification)
  - [5.1. Smoke Test](#51-smoke-test)
  - [5.2. Troubleshooting](#52-troubleshooting)
- [Navigation](#navigation)

</details>

---

**Compliant with**: AI-GUIDELINES.md v1.0

## 1. Prerequisites

### 1.1. System Requirements

**Operating System**:
- Unix-like system (Linux, macOS, BSD)
- Bash 4.0+ or compatible shell

**Core Tools**:
```bash
# Required binaries (must be in PATH)
bash        # Shell interpreter
sqlite3     # Database engine
pandoc      # Document conversion
awk         # Text processing
sed         # Stream editor
find        # File traversal
```

**Disk Space**:
- Minimum 50MB for tool + dependencies
- Variable for SQLite cache (depends on usage)
- Recommended 1GB+ for active PDF generation

### 1.2. External Dependencies

Install required tools using your system package manager:

**Debian/Ubuntu**:
```bash
sudo apt update
sudo apt install bash sqlite3 pandoc gawk sed findutils
```

**macOS (Homebrew)**:
```bash
brew install bash sqlite pandoc gawk gnu-sed findutils
```

**Arch Linux**:
```bash
sudo pacman -S bash sqlite pandoc gawk sed findutils
```

**Alpine Linux** (IDX environment):
```bash
apk add bash sqlite pandoc gawk sed findutils
```

### 1.3. Optional Dependencies

**For Development**:
- `shellspec` - BDD testing framework
- `shellcheck` - Shell script linting
- `shfmt` - Shell script formatting

```bash
# Install shellspec (required for tests)
curl -fsSL https://git.io/shellspec | sh

# Install shellcheck
# Debian/Ubuntu
sudo apt install shellcheck

# macOS
brew install shellcheck

# Install shfmt
# Debian/Ubuntu
sudo apt install shfmt

# macOS
brew install shfmt
```

**For Enhanced Output**:
- `jq` - JSON processing
- `tree` - Directory visualization

```bash
# Install optional tools
# Debian/Ubuntu
sudo apt install jq tree

# macOS
brew install jq tree
```

---

## 2. Installation Steps

### 2.1. Clone or Extract Tool

**If part of larger repository**:
```bash
# Navigate to tool location
cd /path/to/dot-ai/tools/pdf-generator
```

**If standalone installation**:
```bash
# Clone repository
git clone https://github.com/your-org/pndcgn.git
cd pndcgn
```

**Verify structure**:
```bash
# Expected layout
ls -1
# bin/
# src/
# docs/
# README.md
```

### 2.2. Initialize Configuration

**Create configuration file**:
```bash
# Run initialization
./bin/pdf-generator --init

# Or manually create
cat > pdf-generator.toml <<'EOF'
# pndcgn Configuration
[general]
output_type = "pdf"
cache_location = "~/.local/state/pndcgn"

[processing]
parallel_jobs = 4
verbose = false

[database]
wal_mode = true
EOF
```

**Set database location** (optional):
```bash
# Use XDG standard location
export XDG_STATE_HOME="${HOME}/.local/state"

# Or set custom location in config
sed -i 's|cache_location = ".*"|cache_location = "/custom/path/pndcgn"|' \
    pdf-generator.toml
```

**Create database directories**:
```bash
# Ensure state directory exists
mkdir -p "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn"

# Verify permissions
chmod 755 "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn"
```

### 2.3. Verify Installation

**Check tool is executable**:
```bash
# Make script executable
chmod +x ./bin/pdf-generator

# Verify version
./bin/pdf-generator --version
# Expected: pndcgn v6 (or current version)
```

**Test database connectivity**:
```bash
# Initialize database schema
./bin/pdf-generator --init

# Verify tables exist
sqlite3 "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn/cache.sqlite" \
    ".tables"
# Expected: runs  generated_pdfs
```

**Display help**:
```bash
./bin/pdf-generator --help
# Should show usage information
```

---

## 3. Directory Structure

### 3.1. Tool Layout

```log
pdf-generator/
├── bin/
│   └── pdf-generator           # Main executable
├── src/
│   ├── constants.sh            # ANSI color constants
│   ├── database.sh             # SQLite operations
│   ├── processing.sh           # Core logic
│   └── utilities.sh            # Helper functions
├── docs/
│   ├── 000-index.md            # Documentation index
│   ├── 010-overview.md         # System overview
│   ├── 020-requirements.md     # Requirements
│   ├── 030-installation.md     # This file
│   └── ...                     # Additional docs
├── specs/                      # shellspec tests
│   ├── spec_helper.sh
│   └── ...
├── pdf-generator.toml          # Configuration
└── README.md                   # Quick start guide
```

### 3.2. Data Directories

**Default locations** (XDG-compliant):
```log
~/.local/state/pndcgn/
├── cache.sqlite                # Run history + metadata
└── cache.sqlite-wal            # Write-ahead log
```

**Output structure** (per run):
```log
${TARGET_PARENT}/pndcgn/
├── pdf-{ULID}/                 # Run-specific output
│   ├── file1.pdf
│   ├── file2.pdf
│   └── ...
├── epub-{ULID}/                # Different output type
│   └── ...
└── ...
```

---

## 4. Configuration

### 4.1. TOML Configuration

**Default configuration** (`pdf-generator.toml`):
```toml
# General Settings
[general]
output_type = "pdf"             # Default output format
cache_location = "~/.local/state/pndcgn"

# Processing Settings
[processing]
parallel_jobs = 4               # Number of parallel workers
verbose = false                 # Verbose output
dry_run = false                 # Dry-run mode

# Database Settings
[database]
wal_mode = true                 # Write-ahead logging
checkpoint_interval = 1000      # WAL checkpoint frequency
```

**Configuration precedence** (highest to lowest):
1. Command-line arguments
2. Environment variables
3. TOML configuration file
4. Built-in defaults

### 4.2. Environment Variables

**Database location**:
```bash
# Override cache location
export PNDCGN_CACHE_DIR="/custom/path"
```

**XDG Base Directory**:
```bash
# Standard XDG location
export XDG_STATE_HOME="${HOME}/.local/state"
```

**Debugging**:
```bash
# Enable verbose mode
export PNDCGN_VERBOSE=1

# Enable dry-run mode
export PNDCGN_DRY_RUN=1
```

---

## 5. Verification

### 5.1. Smoke Test

**Basic functionality test**:
```bash
# Create test directory
mkdir -p /tmp/pndcgn-test/source
cd /tmp/pndcgn-test

# Create sample markdown file
cat > source/test.md <<'EOF'
# Test Document
This is a test.
EOF

# Run in dry-run mode
/path/to/bin/pdf-generator --dry-run source

# Check output
# Expected: Run ID displayed, no PDFs created

# Run actual generation
/path/to/bin/pdf-generator source

# Verify output exists
ls pndcgn/pdf-*/test.pdf
# Expected: PDF file created

# Clean up
cd /
rm -rf /tmp/pndcgn-test
```

### 5.2. Troubleshooting

**Common issues**:

**Issue**: "command not found: pdf-generator"
```bash
# Solution: Add to PATH or use absolute path
export PATH="/path/to/pdf-generator/bin:${PATH}"
```

**Issue**: "sqlite3: command not found"
```bash
# Solution: Install SQLite
# Debian/Ubuntu
sudo apt install sqlite3

# macOS
brew install sqlite
```

**Issue**: "Database is locked"
```bash
# Solution: Check for stale locks
sqlite3 "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn/cache.sqlite" \
    "PRAGMA wal_checkpoint(RESTART);"

# Or remove lock files
rm -f "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn/cache.sqlite-shm"
rm -f "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn/cache.sqlite-wal"
```

**Issue**: "Permission denied"
```bash
# Solution: Fix permissions
chmod +x /path/to/bin/pdf-generator
chmod 755 "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn"
```

**Issue**: "Pandoc conversion failed"
```bash
# Solution: Test pandoc directly
echo "# Test" | pandoc -f markdown -t pdf -o /tmp/test.pdf

# Check pandoc version
pandoc --version

# Reinstall if necessary
```

**Getting help**:
```bash
# Display usage information
./bin/pdf-generator --help

# Enable verbose mode for debugging
./bin/pdf-generator --verbose source_dir

# Check log files (if implemented)
tail -f "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn/pndcgn.log"
```

---

## Navigation

[← Requirements](020-requirements.md) | [↑ Top](#installation) | [User Guide →](040-user-guide.md)
