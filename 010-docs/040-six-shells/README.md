# Six Shells Documentation Suite

Complete documentation for your unified shell configuration across Bash, Zsh, Fish, Nushell, Xonsh, and PowerShell.

## 📚 Documentation Files

### Getting Started
- **[000-INDEX.md](./000-INDEX.md)** - Start here! Overview and quick reference

### Comparison & Analysis  
- **[100-comparison.md](./100-comparison.md)** - Detailed feature-by-feature comparison
- **[200-recommendations.md](./200-recommendations.md)** - Use case recommendations and best practices

### Individual Shell Documentation
- **[010-bash.md](./010-bash.md)** - Bash configuration and usage
- **[020-zsh.md](./020-zsh.md)** - Zsh configuration and usage
- **[030-fish.md](./030-fish.md)** - Fish configuration and usage
- **[040-nushell.md](./040-nushell.md)** - Nushell configuration and usage
- **[050-xonsh.md](./050-xonsh.md)** - Xonsh configuration and usage
- **[060-powershell.md](./060-powershell.md)** - PowerShell configuration and usage

## 🎯 Quick Start

### Which Document Should I Read?

**New to this setup?**
→ Start with [000-INDEX.md](./000-INDEX.md)

**Choosing between shells?**
→ Read [200-recommendations.md](./200-recommendations.md)

**Want detailed comparison?**
→ See [100-comparison.md](./100-comparison.md)

**Need shell-specific info?**
→ Check individual shell docs (010-060)

## 🚀 Quick Command Reference

### Help Commands

| Shell      | Show Aliases      | Show Utils       |
|------------|-------------------|------------------|
| Bash       | `aliases-help`    | `utils-help`     |
| Zsh        | `aliases-help`    | `utils-help`     |
| Fish       | `aliases-help`    | `utils-help`     |
| Nushell    | `aliases-help`    | `utils-help`     |
| Xonsh      | `show-aliases`    | `show-utils`     |
| PowerShell | `Show-Aliases`    | `Show-Utils`     |

### Launch Any Shell

```bash
bash        # Bash
zsh         # Zsh (macOS default)
fish        # Fish
nu          # Nushell
xonsh       # Xonsh
pwsh        # PowerShell
```

### Test Your Configuration

In any shell:
```bash
gs              # git status (works in all shells)
mkcd test-dir   # create and enter directory
pm-info         # show detected package manager
```

## 📖 What's Documented

### For Each Shell You'll Find:

1. **Overview** - What makes this shell unique
2. **Strengths & Weaknesses** - Honest assessment
3. **Best Use Cases** - When to use this shell
4. **Configuration Details** - File locations and structure
5. **Examples** - Real-world usage patterns
6. **Tips & Tricks** - Shell-specific optimizations

### Cross-Shell Topics:

1. **Unified Aliases** - Same commands everywhere
2. **Utility Functions** - Consistent helpers
3. **Tool Integrations** - Starship, Zoxide, FZF, Atuin
4. **Package Manager Detection** - Auto-detect npm/yarn/pnpm/bun
5. **macOS-Specific Features** - Platform enhancements

## 🎨 Philosophy

### Consistency First
All shells share identical command aliases so you never have to remember which shell you're in.

**Example:** `gs` = `git status` in ALL six shells

### Unique Strengths Preserved
Each shell retains its special features:
- **Bash**: Universal compatibility
- **Zsh**: Plugin ecosystem
- **Fish**: User-friendly UX
- **Nushell**: Structured data
- **Xonsh**: Python integration
- **PowerShell**: Object pipelines

### Right Tool for the Job
Use the shell that best fits your current task:
- Scripts → Bash
- Interactive → Zsh/Fish
- Data → Nushell
- Python → Xonsh
- Cross-platform → PowerShell

## 🔧 Configuration Overview

### File Locations

```
~/.config/
├── bash/
│   ├── .bashrc                    # Main config
│   └── .bashrc.d/                 # Modular fragments
├── fish/
│   ├── config.fish                # Main config
│   └── conf.d/                    # Auto-loaded configs
├── nushell/
│   ├── config.nu                  # Main config
│   ├── env.nu                     # Environment
│   └── scripts/                   # Custom scripts
├── xonsh/
│   ├── rc.xsh                     # Main config
│   └── rc.d/                      # Modules
└── powershell/
    ├── Microsoft.PowerShell_profile.ps1  # Profile
    └── *.ps1                      # Modules

~/.zshrc                           # Zsh config
```

### Shared Features

All configurations include:
- ✅ 100,000 history items
- ✅ PHP/Laravel aliases
- ✅ Git shortcuts
- ✅ Navigation helpers
- ✅ Utility functions
- ✅ Package manager detection
- ✅ macOS-specific commands

## 📊 Quick Comparison

