# Shell Use Cases & Recommendations

Practical guidance on when to use which shell based on specific scenarios and workflows.

## Quick Decision Tree

```
What's your primary use case?

├─ Writing system scripts that need to run everywhere
│  └─ **Use: Bash**
│
├─ Daily interactive work on macOS/Linux with plugins
│  └─ **Use: Zsh** (macOS default) or **Fish** (if POSIX unnecessary)
│
├─ Learning a modern shell with great UX
│  └─ **Use: Fish**
│
├─ Processing JSON/CSV/structured data
│  ├─ From Python background? → **Xonsh**
│  ├─ From .NET background? → **PowerShell**
│  └─ Want modern shell design? → **Nushell**
│
├─ Data science or heavy Python scripting
│  └─ **Use: Xonsh**
│
├─ Windows administration or cross-platform enterprise
│  └─ **Use: PowerShell**
│
└─ Just exploring and having fun
   └─ **Try: All of them!** (You have them configured)
```

## Scenario-Based Recommendations

### 1. Software Development

#### Web Development (PHP/Laravel/JavaScript)
**Recommended: Zsh or Fish**

**Why:**
- Fast, responsive interactive experience
- Excellent autocompletion for commands
- Your aliases (artisan, npm/yarn/pnpm) work identically
- Plugin ecosystem for additional tools

**Daily workflow:**
```bash
# Both Zsh and Fish
cd ~/projects/myapp
gs                    # git status
pas                   # php artisan serve
dev                   # npm/yarn/pnpm run dev
```

**Zsh advantages:**
- More plugins for specific frameworks
- Better integration with Oh My Zsh ecosystem
- Default on macOS

**Fish advantages:**
- Better out-of-box experience
- Faster startup
- More intuitive scripting syntax

---

#### Python Development
**Recommended: Xonsh**

**Why:**
- Mix shell commands and Python seamlessly
- Use Python libraries directly
- REPL for quick prototyping
- Path operations with pathlib

**Example workflow:**
```xonsh
# Navigate and analyze
cd ~/projects/ml-model

# Python + shell mixing
import pandas as pd
data = pd.read_csv('data.csv')
print(f"Rows: {len(data)}")

# Shell commands
git status
pytest tests/

# Python libraries in shell
from pathlib import Path
for py in Path('.').glob('**/*.py'):
    print(f"Processing {py}")
    backup @(py)  # Use your backup function
```

**Fallback: Fish or Zsh** if startup time is critical

---

#### Data Science / Machine Learning
**Recommended: Xonsh or Nushell**

**Xonsh for:**
- Jupyter-like experience in terminal
- Direct access to numpy/pandas/scikit-learn
- Prototyping data pipelines

**Nushell for:**
- Processing CSV/JSON datasets
- Structured data manipulation
- When Python overhead is too much

**Example (Nushell):**
```nushell
# Load and analyze CSV
open data.csv | where age > 30 | select name age | sort-by age

# Process JSON API responses
http get https://api.example.com/data | select id name | save results.json

# Combine with your functions
largest 20 | where type == file | get name
```

---

#### System Administration
**Recommended: Bash (scripts) + Zsh/Fish (interactive)**

**Why:**
- **Bash**: Scripts run on all servers
- **Zsh/Fish**: Better interactive experience

**Best practice:**
```bash
# Write scripts in bash for portability
#!/bin/bash
# deploy.sh - runs on any system

# Use zsh/fish for interactive work
ssh server1
# Then interact with your enhanced shell locally
```

**If Windows included:** PowerShell for cross-platform admin

---

### 2. DevOps & Automation

#### CI/CD Pipelines
**Recommended: Bash**

**Why:**
- Available in all CI environments
- Predictable behavior
- Maximum compatibility
- No dependencies

**Example:**
```bash
#!/bin/bash
# .github/workflows/deploy.sh

set -euo pipefail  # Strict error handling

# Your functions still work if you source them
source ~/.config/bash/.bashrc

# Or use standard commands
npm ci
npm run build
npm test
```

---

#### Infrastructure as Code / Configuration Management
**Recommended: Bash + PowerShell**

**Bash for:**
- Linux/Unix systems
- Ansible playbooks
- Terraform scripts

**PowerShell for:**
- Windows infrastructure
- Azure automation
- Cross-platform tools

---

#### Container Management
**Recommended: Fish or Zsh (interactive), Bash (scripts)**

**Interactive work:**
```fish
# Fish with great autocompletion
docker ps
docker exec -it mycontainer fish  # Use fish in container too!
kubectl get pods
```

**Scripts:**
```bash
#!/bin/bash
# Always use bash for reproducible scripts
docker build -t myapp .
docker push myapp:latest
```

---

### 3. Data Processing

#### Log Analysis
**Recommended: Nushell**

