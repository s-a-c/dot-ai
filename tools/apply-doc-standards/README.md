composer require s-a-c/apply-doc-standards
```

### Standalone Installation

1. Clone the repository:
```bash
git clone https://github.com/s-a-c/apply-doc-standards.git
cd apply-doc-standards
```

2. Install dependencies:
```bash
composer install
```

3. Make the tool executable:
```bash
chmod +x apply-doc-standards
```

## 4. Usage

### Basic Usage

Apply standards to a single file:
```bash
./apply-doc-standards document.md
```

Apply standards to a directory recursively:
```bash
./apply-doc-standards ./docs
```

Apply standards to multiple paths:
```bash
./apply-doc-standards file1.md file2.md ./docs
```

### Command Line Options

| Option | Short | Description |
|--------|-------|-------------|
| `--help` | `-h` | Show help message |
| `--version` | `-v` | Show version information |
| `--recursive` | `-r` | Process directories recursively (default) |
| `--no-recursive` | | Process only specified directories |
| `--dry-run` | `-n` | Show what would be changed without modifying files |
| `--force` | `-f` | Force processing even if files appear to follow standards |
| `--verbose` | | Show detailed processing information |
| `--debug` | | Show debug information |
| `--config` | `-c` | Load configuration from file |
| `--extensions` | | Comma-separated list of file extensions to process |

### Examples

Preview changes without modifying files:
```bash
./apply-doc-standards --dry-run --verbose ./docs
```

Process only specific file extensions:
```bash
./apply-doc-standards --extensions md,txt ./content
```

Force processing of all files:
```bash
./apply-doc-standards --force ./documentation
```

Use custom configuration:
```bash
./apply-doc-standards --config custom-config.json ./docs
```

## 5. Configuration

The tool supports configuration via JSON files. Here's an example configuration:

```json
{
    "file": {
        "extensions": ["md", "markdown"],
        "exclude_patterns": [
            "vendor/",
            "node_modules/",
            ".git/"
        ],
        "max_file_size": 10485760
    },
    "processing": {
        "dry_run": false,
        "force": false,
        "backup": true,
        "encoding": "UTF-8",
        "line_endings": "\n"
    },
    "standards": {
        "require_introduction": true,
        "require_toc": true,
        "require_navigation": true,
        "toc_collapsible": true,
        "section_numbering": true,
        "navigation_format": "Previous: {previous} | Next: {next} | Top"
    },
    "content": {
        "min_content_length": 100,
        "max_title_length": 100,
        "toc_min_sections": 2
    },
    "output": {
        "verbose": false,
        "show_changes": true,
        "color_output": true
    }
}
```

## 6. Documentation Standards Applied

This tool applies the following documentation standards to ensure consistency and accessibility:

### Document Structure
```markdown
# <a id="document-name"></a>Document Title

## 1. Introduction
[Introductory content providing context and overview]

## Table of Contents
<details><summary>Expand Table of Contents</summary>
- [2. Main Section](#2-main-section)
- [3. Next Section](#3-next-section)
</details>

## 2. Main Section
[Content referenced in TOC]

## 3. Next Section
[More content]

## 4. Navigation
**Previous:** [Previous Doc](path) | **Next:** [Next Doc](path) | **Top**
```

### Key Requirements

- **Anchored Titles**: All document titles use `<a id="document-name"></a>` format
- **Introduction Section**: Section 1 provides context and overview
- **Unnumbered TOC Heading**: Uses `## Table of Contents` (not numbered)
- **Collapsible TOC**: Wrapped in `<details>`/`<summary>` HTML tags
- **Numbered References**: TOC entries link to numbered sections (2., 3., 4., etc.)
- **Navigation Footer**: Standard Previous | Next | Top format
- **WCAG 2.1 AA Compliance**: All structures follow accessibility guidelines

## 7. Examples

### Before Processing
```markdown
# My Document

This is some content about my topic.

## Getting Started
Here's how to get started...

## Advanced Usage
More advanced information...
```

### After Processing
```markdown
# <a id="my-document"></a>My Document

## 1. Introduction
This document provides comprehensive information and guidelines.

## Table of Contents
<details><summary>Expand Table of Contents</summary>
- [2. Getting Started](#2-getting-started)
- [3. Advanced Usage](#3-advanced-usage)
</details>

## 2. Getting Started
Here's how to get started...

## 3. Advanced Usage
More advanced information...

## 4. Navigation
**Previous:** [Previous Doc](path) | **Next:** [Next Doc](path) | **Top**
```

## 8. Development

### Running Tests

```bash
composer test
```

### Code Quality Checks

```bash
composer quality
```

### Static Analysis

```bash
composer analyse
```

### Code Style Fixing

```bash
composer cs-fix
```

## 9. Navigation

**Previous:** [validate-links](../validate-links/) | **Next:** [Next Tool](../next-tool/) | **Top**