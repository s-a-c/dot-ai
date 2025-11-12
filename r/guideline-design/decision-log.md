# Decision Log: AI Guidelines Refactoring

This document records the key decisions made during the refactoring of the AI Assistant/Agent guideline documents.

## Inaccessible Files

The original request specified a set of input documents, some of which are located in `$HOME/dotfiles/dot-config/zsh/`. Due to workspace restrictions, I was unable to access the following files:

*   `$HOME/dotfiles/dot-config/zsh/AGENT.md`
*   `$HOME/dotfiles/dot-config/zsh/AGENTS.md`
*   `$HOME/dotfiles/dot-config/zsh/CLAUDE.md`
*   `$HOME/dotfiles/dot-config/zsh/CONTRIBUTING.md`
*   `$HOME/dotfiles/dot-config/zsh/CRUSH.md`
*   `$HOME/dotfiles/dot-config/zsh/WARP.md`
*   `$HOME/dotfiles/dot-config/zsh/.github/copilot-instructions.md`

**Decision:** Proceed with the refactoring using only the information available within the `ai/` directory. The consolidation will be based on the extensive set of guidelines already analyzed. The final output will be comprehensive, but will not include any information that might have been unique to the inaccessible `zsh` files. A note will be added to the main `AI-GUIDELINES.md` to reflect this.