**Why:**
- Parse structured logs natively
- Filter and transform in pipelines
- Export to various formats

**Example:**
```nushell
# Parse nginx logs
open access.log 
  | parse "{ip} - - [{timestamp}] \"{method} {path} {protocol}\" {status} {size}"
  | where status >= 400
  | group-by status
  | sort-by status

# Process JSON logs
open app.log 
  | lines 
  | parse json 
  | where level == "ERROR" 
  | select timestamp message
```

**Alternative: Xonsh** if you need Python libraries (pandas)

---

#### API Development & Testing
**Recommended: Nushell or PowerShell**

**Nushell for:**
- REST API testing
- JSON/YAML processing
- Data transformation

```nushell
# Test API endpoint
http get https://api.example.com/users 
  | where active == true 
  | select id name email 
  | save active_users.json
```

**PowerShell for:**
- Complex object manipulation
- .NET API access
- Windows-specific APIs

```powershell
# Test and process API
$users = Invoke-RestMethod -Uri "https://api.example.com/users"
$users | Where-Object { $_.active } | Select-Object id, name | Export-Csv users.csv
```

---

### 4. Cross-Platform Scenarios

#### Working Across Linux, macOS, and Windows
**Recommended: PowerShell or Nushell**

**PowerShell advantages:**
- Truly cross-platform
- Same commands everywhere
- Strong Windows support
- .NET ecosystem

**Nushell advantages:**
- More Unix-like feel
- Faster
- Better for data processing
- Simpler syntax

**Example (PowerShell):**
```powershell
# Works identically on all platforms
Get-ChildItem | Where-Object { $_.Length -gt 1MB } | Sort-Object Length -Descending
```

**Example (Nushell):**
```nushell
# Works identically on all platforms
ls | where size > 1mb | sort-by size | reverse
```

---

### 5. Learning & Education

#### Learning Shell Scripting
**Recommended: Fish → Bash**

**Start with Fish:**
- Friendly error messages
- Great documentation
- Immediate feedback
- Web-based configuration

**Then learn Bash:**
- Industry standard
- Transferable skills
- Universal applicability

---

#### Teaching Programming Concepts
**Recommended: Xonsh**

**Why:**
- Familiar Python syntax
- Interactive REPL
- Mix concepts gradually
- Powerful for demonstrations

**Example:**
```xonsh
# Start with Python concepts
numbers = [1, 2, 3, 4, 5]
print(sum(numbers))

# Mix in shell commands
ls -la
git status

# Combine both
files = $(ls *.txt).split('\n')
for f in files:
    print(f"Processing {f}")
```

---

### 6. Special Workflows

#### macOS Development
**Recommended: Zsh (primary) + Fish (alternative)**

**Why:**
- Zsh is system default since Catalina
- Your configuration provides macOS-specific aliases
- Excellent Homebrew integration

**macOS-specific features working in all shells:**
```bash
flush       # Flush DNS cache
show/hide   # Toggle hidden files
spotoff     # Disable Spotlight
cleanup     # Remove .DS_Store
ql file     # Quick Look
```

---

#### Remote Server Management
**Recommended: Bash**

**Why:**
- Always available
- Minimal dependencies
- Fast startup
- Reliable

**When SSH'd into servers:**
```bash
# Your bash config works immediately
bash
aliases-help  # See all available aliases
gs            # Your git aliases work
```

---

#### Rapid Prototyping
**Recommended: Xonsh**

**Why:**
- Immediate Python access
- Shell commands when needed
- No context switching
- Quick iteration

**Example:**
```xonsh
# Prototype data pipeline
import requests
import json

# Fetch data
response = requests.get('https://api.example.com/data')
data = response.json()

# Process with shell tools
echo @(json.dumps(data, indent=2)) | jq '.users[]' > users.json

# Python analysis
users = [u['name'] for u in data['users'] if u['active']]
print(f"Active users: {len(users)}")
```

---

#### Windows-Only Environments
**Recommended: PowerShell**

**Why:**
- Native Windows integration
- Active Directory management
- Registry access
- WMI/CIM support
- Windows-specific cmdlets

---

## Optimal Setup Recommendations

### Daily Driver Setup

**Option 1: Zsh-Centric (Recommended for most)**
```bash
# Default: Zsh
chsh -s $(which zsh)

# Use bash for scripts
#!/bin/bash

# Try fish occasionally
fish

# Use nushell for data tasks
nu -c "open data.json | select name age"

# Use xonsh for Python work
xonsh
```

**Option 2: Fish-Centric (For simplicity)**
```bash
# Default: Fish
chsh -s $(which fish)

# Bash for scripts
bash script.sh

# Nushell for data
nu
```

