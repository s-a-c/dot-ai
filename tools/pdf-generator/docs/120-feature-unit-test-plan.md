# Feature/Unit Test Plan

<details>
<summary>Table of Contents</summary>

- [1. Overview](#1-overview)
  - [1.1. Purpose](#11-purpose)
  - [1.2. Test Framework](#12-test-framework)
- [2. Constants Module Tests](#2-constants-module-tests)
- [3. Utilities Module Tests](#3-utilities-module-tests)
- [4. Database Module Tests](#4-database-module-tests)
- [5. Processing Module Tests](#5-processing-module-tests)
- [6. Main Controller Tests](#6-main-controller-tests)
- [7. Test Execution](#7-test-execution)
- [Navigation](#navigation)

</details>

---

**Compliant with**: AI-GUIDELINES.md v1.0

## 1. Overview

### 1.1. Purpose

This document defines unit and feature tests for individual modules. Tests follow BDD style using shellspec and ensure 90%+ code coverage.

### 1.2. Test Framework

**Tool**: shellspec

**Run tests**:
```bash
# All tests
shellspec

# Specific module
shellspec specs/utilities_spec.sh

# With coverage
kcov coverage/ shellspec
```

---

## 2. Constants Module Tests

**File**: `specs/constants_spec.sh`

**Tests**:
```bash
Describe 'Constants Module'
  It 'defines PNDCGN_RED'
    When call printf "%s" "$PNDCGN_RED"
    The output should start with "\\033["
  End
  
  It 'defines all required colors'
    The variable PNDCGN_GREEN should be defined
    The variable PNDCGN_YELLOW should be defined
    The variable PNDCGN_BLUE should be defined
    The variable PNDCGN_BOLD should be defined
    The variable PNDCGN_RESET should be defined
  End
  
  It 'respects NO_COLOR environment variable'
    NO_COLOR=1 source src/constants.sh
    The variable PNDCGN_RED should equal ""
  End
End
```

---

## 3. Utilities Module Tests

**File**: `specs/utilities_spec.sh`

**ULID Generation**:
```bash
Describe 'pndcgn_generate_ulid'
  It 'generates 26-character identifier'
    When call pndcgn_generate_ulid
    The length of output should equal 26
  End
  
  It 'generates unique IDs'
    id1=$(pndcgn_generate_ulid)
    id2=$(pndcgn_generate_ulid)
    The variable id1 should not equal "$id2"
  End
End
```

**Path Resolution**:
```bash
Describe 'pndcgn_resolve_path'
  It 'converts relative to absolute path'
    When call pndcgn_resolve_path "../docs"
    The output should start with "/"
  End
  
  It 'handles current directory'
    When call pndcgn_resolve_path "."
    The output should equal "$PWD"
  End
End
```

**Logging**:
```bash
Describe 'pndcgn_log_info'
  It 'outputs to stdout'
    When call pndcgn_log_info "Test message"
    The output should include "Test message"
  End
End

Describe 'pndcgn_log_error'
  It 'outputs to stderr'
    When call pndcgn_log_error "Error message"
    The error should include "Error message"
  End
End
```

---

## 4. Database Module Tests

**File**: `specs/database_spec.sh`

**Schema Creation**:
```bash
Describe 'pndcgn_db_init'
  It 'creates runs table'
    When call pndcgn_db_init
    The result of "table_exists runs" should be success
  End
  
  It 'creates generated_pdfs table'
    When call pndcgn_db_init
    The result of "table_exists generated_pdfs" should be success
  End
  
  It 'creates indexes'
    When call pndcgn_db_init
    The result of "index_exists idx_fingerprints" should be success
  End
End
```

**Run Management**:
```bash
Describe 'pndcgn_db_start_run'
  It 'inserts run record'
    run_id="TEST_RUN_ID"
    When call pndcgn_db_start_run "$run_id" "/src" "/out" "pdf" 0
    The status should be success
    The result of "run_exists $run_id" should be success
  End
End

Describe 'pndcgn_db_finish_run'
  It 'updates run status'
    When call pndcgn_db_finish_run "$run_id" "complete" 10 5 5 0
    The result of "get_run_status $run_id" should equal "complete"
  End
End
```

**Cache Operations**:
```bash
Describe 'pndcgn_db_store_file'
  It 'stores file metadata'
    When call pndcgn_db_store_file \
        "$run_id" "/src/f.md" "/out/f.pdf" "fp123" 100 1000
    The status should be success
  End
End

Describe 'pndcgn_db_get_fingerprint'
  It 'retrieves cached file'
    When call pndcgn_db_get_fingerprint "fp123" "pdf"
    The output should equal "/out/f.pdf"
  End
  
  It 'returns nothing for missing fingerprint'
    When call pndcgn_db_get_fingerprint "nonexistent" "pdf"
    The output should equal ""
  End
End
```

---

## 5. Processing Module Tests

**File**: `specs/processing_spec.sh`

**File Discovery**:
```bash
Describe 'pndcgn_find_files'
  setup() {
    mkdir -p /tmp/test/sub
    touch /tmp/test/file1.md
    touch /tmp/test/sub/file2.md
  }
  
  It 'finds markdown files'
    When call pndcgn_find_files "/tmp/test"
    The lines of output should equal 2
  End
  
  cleanup() {
    rm -rf /tmp/test
  }
End
```

**Fingerprinting**:
```bash
Describe 'pndcgn_compute_fingerprint'
  It 'computes stable fingerprint'
    fp1=$(pndcgn_compute_fingerprint "/tmp/test.md")
    fp2=$(pndcgn_compute_fingerprint "/tmp/test.md")
    The variable fp1 should equal "$fp2"
  End
  
  It 'format includes size:mtime:hash'
    When call pndcgn_compute_fingerprint "/tmp/test.md"
    The output should match pattern "*:*:*"
  End
End
```

**File Conversion**:
```bash
Describe 'pndcgn_convert_file'
  It 'converts markdown to PDF'
    echo "# Test" > /tmp/test.md
    When call pndcgn_convert_file "/tmp/test.md" "/tmp/test.pdf" "pdf"
    The status should be success
    The path "/tmp/test.pdf" should be file
  End
  
  It 'handles pandoc errors'
    echo "{{invalid}}" > /tmp/invalid.md
    When call pndcgn_convert_file "/tmp/invalid.md" "/tmp/out.pdf" "pdf"
    The status should be failure
  End
End
```

---

## 6. Main Controller Tests

**File**: `specs/cli_spec.sh`

**Argument Parsing**:
```bash
Describe 'CLI argument parsing'
  It 'parses --help'
    When run bin/pdf-generator --help
    The output should include "Usage:"
    The status should be success
  End
  
  It 'parses --version'
    When run bin/pdf-generator --version
    The output should include "pndcgn"
  End
  
  It 'parses --verbose'
    When run bin/pdf-generator --verbose /tmp/test
    The variable pndcgn_verbose should equal "1"
  End
End
```

---

## 7. Test Execution

**Run all tests**:
```bash
shellspec
```

**Run with coverage**:
```bash
kcov coverage/ shellspec
```

**Check coverage report**:
```bash
open coverage/index.html
```

---

## Navigation

[← System Test Plan](100-system-test-plan.md) | [↑ Top](#featureunit-test-plan) | [README →](../README.md)
