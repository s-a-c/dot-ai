# Documentation Standards

## Table of Contents

<details>
<summary>Expand Table of Contents</summary>

- [Documentation Standards](#documentation-standards)
  - [Table of Contents](#table-of-contents)
  - [1. Introduction](#1-introduction)
  - [2. Core Principle: Clarity for Junior Developers](#2-core-principle-clarity-for-junior-developers)
  - [3. Accessibility (WCAG 2.1 AA)](#3-accessibility-wcag-21-aa)
  - [3.1. Color and Contrast](#31-color-and-contrast)
  - [3.2. Mermaid Diagram Standards](#32-mermaid-diagram-standards)
  - [3.3. Code Blocks](#33-code-blocks)
  - [4. Structure and Formatting](#4-structure-and-formatting)
  - [5. Link Integrity: TOC-Heading Synchronization](#5-link-integrity-toc-heading-synchronization)
  - [5.1. GitHub Anchor Generation Algorithm](#51-github-anchor-generation-algorithm)
  - [5.2. TOC Structure Requirements](#52-toc-structure-requirements)
  - [5.3. Remediation Process](#53-remediation-process)
  - [6. Documentation Remediation Implementation Plan (DRIP)](#6-documentation-remediation-implementation-plan-drip)
  - [6.1. DRIP Phases](#61-drip-phases)
  - [6.2. Task Management](#62-task-management)
  - [7. Specific Markdown Formatting Rules](#7-specific-markdown-formatting-rules)
  - [7.1. Headings](#71-headings)
  - [7.2. Lists](#72-lists)
  - [7.4. Links and Images](#74-links-and-images)
  - [7.5. Emphasis](#75-emphasis)
  - [8. Asset Management](#8-asset-management)
  - [9. Exercise Organization](#9-exercise-organization)
  - [10. Document Structure Standards](#10-document-structure-standards)
  - [10.1. Required Elements](#101-required-elements)
  - [10.2. File Naming Convention](#102-file-naming-convention)
  - [10.3. Folder Organization](#103-folder-organization)
  - [10.4. Document Organization Pattern](#104-document-organization-pattern)
  - [11. Quality Assurance](#11-quality-assurance)
  - [11.1. Content Validation](#111-content-validation)
  - [11.2. Link Integrity](#112-link-integrity)
  - [11.3. Accessibility Compliance](#113-accessibility-compliance)
  - [12. Maintenance Procedures](#12-maintenance-procedures)
  - [12.1. Regular Updates](#121-regular-updates)
  - [12.2. Content Refresh](#122-content-refresh)
  - [13. Integration with Development Workflows](#13-integration-with-development-workflows)
  - [13.1. Documentation in Development](#131-documentation-in-development)
  - [13.2. Review Processes](#132-review-processes)
  - [14. Navigation](#14-navigation)

</details>

## 1. Introduction

These documentation standards provide comprehensive guidelines for creating clear, accessible, and maintainable documentation. All documentation MUST be clear, actionable, and suitable for a junior developer to understand and implement. This means using simple language, providing concrete examples, defining technical terms, and explaining the "why" behind decisions.

## 2. Core Principle: Clarity for Junior Developers

All documentation MUST be clear, actionable, and suitable for a junior developer to understand and implement. This means using simple language, providing concrete examples, defining technical terms, and explaining the "why" behind decisions.

## 3. Accessibility (WCAG 2.1 AA)

Accessibility is a primary requirement for all documentation.

## 3.1. Color and Contrast

*   **Contrast Ratio:** All text must have a minimum contrast ratio of **4.5:1** against its background (WCAG AA). Large text (18pt+ or 14pt+ bold) must have a minimum of 3:1.
*   **Color Independence:** Information must not be conveyed by color alone. Use labels, shapes, and patterns in addition to color.
*   **Approved Palette:** Use the approved high-contrast color palette for all visual elements, including diagrams.

## 3.2. Mermaid Diagram Standards

All Mermaid diagrams MUST be created with accessibility in mind.

*   **High-Contrast Themes:** Use the provided high-contrast `dark` (recommended) or `light` theme configurations. These themes use the approved, WCAG-compliant color palette.
*   **`classDef` for Styling:** Use `classDef` to apply styles to nodes, ensuring that `fill`, `stroke`, and `color` (for text) are all explicitly set to high-contrast combinations.
*   **Alt Text:** Provide descriptive alternative text for all diagrams to support screen readers.

**Example of an accessible Mermaid diagram node definition:**

## 3.3. Code Blocks

When a code block is placed inside a colored container, it MUST be wrapped in a dark-themed container to ensure the syntax highlighting has sufficient contrast.

*   **Code Block Background:** `#1e1e1e` (VS Code dark theme)
*   **Code Block Text:** `#d4d4d4` (light gray)

## 4. Structure and Formatting

*   **Markdown:** All documentation must be written in Markdown.
*   **Title Placement:** The first line of every documentation file MUST be the document title as an H1 (`# Document Title`). Place policy acknowledgement or metadata *after* the title.
*   **H1 Headings:** Do NOT add HTML anchors to H1 headings. Use plain `# Document Title` format.
*   **Hierarchical Numbering:** Every heading below the document title MUST include a sequential numeric prefix that reflects its depth (for example, `## 2 Section Title`, `### 2.1 Subsection Title`, `#### 2.1.1 Detail`). Numbers must increment monotonically within each level.
*   **Table of Contents (TOC):** All documents must have an unnumbered TOC heading immediately after the introduction (section 1), with entries linking to numbered sections starting from section 2.
*   **Navigation:** All documentation must end with a `## Navigation` section in the format `[← Previous Title](previous.md) | [↑ Top](#primary-heading-slug) | [Next Title →](next.md)`, wrapping around to the first/last document as needed.

## 5. Link Integrity: TOC-Heading Synchronization

Broken links in documentation are unacceptable. The **TOC-Heading Synchronization Methodology** MUST be used to ensure 100% link integrity.

## 5.1. GitHub Anchor Generation Algorithm

All internal anchor links (`#...`) MUST be generated using the following algorithm, which mimics GitHub's anchor generation:

1.  Convert heading text to lowercase.
2.  Replace spaces with hyphens (`-`).
3.  Remove all characters except alphanumeric characters and hyphens.
4.  Handle special cases like ampersands (`&` becomes `--`).
5.  Remove leading/trailing hyphens.

## 5.2. TOC Structure Requirements

**TOC Requirements:**
- **Unnumbered Heading:** TOC must use unnumbered heading (e.g., `## Table of Contents`, not `## 2. Table of Contents`)
- **Collapsible Format:** TOC must be wrapped in HTML `<details>`/`<summary>` tags for collapsibility
- **Numbered References:** TOC entries must reference numbered sections (e.g., "2. Accessibility")
- **Placement:** TOC must be placed immediately after the document title (before the introduction section)
- **Complete Coverage:** TOC must include all main sections and subsections
- **GitHub-Compatible Anchors:** All TOC links must use GitHub's anchor generation algorithm

**TOC Format Example:**
```markdown
# Document Title

## Table of Contents

<details>
<summary>Expand Table of Contents</summary>

- [1. Introduction](#1-introduction)
- [2. Main Section](#2-main-section)
- [3. Next Section](#3-next-section)
- [4. Navigation](#4-navigation)

</details>

## 1. Introduction

[Introduction content here]

## 2. Main Section

[Content referenced in TOC]

## 3. Next Section

[More content]

## 4. Navigation

[← Previous Title](previous.md) | [↑ Top](#document-title) | [Next Title →](next.md)
```

## 5.3. Remediation Process

When fixing broken links, **prioritize creating the missing content** over removing the broken link from the Table of Contents. This ensures the documentation becomes more complete over time.

## 6. Documentation Remediation Implementation Plan (DRIP)

For large-scale documentation refactoring or remediation projects, the DRIP methodology provides a structured, 4-week plan.

## 6.1. DRIP Phases

*   **Week 1: Analysis & Planning:** Audit the documentation, identify gaps, and create a remediation strategy.
*   **Week 2: Content Remediation:** Fix accessibility issues, create and enhance content, and modernize code examples.
*   **Week 3: Link Integrity & Navigation:** Apply the TOC-Heading Synchronization methodology to fix all broken links.
*   **Week 4: Quality Assurance & Validation:** Perform a final, comprehensive validation of all documentation.

## 6.2. Task Management

DRIP uses a hierarchical task list with color-coded status indicators (🔴, 🟡, 🟠, 🟢) and a priority system (P1-P5) to track progress.

## 7. Specific Markdown Formatting Rules

To ensure consistency and prevent common rendering issues, the following specific Markdown rules MUST be followed.

## 7.1. Headings

*   **Style:** Use ATX style headings (`#`).
*   **H1 Headings:** Do NOT add HTML anchors to H1 headings. Use plain `# Document Title` format.
*   **Increment:** Increment heading levels by one at a time (e.g., `##` follows `#`). (MD001)
*   **Spacing:** Add a single space after the hash. (MD018)
*   **Surrounding Lines:** Surround all headings with blank lines. (MD022)

## 7.2. Lists

*   **Style:** Use fenced code blocks (three backticks ```). (MD046)
*   **Language Specifier:** Always specify the language for syntax highlighting (e.g., ```php). Use `log` for plain text or unknown/unspecified source languages. (MD040)
*   **Surrounding Lines:** Surround all fenced code blocks with blank lines. (MD031)

## 7.4. Links and Images

*   **Syntax:** Use standard Markdown link syntax `[text](https://example.com)`.
*   **Alt Text:** All images MUST have descriptive alternate text. (MD045)

## 7.5. Emphasis

*   **Italics:** Use a single asterisk (`*text*`). (MD049)
*   **Bold:** Use double asterisks (`**text**`). (MD050)

## 8. Asset Management

*   **Storage:** All assets (images, diagrams, etc.) used in project documentation MUST be stored in a suitably-named folder within the `docs/assets/` directory of the project root.
*   **Linking:** Reference these assets using relative paths.

## 9. Exercise Organization

For educational documentation, exercises and their solutions should be organized systematically:

*   **Exercises:** Place all exercise files in a dedicated `888-exercises` folder.
*   **Answers:** Place all corresponding sample answers in an `888-sample-answers` folder.
*   **Consistency:** Ensure a clear and consistent naming convention between exercise files and their answers.

## 10. Document Structure Standards

## 10.1. Required Elements

Every document MUST include:

1. **H1 Heading with Anchor:** `<a id="document-name"></a>Document Title`
2. **Introduction Section:** Section 1 (numbered)
3. **TOC Section:** Unnumbered TOC heading immediately after introduction, with collapsible TOC entries
4. **Numbered Sections:** All sections after TOC are numbered sequentially (2., 3., 4., etc.)
5. **Navigation Footer:** `[← Previous Title](previous.md) | [↑ Top](#primary-heading-slug) | [Next Title →](next.md)`

## 10.2. File Naming Convention

*   **Prefix:** 3-digit number in multiples of 10 (010, 020, 030...)
*   **Descriptive Name:** Clear, hyphen-separated filename
*   **Extension:** `.md` for all documentation files

## 10.3. Folder Organization

*   **Index Files:** Each folder must have a `000-index.md` file
*   **Sequential Ordering:** Files are ordered by prefix number
*   **Logical Grouping:** Related content grouped in appropriate folders

## 10.4. Document Organization Pattern

**Standard Document Structure:**
1. **H1 Title** (with anchor)
2. **Introduction** (section 1 - numbered)
3. **Table of Contents** (unnumbered heading, collapsible)
4. **Numbered Sections** (sections 2+ - numbered sequentially)
5. **Navigation Footer** (final unnumbered section using `[← Previous Title](previous.md) | [↑ Top](#primary-heading-slug) | [Next Title →](next.md)`)

**TOC Requirements:**
- TOC heading must be unnumbered (e.g., `## Table of Contents`)
- TOC must be collapsible using `<details>`/`<summary>` tags
- TOC entries must reference numbered sections (2., 3., 4., etc.)
- All content after TOC follows sequential numbering starting from section 2

## 11. Quality Assurance

## 11.1. Content Validation

*   **Accuracy:** All technical information must be current and accurate
*   **Clarity:** Content must be understandable by junior developers
*   **Completeness:** Include all necessary context and examples
*   **Consistency:** Maintain consistent formatting and terminology

## 11.2. Link Integrity

*   **Internal Links:** All internal links must work correctly
*   **External Links:** Regular validation of external references
*   **Anchor Generation:** Use GitHub's anchor generation algorithm
*   **Cross-References:** Maintain accurate cross-references between documents

## 11.3. Accessibility Compliance

*   **WCAG 2.1 AA:** All content must meet accessibility standards
*   **Color Contrast:** Minimum 4.5:1 contrast ratio for text
*   **Screen Readers:** Proper alt text and structure for screen readers
*   **Visual Design:** High contrast themes and clear typography

## 12. Maintenance Procedures

## 12.1. Regular Updates

*   **Quarterly Reviews:** Comprehensive review of all documentation
*   **Version Updates:** Update examples with current best practices
*   **Link Validation:** Check and fix broken links
*   **Accessibility Audit:** Regular WCAG compliance checks

## 12.2. Content Refresh

*   **Technical Accuracy:** Verify all code examples and commands
*   **Best Practices:** Update with current industry standards
*   **User Feedback:** Incorporate feedback from development team
*   **Performance:** Optimize document loading and navigation

## 13. Integration with Development Workflows

## 13.1. Documentation in Development

*   **Code Comments:** Follow documentation standards for inline comments
*   **README Files:** Apply standards to project README files
*   **API Documentation:** Use consistent formatting for API docs
*   **Change Logs:** Maintain structured change documentation

## 13.2. Review Processes

*   **Peer Review:** Documentation review as part of code review process
*   **Accessibility Review:** Include accessibility checks in reviews
*   **Link Validation:** Automated checking of link integrity
*   **Style Consistency:** Validation of formatting standards

## 14. Navigation

**Previous:** [Previous Doc](path) | **Next:** [Next Doc](path) | **Top**(#documentation-standards)
