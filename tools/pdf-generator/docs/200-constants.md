# ANSI Constants Specification

Compliant with AI-GUIDELINES.md

## Table of Contents

<details>
<summary>Expand Table of Contents</summary>

- [1. Introduction](#1-introduction)
- [2. Purpose and Usage](#2-purpose-and-usage)
- [3. ANSI Color Codes](#3-ansi-color-codes)
  - [3.1. Message Type Colors](#31-message-type-colors)
  - [3.2. Status Colors](#32-status-colors)
  - [3.3. Formatting Codes](#33-formatting-codes)
- [4. Spinner Characters](#4-spinner-characters)
- [5. Format Constants](#5-format-constants)
- [6. File Location and Structure](#6-file-location-and-structure)
- [7. Usage in Production Code](#7-usage-in-production-code)
- [8. Usage in Tests](#8-usage-in-tests)
- [9. Accessibility Considerations](#9-accessibility-considerations)
- [10. Navigation](#10-navigation)

</details>

## 1. Introduction

The `constants.sh` file provides centralized ANSI escape codes and constants used throughout the PDF Generator tool for consistent, colored terminal output. This shared resource ensures uniform styling across both production code and test suites.

**Purpose**: Maintain consistent visual feedback, improve readability of terminal output, and provide clear indication of different message types and processing states.

**Location**: `tools/pdf-generator/src/constants.sh`

## 2. Purpose and Usage

The constants file serves three primary purposes:

1. **Visual Consistency**: All output messages use the same color scheme
2. **Code Maintainability**: Color codes defined once, used everywhere
3. **Test Integration**: Tests can use same constants for output verification

**Design Decision**: By centralizing these values, color scheme changes require modification in only one location, and tests can validate that correct message types are used.

## 3. ANSI Color Codes

### 3.1. Message Type Colors

**Error Messages** (`ERROR_COLOR`):
```bash
ERROR_COLOR='\033[31;1m'  # Bold Red
```
- Used for: Fatal errors, missing prerequisites, invalid arguments
- Example: `ERROR: Prerequisite not found: 'pandoc'`

**Success Messages** (`SUCCESS_COLOR`):
```bash
SUCCESS_COLOR='\033[32;1m'  # Bold Green
```
- Used for: Successful completion, validation passed, confirmation
- Example: `Success! All documentation has been generated.`

**Warning Messages** (`WARNING_COLOR`):
```bash
WARNING_COLOR='\033[33;1m'  # Bold Yellow
```
- Used for: Non-fatal issues, deprecation notices, confirmations needed
- Example: `Warning: This operation will delete all cached data.`

**Info Messages** (`INFO_COLOR`):
```bash
INFO_COLOR='\033[36m'  # Cyan
```
- Used for: Progress updates, status information, helpful tips
- Example: `Initializing SQLite database...`

### 3.2. Status Colors

**Processing Status** (`PROCESSING_COLOR`):
```bash
PROCESSING_COLOR='\033[1m'  # Bold
```
- Used for: Current operation highlight
- Example: `Processing 'docs/architecture'...`

**Skipped Items** (`SKIPPED_COLOR`):
```bash
SKIPPED_COLOR='\033[2m'  # Dim
```
- Used for: Cache hits, already processed items
- Example: `Skipping (cached): 100-laravel.pdf`

**Progress Indicator** (`PROGRESS_COLOR`):
```bash
PROGRESS_COLOR='\033[0;36m'  # Cyan (not bold)
```
- Used for: Animated spinners, progress bars
- Example: `[—] Overall Progress: 42/100 (42%)`

### 3.3. Formatting Codes

**Reset** (`RESET_COLOR`):
```bash
RESET_COLOR='\033[0m'  # Reset all formatting
```
- Used after every colored message to return to default terminal colors
- Critical for preventing color bleed into subsequent output

**Bold** (`BOLD`):
```bash
BOLD='\033[1m'  # Bold text
```
- Used for emphasis in informational messages

## 4. Spinner Characters

The animated progress indicator cycles through four characters:

```bash
SPINNER_CHARS=('—' '\' '|' '/')
```

**Animation Sequence**:
1. `—` (em dash)
2. `\` (backslash)
3. `|` (vertical bar)
4. `/` (forward slash)

**Implementation**: Character overstrike creates smooth rotation effect without console flooding. Each character displayed briefly, then overwritten with next in sequence.

**Visual Effect**:
```log
[—] Overall Progress: 42/100 (42%)  # Frame 1
[\] Overall Progress: 42/100 (42%)  # Frame 2
[|] Overall Progress: 42/100 (42%)  # Frame 3
[/] Overall Progress: 42/100 (42%)  # Frame 4
```

## 5. Format Constants

**Progress Bar Format**:
```bash
PROGRESS_FORMAT="[%s] Overall Progress: %d/%d (%d%%)"
```
- `%s`: Spinner character
- First `%d`: Current count
- Second `%d`: Total count
- Third `%d`: Percentage

**Per-Folder Format**:
```bash
FOLDER_PROGRESS_FORMAT="  - %s Processing '%s': %d/%d files (%d%%)"
```
- Indented for visual hierarchy
- Shows folder name and file progress

**Timestamp Format**:
```bash
TIMESTAMP_FORMAT="%Y-%m-%d %H:%M:%S"
```
- ISO 8601 compatible
- Used in run identification and logging

## 6. File Location and Structure

**Path**: `tools/pdf-generator/src/constants.sh`

**File Structure**:
```bash
#!/usr/bin/env bash
#
# ANSI Constants for PDF Generator
# Shared between production code and tests
#

# Color Codes
readonly ERROR_COLOR='\033[31;1m'
readonly SUCCESS_COLOR='\033[32;1m'
readonly WARNING_COLOR='\033[33;1m'
readonly INFO_COLOR='\033[36m'
readonly PROCESSING_COLOR='\033[1m'
readonly SKIPPED_COLOR='\033[2m'
readonly PROGRESS_COLOR='\033[0;36m'
readonly RESET_COLOR='\033[0m'
readonly BOLD='\033[1m'

# Spinner
readonly SPINNER_CHARS=('—' '\' '|' '/')

# Format Strings
readonly PROGRESS_FORMAT="[%s] Overall Progress: %d/%d (%d%%)"
readonly FOLDER_PROGRESS_FORMAT="  - %s Processing '%s': %d/%d files (%d%%)"
readonly TIMESTAMP_FORMAT="%Y-%m-%d %H:%M:%S"
```

**Usage Pattern**: Source this file at the beginning of scripts:
```bash
source "$(dirname "${BASH_SOURCE[0]}")/../src/constants.sh"
```

## 7. Usage in Production Code

**Example: Error Message**:
```bash
echo -e "${ERROR_COLOR}ERROR:${RESET_COLOR} Missing prerequisite: pandoc" >&2
exit 1
```

**Example: Success Message**:
```bash
echo -e "\n${SUCCESS_COLOR}Success!${RESET_COLOR} All documentation generated."
```

**Example: Progress Indicator**:
```bash
printf "${PROGRESS_FORMAT}\r" \
    "${SPINNER_CHARS[$spinner_index]}" \
    "$current" "$total" "$percentage"
```

**Example: Skipped Item**:
```bash
echo -e "${SKIPPED_COLOR}Skipping (cached):${RESET_COLOR} $filename"
```

## 8. Usage in Tests

shellspec tests can source constants for output validation:

**Example: Testing Error Output**:
```bash
Describe 'Error Handling'
  setup() {
    source ./src/constants.sh
  }

  It 'shows error in red when prerequisite missing'
    When call check_prerequisites
    The stderr should include "${ERROR_COLOR}ERROR:"
    The status should equal 1
  End
End
```

**Example: Testing Progress Output**:
```bash
It 'displays progress with spinner'
  When call generate_pdf "test-dir"
  The output should include "${PROGRESS_COLOR}"
  The output should match pattern "*${SPINNER_CHARS[0]}*"
End
```

**Benefits**:
- Tests validate correct message types used
- Ensures consistency between code and tests
- Changes to colors automatically propagate to tests

## 9. Accessibility Considerations

**Color Independence**: While colors enhance UX, the system does not rely solely on color:

- **Error messages**: Include "ERROR:" text prefix
- **Success messages**: Include "Success!" text prefix
- **Warnings**: Include "Warning:" text prefix
- **Status**: Descriptive text accompanies all colored output

**Contrast Ratios**:
- Bold red, green, yellow on dark terminal: High contrast (≥4.5:1)
- Cyan on dark terminal: Meets WCAG AA (≥4.5:1)
- Dim text for skipped items: 3:1 ratio (acceptable for non-critical info)

**Screen Reader Compatibility**: Text prefixes ensure screen readers convey message type even when colors not visible.

**User Customization**: Terminal emulator color schemes may override ANSI codes - system remains functional regardless.

## 10. Navigation

[← Statistics](080-statistics.md) | [↑ Top](#ansi-constants-specification) | [Next: System Test Plan →](100-system-test-plan.md)
