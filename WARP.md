# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## 1. Repository Overview

This repository contains a structured AI-assisted development workflow system designed for use with AI editors like Cursor. It provides:
- `.mdc` (Markdown Command) files for systematic feature development (PRD → Tasks → Implementation)
- Comprehensive AI guidelines for PHP/Laravel, documentation, and testing standards
- Tools for link validation and documentation quality assurance
- Project-specific documentation organized by numbered directories

## 2. Common Development Commands

### 2.1. MDC Workflow (AI-Assisted Development)

These `.mdc` files guide AI assistants through structured feature development:

**Step 1: Create PRD**
```bash
# In your AI assistant (e.g., Cursor), reference:
@tasks/create-prd.mdc
# Describe your feature, answer clarifying questions
# Output: /.ai/tasks/[feature-name]/prd-[feature-name].md
```

**Step 2: Generate Task List**
```bash
# Reference the PRD and generation command:
@tasks/generate-tasks.mdc @prd-[feature-name].md
# Output: /.ai/tasks/[feature-name]/tasks-[feature-name].md
```

**Step 3: Execute Tasks**
```bash
# Reference the process command:
@tasks/process-task-list.mdc
# Start with task 1.1, wait for approval between tasks
```

### 2.2. Tools: Validation and Documentation

**Validate Links**
```bash
cd tools/validate-links/
./validate-links ../../docs/ --fix
./validate-links ../../docs/ --fix --dry-run  # Preview fixes
```

**Apply Documentation Standards**
```bash
cd tools/apply-doc-standards/
# Run the tool per its internal documentation
```

### 2.3. Scripts: PHP Utilities

**Run Scripts**
```bash
php scripts/simple-pest-fixes.php --dry-run
php scripts/phpstan-test-fixes.php
php scripts/policy-check.php
```

**PHPStan Level 10 Fixes**
```bash
# Preview changes
php scripts/simple-pest-fixes.php --dry-run

# Apply fixes
php scripts/simple-pest-fixes.php

# Check specific directory
php scripts/simple-pest-fixes.php --dir tests/Unit
```

### 2.4. Version Control Workflows

**Git Workflow** (following commit message standards)
```bash
git status
git checkout -b feature/my-feature
git add -A
git commit -m "Add: Feature description" \
    -m "" \
    -m "Detailed explanation of changes." \
    -m "* Bullet point for specific change" \
    -m "* Another specific change" \
    -m "Recommended tag: v1.0.1"
git push -u origin HEAD
```

**Jujutsu (jj) Workflow**
```bash
jj status
jj new -m "feature/my-feature"
jj describe -m "Add: Feature description" \
             -m "" \
             -m "Detailed explanation of changes." \
             -m "* Bullet point for specific change" \
             -m "* Another specific change"
jj git push
```

Note: This repository has both `.git` and `.jj` directories - choose your preferred VCS.

## 3. High-Level Architecture

### 3.1. Three-Phase Development Flow

1. **PRD Creation** (`create-prd.mdc`) → Defines feature requirements
2. **Task Generation** (`generate-tasks.mdc`) → Breaks down into hierarchical tasks
3. **Implementation** (`process-task-list.mdc`) → Executes one task at a time with approval gates

### 3.2. Directory Structure

```
/
├── tasks/              # .mdc workflow files
├── AI-GUIDELINES/      # Comprehensive development standards
│   ├── Documentation/
│   ├── PHP-Laravel/
│   ├── JavaScript-TypeScript/
│   ├── Shell-CLI/
│   ├── Testing/
│   └── Workflows/
├── tools/              # Validation and QA tools
│   ├── validate-links/
│   └── apply-doc-standards/
├── scripts/            # PHP automation scripts
├── guides/             # Style guides and technical guides
├── templates/          # HIP (Hierarchical Implementation Plan) templates
└── [NNN-project]/      # Numbered project directories (010, 100, 200, etc.)
```

### 3.3. AI-GUIDELINES System

Central comprehensive standards located in `AI-GUIDELINES/`:
- **Core Principle**: Write for junior developers; clarity over cleverness
- **Documentation**: Numbered headings (1.1, 1.1.1), explicit code fence languages, WCAG 2.1 AA
- **Communication**: Professional, direct, dry humor, challenge assumptions constructively
- **Testing**: 90%+ coverage, Pest PHP framework preferred
- **Security**: No secrets in repo, RBAC with spatie/laravel-permission

### 3.4. Numbering Conventions

