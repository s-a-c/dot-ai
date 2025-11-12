# ZSH Testing Standards and Architecture

## 1. Executive Summary

All ZSH test, performance, and QA scripts **MUST** be executable with `zsh -f` (no startup files). This fundamental requirement ensures tests are fast, reliable, portable, and maintainable. This document establishes the standards, patterns, and practices for achieving this goal.

## 2. Core Principles

### 2.1. The Prime Directive: `zsh -f` Compatibility

Every test script **MUST** pass when launched individually using:
```bash
zsh -f /path/to/test.zsh
```

This means:
- No dependency on `.zshenv`, `.zshrc`, or any startup files.
- No assumptions about the user's environment.
- Explicit setup of all required state.
- Self-contained execution.

### 2.2. Test Independence

Each test is a standalone program that happens to test shell configuration, NOT a script that requires shell configuration to run.

### 2.3. Explicit Over Implicit

- Explicitly define all functions used.
- Explicitly source required modules.
- Explicitly set required variables.
- Explicitly configure PATH if needed.

## 3. Test Categories and Rules

### 3.1. Unit Tests
**Requirement Level**: MANDATORY `zsh -f` compatibility

```zsh
#!/usr/bin/env zsh
# TEST_CLASS: unit
# TEST_MODE: zsh-f-required

set -euo pipefail

# Minimal environment
export PATH="/usr/bin:/bin:/usr/sbin:/sbin"
REPO_ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"

# Source ONLY the unit being tested
source "$REPO_ROOT/modules/specific-module.zsh" || exit 1

# Test the unit in isolation
# ...
```

### 3.2. Integration Tests
**Requirement Level**: MANDATORY `zsh -f` compatibility with controlled sourcing

```zsh
#!/usr/bin/env zsh
# TEST_CLASS: integration
# TEST_MODE: zsh-f-required

set -euo pipefail

export PATH="/usr/bin:/bin:/usr/sbin:/sbin"
REPO_ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"

# Source components being integrated
source "$REPO_ROOT/modules/component-a.zsh" || exit 1
source "$REPO_ROOT/modules/component-b.zsh" || exit 1

# Test integration
# ...
```

## 4. Environment and Utilities

### 4.1. Environment Configuration

Every test MUST establish sensible, overridable defaults for variables like `TEST_NAME`, `REPO_ROOT`, and `PATH`.

### 4.2. Temporary Resources

Tests requiring temporary files or directories MUST create them in a test-specific temporary directory and ensure cleanup using a `trap` on `EXIT`, `INT`, and `TERM`.

### 4.3. Self-Contained Assertions

Tests MUST NOT depend on external test frameworks. They should define their own simple, self-contained assertion functions.

```zsh
# Basic assertion framework
typeset -i PASS_COUNT=0
typeset -i FAIL_COUNT=0

assert_equals() {
    local expected="$1"
    local actual="$2"
    local message="${3:-Assertion}"
    
    if [[ "$expected" == "$actual" ]]; then
        ((PASS_COUNT++))
    else
        ((FAIL_COUNT++))
        echo "FAIL: $message: expected='$expected', actual='$actual'"
    fi
}
```

### 4.4. Use ZSH Built-ins

Prefer ZSH built-in commands and parameter expansion over external commands like `grep`, `sed`, `awk`, and `date` to ensure portability and performance.

*   **Instead of `grep`:** `[[ "$var" == *pattern* ]]`
*   **Instead of `sed`:** `${var//old/new}`
*   **Instead of `date`:** `typeset -F SECONDS` or `zmodload zsh/datetime`

## 5. CI/CD Integration

Integrate `zsh -f` test execution directly into the CI/CD pipeline (e.g., GitHub Actions) to validate changes on every push and pull request.

```yaml
- name: Run ZSH Unit Tests
  run: |
    for test in tests/unit/**/*.zsh; do
      zsh -f "$test" || exit 1
    done
```

## 6. Performance Benefits

The `zsh -f` approach eliminates shell startup overhead, leading to significant performance improvements:

*   **Startup Overhead:** Reduced from ~45 seconds to 0 seconds for a 137-test suite.
*   **Total Runtime:** Reduced from over 60 seconds to under 10 seconds.
*   **Reliability:** 100% reduction in timeout failures in CI.