| Shell | Best For | Startup | Memory | Ecosystem |
|-------|----------|---------|--------|-----------|
| **Bash** | Scripts, compatibility | ⚡⚡⚡ | 5MB | ⭐⭐⭐⭐⭐ |
| **Zsh** | Daily interactive | ⚡⚡ | 15MB | ⭐⭐⭐⭐⭐ |
| **Fish** | User-friendly | ⚡⚡⚡ | 10MB | ⭐⭐⭐⭐ |
| **Nushell** | Data processing | ⚡⚡ | 20MB | ⭐⭐⭐ |
| **Xonsh** | Python integration | ⚡ | 50MB | ⭐⭐ |
| **PowerShell** | Cross-platform | ⚡⚡ | 40MB | ⭐⭐⭐⭐ |

## 🎓 Learning Path

### Beginner
1. Read [000-INDEX.md](./000-INDEX.md)
2. Try Fish (easiest, most friendly)
3. Explore [030-fish.md](./030-fish.md)

### Intermediate
1. Master Zsh or Fish for daily use
2. Learn Bash for scripting
3. Read [200-recommendations.md](./200-recommendations.md)

### Advanced
1. Add Nushell for data tasks
2. Try Xonsh for Python work
3. Study [100-comparison.md](./100-comparison.md)

## 🔍 Use Case Quick Reference

| I want to... | Use... |
|--------------|--------|
| Write portable scripts | **Bash** |
| Work interactively daily | **Zsh** or **Fish** |
| Process JSON/CSV data | **Nushell** |
| Mix Python with shell | **Xonsh** |
| Manage Windows systems | **PowerShell** |
| Learn modern shell | **Fish** |
| Maximum compatibility | **Bash** |
| Data science workflows | **Xonsh** |

## 🛠️ Maintenance

### Updating Configurations

Each shell's config can be updated independently:

```bash
# Edit bash config
$EDITOR ~/.config/bash/.bashrc

# Edit fish config
$EDITOR ~/.config/fish/config.fish

# Edit nushell config
$EDITOR ~/.config/nushell/config.nu

# Etc...
```

### Reload After Changes

```bash
# Bash
source ~/.bashrc

# Zsh
source ~/.zshrc

# Fish
source ~/.config/fish/config.fish

# Nushell
source ~/.config/nushell/config.nu

# Xonsh
source ~/.config/xonsh/rc.xsh

# PowerShell
. $PROFILE
```

## 📝 Common Aliases (All Shells)

### PHP/Laravel
```bash
artisan, bob, pas, pats, pam, pamf, pamfs
pest, phpstan, rector, sail
```

### Git
```bash
gs, ga, gaa, gc, gcm, gca, gp, gpl, gf
root, main, git-clean, git-undo
```

### Navigation
```bash
h, dl, dt, doc
.., ..., ....
```

### Package Manager
```bash
install/ni, dev, build/nb, start/ns
pm-info
```

## 🔗 External Resources

### Official Documentation
- [Bash Manual](https://www.gnu.org/software/bash/manual/)
- [Zsh Documentation](https://zsh.sourceforge.io/Doc/)
- [Fish Documentation](https://fishshell.com/docs/current/)
- [Nushell Book](https://www.nushell.sh/book/)
- [Xonsh Documentation](https://xon.sh/)
- [PowerShell Documentation](https://learn.microsoft.com/en-us/powershell/)

### Community Resources
- [Oh My Zsh](https://ohmyz.sh/)
- [Fisher (Fish plugin manager)](https://github.com/jorgebucaran/fisher)
- [Awesome Shell](https://github.com/alebcay/awesome-shell)
- [Awesome PowerShell](https://github.com/janikvonrotz/awesome-powershell)

## 💡 Tips

1. **Use the right shell for the task** - Don't force one shell for everything
2. **Keep Bash proficiency** - It's everywhere and essential for servers
3. **Try them all** - Each shell teaches different concepts
4. **Consistent aliases = less cognitive load** - Your muscle memory works everywhere
5. **Read the help** - Each shell has built-in `aliases-help` and `utils-help`

## 🆘 Getting Help

### In-Shell Help
```bash
# Show all aliases
aliases-help        # (or show-aliases in xonsh)

# Show all utility functions
utils-help          # (or show-utils in xonsh)

# Package manager detection
pm-info
```

### Shell-Specific Help
```bash
# Bash/Zsh
man bash
man zsh

# Fish
help <command>
fish_config         # Web interface

# Nushell
help <command>

# Xonsh
xonsh-help

# PowerShell
Get-Help <command>
```

## 🎉 What's Next?

1. **Start with** [000-INDEX.md](./000-INDEX.md) for overview
2. **Choose your path** with [200-recommendations.md](./200-recommendations.md)
3. **Deep dive** into [100-comparison.md](./100-comparison.md)
4. **Explore** individual shell docs (010-060)

## 📅 Last Updated

December 2024

---

**Ready to dive in?** Start with [000-INDEX.md](./000-INDEX.md) →
