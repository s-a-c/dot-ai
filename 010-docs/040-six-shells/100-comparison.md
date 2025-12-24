# Shell Comparison: All Six Shells

Complete feature-by-feature comparison of Bash, Zsh, Fish, Nushell, Xonsh, and PowerShell.

## Quick Comparison Table

| Feature                | Bash | Zsh | Fish | Nushell | Xonsh | PowerShell |
|------------------------|------|-----|------|---------|-------|------------|
| **POSIX Compatible**   | ✅   | ✅  | ❌   | ❌      | ❌    | ❌         |
| **Interactive Features**| ⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐ |
| **Scripting Power**    | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Plugin Ecosystem**   | ⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐ | ⭐⭐ | ⭐⭐⭐ |
| **Learning Curve**     | Easy | Medium | Easy | Medium | Medium | Medium |
| **Startup Speed**      | ⚡⚡⚡ | ⚡⚡ | ⚡⚡⚡ | ⚡⚡ | ⚡ | ⚡⚡ |
| **Cross-Platform**     | Unix | Unix | Unix | ✅ | Unix | ✅ |
| **Tab Completion**     | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Syntax Highlighting**| ❌   | ✅  | ✅   | ✅      | ✅    | ✅         |
| **Built-in Help**      | man | man | ✅   | ✅      | ✅    | ✅         |

## Detailed Comparison

### 1. Bash - Universal Foundation

**Strengths:**
- ✅ **Universal Availability** - Pre-installed on virtually every Unix system
- ✅ **POSIX Compliance** - Scripts are portable across systems
- ✅ **Battle-Tested** - Decades of production use and refinement
- ✅ **Fast Startup** - Minimal overhead
- ✅ **Extensive Documentation** - Vast community knowledge base
- ✅ **Scripting Standard** - Default for system scripts and automation

**Weaknesses:**
- ❌ **Basic Interactive Features** - Limited out-of-box experience
- ❌ **Completion** - Basic compared to modern shells
- ❌ **Syntax** - Can be arcane (e.g., `[[ ]]` vs `[ ]`, `${}` expansions)
- ❌ **Error Messages** - Often cryptic
- ❌ **No Built-in Features** - Requires external tools for advanced features

**Best For:**
- System scripts and automation
- CI/CD pipelines
- Maximum compatibility needs
- Server environments
- Shell scripts that need to run anywhere

**Configuration:**
- Main: `~/.config/bash/.bashrc`
- Fragments: `~/.bashrc.d/*.bash`
- Size: ~12KB main config

---

### 2. Zsh - Power User's Choice

**Strengths:**
- ✅ **Powerful Completion** - Context-aware, menu-driven completions
- ✅ **Plugin Ecosystem** - Oh My Zsh, Prezto, thousands of plugins
- ✅ **Customizable** - Extensive configuration options
- ✅ **POSIX Compatible** - Can run bash scripts
- ✅ **Globbing** - Advanced pattern matching (`**/*.txt`)
- ✅ **History** - Shared history across sessions
- ✅ **Correction** - Command and filename correction

**Weaknesses:**
- ❌ **Startup Time** - Can be slow with many plugins
- ❌ **Configuration Complexity** - Can become overwhelming
- ❌ **Memory Usage** - Higher than bash with plugins
- ❌ **Learning Curve** - Many features to learn

**Best For:**
- Daily interactive use
- Power users who want customization
- Development environments
- When you need bash compatibility + modern features
- macOS (default shell since Catalina)

**Configuration:**
- Main: `~/.zshrc`
- Plugins: Via Oh My Zsh or manual
- Typically: 500+ lines with plugins

---

### 3. Fish - Friendly Interactive Shell

**Strengths:**
- ✅ **User-Friendly** - Amazing out-of-box experience
- ✅ **Syntax Highlighting** - Real-time, inline
- ✅ **Autosuggestions** - Based on history and completions
- ✅ **Web Configuration** - `fish_config` browser interface
- ✅ **Sane Defaults** - No configuration needed
- ✅ **Readable Syntax** - Clean, intuitive scripting
- ✅ **Built-in Help** - `help command` opens documentation
- ✅ **Fast** - Compiled, efficient

**Weaknesses:**
- ❌ **Not POSIX** - Bash scripts won't work
- ❌ **Different Syntax** - `set` instead of `=`, `and`/`or` instead of `&&`/`||`
- ❌ **Smaller Community** - Fewer plugins than zsh
- ❌ **Script Portability** - Fish scripts only work in fish

**Best For:**
- Newcomers to advanced shells
- Interactive daily use
- When you want features without configuration
- Developers who want immediate productivity
- Users who prefer function over compatibility

**Configuration:**
- Main: `~/.config/fish/config.fish`
- Functions: `~/.config/fish/functions/`
- Conf: `~/.config/fish/conf.d/`
- Size: ~5KB main config

---

### 4. Nushell - Modern Data Shell

