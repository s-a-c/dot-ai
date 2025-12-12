# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

---

## [2.0.0] - PLANNED
### Added
- **Centralized Configuration**: All tool settings are now managed in `pdf-generator.toml`, moving away from hardcoded script variables (`ADR-001`).
- **Configuration Initialization**: A `--init` command to generate a default `pdf-generator.toml` file (`1.2`).
- **Safe State Destruction**: New `--clean <ID>` and `--drop` commands with mandatory interactive confirmation prompts for targeted and global artifact removal (`2.2`, `2.3`).
- **"Plan and Apply" Workflow**: A `--dry-run` flag to preview changes and an `--execute-run` flag to apply them, with fingerprinting to prevent state mismatches (`4.0`).
- **Version Information**: A `--version` flag to display the current tool version (`3.3`).
- **ULID for Run IDs**: `RUN_ID`s now use the ULID format for chronological sorting (`ADR-004`).

### Changed
- **BREAKING: Internal API Namespacing**: Complete internal refactor to apply the `pndcgn_` prefix to all functions and global variables to prevent script collisions (`ADR-003`).
- **BREAKING: Configuration-Driven Paths**: The script now resolves database and output paths from the configuration file, including support for environment variables (`2.1`).
- **Improved Reporting**: Run reports are now more detailed, including enhanced statistics and a Mermaid-based project map.

### Removed
- **BREAKING: Unsafe `--clean` Flag**: The original `--clean` flag, which deleted all outputs without confirmation, has been replaced by the safer, more granular state destruction commands.

*(Note: Numbers in parentheses refer to the corresponding section in the System Design document.)*
