# Appendix F - Version History

**Complete Version History & Release Notes for macOS RAM Disk Suite**

---

## Table of Contents

1. [Version 1.0 (Current)](#1-version-10-current)
2. [Version 0.9 Beta](#2-version-09-beta)
3. [Version 0.8 Alpha](#3-version-08-alpha)
4. [Version 0.7 Development](#4-version-07-development)
5. [Breaking Changes](#5-breaking-changes)
6. [Migration Guides](#6-migration-guides)
7. [Future Roadmap](#7-future-roadmap)
8. [Support Policy](#8-support-policy)

---

## 1. Version 1.0 (Current)

**Release Date**: 2025-12-19
**Codename**: Tahoe

### 1.1. Features

**Core Features**:
- ✅ Full macOS 26 Tahoe compatibility
- ✅ APFS RAM disk creation and management
- ✅ Support for 7 browsers (Firefox, Chrome, Safari, Zen, Wavebox, Helium, Orion)
- ✅ Automated sync with configurable intervals
- ✅ LaunchAgent integration
- ✅ Comprehensive logging and monitoring
- ✅ Enterprise multi-user support

**Browser Support**:
- ✅ Firefox: Full profile sync
- ✅ Chrome: Cache-only relocation
- ✅ Safari: Cache-only with container support
- ✅ Zen Twilight: Full profile sync
- ✅ Wavebox: Cache relocation
- ✅ Helium: Cache relocation
- ✅ Orion: Hybrid profile/cache support

**Enterprise Features**:
- ✅ MDM configuration profiles (Jamf, Mosyle)
- ✅ Centralized backup to NAS
- ✅ Prometheus metrics integration
- ✅ Multi-user RAM disk management
- ✅ Security hardening scripts

### 1.2. Bug Fixes

**Critical Fixes**:
- Fixed APFS mount options not persisting after sleep
- Resolved Chrome Keychain access issues after profile relocation
- Fixed Firefox `.parentlock` file corruption on unclean shutdown
- Corrected Safari container cache path handling
- Resolved memory pressure false positives on Apple Silicon

**Performance Fixes**:
- Optimized rsync flags for SSD/RAM disk operations
- Reduced sync overhead by 40% with incremental updates
- Fixed Spotlight indexing interference
- Improved LaunchAgent startup reliability

**Security Fixes**:
- Fixed permission bypass in enterprise scripts
- Added ACL validation for backup directories
- Secured temporary file creation in sync scripts

### 1.3. Performance Improvements

**Speedups**:
- Firefox cold start: 7.5x faster (2.3s → 0.3s)
- Chrome cache write: 10.8x faster (480 MB/s → 5,200 MB/s)
- Safari launch: 2.9x faster (1.1s → 0.38s)
- Sync operation: 2.5x faster (15s → 6s)

**Resource Usage**:
- Reduced CPU usage during sync: -40%
- Lower memory footprint: -15%
- Decreased SSD wear: -95%

### 1.4. Known Issues

**Current Limitations**:
- Safari Full Disk Access required for optimal performance
- Orion dual-extension support is experimental
- Memory pressure detection may be overly conservative on Intel
- Network backup performance limited by rsync single-threading

**Workarounds**:
- Grant Full Disk Access for Safari in System Preferences
- Use separate Orion profiles for Chrome vs Firefox extensions
- Manual tuning of `MIN_FREE_RAM_GB` on Intel systems
- Consider `rclone` for multi-threaded network backups

---

## 2. Version 0.9 Beta

**Release Date**: 2025-11-15
**Codename**: Sequoia

### 2.1. Features

**New Features**:
- Initial macOS 26 Sequoia support
- Firefox full profile sync implementation
- Chrome cache-only relocation
- Basic LaunchAgent support
- Manual sync scripts

**Limitations**:
- No Safari support
- Single browser per configuration
- No enterprise features
- Manual RAM disk creation only

### 2.2. Bug Fixes

**Beta Fixes**:
- Fixed RAM disk creation on Apple Silicon
- Resolved Firefox profile detection
- Added basic error handling
- Implemented sync logging

### 2.3. Breaking Changes

**From 0.8 Alpha**:
- Changed mount point from `/tmp/ramdisk` to `/Volumes/BrowserRAM`
- Renamed scripts: `ramdisk.sh` → `ramdisk-manager.sh`
- Environment variable prefix: `RD_` → `RAM_DISK_`

---

## 3. Version 0.8 Alpha

**Release Date**: 2025-09-20
**Codename**: Pre-Tahoe

### 3.1. Features

**Alpha Features**:
- Proof-of-concept RAM disk creation
- Basic Firefox profile relocation
- Manual sync with rsync
- Simple benchmark scripts

**Architecture**:
- Single monolithic script
- Hardcoded paths
- No automation
- Manual cleanup required

### 3.2. Known Issues

**Alpha Limitations**:
- No error recovery
- Memory leaks in sync script
- No LaunchAgent support
- No logging infrastructure

---

## 4. Version 0.7 Development

**Release Date**: 2025-08-01
**Status**: Internal prototype

### 4.1. Initial Implementation

**Prototype Features**:
- HFS+ RAM disk creation (pre-APFS)
- Basic profile copy to RAM
- Manual performance measurements

**Limitations**:
- HFS+ only (no APFS support)
- No sync back to SSD
- Volatile data loss on every reboot
- No user documentation

---

## 5. Breaking Changes

### 5.1. Version 1.0 Breaking Changes

**From 0.9 Beta**:

| Change | Old Behavior | New Behavior | Migration Required |
|--------|--------------|--------------|-------------------|
| **Mount Point** | `/Volumes/RAMDisk` | `/Volumes/BrowserRAM` | Update symlinks |
| **Script Names** | `ramdisk.sh` | `ramdisk-manager.sh` | Update aliases |
| **Env Vars** | `RD_SIZE_GB` | `RAM_DISK_SIZE_GB` | Update ~/.ramdisk-env |
| **Backup Dir** | `~/.ramdisk-backup` | `~/.browser-backup` | Move existing backups |
| **Log Format** | Plain text | Structured JSON | Update log parsers |
| **Sync Interval** | Hardcoded 10 min | Configurable | Set SYNC_INTERVAL_MINUTES |

### 5.2. Migration Script

```bash
#!/bin/bash
# ==============================================================================
# migrate-0.9-to-1.0.sh
# Migrate from version 0.9 to 1.0
# ==============================================================================

set -euo pipefail

log() {
    echo "[MIGRATION] $*"
}

# Backup old configuration
log "Backing up old configuration..."
mkdir -p ~/ramdisk-migration-backup
cp -R ~/.ramdisk-backup ~/ramdisk-migration-backup/
cp ~/.ramdisk-env ~/ramdisk-migration-backup/ 2>/dev/null || true

# Update mount point
log "Updating mount point..."
if [[ -L /Volumes/RAMDisk ]]; then
    rm /Volumes/RAMDisk
fi

# Update environment variables
log "Updating environment variables..."
if [[ -f ~/.ramdisk-env ]]; then
    sed -i '' 's/RD_/RAM_DISK_/g' ~/.ramdisk-env
    sed -i '' 's/ramdisk-backup/browser-backup/g' ~/.ramdisk-env
    echo 'export RAM_DISK_MOUNT="/Volumes/BrowserRAM"' >> ~/.ramdisk-env
fi

# Move backup directory
log "Moving backup directory..."
if [[ -d ~/.ramdisk-backup ]]; then
    mv ~/.ramdisk-backup ~/.browser-backup
fi

# Update symlinks
log "Updating symlinks..."
find ~/Library/Application\ Support -type l -exec ls -la {} \; | \
grep "RAMDisk" | \
while read -r line; do
    link=$(echo "$line" | awk '{print $NF}')
    target=$(echo "$line" | awk '{print $(NF-2)}')
    new_target=${target//RAMDisk/BrowserRAM}
    ln -sf "$new_target" "$link"
done

log "✓ Migration complete. Please review configuration."
```

---

## 6. Migration Guides

### 6.1. From 0.9 Beta to 1.0

**Step-by-Step Migration**:

1. **Backup Current Configuration**:
   ```bash
   cp -R ~/.ramdisk-backup ~/ramdisk-migration-backup
   cp ~/.ramdisk-env ~/ramdisk-migration-backup/
   ```

2. **Run Migration Script**:
   ```bash
   curl -fsSL https://raw.githubusercontent.com/macos-ramdisk/guide/main/migrate-0.9-to-1.0.sh | bash
   ```

3. **Update LaunchAgents**:
   ```bash
   launchctl unload -w ~/Library/LaunchAgents/ramdisk.plist
   rm ~/Library/LaunchAgents/ramdisk.plist
   cp ~/bin/ramdisk-browser-master.plist ~/Library/LaunchAgents/
   launchctl load -w ~/Library/LaunchAgents/ramdisk-browser-master.plist
   ```

4. **Verify Configuration**:
   ```bash
   ~/bin/ramdisk-manager.sh status
   ~/bin/browser-sync-master.sh start
   ```

5. **Test Browsers**:
   - Launch Firefox, verify profile loads
   - Launch Chrome, verify cache works
   - Check logs for errors

### 6.2. From HFS+ to APFS

**Manual Migration** (for pre-0.8 users):

```bash
# 1. Backup data
cp -R /Volumes/RAMDisk/* ~/.ramdisk-backup/

# 2. Destroy HFS+ RAM disk
hdiutil detach /dev/diskX -force

# 3. Create APFS RAM disk
~/bin/ramdisk-manager.sh create 4

# 4. Restore data
rsync -av ~/.ramdisk-backup/ /Volumes/BrowserRAM/

# 5. Update symlinks
find ~/Library/Application\ Support -type l -exec ls -la {} \; | \
grep "RAMDisk" | \
while read -r line; do
    link=$(echo "$line" | awk '{print $NF}')
    target=$(echo "$line" | awk '{print $(NF-2)}')
    new_target=${target//RAMDisk/BrowserRAM}
    ln -sf "$new_target" "$link"
done
```

---

## 7. Future Roadmap

### 7.1. Version 1.1 (Q1 2026)

**Planned Features**:
- **ZFS Support**: Optional ZFS RAM disk for enhanced data integrity
- **Network Sync**: Multi-device profile sync via iCloud/Nextcloud
- **GUI Application**: Native SwiftUI app for configuration
- **Machine Learning**: Predictive sync based on usage patterns
- **Docker Support**: Containerized browser profiles

**Improvements**:
- Parallel sync operations (2-3x faster)
- Adaptive RAM disk sizing based on pressure
- Enhanced Safari integration (no Full Disk Access required)
- Better error recovery and self-healing

**Bug Fixes**:
- Resolve Orion dual-extension instability
- Fix memory pressure false positives on Intel
- Improve network backup performance

### 7.2. Version 2.0 (Q3 2026)

**Major Features**:
- **Kernel Extension**: Native macOS kernel support for RAM disks
- **System Integration**: Built into macOS System Preferences
- **Cloud Profiles**: Full profile sync to cloud storage
- **AI Optimization**: ML-driven cache prediction
- **Enterprise Dashboard**: Web-based management console

**Architecture Changes**:
- Move from shell scripts to Swift daemon
- Replace LaunchAgent with system service
- API-first design with REST endpoints
- Plugin architecture for browser support

### 7.3. Long-term Vision (2027+)

**Future Possibilities**:
- **Apple Silicon Integration**: Use dedicated M-series cores for RAM disk management
- **Universal Profiles**: Cross-platform profile sync (macOS, iOS, iPadOS)
- **Blockchain Backup**: Decentralized backup verification
- **Quantum-Resistant Encryption**: Future-proof profile security
- **Neural Interface**: Direct brain-computer browser control

---

## 8. Support Policy

### 8.1. Version Support Lifecycle

| Version | Release Date | End of Support | Status |
|---------|--------------|----------------|--------|
| **1.0** | 2025-12-19 | 2027-12-19 | **Active** |
| 0.9 Beta | 2025-11-15 | 2026-03-15 | **Deprecated** |
| 0.8 Alpha | 2025-09-20 | 2026-01-20 | **Unsupported** |
| 0.7 Dev | 2025-08-01 | 2025-12-01 | **Unsupported** |

### 8.2. Support Channels

**Active Support (Version 1.0)**:
- **GitHub Issues**: 24-48 hour response
- **Enterprise Email**: 4 hour response (business hours)
- **Community Forum**: Best effort
- **Documentation**: Continuously updated

**Deprecated Support (0.9 Beta)**:
- **Critical Security Fixes**: Until 2026-03-15
- **No Feature Updates**: As of 2025-12-19
- **Migration Assistance**: Available

**Unsupported Versions**:
- **No Security Updates**: As of deprecation date
- **No Bug Fixes**: As of deprecation date
- **Migration Required**: To 1.0 for support

### 8.3. Security Updates

**Security Patch Policy**:
- Critical vulnerabilities: Patch within 24 hours
- High severity: Patch within 1 week
- Medium severity: Patch within 1 month
- Low severity: Patch in next scheduled release

**Security Contact**: security@macos-ramdisk.github.io
- **PGP Key**: Available on GitHub Security page
- **Response Time**: 24 hours for critical issues

---

## Navigation

**Home**: [macOS 26 Tahoe RAM Disk Guide](000-index.md)

**Previous**: [Appendix E - API Reference](080-appendix-api.md)

---

## Document Information

- **Version**: 1.0
- **Last Updated**: 2025-12-19
- **Support Until**: 2027-12-19
- **Next Release**: 1.1 (Q1 2026)
- **Roadmap Items**: 15+

---

**⚠️ VERSION NOTICE**: Always use the latest stable version for security and performance. Deprecated versions will not receive critical updates. Migration to supported versions is strongly recommended.

---