- **Directories**: Use multiples of 10 (010-docs, 100-laravel, 200-l-s-f, 300-aureuserp, 400-fm4, 700-r-and-d)
- **Headings**: Hierarchical numbering (1, 1.1, 1.1.1) - exclude main document title
- **Tasks**: Three-level breakdown (1.0 → 1.1 → 1.1.1)
- **Index Files**: Every directory should have `000-index.md` or `README.md`

### 3.5. Task Management Patterns

**Status Indicators**:
- 🔴 Not Started (0%)
- 🟡 In Progress (1-99%)
- 🟢 Completed (100%)
- ⚪ Skipped/Not Applicable
- 🟠 Blocked/Waiting

**Priority Levels**:
- 🟣 P1 - Critical (Must have)
- 🔴 P2 - High (Should have)
- 🟡 P3 - Medium (Could have)
- 🟠 P4 - Low (Won't have this iteration)
- 🟢 P5 - Future (Next iteration)

## 4. Important Guidelines

### 4.1. Commit Message Standard

**Format**:
- Max 50 characters for summary line
- Imperative mood (e.g., "Fix bug," "Add feature")
- Multi-line body with context
- Bullet points for changes
- Issue/PR references
- Tag recommendations

**Example**:
```bash
git commit -m "Fix: Prevent crash on null input" \
    -m "" \
    -m "Addresses issue #123." \
    -m "The application was crashing when processing null input." \
    -m "This commit adds a check for null values and handles them gracefully." \
    -m "* Added null check in process_input function" \
    -m "* Updated unit tests to cover null input scenarios" \
    -m "Recommended tag: v1.0.1"
```

### 4.2. Documentation Standards

**From `guides/DOCUMENTATION_STYLE_GUIDE.md`**:
- Use numbered headings (exclude document title from numbering)
- All code blocks must specify language (use `log` for plain text)
- Format links as `[text](url)` - test all links
- Include Table of Contents in collapsible `<details>` tags
- Navigation footer: `[← Previous](path) | [↑ Top](#anchor) | [Next →](path)`

### 4.3. Testing Approach

When modifying code or running tests:
- Check README or search codebase for test commands first
- For PHP projects with Pest: `./vendor/bin/pest`
- For single tests: `./vendor/bin/pest tests/Feature/ExampleTest.php -t "case name"`
- Don't assume specific frameworks - verify first

## 5. Key Patterns to Follow

### 5.1. MDC Files
`.mdc` files are machine-actionable instructions for AI assistants - they define workflows, validation rules, and output formats.

### 5.2. Hierarchical Task Breakdown
- **Parent Tasks** (1.0): Major implementation areas
- **Sub-tasks** (1.1): Detailed implementation steps
- **Sub-sub-tasks** (1.1.1): Granular actions (1-3 hours each)

### 5.3. One Task at a Time
When using `process-task-list.mdc`, implement one sub-task at a time and wait for user approval before proceeding.

### 5.4. Orchestration Policy
AI-authored artifacts should include compliance acknowledgment:
```markdown
Compliant with AI-GUIDELINES.md v<checksum>
```

### 5.5. Laravel Development Standards
For Laravel projects (in numbered directories):
- Use Eloquent exclusively (avoid raw SQL)
- Modern Laravel patterns (PHP 8.4, Laravel 12)
- `casts()` method over `$casts` property
- PHPStan level 9+, Psalm level 1
- 90%+ test coverage with Pest

## 6. File Organization

### 6.1. Task Storage
Store feature implementation files under `/.ai/tasks/[feature-name]/`:
- `prd-[feature-name].md` - Requirements
- `tasks-[feature-name].md` - Task list
- `commit-message.md` - Conventional commit message

### 6.2. Project Documentation
Numbered directories contain project-specific documentation:
- `010-docs/` - Documentation projects
- `100-laravel/` - Laravel-specific resources
- `200-l-s-f/`, `300-aureuserp/`, `400-fm4/` - Specific projects
- `700-r-and-d/` - Research and development

### 6.3. Templates
Use `templates/HIP_Chinook_Template_2025-07-13.md` as starting point for Hierarchical Implementation Plans.

## 7. Quick Reference

**Start a new feature**:
1. Use `@tasks/create-prd.mdc` with AI assistant
2. Generate tasks with `@tasks/generate-tasks.mdc`
3. Execute with `@tasks/process-task-list.mdc`

**Validate documentation**:
```bash
cd tools/validate-links && ./validate-links ../../ --fix --dry-run
```

**Key files to review**:
- `AI-GUIDELINES.md` - Comprehensive development standards
- `README.md` - Repository overview and workflow explanation
- `guides/DOCUMENTATION_STYLE_GUIDE.md` - Documentation formatting rules
- `AI-GUIDELINES/000-index.md` - Guidelines index
