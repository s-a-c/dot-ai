# Appendix E - API Reference

**Programmatic Interface for macOS 26 Tahoe RAM Disk Management**

---

## Table of Contents

1. [Shell Script API](#1-shell-script-api)
2. [LaunchAgent Configuration API](#2-launchagent-configuration-api)
3. [Environment Variable API](#3-environment-variable-api)
4. [Logging API](#4-logging-api)
5. [Monitoring API](#5-monitoring-api)
6. [Enterprise Management API](#6-enterprise-management-api)
7. [Exit Codes](#7-exit-codes)
8. [Integration Examples](#8-integration-examples)

---

## 1. Shell Script API

### 1.1. `ramdisk-manager.sh`

**Command Interface**:
```bash
ramdisk-manager.sh <command> [parameters]
```

**Commands**:
- `create <size_gb>`: Create RAM disk with specified size in GB
- `destroy`: Destroy existing RAM disk
- `status`: Show current RAM disk status

**Parameters**:
- `size_gb`: Integer between 1-128 (default: 2)

**Exit Codes**:
- `0`: Success
- `1`: Invalid parameters
- `2`: Insufficient memory
- `3`: RAM disk already exists
- `4`: Permission denied
- `5`: Device attach failed

**Example Usage**:
```bash
# Create 4GB RAM disk
~/bin/ramdisk-manager.sh create 4

# Check status
~/bin/ramdisk-manager.sh status

# Destroy
~/bin/ramdisk-manager.sh destroy
```

**Return Values** (via stdout):
```bash
# Status command returns JSON
{
  "mounted": true,
  "mount_point": "/Volumes/BrowserRAM",
  "size_gb": 4,
  "used_gb": 1.2,
  "available_gb": 2.8,
  "device": "/dev/disk3",
  "filesystem": "apfs"
}
```

### 1.2. `browser-sync-master.sh`

**Command Interface**:
```bash
browser-sync-master.sh <command>
```

**Commands**:
- `start`: Initialize all browser configurations
- `stop`: Stop and sync all configurations
- `sync`: Periodic sync of profiles

**Environment Variables**:
- `SYNC_INTERVAL_MINUTES`: Sync frequency (default: 15)
- `BACKUP_DIR`: Backup location (default: ~/.browser-backup)

**Exit Codes**:
- `0`: Success
- `10`: RAM disk not mounted
- `11`: Sync script not found
- `12`: Backup directory not accessible
- `13`: Permission error

**Example Usage**:
```bash
# Start all configurations
~/bin/browser-sync-master.sh start

# Periodic sync (for cron)
~/bin/browser-sync-master.sh sync

# Stop and backup
~/bin/browser-sync-master.sh stop
```

**Logging**:
- Logs to: `~/Library/Logs/browser-sync-master.log`
- Log format: `[YYYY-MM-DD HH:MM:SS] <level> <message>`

### 1.3. `firefox-ramsync.sh`

**Command Interface**:
```bash
firefox-ramsync.sh <command>
```

**Commands**:
- `start`: Move profile to RAM disk
- `stop`: Sync back to SSD
- `sync`: Periodic sync

**Configuration**:
- `FIREFOX_PROFILE_NAME`: Auto-detected if not set
- `BACKUP_DIR`: `~/.browser-backup/firefox`

**Exit Codes**:
- `0`: Success
- `20`: Firefox profile not found
- `21`: RAM disk not mounted
- `22`: Backup directory not writable
- `23`: Sync failed

**Example Usage**:
```bash
# Initial setup
~/bin/firefox-ramsync.sh start

# Periodic sync
~/bin/firefox-ramsync.sh sync

# Shutdown sync
~/bin/firefox-ramsync.sh stop
```

---

## 2. LaunchAgent Configuration API

### 2.1. LaunchAgent Properties

**Standard Properties**:
```xml
<key>Label</key>
<string>ramdisk-browser-master</string>

<key>ProgramArguments</key>
<array>
    <string>/bin/bash</string>
    <string>-c</string>
    <string>/Users/$USER/bin/browser-sync-master.sh start</string>
</array>

<key>RunAtLoad</key>
<true/>

<key>StandardOutPath</key>
<string>/Users/$USER/Library/Logs/ramdisk-browser-master.log</string>

<key>StandardErrorPath</key>
<string>/Users/$USER/Library/Logs/ramdisk-browser-master.log</string>
```

**Advanced Properties**:
```xml
<!-- Run with lower priority -->
<key>Nice</key>
<integer>10</integer>

<!-- Throttle execution -->
<key>ThrottleInterval</key>
<integer>30</integer>

<!-- Limit to user session -->
<key>LimitLoadToSessionType</key>
<array>
    <string>Aqua</string>
</array>

<!-- Environment variables -->
<key>EnvironmentVariables</key>
<dict>
    <key>SYNC_INTERVAL_MINUTES</key>
    <string>15</string>
    <key>LOG_LEVEL</key>
    <string>INFO</string>
</dict>
```

### 2.2. LaunchAgent Management

**Load/Unload Commands**:
```bash
# Load LaunchAgent
launchctl load -w ~/Library/LaunchAgents/ramdisk-browser-master.plist

# Unload LaunchAgent
launchctl unload -w ~/Library/LaunchAgents/ramdisk-browser-master.plist

# Check status
launchctl list | grep ramdisk

# Print detailed status
launchctl print gui/$(id -u)/ramdisk-browser-master
```

**Programmatic Control**:
```bash
# Start service
launchctl start ramdisk-browser-master

# Stop service
launchctl stop ramdisk-browser-master

# Restart service
launchctl kickstart -k gui/$(id -u)/ramdisk-browser-master
```

---

## 3. Environment Variable API

### 3.1. Core Configuration Variables

```bash
# RAM Disk Configuration
export RAM_DISK_MOUNT="/Volumes/BrowserRAM"          # Mount point
export RAM_DISK_SIZE_GB="4"                          # Default size
export RAM_DISK_MAX_PERCENT="25"                     # Max % of total RAM
export RAM_DISK_MIN_FREE_GB="4"                      # Min free RAM

# Sync Configuration
export SYNC_INTERVAL_MINUTES="15"                    # Sync frequency
export SYNC_BACKUP_DIR="$HOME/.browser-backup"       # Backup location
export SYNC_RETENTION_DAYS="7"                       # Backup retention

# Browser Configuration
export ENABLE_FIREFOX="true"                         # Enable Firefox
export ENABLE_CHROME="true"                          # Enable Chrome
export ENABLE_SAFARI="true"                          # Enable Safari
export ENABLE_ZEN="false"                            # Enable Zen
export ENABLE_WAVEBOX="false"                        # Enable Wavebox
export ENABLE_HELIUM="false"                         # Enable Helium
export ENABLE_ORION="false"                          # Enable Orion

# Logging Configuration
export LOG_LEVEL="INFO"                              # Log level
export LOG_DIR="$HOME/Library/Logs"                  # Log directory
export LOG_MAX_SIZE_MB="10"                          # Max log size
export LOG_MAX_FILES="5"                             # Max log files

# Performance Configuration
export RSYNC_COMPRESSION="false"                     # Disable compression
export RSYNC_BANDWIDTH_LIMIT="0"                     # No bandwidth limit
```

### 3.2. Browser-Specific Variables

```bash
# Firefox
export FIREFOX_PROFILE_NAME=""                       # Auto-detect if empty
export FIREFOX_SYNC_FULL_PROFILE="true"              # Sync full profile
export FIREFOX_CACHE_SIZE_MB="512"                   # Cache size

# Chrome
export CHROME_CACHE_ONLY="true"                      # Cache-only mode
export CHROME_CACHE_SIZE_MB="1024"                   # Cache size
export CHROME_DISABLE_GPU_CACHE="false"              # Disable GPU cache

# Safari
export SAFARI_CACHE_SIZE_MB="256"                    # Cache size
export SAFARI_FULL_DISK_ACCESS="false"               # Full disk access

# Enterprise
export ENTERPRISE_MODE="false"                       # Enterprise features
export BACKUP_SERVER=""                              # Backup server
export MONITORING_ENABLED="false"                    # Enable monitoring
export ALERT_EMAIL=""                                # Alert email
```

### 3.3. Loading Environment Variables

```bash
# Source in shell profile
echo '[[ -f ~/.ramdisk-env ]] && source ~/.ramdisk-env' >> ~/.zshrc

# Load in scripts
source ~/.ramdisk-env

# Override for specific execution
SYNC_INTERVAL_MINUTES=30 ~/bin/browser-sync-master.sh start
```

---

## 4. Logging API

### 4.1. Log Levels

```bash
# Log level hierarchy
DEBUG < INFO < WARNING < ERROR < CRITICAL

# Set log level
export LOG_LEVEL="INFO"

# Log output format
[YYYY-MM-DD HH:MM:SS] [LEVEL] Message
```

### 4.2. Log File Management

**Default Log Files**:
```bash
~/Library/Logs/ramdisk.log                    # Master log
~/Library/Logs/firefox-ramsync.log            # Firefox sync log
~/Library/Logs/chrome-cache-setup.log         # Chrome cache log
~/Library/Logs/ramdisk-monitor.log            # Monitor log
~/Library/Logs/browser-sync-master.log        # Master sync log
```

**Log Rotation**:
```bash
# Manual rotation
logrotate -f ~/.ramdisk-logrotate.conf

# Configuration
# Max size: 10MB per log
# Max files: 5 rotated files
# Compression: gzip
# Retention: 7 days
```

**Log Analysis Functions**:
```bash
# Error count
error_count() {
    grep -c "ERROR" ~/Library/Logs/ramdisk.log
}

# Last error
last_error() {
    grep "ERROR" ~/Library/Logs/ramdisk.log | tail -n1
}

# Performance metrics
sync_duration() {
    grep "sync complete" ~/Library/Logs/firefox-ramsync.log | \
    awk '{print $NF}' | sed 's/s//'
}
```

---

## 5. Monitoring API

### 5.1. Prometheus Metrics

**Metrics Endpoint**: `/tmp/ramdisk-metrics.prom`

**Available Metrics**:
```prometheus
# HELP ramdisk_size_bytes Total size of RAM disk
# TYPE ramdisk_size_bytes gauge
ramdisk_size_bytes 4294967296

# HELP ramdisk_used_bytes Used bytes on RAM disk
# TYPE ramdisk_used_bytes gauge
ramdisk_used_bytes 1288490189

# HELP ramdisk_sync_success_total Total successful syncs
# TYPE ramdisk_sync_success_total counter
ramdisk_sync_success_total 42

# HELP ramdisk_sync_failures_total Total sync failures
# TYPE ramdisk_sync_failures_total counter
ramdisk_sync_failures_total 2

# HELP ramdisk_sync_duration_seconds Sync duration
# TYPE ramdisk_sync_duration_seconds histogram
ramdisk_sync_duration_seconds_bucket{le="1"} 10
ramdisk_sync_duration_seconds_bucket{le="5"} 40
ramdisk_sync_duration_seconds_bucket{le="10"} 42
ramdisk_sync_duration_seconds_sum 126
ramdisk_sync_duration_seconds_count 42

# HELP ramdisk_memory_pressure Memory pressure level
# TYPE ramdisk_memory_pressure gauge
ramdisk_memory_pressure 0.25

# HELP ramdisk_cpu_usage_percent CPU usage during sync
# TYPE ramdisk_cpu_usage_percent gauge
ramdisk_cpu_usage_percent 15.5
```

**Scraping Configuration**:
```yaml
# prometheus.yml
scrape_configs:
  - job_name: 'ramdisk'
    static_configs:
      - targets: ['localhost:9100']
    file_sd_configs:
      - files: ['/tmp/ramdisk-metrics.prom']
```

### 5.2. Alert Rules

```yaml
# alert.rules.yml
groups:
  - name: ramdisk
    rules:
      - alert: RAMDiskFull
        expr: ramdisk_used_bytes / ramdisk_size_bytes > 0.9
        for: 5m
        labels:
          severity: warning
        annotations:
          summary: "RAM disk usage above 90%"
          
      - alert: SyncFailures
        expr: increase(ramdisk_sync_failures_total[1h]) > 3
        for: 10m
        labels:
          severity: critical
        annotations:
          summary: "Multiple sync failures detected"
          
      - alert: HighMemoryPressure
        expr: ramdisk_memory_pressure > 0.8
        for: 15m
        labels:
          severity: warning
        annotations:
          summary: "High memory pressure detected"
```

---

## 6. Enterprise Management API

### 6.1. Multi-User Management

**Command**: `/usr/local/bin/ramdisk-enterprise.sh`

**Interface**:
```bash
ramdisk-enterprise.sh create <username> [size_gb]
ramdisk-enterprise.sh destroy <username>
ramdisk-enterprise.sh status <username>
```

**Exit Codes**:
- `0`: Success
- `100`: User not found
- `101`: Insufficient system memory
- `102`: Permission denied (must run as root)
- `103`: Backup server unreachable

**Example**:
```bash
# Create RAM disk for user
sudo /usr/local/bin/ramdisk-enterprise.sh create john.doe 4

# Check status
sudo /usr/local/bin/ramdisk-enterprise.sh status john.doe

# Destroy
sudo /usr/local/bin/ramdisk-enterprise.sh destroy john.doe
```

### 6.2. Centralized Backup API

**Backup Script**: `/usr/local/bin/ramdisk-backup-central.sh`

**Commands**:
```bash
ramdisk-backup-central.sh backup <username>
ramdisk-backup-central.sh restore <username> [timestamp]
ramdisk-backup-central.sh list <username>
ramdisk-backup-central.sh verify <username>
```

**Environment Variables**:
```bash
export BACKUP_SERVER="nas.enterprise.local"
export BACKUP_PATH="/Volumes/BackupNAS/browser-backups"
export BACKUP_RETENTION_DAYS="30"
```

**Return Format** (JSON):
```json
{
  "status": "success",
  "user": "john.doe",
  "operation": "backup",
  "timestamp": "2025-12-19T10:30:00Z",
  "duration_seconds": 8.5,
  "bytes_transferred": 1258291200,
  "backup_path": "/Volumes/BackupNAS/browser-backups/john.doe/20251219_103000"
}
```

---

## 7. Exit Codes

### 7.1. Standard Exit Codes

| Code | Meaning | Used By |
|------|---------|---------|
| `0` | Success | All scripts |
| `1` | General error | All scripts |
| `2` | Invalid parameters | ramdisk-manager.sh |
| `3` | Resource unavailable | ramdisk-manager.sh |
| `4` | Permission denied | All scripts |
| `5` | Device error | ramdisk-manager.sh |
| `10` | RAM disk not mounted | browser-sync-master.sh |
| `11` | Script not found | browser-sync-master.sh |
| `12` | Backup directory error | browser-sync-master.sh |
| `13` | Sync failed | *-ramsync.sh |
| `20` | Profile not found | firefox-ramsync.sh |
| `21` | RAM disk error | *-ramsync.sh |
| `22` | Backup failed | *-ramsync.sh |
| `100` | User not found | ramdisk-enterprise.sh |
| `101` | Insufficient memory | ramdisk-enterprise.sh |
| `102` | Root required | ramdisk-enterprise.sh |
| `103` | Server unreachable | ramdisk-enterprise.sh |

### 7.2. Custom Exit Handlers

```bash
# Example exit handler
cleanup_on_exit() {
    local exit_code=$?
    
    case $exit_code in
        0)
            log "Operation completed successfully"
            ;;
        1)
            log "ERROR: General failure"
            send_alert "Script failed with code 1"
            ;;
        10)
            log "ERROR: RAM disk not mounted"
            attempt_remount
            ;;
        *)
            log "ERROR: Unexpected exit code $exit_code"
            ;;
    esac
}

trap cleanup_on_exit EXIT
```

---

## 8. Integration Examples

### 8.1. Alfred Workflow Integration

```bash
#!/bin/bash
# Alfred Workflow: RAM Disk Control

# Keyword: ramdisk
# Argument: create|destroy|status|sync

case "{query}" in
    create*)
        size=$(echo "{query}" | awk '{print $2}')
        ~/bin/ramdisk-manager.sh create "${size:-4}"
        echo "RAM disk created"
        ;;
    destroy)
        ~/bin/ramdisk-manager.sh destroy
        echo "RAM disk destroyed"
        ;;
    status)
        ~/bin/ramdisk-manager.sh status
        ;;
    sync)
        ~/bin/browser-sync-master.sh sync
        echo "Sync completed"
        ;;
    *)
        echo "Usage: ramdisk [create <size>|destroy|status|sync]"
        ;;
esac
```

### 8.2. Keyboard Maestro Macro

```applescript
# Keyboard Maestro Macro: RAM Disk Toggle

# Trigger: F12
# Action: Toggle RAM disk

tell application "Terminal"
    if (do shell script "~/bin/ramdisk-manager.sh status | grep -q 'not mounted'") then
        do shell script "~/bin/ramdisk-manager.sh create 4"
        display notification "RAM disk created" with title "RAM Disk"
    else
        do shell script "~/bin/ramdisk-manager.sh destroy"
        display notification "RAM disk destroyed" with title "RAM Disk"
    end if
end tell
```

### 8.3. Hammerspoon Integration

```lua
-- Hammerspoon Configuration: RAM Disk Monitor

-- Menu bar indicator
ramdiskMenu = hs.menubar.new()

function updateRamdiskStatus()
    local mounted = hs.execute("test -d /Volumes/BrowserRAM && echo 1 || echo 0")
    if mounted == "1\n" then
        local usage = hs.execute("df /Volumes/BrowserRAM | awk 'NR==2{print $5}'")
        ramdiskMenu:setTitle("RAM: " .. usage)
        ramdiskMenu:setTooltip("RAM Disk Status")
    else
        ramdiskMenu:setTitle("RAM: OFF")
    end
end

-- Update every 30 seconds
hs.timer.doEvery(30, updateRamdiskStatus)

-- Hotkey: Cmd+Shift+R to toggle
hs.hotkey.bind({"cmd", "shift"}, "R", function()
    hs.execute("~/bin/ramdisk-manager.sh status | grep -q 'not mounted' && ~/bin/ramdisk-manager.sh create 4 || ~/bin/ramdisk-manager.sh destroy")
    updateRamdiskStatus()
end)
```

---

## Navigation

**Next**: [Appendix F - Version History](090-appendix-version-history.md)

**Previous**: [Appendix D - Troubleshooting Index](070-appendix-troubleshooting-000-index.md)

**Home**: [macOS 26 Tahoe RAM Disk Guide](000-index.md)

---

## Document Information

- **Version**: 1.0
- **Last Updated**: 2025-12-19
- **API Functions**: 25+
- **Exit Codes**: 20+
- **Integration Examples**: 3
- **Enterprise APIs**: 2

---

**⚠️ API STABILITY**: All APIs are stable for version 1.0. Breaking changes will be documented in release notes. Enterprise APIs require root privileges. Always validate inputs before calling APIs.

---
