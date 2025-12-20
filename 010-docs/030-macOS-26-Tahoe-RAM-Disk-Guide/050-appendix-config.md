# Appendix B - Configuration Reference

**Complete Configuration Guide for macOS 26 Tahoe RAM Disk Deployment**

---

## Table of Contents

1. [Environment Variables](#1-environment-variables)
2. [Configuration Files](#2-configuration-files)
3. [MDM Configuration Profiles](#3-mdm-configuration-profiles)
4. [Cron Configuration](#4-cron-configuration)
5. [Log Rotation](#5-log-rotation)
6. [System Tuning Parameters](#6-system-tuning-parameters)
7. [Browser-Specific Configurations](#7-browser-specific-configurations)
8. [Security Policies](#8-security-policies)

---

## 1. Environment Variables

### 1.1. Global Environment Variables

**File**: `~/.ramdisk-env` (sourced by all scripts)

```bash
#!/bin/bash
# ==============================================================================
# ~/.ramdisk-env
# Global environment variables for RAM disk configuration
# ==============================================================================

# RAM Disk Configuration
export RAM_DISK_MOUNT="/Volumes/BrowserRAM"
export RAM_DISK_SIZE_GB=${RAM_DISK_SIZE_GB:-4}
export RAM_DISK_MAX_PERCENT=${RAM_DISK_MAX_PERCENT:-25}
export RAM_DISK_MIN_FREE_GB=${RAM_DISK_MIN_FREE_GB:-4}

# Sync Configuration
export SYNC_INTERVAL_MINUTES=${SYNC_INTERVAL_MINUTES:-15}
export SYNC_BACKUP_DIR="${SYNC_BACKUP_DIR:-$HOME/.browser-backup}"
export SYNC_RETENTION_DAYS=${SYNC_RETENTION_DAYS:-7}

# Browser Configuration
export ENABLE_FIREFOX=${ENABLE_FIREFOX:-true}
export ENABLE_CHROME=${ENABLE_CHROME:-true}
export ENABLE_SAFARI=${ENABLE_SAFARI:-true}
export ENABLE_ZEN=${ENABLE_ZEN:-false}
export ENABLE_WAVEBOX=${ENABLE_WAVEBOX:-false}
export ENABLE_HELIUM=${ENABLE_HELIUM:-false}
export ENABLE_ORION=${ENABLE_ORION:-false}

# Logging Configuration
export LOG_LEVEL=${LOG_LEVEL:-INFO}
export LOG_DIR="${LOG_DIR:-$HOME/Library/Logs}"
export LOG_MAX_SIZE_MB=${LOG_MAX_SIZE_MB:-10}
export LOG_MAX_FILES=${LOG_MAX_FILES:-5}

# Performance Configuration
export RSYNC_COMPRESSION=${RSYNC_COMPRESSION:-false}
export RSYNC_BANDWIDTH_LIMIT=${RSYNC_BANDWIDTH_LIMIT:-0}  # 0 = unlimited

# Enterprise Configuration
export ENTERPRISE_MODE=${ENTERPRISE_MODE:-false}
export BACKUP_SERVER=${BACKUP_SERVER:-""}
export MONITORING_ENABLED=${MONITORING_ENABLED:-false}
export ALERT_EMAIL=${ALERT_EMAIL:-""}
```

**Installation**:
```bash
# Save as ~/.ramdisk-env
chmod 600 ~/.ramdisk-env

# Source in shell profile
echo '[[ -f ~/.ramdisk-env ]] && source ~/.ramdisk-env' >> ~/.zshrc
```

### 1.2. Per-Browser Environment Variables

**Firefox**:
```bash
export FIREFOX_PROFILE_NAME=${FIREFOX_PROFILE_NAME:-""}
export FIREFOX_SYNC_FULL_PROFILE=${FIREFOX_SYNC_FULL_PROFILE:-true}
export FIREFOX_CACHE_SIZE_MB=${FIREFOX_CACHE_SIZE_MB:-512}
```

**Chrome**:
```bash
export CHROME_CACHE_ONLY=${CHROME_CACHE_ONLY:-true}
export CHROME_CACHE_SIZE_MB=${CHROME_CACHE_SIZE_MB:-1024}
export CHROME_DISABLE_GPU_CACHE=${CHROME_DISABLE_GPU_CACHE:-false}
```

**Safari**:
```bash
export SAFARI_CACHE_SIZE_MB=${SAFARI_CACHE_SIZE_MB:-256}
export SAFARI_FULL_DISK_ACCESS=${SAFARI_FULL_DISK_ACCESS:-false}
```

---

## 2. Configuration Files

### 2.1. Script Configuration File

**File**: `~/.ramdisk-config.json`

```json
{
  "ram_disk": {
    "mount_point": "/Volumes/BrowserRAM",
    "default_size_gb": 4,
    "max_percent_of_total_ram": 25,
    "min_free_ram_gb": 4,
    "prevent_spotlight": true,
    "create_directories": true
  },
  "sync": {
    "interval_minutes": 15,
    "backup_dir": "$HOME/.browser-backup",
    "retention_days": 7,
    "compression": false,
    "bandwidth_limit_mbps": 0
  },
  "browsers": {
    "firefox": {
      "enabled": true,
      "sync_full_profile": true,
      "profile_name": "",
      "cache_size_mb": 512
    },
    "chrome": {
      "enabled": true,
      "cache_only": true,
      "cache_size_mb": 1024,
      "disable_gpu_cache": false
    },
    "safari": {
      "enabled": true,
      "cache_size_mb": 256,
      "full_disk_access": false
    },
    "zen": {
      "enabled": false,
      "sync_full_profile": true
    },
    "wavebox": {
      "enabled": false,
      "cache_only": true
    },
    "helium": {
      "enabled": false,
      "cache_only": true
    },
    "orion": {
      "enabled": false,
      "sync_full_profile": true,
      "cache_size_mb": 512
    }
  },
  "logging": {
    "level": "INFO",
    "dir": "$HOME/Library/Logs",
    "max_size_mb": 10,
    "max_files": 5,
    "syslog_enabled": false
  },
  "enterprise": {
    "enabled": false,
    "backup_server": "",
    "monitoring_enabled": false,
    "alert_email": "",
    "mdm_managed": false
  }
}
```

**Validation**:
```bash
# Validate JSON configuration
if command -v jq &>/dev/null; then
    jq . ~/.ramdisk-config.json >/dev/null && echo "✓ Valid JSON"
else
    echo "Install jq for JSON validation: brew install jq"
fi
```

---

## 3. MDM Configuration Profiles

### 3.1. Jamf Pro Custom Settings Profile

**File**: `ramdisk-jamf.mobileconfig`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>PayloadContent</key>
    <array>
        <dict>
            <key>PayloadDescription</key>
            <string>Browser RAM Disk Configuration</string>
            <key>PayloadDisplayName</key>
            <string>Browser RAM Disk Policy</string>
            <key>PayloadIdentifier</key>
            <string>com.enterprise.ramdisk.config</string>
            <key>PayloadType</key>
            <string>com.apple.ManagedClient.preferences</string>
            <key>PayloadUUID</key>
            <string>12345678-1234-1234-1234-123456789012</string>
            <key>PayloadVersion</key>
            <integer>1</integer>
            <key>PayloadContent</key>
            <dict>
                <key>com.enterprise.ramdisk</key>
                <dict>
                    <key>Forced</key>
                    <array>
                        <dict>
                            <key>mcx_preference_settings</key>
                            <dict>
                                <key>RAMDiskEnabled</key>
                                <true/>
                                <key>RAMDiskSizeGB</key>
                                <integer>4</integer>
                                <key>SyncIntervalMinutes</key>
                                <integer>15</integer>
                                <key>EnableFirefox</key>
                                <true/>
                                <key>EnableChrome</key>
                                <true/>
                                <key>EnableSafari</key>
                                <true/>
                                <key>BackupServer</key>
                                <string>nas.enterprise.local</string>
                                <key>BackupPath</key>
                                <string>/Volumes/BackupNAS/browser-backups</string>
                                <key>MonitoringEnabled</key>
                                <true/>
                                <key>AlertEmail</key>
                                <string>admin@enterprise.local</string>
                                <key>MaxRAMPercent</key>
                                <integer>25</integer>
                                <key>MinFreeRAMGB</key>
                                <integer>4</integer>
                                <key>LogLevel</key>
                                <string>INFO</string>
                            </dict>
                        </dict>
                    </array>
                </dict>
            </dict>
        </dict>
    </array>
    <key>PayloadDescription</key>
    <string>Configures browser RAM disk settings for all users</string>
    <key>PayloadDisplayName</key>
    <string>Browser RAM Disk Policy</string>
    <key>PayloadIdentifier</key>
    <string>com.enterprise.ramdisk.master</string>
    <key>PayloadScope</key>
    <string>System</string>
    <key>PayloadType</key>
    <string>Configuration</string>
    <key>PayloadUUID</key>
    <string>87654321-4321-4321-4321-210987654321</string>
    <key>PayloadVersion</key>
    <integer>1</integer>
</dict>
</plist>
```

**Deployment**:
```bash
# Upload to Jamf Pro
# Computers → Configuration Profiles → Upload
# Scope: All Computers or specific Smart Groups
```

### 3.2. Mosyle MDM Custom Command

**File**: `mosyle-ramdisk.sh`

```bash
#!/bin/bash
# ==============================================================================
# Mosyle MDM Custom Command
# Deploys RAM disk configuration to managed Macs
# ==============================================================================

# Mosyle API token
API_TOKEN="${API_TOKEN:-your-api-token}"
# Mosyle tenant URL
TENANT_URL="${TENANT_URL:-your-tenant.mosyle.com}"

# Install scripts
install_scripts() {
    local scripts_dir="/Library/Application Support/Mosyle/Scripts"
    mkdir -p "$scripts_dir"
    
    # Download enterprise scripts
    curl -fsSL "https://git.enterprise.local/scripts/ramdisk-enterprise.sh" \
        -o "$scripts_dir/ramdisk-enterprise.sh"
    chmod +x "$scripts_dir/ramdisk-enterprise.sh"
    
    # Install per-user LaunchAgent template
    cat > "$scripts_dir/ramdisk-user.plist" <<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>Label</key>
    <string>com.mosyle.ramdisk</string>
    <key>ProgramArguments</key>
    <array>
        <string>/bin/bash</string>
        <string>-c</string>
        <string>/Library/Application\ Support/Mosyle/Scripts/ramdisk-enterprise.sh create $(stat -f%Su /dev/console)</string>
    </array>
    <key>RunAtLoad</key>
    <true/>
</dict>
</plist>
EOF
    
    log "Scripts installed to $scripts_dir"
}

# Deploy to devices
deploy_to_devices() {
    local device_ids="${1:-all}"
    
    # Use Mosyle API to deploy
    curl -X POST "https://$TENANT_URL/v1/commands" \
        -H "Authorization: Bearer $API_TOKEN" \
        -H "Content-Type: application/json" \
        -d "{
            \"devices\": \"$device_ids\",
            \"command\": \"run_script\",
            \"parameters\": {
                \"script_path\": \"/Library/Application Support/Mosyle/Scripts/ramdisk-enterprise.sh\",
                \"args\": [\"create\", \"all\", \"4\"]
            }
        }"
}

# Main execution
case "${1:-install}" in
    install)
        install_scripts
        ;;
    deploy)
        deploy_to_devices "${2:-all}"
        ;;
    *)
        echo "Usage: $0 install | deploy [device_ids]" >&2
        exit 1
        ;;
esac
```

---

## 4. Cron Configuration

### 4.1. User Crontab Configuration

**File**: `crontab-template.txt`

```bash
# ==============================================================================
# RAM Disk Sync Cron Jobs
# Generated: $(date)
# ==============================================================================

# Sync all browser profiles every 15 minutes
*/15 * * * * $HOME/bin/browser-sync-master.sh sync

# Monitor RAM disk usage every 5 minutes
*/5 * * * * $HOME/bin/ramdisk-monitor.sh --log

# Daily backup verification (2 AM)
0 2 * * * $HOME/bin/backup-verify.sh

# Weekly log cleanup (Sunday 3 AM)
0 3 * * 0 find $HOME/Library/Logs -name "ramdisk*.log" -mtime +7 -delete

# Monthly usage report (1st of month 4 AM)
0 4 1 * * $HOME/bin/ramdisk-report.sh
```

**Installation**:
```bash
# Install crontab
crontab crontab-template.txt

# Verify installation
crontab -l | grep ramdisk
```

### 4.2. System Crontab (Enterprise)

**File**: `/etc/cron.d/ramdisk-enterprise`

```bash
# ==============================================================================
# System-wide RAM Disk Cron Jobs
# ==============================================================================

# Sync all users every 15 minutes
*/15 * * * * root /usr/local/bin/ramdisk-enterprise-sync-all.sh

# Monitor all RAM disks every 5 minutes
*/5 * * * * root /usr/local/bin/ramdisk-monitor-all.sh --alert

# Daily backup verification
0 2 * * * root /usr/local/bin/ramdisk-verify-all-backups.sh

# Weekly report to admins
0 3 * * 0 root /usr/local/bin/ramdisk-weekly-report.sh | mail -s "RAM Disk Weekly Report" admin@enterprise.local

# Monthly cleanup
0 4 1 * * root find /var/log -name "ramdisk*.log" -mtime +30 -delete
```

**Note**: On macOS, system crontab requires Full Disk Access for root.

---

## 5. Log Rotation

### 5.1. User-Level Log Rotation

**File**: `~/.ramdisk-logrotate.conf`

```bash
# ==============================================================================
# Logrotate Configuration for RAM Disk Logs
# ==============================================================================

# Individual log files
/Users/*/Library/Logs/ramdisk.log {
    daily
    rotate 7
    compress
    delaycompress
    missingok
    notifempty
    create 644 user staff
    size 10M
    postrotate
        # Send signal to reload logs if needed
        pkill -HUP -f "ramdisk-monitor.sh" || true
    endscript
}

/Users/*/Library/Logs/firefox-ramsync.log {
    daily
    rotate 5
    compress
    delaycompress
    missingok
    notifempty
    create 644 user staff
    size 5M
}

/Users/*/Library/Logs/chrome-cache-setup.log {
    weekly
    rotate 4
    compress
    missingok
    notifempty
    create 644 user staff
    size 2M
}

# Combined pattern
/Users/*/Library/Logs/ramdisk-*.log {
    daily
    rotate 3
    compress
    missingok
    notifempty
    create 644 user staff
    size 1M
}
```

**Installation**:
```bash
# Install logrotate (via Homebrew)
brew install logrotate

# Copy configuration
cp ~/.ramdisk-logrotate.conf /usr/local/etc/logrotate.d/ramdisk

# Test configuration
logrotate -d /usr/local/etc/logrotate.d/ramdisk

# Run manually
logrotate -f /usr/local/etc/logrotate.d/ramdisk
```

### 5.2. System-Level Log Rotation (Enterprise)

**File**: `/etc/newsyslog.d/ramdisk-enterprise.conf`

```bash
# ==============================================================================
# System Log Rotation for RAM Disk Enterprise
# ==============================================================================

# Format: logfilename          owner:group   mode count size when  flags [/pid_file] [sig_num]

/var/log/ramdisk-enterprise.log  root:wheel  644  30  10M  *  G  /var/run/ramdisk.pid 15
/var/log/ramdisk-monitor.log     root:wheel  644  30  5M   *  G  /var/run/ramdisk.pid 15
/var/log/ramdisk-verify.log      root:wheel  644  30  2M   *  G  /var/run/ramdisk.pid 15
```

---

## 6. System Tuning Parameters

### 6.1. Kernel Parameters

**File**: `/etc/sysctl.conf` (create if not exists)

```bash
# ==============================================================================
# RAM Disk Performance Tuning
# ==============================================================================

# Increase maximum memory map areas
vm.max_map_count=262144

# Increase APFS metadata cache
vfs.generic.apfs.max_metadata_cache_size=134217728

# Increase file descriptor limits
kern.maxfiles=65536
kern.maxfilesperproc=32768

# Increase shared memory limits
kern.sysv.shmmax=1073741824
kern.sysv.shmmin=1
kern.sysv.shmmni=4096
kern.sysv.shmseg=128
kern.sysv.shmall=262144

# Increase mbuf clusters
kern.ipc.maxsockbuf=8388608
kern.ipc.somaxconn=2048
```

**Application**:
```bash
# Load sysctl settings
sudo sysctl -p /etc/sysctl.conf

# Verify settings
sysctl vm.max_map_count
sysctl vfs.generic.apfs.max_metadata_cache_size
```

### 6.2. APFS Mount Options

**File**: `/etc/fstab` (for persistent mount options)

```bash
# ==============================================================================
# APFS RAM Disk Mount Options
# ==============================================================================

# RAM disk with noatime (reduces writes)
# Format: <device> <mount_point> <fs_type> <options> <dump> <pass>
# Note: RAM disk device changes on each boot, so this is for reference only

# Example entry (device will vary):
# /dev/disk3 /Volumes/BrowserRAM apfs rw,noatime,nodiratime 0 0
```

**Runtime mount options**:
```bash
# Apply noatime to mounted RAM disk
sudo mount -u -o noatime /Volumes/BrowserRAM

# Verify
mount | grep BrowserRAM
# Should show: /dev/disk3 on /Volumes/BrowserRAM (apfs, local, nodev, nosuid, noatime)
```

---

## 7. Browser-Specific Configurations

### 7.1. Firefox Configuration

**File**: `~/.ramdisk-firefox.js`

```javascript
// ==============================================================================
// Firefox Configuration for RAM Disk
// Place in Firefox profile directory (user.js)
// ==============================================================================

// Cache settings
user_pref("browser.cache.disk.enable", true);
user_pref("browser.cache.disk.capacity", 524288); // 512MB
user_pref("browser.cache.disk.parent_directory", "/Volumes/BrowserRAM/firefox-cache");

// Session store interval (reduce writes)
user_pref("browser.sessionstore.interval", 60000); // 60 seconds

// Disable crash reporter
user_pref("toolkit.crashreporter.enabled", false);

// Reduce history expiration
user_pref("places.history.expiration.max_pages", 50000);

// Disable URL prefetch
user_pref("network.prefetch-next", false);

// Optimize for SSD/RAM
user_pref("browser.urlbar.trimURLs", false);
user_pref("browser.sessionstore.max_tabs_undo", 10);
user_pref("browser.sessionstore.max_windows_undo", 3);
```

**Installation**:
```bash
# Copy to Firefox profile
cp ~/.ramdisk-firefox.js /Volumes/BrowserRAM/firefox-profile/user.js
```

### 7.2. Chrome Configuration

**File**: `~/.ramdisk-chrome-flags.conf`

```bash
# ==============================================================================
# Chrome Command Line Flags for RAM Disk
# Add to Chrome launch command or shortcut
# ==============================================================================

# Cache directory
--disk-cache-dir=/Volumes/BrowserRAM/chrome-cache

# Cache size (bytes)
--disk-cache-size=1073741824  # 1GB

# Disable GPU cache if needed
--disable-gpu-shader-disk-cache

# Reduce profile writes
--disable-features=InterestFeedContentSuggestions

# Optimize for low latency
--enable-low-end-device-mode

# Disable crash reporting
--disable-breakpad

# Reduce session restore overhead
--max-sessions-to-restore=5
```

**Usage**:
```bash
# Launch Chrome with flags
open -a "Google Chrome" --args $(cat ~/.ramdisk-chrome-flags.conf)
```

---

## 8. Security Policies

### 8.1. Full Disk Access (for Safari)

**File**: `ramdisk-safari-privacy.mobileconfig`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>PayloadContent</key>
    <array>
        <dict>
            <key>PayloadDescription</key>
            <string>Grant Full Disk Access to Safari for RAM disk cache</string>
            <key>PayloadDisplayName</key>
            <string>Safari Full Disk Access</string>
            <key>PayloadIdentifier</key>
            <string>com.apple.TCC.configuration-profile-policy</string>
            <key>PayloadType</key>
            <string>com.apple.TCC.configuration-profile-policy</string>
            <key>PayloadUUID</key>
            <string>tcc-policy-safari-ramdisk</string>
            <key>PayloadVersion</key>
            <integer>1</integer>
            <key>Services</key>
            <dict>
                <key>SystemPolicyAllFiles</key>
                <array>
                    <dict>
                        <key>Identifier</key>
                        <string>com.apple.Safari</string>
                        <key>IdentifierType</key>
                        <string>bundleID</string>
                        <key>CodeRequirement</key>
                        <string>identifier "com.apple.Safari" and anchor apple</string>
                        <key>Allowed</key>
                        <true/>
                    </dict>
                </array>
            </dict>
        </dict>
    </array>
    <key>PayloadDescription</key>
    <string>Privacy Preferences Policy Control</string>
    <key>PayloadDisplayName</key>
    <string>Privacy Preferences Policy Control</string>
    <key>PayloadIdentifier</key>
    <string>com.apple.TCC.configuration-profile-policy</string>
    <key>PayloadScope</key>
    <string>System</string>
    <key>PayloadType</key>
    <string>Configuration</string>
    <key>PayloadUUID</key>
    <string>tcc-policy-master</string>
    <key>PayloadVersion</key>
    <integer>1</integer>
</dict>
</plist>
```

**Note**: Full Disk Access for Safari is required for cache relocation to work properly.

---

## Navigation

**Next**: [Appendix C - Performance Benchmarks](060-appendix-benchmarks.md)

**Previous**: [Appendix A - Script Reference](040-appendix-scripts.md)

**Home**: [macOS 26 Tahoe RAM Disk Guide](000-index.md)

---

## Document Information

- **Version**: 1.0
- **Last Updated**: 2025-12-19
- **Configuration Files**: 12 templates
- **MDM Profiles**: 2 examples
- **System Parameters**: 15+ tunable options

---

**⚠️ CONFIGURATION SECURITY**: Store sensitive configurations (API tokens, passwords) in Keychain, not plain text files. Use environment variables for deployment-specific settings. Always validate configurations before deployment.

---
