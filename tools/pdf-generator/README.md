# PDF Generation Tool (`pdf-generator`)

A robust, configurable, and high-performance command-line tool for converting Markdown-based documentation sets into professional, consistent PDF documents.

This tool is designed for developers and technical writers who need to automate the production of high-quality documentation. It leverages Pandoc and a suite of powerful filters to handle complex content like source code, tables of contents, and embedded diagrams (PlantUML, Mermaid, DBML) automatically.

---

<details><summary>Table of Contents</summary>

- [PDF Generation Tool (`pdf-generator`)](#pdf-generation-tool-pdf-generator)
  - [1. Key Features](#1-key-features)
  - [2. Dependencies](#2-dependencies)
  - [3. Basic Usage](#3-basic-usage)
  - [4. Documentation](#4-documentation)

</details>

---

## 1. Key Features

*   **Declarative Configuration**: Configure all aspects of the tool via a simple `pdf-generator.toml` file.
*   **Intelligent Caching**: Blazingly fast builds. Only regenerates documents when their content has actually changed.
*   **Rich Diagram Support**: Natively embeds **PlantUML**, **Mermaid**, and **DBML** diagrams from code blocks.
*   **Professional Templates**: Supports custom Pandoc/LaTeX templates for complete control over PDF appearance.
*   **Safe & Predictable**: Use `--dry-run` to see what will be built, then `--execute-run` to proceed with confidence.
*   **Standardized & Portable**: Follows XDG standards for state management and can be run on any project structure.

## 2. Dependencies

*   **Core Engine**: `pandoc`
*   **Configuration Parser**: `yq`
*   **Diagram & Content Filters**: `pandoc-plantuml-filter`, `mermaid-filter`, `pandoc-dbml-filter`, `pandoc-fignos`, `pandoc-tablenos`, `pandoc-secnos`, `pandoc-imagine`, `pandoc-include`
*   **TeX Distribution**: A LaTeX distribution (like TeX Live) is required by Pandoc for PDF generation.

## 3. Basic Usage

1.  **Configure**: Create a `pdf-generator.toml` file at your project root.
2.  **Write**: Author your content in Markdown files.
3.  **Generate**: Run the tool from your project's root directory.

```bash
# Preview the build plan
./tools/pdf-generator/bin/pdf-generator --dry-run

# Execute the build
./tools/pdf-generator/bin/pdf-generator
```

## 4. Documentation

For a complete guide to installation, configuration, and advanced features, please see the **[User Guide](./docs/USER_GUIDE.md)**.

---