**Strengths:**
- ✅ **Structured Data** - Everything is tables, records, lists
- ✅ **Type System** - Strong typing for data
- ✅ **Pipeline-Oriented** - Process data through pipelines
- ✅ **Built-in Commands** - Extensive standard library
- ✅ **Cross-Platform** - Works identically on Unix/Windows
- ✅ **Modern Syntax** - Clean, consistent, Rust-like
- ✅ **Error Messages** - Clear, helpful
- ✅ **Plugin System** - Extensible with Rust/Nu

**Weaknesses:**
- ❌ **Young Project** - Still evolving, breaking changes possible
- ❌ **Learning Curve** - Different paradigm from traditional shells
- ❌ **Performance** - Can be slower for large datasets
- ❌ **Ecosystem** - Smaller community and fewer resources
- ❌ **External Commands** - Need `^` prefix, can be confusing

**Best For:**
- Data processing and analysis
- Working with JSON/CSV/YAML
- Cross-platform scripting
- Users who think in tables/dataframes
- Modern development workflows

**Configuration:**
- Env: `~/.config/nushell/env.nu`
- Config: `~/.config/nushell/config.nu`
- Scripts: `~/.config/nushell/scripts/`
- Size: ~5KB config

---

### 5. Xonsh - Python-Shell Hybrid

**Strengths:**
- ✅ **Python Integration** - Full Python in shell
- ✅ **Dual Mode** - Shell mode + Python mode seamlessly
- ✅ **Scripting Power** - Use Python libraries directly
- ✅ **Familiar** - If you know Python, you know xonsh
- ✅ **Subprocess Capture** - `$(cmd)` returns strings
- ✅ **Path Objects** - Python pathlib in shell
- ✅ **REPL** - Interactive Python exploration

**Weaknesses:**
- ❌ **Slow Startup** - Python overhead
- ❌ **Memory Usage** - Higher than traditional shells
- ❌ **Smaller Community** - Niche user base
- ❌ **Compatibility** - Not POSIX, special syntax needed
- ❌ **Tool Support** - Some tools don't work well with xonsh

**Best For:**
- Python developers
- Data science workflows
- Scripting that needs Python libraries
- Prototyping and experimentation
- When you need shell + Python in one

**Configuration:**
- Main: `~/.config/xonsh/rc.xsh`
- Modules: `~/.config/xonsh/rc.d/`
- Size: ~13KB main config

---

### 6. PowerShell - Object-Oriented Shell

**Strengths:**
- ✅ **Object Pipelines** - Pass objects, not text
- ✅ **Cross-Platform** - Windows, Linux, macOS
- ✅ **.NET Integration** - Full .NET framework access
- ✅ **Discoverable** - `Get-Command`, `Get-Help` built-in
- ✅ **Consistent Syntax** - Verb-Noun cmdlets
- ✅ **Remote Management** - Built-in remoting capabilities
- ✅ **Structured Output** - Everything is typed
- ✅ **Windows Native** - First-class Windows support

**Weaknesses:**
- ❌ **Verbose** - Long command names (though aliasable)
- ❌ **Startup Time** - Slower than native Unix shells
- ❌ **Memory Usage** - Higher overhead
- ❌ **Unix Integration** - Not as seamless as native shells
- ❌ **Learning Curve** - Different paradigm for Unix users

**Best For:**
- Windows system administration
- Cross-platform automation
- Working with APIs and structured data
- .NET development
- When objects > text parsing
- Enterprise environments

**Configuration:**
- Profile: `~/.config/powershell/Microsoft.PowerShell_profile.ps1`
- Modules: `~/.config/powershell/*.ps1`
- Size: ~7KB profile + modules

---

## Feature Comparison Matrix

### Interactive Features

| Feature                    | Bash | Zsh | Fish | Nu | Xonsh | PS |
|----------------------------|------|-----|------|-------|-------|-----|
| Syntax Highlighting        | ❌   | ✅* | ✅   | ✅    | ✅    | ✅  |
| Autosuggestions            | ❌   | ✅* | ✅   | ✅    | ✅    | ✅  |
| Context-aware Completion   | ⭐   | ⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐   | ⭐⭐  | ⭐⭐⭐ |
| Inline Help                | ❌   | ❌  | ✅   | ✅    | ✅    | ✅  |
| Command Correction         | ❌   | ✅  | ✅   | ✅    | ❌    | ❌  |
| Vi/Emacs Mode              | ✅   | ✅  | ✅   | ✅    | ✅    | ✅  |

*With plugins

### Scripting Features

| Feature                    | Bash | Zsh | Fish | Nu | Xonsh | PS |
|----------------------------|------|-----|------|-------|-------|-----|
| Arrays                     | ✅   | ✅  | ✅   | ✅    | ✅    | ✅  |
| Associative Arrays         | ✅   | ✅  | ❌   | ✅    | ✅    | ✅  |
| Functions                  | ✅   | ✅  | ✅   | ✅    | ✅    | ✅  |
| Error Handling             | ⭐⭐ | ⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐  | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| Regular Expressions        | ✅   | ✅  | ✅   | ✅    | ✅    | ✅  |
| External Language Access   | ❌   | ❌  | ❌   | ❌    | ✅ (Python) | ✅ (.NET) |

