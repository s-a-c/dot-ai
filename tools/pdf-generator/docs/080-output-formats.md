# Output Formats

<details>
<summary>Table of Contents</summary>

- [1. Overview](#1-overview)
  - [1.1. Supported Formats](#11-supported-formats)
  - [1.2. Pandoc Integration](#12-pandoc-integration)
- [2. PDF Output](#2-pdf-output)
  - [2.1. Characteristics](#21-characteristics)
  - [2.2. Use Cases](#22-use-cases)
  - [2.3. Limitations](#23-limitations)
- [3. EPUB Output](#3-epub-output)
  - [3.1. Characteristics](#31-characteristics)
  - [3.2. Use Cases](#32-use-cases)
  - [3.3. Limitations](#33-limitations)
- [4. HTML Output](#4-html-output)
  - [4.1. Characteristics](#41-characteristics)
  - [4.2. Use Cases](#42-use-cases)
  - [4.3. Limitations](#43-limitations)
- [5. DOCX Output](#5-docx-output)
  - [5.1. Characteristics](#51-characteristics)
  - [5.2. Use Cases](#52-use-cases)
  - [5.3. Limitations](#53-limitations)
- [6. Other Formats](#6-other-formats)
  - [6.1. ODT (OpenDocument)](#61-odt-opendocument)
  - [6.2. RTF (Rich Text Format)](#62-rtf-rich-text-format)
  - [6.3. LaTeX](#63-latex)
- [7. Format Selection Guide](#7-format-selection-guide)
- [Navigation](#navigation)

</details>

---

**Compliant with**: AI-GUIDELINES.md v1.0

## 1. Overview

### 1.1. Supported Formats

**pndcgn** supports all output formats provided by pandoc:

**Document formats**:
- `pdf` - Portable Document Format (default)
- `epub` - Electronic Publication (ebook)
- `docx` - Microsoft Word
- `odt` - OpenDocument Text
- `rtf` - Rich Text Format

**Web formats**:
- `html` - HTML5
- `html4` - HTML 4.01

**Markup formats**:
- `latex` - LaTeX source
- `markdown` - Markdown (various flavors)
- `rst` - reStructuredText

### 1.2. Pandoc Integration

**Format specification**:
```bash
# Via command-line
pdf-generator --type <format> source_dir

# Pandoc invocation
pandoc --from markdown --to <format> --output file.<ext> source.md
```

**Automatic file extensions**:
- `pdf` → `.pdf`
- `epub` → `.epub`
- `html` → `.html`
- `docx` → `.docx`
- etc.

---

## 2. PDF Output

### 2.1. Characteristics

**Format**: PDF 1.5+ (depends on pandoc/LaTeX version)

**Features**:
- Pagination with page numbers
- Hyperlinked table of contents
- Embedded fonts
- Vector graphics support
- Searchable text

**Generation method**: Pandoc uses LaTeX engine (typically pdflatex or xelatex)

**File size**: 50KB - 5MB (typical for text documents)

### 2.2. Use Cases

**Best for**:
- Print-ready documents
- Archival (long-term preservation)
- Documents requiring precise layout
- Official reports and papers
- Forms and certificates

**Example**:
```bash
# Generate PDFs for documentation
pdf-generator --type pdf ~/docs/manuals
```

### 2.3. Limitations

**Constraints**:
- Requires LaTeX installation (texlive, mactex)
- Large dependency footprint (~500MB+)
- Slower conversion than other formats
- Complex tables may not render well
- Limited support for interactive elements

**Workaround**: Use HTML for interactive content, PDF for printing

---

## 3. EPUB Output

### 3.1. Characteristics

**Format**: EPUB 3.0 (ZIP archive with XHTML + CSS)

**Features**:
- Reflowable text (adapts to screen size)
- Embedded images and styles
- Chapter-based navigation
- Metadata (title, author, language)
- Supports MathML

**File size**: 10KB - 1MB (typical for text)

### 3.2. Use Cases

**Best for**:
- E-readers (Kindle, Kobo, Nook)
- Mobile reading apps
- Accessible documents (screen readers)
- Books and long-form content
- Multi-chapter documents

**Example**:
```bash
# Generate EPUBs for e-reader library
pdf-generator --type epub ~/library/books
```

### 3.3. Limitations

**Constraints**:
- Limited layout control
- Complex formatting may be lost
- Tables may not render well on small screens
- No page numbers (reflowable text)

**Workaround**: Keep markdown simple, use semantic headings

---

## 4. HTML Output

### 4.1. Characteristics

**Format**: HTML5 with embedded CSS

**Features**:
- Standalone HTML file (no external resources)
- Responsive design (adapts to viewport)
- Hyperlinks preserved
- Syntax highlighting for code blocks
- MathJax for equations

**File size**: 5KB - 500KB (typical)

### 4.2. Use Cases

**Best for**:
- Web publishing
- Preview/quick view
- Email-friendly documents
- Sharing via HTTP
- Interactive content (links, anchors)

**Example**:
```bash
# Generate HTML for web deployment
pdf-generator --type html ~/blog/posts

# Serve with simple HTTP server
cd ~/blog/posts/pndcgn/html-*/
python3 -m http.server 8000
```

### 4.3. Limitations

**Constraints**:
- No print-specific features (page breaks)
- Depends on browser rendering
- Single file can be large with embedded images

**Workaround**: Use `--standalone` flag in pandoc for self-contained HTML

---

## 5. DOCX Output

### 5.1. Characteristics

**Format**: Microsoft Word 2007+ (.docx)

**Features**:
- Editable in Word, LibreOffice, Google Docs
- Preserves formatting (headings, lists, bold/italic)
- Supports tables and images
- Track changes compatible

**File size**: 10KB - 2MB (typical)

### 5.2. Use Cases

**Best for**:
- Collaborative editing
- Business documents (reports, proposals)
- Integration with MS Office workflows
- Documents requiring further editing
- Comment/review workflows

**Example**:
```bash
# Generate DOCX for review process
pdf-generator --type docx ~/drafts

# Open in Word for editing
open ~/drafts/pndcgn/docx-*/document.docx
```

### 5.3. Limitations

**Constraints**:
- Complex markdown may not convert perfectly
- Code blocks have basic formatting
- Not ideal for version control (binary format)

**Workaround**: Keep markdown simple, use styles consistently

---

## 6. Other Formats

### 6.1. ODT (OpenDocument)

**Characteristics**:
- Open standard (ISO/IEC 26300)
- Compatible with LibreOffice, OpenOffice
- XML-based (can be extracted/inspected)

**Usage**:
```bash
pdf-generator --type odt ~/documents
```

**Use cases**: Linux environments, open-source workflows, government documents

### 6.2. RTF (Rich Text Format)

**Characteristics**:
- Legacy format (Microsoft)
- Plain-text based (human-readable)
- Wide compatibility (all word processors)

**Usage**:
```bash
pdf-generator --type rtf ~/legacy-docs
```

**Use cases**: Maximum compatibility, legacy systems, email attachments

### 6.3. LaTeX

**Characteristics**:
- LaTeX source code (not rendered)
- Allows fine-grained control
- Can be compiled to PDF with pdflatex

**Usage**:
```bash
pdf-generator --type latex ~/papers

# Compile manually
cd ~/papers/pndcgn/latex-*/
pdflatex document.tex
```

**Use cases**: Academic papers, presentations (beamer), custom styling

---

## 7. Format Selection Guide

**Quick reference**:

| Use Case | Recommended Format | Alternative |
|----------|-------------------|-------------|
| Print distribution | PDF | - |
| E-reader consumption | EPUB | PDF |
| Web publishing | HTML | - |
| Collaborative editing | DOCX | ODT |
| Archival (long-term) | PDF | ODT |
| Email attachment | PDF | RTF |
| Academic paper | PDF (via LaTeX) | DOCX |
| Mobile viewing | EPUB | HTML |
| Accessible documents | EPUB | HTML |
| Maximum compatibility | RTF | DOCX |

**Decision tree**:
```log
Need to print?
├─ Yes → PDF
└─ No
   ├─ Need to edit?
   │  ├─ Yes → DOCX (or ODT)
   │  └─ No
   │     ├─ For e-reader? → EPUB
   │     ├─ For web? → HTML
   │     └─ Maximum compatibility? → RTF
```

**Multiple formats**:
```bash
# Generate PDF and EPUB from same source
pdf-generator --type pdf ~/book
pdf-generator --type epub ~/book

# Cache reused, conversion is fast
```

---

## Navigation

[← API Reference](070-api-reference.md) | [↑ Top](#output-formats) | [Implementation Plan →](110-implementation-plan.md)
