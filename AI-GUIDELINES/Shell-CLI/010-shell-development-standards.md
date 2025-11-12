# Shell & CLI Development Standards

This document provides standards and patterns for developing and maintaining shell scripts and configurations, with a primary focus on ZSH.

## 1. Core Principles

*   **Minimize Side Effects:** Prefer additive, scoped changes. Avoid global behavioral changes unless explicitly requested.
*   **Preserve User Intent:** Respect the existing layering architecture and user overrides.
*   **Explicit Over Implicit:** Explicitly define functions, source modules, and set variables. Do not rely on the user's environment.
*   **Idempotency:** Re-sourcing a script or fragment should be harmless. Use guards to prevent duplicate initializations.
*   **Performance:** Avoid slow operations like spawning subshells in tight loops during shell initialization. Defer heavy operations using hooks (`add-zsh-hook precmd`) or lazy-loaded functions.

## 2. ZSH Layering Architecture

The ZSH configuration follows a strict layering model to ensure a predictable and overrideable startup sequence. When adding or modifying files, respect this order:

| Phase | Directory Pattern                  | Purpose                                      |
| :---- | :--------------------------------- | :------------------------------------------- |
| 1     | `.zshenv*`, `.zshrc.pre-plugins.d/` | Early environment, safety, path shaping      |
| 2     | `.zshrc.add-plugins.d/`            | Plugin registration & core dev stacks        |
| 3     | `.zshrc.d/`                        | Post-plugin augmentation (e.g., prompt init) |
| 4     | `.zshrc.Darwin.d/`                 | Platform-specific (macOS) configurations   |
| 5     | `.zshrc.local`, `.zshenv.local`    | User overrides (NEVER overwrite)             |

## 3. Code Style and Conventions

### 3.1. Naming

*   **Internal Helpers:** Namespace with a prefix (e.g., `zf::`) to avoid polluting the global space.
*   **Feature Toggles:** Prefix environment variables with `ZF_` (e.g., `ZF_DISABLE_METRICS`).
*   **Files:** Use descriptive, kebab-case names (e.g., `520-prompt-starship.zsh`).

### 3.2. Formatting

*   **Indentation:** Use 4 spaces, no tabs.
*   **Variables:** Quote variables during expansion (`"$variable"`) and use `nounset`-safe patterns (`${VAR:-default}`) when their existence is not guaranteed.
*   **Conditionals:** Prefer `[[ ]]` over `[ ]`.

### 3.3. New File Fragments

When adding a new file, it MUST include a header block describing its purpose, phase, and dependencies.

```zsh
# Filename: 123-my-new-feature.zsh
# Purpose:  Adds a custom widget to the prompt.
# Phase:    Post-plugin (.zshrc.d/)
# Requires: 520-prompt-starship.zsh (must run after)
```

## 4. Tooling and Commands

### 4.1. Plugin Management (zgenom)

*   Use `zgenom load user/repo` to load plugins.
*   Use `zgenom reset` to regenerate the plugin cache.
*   Use `zgenom update` to update plugins.

### 4.2. Linting and Testing

*   **Syntax Check:** `zsh -n /path/to/file.zsh`
*   **ShellCheck:** Use `shellcheck` for static analysis where possible.
*   **Testing:** Adhere to the [ZSH Testing Standards](020-zsh-testing-standards.md).

## 5. Prohibited Actions

| Action                                      | Policy                                   |
| :------------------------------------------ | :--------------------------------------- |
| Force reinstall of plugin manager           | Never do automatically.                  |
| Replace user’s `.zshrc.local`               | Forbidden.                               |
| Introduce unguarded `set -u` mid-pipeline   | Must remain disabled until compatibility is confirmed. |
| Hard-code absolute paths beyond `$HOME`     | Use `$HOME` or `$ZDOTDIR`.               |
| Make network calls in the shell init path   | Only on explicit user request.           |