### Data Processing

| Feature                    | Bash | Zsh | Fish | Nu | Xonsh | PS |
|----------------------------|------|-----|------|-------|-------|-----|
| Text Streams               | ✅   | ✅  | ✅   | ✅    | ✅    | ✅  |
| Structured Data (Native)   | ❌   | ❌  | ❌   | ✅    | ✅    | ✅  |
| JSON Parsing               | ❌*  | ❌* | ❌*  | ✅    | ✅    | ✅  |
| CSV/Table Handling         | ❌*  | ❌* | ❌*  | ✅    | ✅    | ✅  |
| Type System                | ❌   | ❌  | ❌   | ✅    | ✅    | ✅  |

*Requires external tools

### Performance

| Aspect                     | Bash | Zsh | Fish | Nu | Xonsh | PS |
|----------------------------|------|-----|------|-------|-------|-----|
| Startup Time (cold)        | <50ms | ~100ms | <50ms | ~100ms | ~500ms | ~200ms |
| Startup Time (w/ config)   | ~100ms | ~300ms | ~100ms | ~150ms | ~1s | ~300ms |
| Memory Usage (idle)        | ~5MB | ~15MB | ~10MB | ~20MB | ~50MB | ~40MB |
| Script Execution Speed     | ⚡⚡⚡ | ⚡⚡⚡ | ⚡⚡⚡ | ⚡⚡ | ⚡ | ⚡⚡ |

### Ecosystem

| Aspect                     | Bash | Zsh | Fish | Nu | Xonsh | PS |
|----------------------------|------|-----|------|-------|-------|-----|
| Plugin/Module Count        | ~100 | ~1000 | ~200 | ~50 | ~20 | ~500 |
| Community Size             | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐ | ⭐⭐⭐⭐ |
| Documentation Quality      | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| Stack Overflow Questions   | ~100k | ~50k | ~10k | ~1k | ~500 | ~100k |

## Your Configuration Comparison

### Unified Features Across All Shells

✅ **Aliases**: All shells share identical aliases  
✅ **Functions**: Same utility functions in all shells  
✅ **History**: 100,000 items in all shells  
✅ **Tools**: Starship, Zoxide, FZF, Atuin integrated  
✅ **macOS**: Platform-specific features when on macOS  

### Help Commands

- **Bash/Zsh/Fish/Nushell**: `aliases-help`, `utils-help`
- **Xonsh**: `show-aliases`, `show-utils` (avoid built-in conflicts)
- **PowerShell**: `Show-Aliases`, `Show-Utils` (PowerShell naming conventions)

### Configuration Size

| Shell      | Lines | Size  | Modules |
|------------|-------|-------|---------|
| Bash       | ~1500 | ~50KB | 5 files |
| Zsh        | Varies | Varies | Many plugins |
| Fish       | ~500  | ~20KB | 4 files |
| Nushell    | ~600  | ~18KB | 4 files |
| Xonsh      | ~1000 | ~40KB | 3 files |
| PowerShell | ~1200 | ~45KB | 6 files |

## Compatibility Matrix

### Running Scripts From Other Shells

|            | Bash | Zsh | Fish | Nu | Xonsh | PS |
|------------|------|-----|------|----|-------|-----|
| **From Bash** | ✅  | ✅  | ❌  | ❌ | ❌   | ❌  |
| **From Zsh**  | ⚠️  | ✅  | ❌  | ❌ | ❌   | ❌  |
| **From Fish** | ❌  | ❌  | ✅  | ❌ | ❌   | ❌  |
| **From Nu**   | ❌  | ❌  | ❌  | ✅ | ❌   | ❌  |
| **From Xonsh**| ❌  | ❌  | ❌  | ❌ | ✅   | ❌  |
| **From PS**   | ❌  | ❌  | ❌  | ❌ | ❌   | ✅  |

✅ = Works  
⚠️ = Mostly works (zshisms may break)  
❌ = Incompatible

### Calling External Commands

All shells can call external commands, but syntax varies:

- **Bash/Zsh**: `command arg`
- **Fish**: `command arg`
- **Nushell**: `^command arg` (external) or `command arg` (internal)
- **Xonsh**: `command arg` or `$(command arg)`
- **PowerShell**: `command arg` or `& command arg`

## Summary: Which Shell When?

Quick decision guide:

1. **Need maximum compatibility?** → **Bash**
2. **Daily interactive use with plugins?** → **Zsh**
3. **Want easy, beautiful, no config?** → **Fish**
4. **Working with structured data?** → **Nushell**
5. **Need Python integration?** → **Xonsh**
6. **Cross-platform with objects?** → **PowerShell**

## Next Steps

- Read **[Use Cases & Recommendations](./200-recommendations.md)** for specific scenarios
- Check individual shell docs for detailed configuration info
- Try each shell with `bash`, `zsh`, `fish`, `nu`, `xonsh`, `pwsh`

---

**See Also:**
- [Use Cases & Recommendations](./200-recommendations.md)
- [Individual Shell Documentation](./000-INDEX.md#individual-shell-docs)