**Option 3: Multi-Shell (Power users)**
```bash
# Keep zsh as default
# Launch specific shells as needed:

fish      # Interactive work
nu        # Data processing
xonsh     # Python scripting
pwsh      # Cross-platform tasks
bash      # Script writing
```

---

### Tool-Specific Recommendations

#### With Starship Prompt
**Works best with:** Zsh, Fish, Bash, PowerShell
**Note:** Xonsh and Nushell support varies

#### With Oh My Zsh
**Use:** Zsh exclusively for these sessions

#### With Heavy Python
**Use:** Xonsh as default, bash for scripts

#### With Data Science Notebooks
**Use:** Xonsh for notebook-like terminal experience

---

## Anti-Patterns (What NOT to Do)

### ❌ Don't: Use Fish for system scripts
**Why:** Not POSIX-compatible, won't run on other systems
**Do instead:** Use Bash for scripts, Fish for interactive

### ❌ Don't: Use Xonsh on resource-constrained systems
**Why:** Slow startup, high memory usage
**Do instead:** Use Bash or Fish

### ❌ Don't: Write production scripts in Nushell (yet)
**Why:** Young project, API still changing
**Do instead:** Use Bash or PowerShell

### ❌ Don't: Force PowerShell on Unix-only teams
**Why:** Unix shells are more natural on Unix
**Do instead:** Use PowerShell when cross-platform is needed

### ❌ Don't: Mix shell syntaxes in one script
**Why:** Confusing and error-prone
**Do instead:** Choose one shell per script, clearly marked

---

## Migration Strategy

### From Bash to Zsh/Fish
1. Start using Zsh/Fish interactively
2. Keep writing scripts in Bash
3. Gradually adopt shell-specific features
4. Maintain Bash proficiency for servers

### From Zsh to Fish
1. Try Fish in separate terminal
2. Learn Fish syntax differences
3. Migrate custom functions gradually
4. Keep Zsh for plugin-dependent workflows

### Adding Nushell to Workflow
1. Use for specific data tasks first
2. Learn structured data concepts
3. Gradually replace text-processing scripts
4. Keep traditional shell for other tasks

### Adding Xonsh for Python Projects
1. Use in Python project directories
2. Write project-specific aliases in Xonsh
3. Keep other shells for general work
4. Embrace Python+shell hybrid

---

## Performance Considerations

### Startup Time Critical?
**Ranking (fastest to slowest):**
1. Bash (~50-100ms)
2. Fish (~50-100ms)
3. Nushell (~100-150ms)
4. Zsh (~100-300ms, depends on plugins)
5. PowerShell (~200-300ms)
6. Xonsh (~500-1000ms)

**Recommendation:** Bash or Fish for quick scripts

### Memory Usage Critical?
**Ranking (least to most):**
1. Bash (~5MB)
2. Fish (~10MB)
3. Zsh (~15MB)
4. Nushell (~20MB)
5. PowerShell (~40MB)
6. Xonsh (~50MB)

**Recommendation:** Bash for minimal environments

---

## Summary Matrix

| Use Case | 1st Choice | 2nd Choice | Avoid |
|----------|------------|------------|-------|
| **Daily interactive** | Zsh/Fish | Bash | Xonsh (slow) |
| **System scripts** | Bash | PowerShell | Fish, Nushell |
| **Data processing** | Nushell | Xonsh/PowerShell | Bash |
| **Python work** | Xonsh | Bash+Python | Fish |
| **Cross-platform** | PowerShell | Nushell | Bash, Zsh |
| **Learning** | Fish | Zsh | PowerShell |
| **macOS daily** | Zsh | Fish | PowerShell |
| **Windows admin** | PowerShell | - | All others |
| **Servers** | Bash | Zsh | Xonsh, PowerShell |
| **Quick scripts** | Bash | Fish | Xonsh |

---

## Final Recommendation

### Your Optimal Setup

Given your configuration with all six shells:

**Daily Work:** **Zsh** (macOS default, great plugins)
- Interactive use
- Development
- Git operations
- PHP/Laravel work

**System Scripts:** **Bash**
- Automation
- CI/CD
- Server management
- Maximum compatibility

**Data Tasks:** **Nushell**
- JSON/CSV processing
- API testing
- Log analysis
- Structured queries

**Python Projects:** **Xonsh**
- Data science
- Prototyping
- Python-heavy work
- ML workflows

**Keep Available:** **Fish** and **PowerShell**
- Fish: Alternative interactive shell
- PowerShell: Cross-platform tasks

**Benefits of This Setup:**
- ✅ Consistent muscle memory (your aliases work everywhere)
- ✅ Right tool for each job
- ✅ Learn multiple paradigms
- ✅ Maximum flexibility

---

**See Also:**
- [Shell Comparison](./100-comparison.md) - Detailed feature comparison
- [Individual Shell Docs](./000-INDEX.md#individual-shell-docs) - Configuration details
