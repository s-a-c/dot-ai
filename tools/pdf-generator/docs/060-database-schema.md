# Database Schema

<details>
<summary>Table of Contents</summary>

- [1. Overview](#1-overview)
  - [1.1. Purpose](#11-purpose)
  - [1.2. Technology](#12-technology)
- [2. Schema Definition](#2-schema-definition)
  - [2.1. Table: runs](#21-table-runs)
  - [2.2. Table: generated_pdfs](#22-table-generated_pdfs)
  - [2.3. Indexes](#23-indexes)
  - [2.4. Relationships](#24-relationships)
- [3. Data Types](#3-data-types)
  - [3.1. Status Values](#31-status-values)
  - [3.2. Timestamps](#32-timestamps)
  - [3.3. File Paths](#33-file-paths)
- [4. Common Queries](#4-common-queries)
  - [4.1. Run Information](#41-run-information)
  - [4.2. File Information](#42-file-information)
  - [4.3. Statistics](#43-statistics)
- [5. Maintenance](#5-maintenance)
  - [5.1. Cleanup Operations](#51-cleanup-operations)
  - [5.2. Integrity Checks](#52-integrity-checks)
  - [5.3. Vacuum and Optimization](#53-vacuum-and-optimization)
- [Navigation](#navigation)

</details>

---

**Compliant with**: AI-GUIDELINES.md v1.0

## 1. Overview

### 1.1. Purpose

The database serves as persistent state storage for pndcgn, enabling:
- **Caching**: Skip regeneration of unchanged files
- **Resumability**: Continue interrupted runs
- **History**: Track all processing runs
- **Statistics**: Query conversion history and performance

### 1.2. Technology

**Database Engine**: SQLite 3.x
- Single file: `${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn/cache.sqlite`
- Journal mode: WAL (Write-Ahead Logging)
- No server required
- ACID compliant

**Benefits of SQLite**:
- Zero configuration
- Cross-platform
- Reliable crash recovery
- Concurrent read access
- Small footprint (~1MB including indexes)

---

## 2. Schema Definition

### 2.1. Table: runs

**Purpose**: Track all processing runs

```sql
CREATE TABLE IF NOT EXISTS runs (
    run_id TEXT PRIMARY KEY,
    start_time INTEGER NOT NULL,
    end_time INTEGER,
    source_dir TEXT NOT NULL,
    target_dir TEXT NOT NULL,
    output_type TEXT NOT NULL,
    status TEXT NOT NULL,
    dry_run INTEGER NOT NULL,
    files_total INTEGER DEFAULT 0,
    files_processed INTEGER DEFAULT 0,
    files_skipped INTEGER DEFAULT 0,
    files_failed INTEGER DEFAULT 0
);
```

**Column descriptions**:

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `run_id` | TEXT | PRIMARY KEY | ULID identifier (26 chars) |
| `start_time` | INTEGER | NOT NULL | Unix timestamp (seconds since epoch) |
| `end_time` | INTEGER | NULL allowed | Completion time (NULL if incomplete) |
| `source_dir` | TEXT | NOT NULL | Absolute path to input directory |
| `target_dir` | TEXT | NOT NULL | Absolute path to output parent |
| `output_type` | TEXT | NOT NULL | Output format (pdf, epub, html, etc.) |
| `status` | TEXT | NOT NULL | Run status (see status values) |
| `dry_run` | INTEGER | NOT NULL | Boolean: 1 = dry-run, 0 = actual |
| `files_total` | INTEGER | DEFAULT 0 | Total files discovered |
| `files_processed` | INTEGER | DEFAULT 0 | Files converted in this run |
| `files_skipped` | INTEGER | DEFAULT 0 | Files skipped (cached) |
| `files_failed` | INTEGER | DEFAULT 0 | Files that failed conversion |

**Example record**:
```sql
INSERT INTO runs VALUES (
    '01HN7XJKQM3R8Y2VWSDP4T6FGZ',
    1699564800,
    1699564920,
    '/home/user/docs',
    '/home/user/output',
    'pdf',
    'complete',
    0,
    150,
    10,
    140,
    0
);
```

### 2.2. Table: generated_pdfs

**Purpose**: Track all generated files and their fingerprints

```sql
CREATE TABLE IF NOT EXISTS generated_pdfs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    run_id TEXT NOT NULL,
    source_path TEXT NOT NULL,
    output_path TEXT NOT NULL,
    fingerprint TEXT NOT NULL,
    file_size INTEGER NOT NULL,
    mtime INTEGER NOT NULL,
    generated_at INTEGER NOT NULL,
    FOREIGN KEY (run_id) REFERENCES runs(run_id) ON DELETE CASCADE
);
```

**Column descriptions**:

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | INTEGER | PK, AUTO | Unique record ID |
| `run_id` | TEXT | NOT NULL, FK | Associated run ID |
| `source_path` | TEXT | NOT NULL | Absolute path to source .md file |
| `output_path` | TEXT | NOT NULL | Absolute path to generated file |
| `fingerprint` | TEXT | NOT NULL | Content fingerprint (size:mtime:sha256) |
| `file_size` | INTEGER | NOT NULL | Source file size (bytes) |
| `mtime` | INTEGER | NOT NULL | Source modification time (Unix timestamp) |
| `generated_at` | INTEGER | NOT NULL | Generation time (Unix timestamp) |

**Example record**:
```sql
INSERT INTO generated_pdfs (run_id, source_path, output_path, fingerprint, file_size, mtime, generated_at)
VALUES (
    '01HN7XJKQM3R8Y2VWSDP4T6FGZ',
    '/home/user/docs/notes.md',
    '/home/user/output/pndcgn/pdf-01HN7X.../notes.pdf',
    '12345:1699564800:a3f5b9c2d8e1f4a7b6c3d2e9f1a8b4c7',
    12345,
    1699564800,
    1699564805
);
```

### 2.3. Indexes

**Index on fingerprints** (for cache lookups):
```sql
CREATE INDEX IF NOT EXISTS idx_fingerprints 
    ON generated_pdfs(fingerprint);
```

**Index on run_id** (for filtering by run):
```sql
CREATE INDEX IF NOT EXISTS idx_run_id 
    ON generated_pdfs(run_id);
```

**Index on source_path** (for detecting duplicates):
```sql
CREATE INDEX IF NOT EXISTS idx_source_path 
    ON generated_pdfs(source_path);
```

**Index performance**:
- Fingerprint lookups: O(log n) instead of O(n)
- Negligible storage overhead (~5% of table size)
- Automatically maintained by SQLite

### 2.4. Relationships

```log
┌──────────────────┐
│      runs        │
│                  │
│  run_id (PK)     │◄──────┐
│  start_time      │       │
│  end_time        │       │
│  source_dir      │       │
│  target_dir      │       │
│  output_type     │       │
│  status          │       │
│  dry_run         │       │
│  files_*         │       │
└──────────────────┘       │
                           │
                           │ Foreign Key
                           │ ON DELETE CASCADE
                           │
                    ┌──────┴──────────────┐
                    │  generated_pdfs     │
                    │                     │
                    │  id (PK)            │
                    │  run_id (FK)        │
                    │  source_path        │
                    │  output_path        │
                    │  fingerprint        │
                    │  file_size          │
                    │  mtime              │
                    │  generated_at       │
                    └─────────────────────┘
```

**Cascade behavior**:
- Deleting a run automatically deletes associated generated_pdfs records
- Maintains referential integrity
- Prevents orphaned records

---

## 3. Data Types

### 3.1. Status Values

**Enum (stored as TEXT)**:
- `running` - Run in progress
- `complete` - Run finished successfully
- `failed` - Run failed (unrecoverable error)
- `interrupted` - Run stopped mid-processing (resumable)
- `partial` - Run completed with some file failures

**State transitions**:
```log
running → complete      (all files processed)
running → failed        (fatal error)
running → interrupted   (user/system termination)
running → partial       (some files failed)
```

### 3.2. Timestamps

**Format**: Unix timestamp (INTEGER)
- Seconds since 1970-01-01 00:00:00 UTC
- Standard across Unix systems
- Easy arithmetic (durations, comparisons)

**Example conversions**:
```bash
# Current time
date +%s
# 1699564800

# Convert to human-readable
date -d @1699564800
# Fri Nov 10 00:00:00 UTC 2023

# Duration
echo $((end_time - start_time))
# 120 (seconds)
```

### 3.3. File Paths

**Format**: Absolute paths (TEXT)
- Always resolved to absolute form
- Symlinks dereferenced
- Spaces and special characters allowed

**Example**:
```log
/home/user/Documents/My Notes/chapter 1.md
```

**Path handling in queries**:
```sql
-- Use LIKE for pattern matching
SELECT * FROM generated_pdfs WHERE source_path LIKE '%/notes/%';

-- Escape special characters
SELECT * FROM generated_pdfs WHERE source_path = '/path/with spaces/file.md';
```

---

## 4. Common Queries

### 4.1. Run Information

**List recent runs**:
```sql
SELECT 
    run_id,
    datetime(start_time, 'unixepoch', 'localtime') AS start,
    output_type,
    status,
    files_processed,
    files_skipped,
    files_failed
FROM runs
ORDER BY start_time DESC
LIMIT 10;
```

**Get run details**:
```sql
SELECT * FROM runs WHERE run_id = '01HN7XJKQM3R8Y2VWSDP4T6FGZ';
```

**Find incomplete runs**:
```sql
SELECT run_id, start_time, status
FROM runs
WHERE status IN ('running', 'interrupted')
ORDER BY start_time DESC;
```

### 4.2. File Information

**List files for run**:
```sql
SELECT 
    source_path,
    output_path,
    datetime(generated_at, 'unixepoch', 'localtime') AS generated
FROM generated_pdfs
WHERE run_id = '01HN7XJKQM3R8Y2VWSDP4T6FGZ'
ORDER BY source_path;
```

**Check if file cached**:
```sql
SELECT output_path
FROM generated_pdfs
WHERE fingerprint = '12345:1699564800:a3f5b9c2d8e1f4a7b6c3d2e9f1a8b4c7'
AND output_path LIKE '%.pdf'
ORDER BY generated_at DESC
LIMIT 1;
```

**Find duplicates** (same source processed multiple times):
```sql
SELECT source_path, COUNT(*) AS count
FROM generated_pdfs
GROUP BY source_path
HAVING count > 1
ORDER BY count DESC;
```

### 4.3. Statistics

**Total files processed**:
```sql
SELECT COUNT(*) AS total FROM generated_pdfs;
```

**Processing rate** (files per minute):
```sql
SELECT 
    run_id,
    files_processed,
    (end_time - start_time) / 60.0 AS duration_minutes,
    files_processed / ((end_time - start_time) / 60.0) AS rate
FROM runs
WHERE status = 'complete' AND end_time IS NOT NULL
ORDER BY start_time DESC;
```

**Cache efficiency**:
```sql
SELECT 
    run_id,
    files_total,
    files_skipped,
    ROUND(100.0 * files_skipped / files_total, 2) AS cache_hit_rate
FROM runs
WHERE files_total > 0
ORDER BY start_time DESC;
```

**Disk usage by output type**:
```sql
SELECT 
    output_type,
    COUNT(*) AS file_count,
    SUM(file_size) / 1024 / 1024 AS total_mb
FROM generated_pdfs gp
JOIN runs r ON gp.run_id = r.run_id
GROUP BY output_type;
```

---

## 5. Maintenance

### 5.1. Cleanup Operations

**Remove completed runs older than 30 days**:
```sql
DELETE FROM runs
WHERE status = 'complete'
AND end_time < strftime('%s', 'now', '-30 days');
-- Note: CASCADE deletes associated generated_pdfs
```

**Remove orphaned file records** (output no longer exists):
```bash
# This requires external verification
sqlite3 cache.sqlite <<'EOF'
SELECT id, output_path FROM generated_pdfs;
EOF | while IFS='|' read -r id path; do
    if [[ ! -f "${path}" ]]; then
        sqlite3 cache.sqlite "DELETE FROM generated_pdfs WHERE id = ${id};"
    fi
done
```

**Remove dry-run entries**:
```sql
DELETE FROM runs WHERE dry_run = 1;
```

### 5.2. Integrity Checks

**Check for orphaned records** (generated_pdfs without run):
```sql
SELECT gp.*
FROM generated_pdfs gp
LEFT JOIN runs r ON gp.run_id = r.run_id
WHERE r.run_id IS NULL;
```

**Check for runs without files**:
```sql
SELECT r.run_id, r.files_total
FROM runs r
LEFT JOIN generated_pdfs gp ON r.run_id = gp.run_id
WHERE r.status = 'complete' AND gp.id IS NULL;
```

**Verify referential integrity**:
```sql
PRAGMA foreign_key_check;
-- Returns empty result if integrity is OK
```

### 5.3. Vacuum and Optimization

**Reclaim space after deletions**:
```sql
VACUUM;
```

**Analyze for query optimization**:
```sql
ANALYZE;
```

**Check database size**:
```bash
du -h "${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn/cache.sqlite"
```

**Checkpoint WAL**:
```sql
PRAGMA wal_checkpoint(RESTART);
```

**Optimize indexes**:
```sql
REINDEX;
```

**Full maintenance routine**:
```bash
#!/usr/bin/env bash
# Maintenance script for pndcgn database

db="${XDG_STATE_HOME:-${HOME}/.local/state}/pndcgn/cache.sqlite"

printf "Running database maintenance...\\n"

# Integrity check
printf "Checking integrity...\\n"
sqlite3 "${db}" "PRAGMA integrity_check;" | head -1

# Remove old completed runs (>90 days)
printf "Cleaning old runs...\\n"
sqlite3 "${db}" "DELETE FROM runs WHERE status = 'complete' AND end_time < strftime('%s', 'now', '-90 days');"

# Checkpoint WAL
printf "Checkpointing WAL...\\n"
sqlite3 "${db}" "PRAGMA wal_checkpoint(RESTART);"

# Vacuum
printf "Vacuuming...\\n"
sqlite3 "${db}" "VACUUM;"

# Analyze
printf "Analyzing...\\n"
sqlite3 "${db}" "ANALYZE;"

printf "Maintenance complete\\n"
```

---

## Navigation

[← Technical Specification](050-technical-specification.md) | [↑ Top](#database-schema) | [API Reference →](070-api-reference.md)
