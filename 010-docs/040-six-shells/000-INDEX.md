# Six Shells Documentation Suite

Complete documentation for your unified shell configuration across six different shells.

## Overview

Your shell environment is configured to provide **consistent muscle memory** across six modern shells while leveraging each shell's unique strengths.

## Configured Shells

1. **[Bash](./010-bash.md)** - The universal POSIX-compatible shell
2. **[Zsh](./020-zsh.md)** - Feature-rich with extensive plugin ecosystem
3. **[Fish](./030-fish.md)** - User-friendly with intuitive defaults
4. **[Nushell](./040-nushell.md)** - Modern shell with structured data
5. **[Xonsh](./050-xonsh.md)** - Python-shell hybrid for scripting power
6. **[PowerShell](./060-powershell.md)** - Object-oriented cross-platform shell

## Comparison & Recommendations

- **[Shell Comparison](./100-comparison.md)** - Side-by-side comparison of all six shells
- **[Use Cases & Recommendations](./200-recommendations.md)** - When to use which shell

## Quick Reference

### Common to All Shells

All shells share these consistent features:
- **100,000 history items** with deduplication
- **Unified aliases** (PHP/Laravel, Git, System, macOS)
- **Utility functions** (mkcd, extract, backup, fif, largest)
- **Package manager detection** (bun/pnpm/yarn/npm)
- **Tool integrations** (Starship, Zoxide, FZF, Atuin)

### Help Commands

| Shell      | Aliases Help         | Utils Help         |
|------------|----------------------|--------------------|
| Bash       | `aliases-help`       | `utils-help`       |
| Zsh        | `aliases-help`       | `utils-help`       |
| Fish       | `aliases-help`       | `utils-help`       |
| Nushell    | `aliases-help`       | `utils-help`       |
| Xonsh      | `show-aliases`       | `show-utils`       |
| PowerShell | `Show-Aliases`       | `Show-Utils`       |

### Configuration Locations

| Shell      | Main Config                                    | Modules/Fragments                |
|------------|------------------------------------------------|----------------------------------|
| Bash       | `~/.config/bash/.bashrc`                       | `~/.bashrc.d/*.bash`             |
| Zsh        | `~/.zshrc`                                     | Various (plugins, functions)     |
| Fish       | `~/.config/fish/config.fish`                   | `~/.config/fish/conf.d/`         |
| Nushell    | `~/.config/nushell/config.nu`                  | `~/.config/nushell/scripts/`     |
| Xonsh      | `~/.config/xonsh/rc.xsh`                       | `~/.config/xonsh/rc.d/`          |
| PowerShell | `~/.config/powershell/Microsoft.PowerShell_profile.ps1` | `~/.config/powershell/*.ps1` |

## Installation Status

✅ All shells configured and tested  
✅ Consistent aliases across all shells  
✅ Tool integrations active where supported  
✅ macOS-specific features enabled  

## Quick Start Guide

### Launch Any Shell

```bash
# From your current shell, launch another:
bash
zsh
fish
nu          # nushell
xonsh
pwsh        # powershell
```

### Test Configuration

In any shell:

```bash
# Show available aliases
aliases-help    # (or show-aliases in xonsh, Show-Aliases in PowerShell)

# Show utility functions
utils-help      # (or show-utils in xonsh, Show-Utils in PowerShell)

# Test a common alias
gs              # git status (works in all shells)

# Test a utility function
mkcd test-dir   # create and enter directory (works in all shells)
```

### Switch Default Shell

```bash
# Example: Make fish your default shell
chsh -s $(which fish)

# Log out and back in for changes to take effect
```

## Documentation Files

### Individual Shell Docs

- **[010-bash.md](./010-bash.md)** - Bash configuration details
- **[020-zsh.md](./020-zsh.md)** - Zsh configuration details
- **[030-fish.md](./030-fish.md)** - Fish configuration details
- **[040-nushell.md](./040-nushell.md)** - Nushell configuration details
- **[050-xonsh.md](./050-xonsh.md)** - Xonsh configuration details
- **[060-powershell.md](./060-powershell.md)** - PowerShell configuration details

### Comparison & Analysis

- **[100-comparison.md](./100-comparison.md)** - Feature-by-feature comparison
- **[200-recommendations.md](./200-recommendations.md)** - Use case recommendations

## Key Features Across All Shells

### 1. PHP/Laravel Development

```bash
artisan, bob, pas, pats, pam, pamf, pamfs
pest, phpstan, rector, sail
```

### 2. Git Operations

```bash
gs (status), ga (add), gc (commit), gp (push), gl (pull)
root (cd to repo root), main (checkout main/master)
git-clean, git-undo
```

### 3. Navigation

```bash
h (home), dl (Downloads), dt (Desktop), doc (Documents)
.., ..., ....
```

### 4. Utility Functions

```bash
mkcd <dir>              # Make directory and cd
extract <file>          # Extract any archive
backup <file>           # Timestamped backup
fif <term>              # Find in files
largest [n]             # Show largest files
```

### 5. Package Manager Detection

```bash
install/ni              # Auto-detect and install
dev                     # Run dev server
build/nb                # Build project
pm-info                 # Show detected PM
```

## Philosophy

### Consistency

All shells maintain **identical command aliases and function names** so you never have to remember which shell you're in.

### Unique Strengths

Each shell preserves its unique features:
- **Bash**: Universal compatibility
- **Zsh**: Powerful completion and plugins
- **Fish**: Syntax highlighting and suggestions
- **Nushell**: Structured data pipelines
- **Xonsh**: Python integration
- **PowerShell**: Object-oriented pipelines

### Tool Integration

Common tools work across shells:
- **Starship** - Universal prompt
- **Zoxide** - Smart directory jumping
- **FZF** - Fuzzy finding
- **Atuin** - Enhanced history

## Contributing

To add custom functionality:

1. **Bash**: Add to `~/.config/bash/.bashrc` or `~/.bashrc.d/`
2. **Zsh**: Add to `~/.zshrc`
3. **Fish**: Add to `~/.config/fish/conf.d/`
4. **Nushell**: Add to `~/.config/nushell/scripts/`
5. **Xonsh**: Add to `~/.config/xonsh/rc.d/`
6. **PowerShell**: Add to `~/.config/powershell/`

## Resources

- [Bash Manual](https://www.gnu.org/software/bash/manual/)
- [Zsh Documentation](https://zsh.sourceforge.io/Doc/)
- [Fish Documentation](https://fishshell.com/docs/current/)
- [Nushell Book](https://www.nushell.sh/book/)
- [Xonsh Documentation](https://xon.sh/)
- [PowerShell Documentation](https://learn.microsoft.com/en-us/powershell/)

## Last Updated

December 2024

---

**Next**: Read [Shell Comparison](./100-comparison.md) to understand which shell to use when.
