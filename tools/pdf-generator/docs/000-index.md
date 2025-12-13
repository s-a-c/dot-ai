# PDF Generator Documentation Index

Compliant with AI-GUIDELINES.md

## Table of Contents

<details>
<summary>Expand Table of Contents</summary>

- [1. Introduction](#1-introduction)
- [2. Quick Start](#2-quick-start)
- [3. Documentation Structure](#3-documentation-structure)
  - [3.1. Core Documentation](#31-core-documentation)
  - [3.2. Requirements and User Documentation](#32-requirements-and-user-documentation)
  - [3.3. Technical Documentation](#33-technical-documentation)
  - [3.4. Testing Documentation](#34-testing-documentation)
  - [3.5. Supporting Documentation](#35-supporting-documentation)
  - [3.6. Development History](#36-development-history)
- [4. For Specific Tasks](#4-for-specific-tasks)
- [5. Document Formatting Standards](#5-document-formatting-standards)
- [6. Testing Framework](#6-testing-framework)
- [7. Navigation](#7-navigation)

</details>

## 1. Introduction

This documentation set provides comprehensive guidance for the PDF Generator tool, an advanced system for creating organized, hyperlinked PDF documentation from project source files. The tool features intelligent caching, parallel processing, and a sophisticated filter-based architecture.

**Target Audience**: All documentation is written to be clear, actionable, and suitable for junior developers to understand and implement.

**Documentation Philosophy**: These documents capture the knowledge, decisions, and understanding achieved during the tool's development, preserving the technical rationale behind design choices.

## 2. Quick Start

**New to PDF Generator?** Start here:

1. Read [010-overview.md](010-overview.md) for system overview and core objectives
2. Review [030-installation.md](030-installation.md) for setup instructions
3. Read [040-user-guide.md](040-user-guide.md) for usage and CLI options
4. Explore [050-architecture.md](050-architecture.md) to understand the system design

**Ready to Develop?**

1. Review [020-requirements.md](020-requirements.md) for BDD requirements
2. Read [110-implementation-plan.md](110-implementation-plan.md) for implementation guidance
3. Study [100-system-test-plan.md](100-system-test-plan.md) and [120-feature-unit-test-plan.md](120-feature-unit-test-plan.md) for testing approach

## 3. Documentation Structure

### 3.1. Core Documentation

- [000-index.md](000-index.md) - This document; central navigation hub

### 3.2. Requirements and User Documentation

- [010-overview.md](010-overview.md) - Project overview, core objectives, and feature summary
- [020-requirements.md](020-requirements.md) - BDD requirements as user stories with acceptance criteria
- [030-installation.md](030-installation.md) - Installation guide for IDX and manual setup
- [040-user-guide.md](040-user-guide.md) - Comprehensive user guide with CLI options and workflows

### 3.3. Technical Documentation

- [050-architecture.md](050-architecture.md) - System architecture, SQLite schema, and caching strategy
- [060-filters.md](060-filters.md) - Pandoc filter ecosystem and integration
- [070-dewey-decimal.md](070-dewey-decimal.md) - Dewey Decimal naming algorithm specification
- [080-statistics.md](080-statistics.md) - Statistics tracking and resumption system

### 3.4. Testing Documentation

- [100-system-test-plan.md](100-system-test-plan.md) - System tests mapped to requirements (shellspec)
- [110-implementation-plan.md](110-implementation-plan.md) - Implementation plan with requirements references
- [120-feature-unit-test-plan.md](120-feature-unit-test-plan.md) - Feature and unit tests for implementation (shellspec)

### 3.5. Supporting Documentation

- [200-constants.md](200-constants.md) - ANSI color codes and constants specification

### 3.6. Development History

- [900-changelog.md](900-changelog.md) - Evolution history from initial concept to current design

## 4. For Specific Tasks

**Installation and Setup**:
- IDX platform setup → [030-installation.md](030-installation.md)
- Manual installation → [030-installation.md](030-installation.md)
- Prerequisites verification → [030-installation.md](030-installation.md)

**Using PDF Generator**:
- First run → [040-user-guide.md](040-user-guide.md)
- CLI options → [040-user-guide.md](040-user-guide.md)
- Resuming interrupted runs → [040-user-guide.md](040-user-guide.md)
- Cleaning specific runs → [040-user-guide.md](040-user-guide.md)

**Understanding the System**:
- SQLite caching → [050-architecture.md](050-architecture.md)
- Filter chain → [060-filters.md](060-filters.md)
- PDF naming → [070-dewey-decimal.md](070-dewey-decimal.md)
- Statistics tracking → [080-statistics.md](080-statistics.md)

**Development and Testing**:
- Requirements → [020-requirements.md](020-requirements.md)
- Implementation tasks → [110-implementation-plan.md](110-implementation-plan.md)
- Writing tests → [100-system-test-plan.md](100-system-test-plan.md), [120-feature-unit-test-plan.md](120-feature-unit-test-plan.md)
- ANSI constants → [200-constants.md](200-constants.md)

**Troubleshooting**:
- Common issues → [040-user-guide.md](040-user-guide.md)
- Design decisions → [900-changelog.md](900-changelog.md)

## 5. Document Formatting Standards

All documentation follows these standards from AI-GUIDELINES:

- **Plain H1 headings**: No HTML anchors (e.g., `# Document Title`)
- **Numbered headings**: All headings below H1 are numbered (1, 1.1, 1.1.1)
- **Table of Contents**: Collapsible TOC in `<details>` tags after document title
- **Navigation footer**: Format: `[← Previous](path) | [↑ Top](#anchor) | [Next →](path)`
- **Code blocks**: All code blocks specify language (use `log` for plain text)
- **Links**: Standard markdown syntax `[text](url)`
- **Accessibility**: WCAG 2.1 AA compliance for color contrast and structure
- **File naming**: 3-digit prefixes in multiples of 10 (000, 010, 020, etc.)

## 6. Testing Framework

This project uses **shellspec** for BDD/TDD testing of shell scripts:

- shellspec is already configured in `.idx/dev.nix`
- Test files are located in `spec/` directory
- System tests validate requirements → [100-system-test-plan.md](100-system-test-plan.md)
- Feature/unit tests validate implementation → [120-feature-unit-test-plan.md](120-feature-unit-test-plan.md)
- `constants.sh` is shared between production code and tests → [200-constants.md](200-constants.md)

## 7. Navigation

[↑ Top](#pdf-generator-documentation-index) | [Next: Overview →](010-overview.md)
