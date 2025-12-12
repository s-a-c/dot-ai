# AI Dev Tasks Constitution

<!--
Sync Impact Report:
- Version change: 0.0.0 → 1.0.0
- List of modified principles: None
- Added sections: Core Principles, Development Workflow, Governance
- Removed sections: None
- Templates requiring updates:
  - ✅ .specify/templates/plan-template.md
  - ✅ .specify/templates/spec-template.md
  - ✅ .specify/templates/tasks-template.md
- Follow-up TODOs: None
-->

## Core Principles

### I. Clarity and Specificity
All tasks and requirements MUST be clearly and specifically defined to ensure the AI agent has unambiguous instructions. Vague goals lead to unpredictable results.

### II. Modularity
Complex problems MUST be broken down into smaller, self-contained, and independently verifiable tasks. This facilitates iterative development and simplifies debugging.

### III. Iterative Development
Development MUST follow an iterative process. Implement one task, verify the result, and then proceed to the next. This ensures the agent stays on track and allows for course correction.

### IV. Test-Driven Development (TDD)
For any code generation or modification, a corresponding test case SHOULD be defined first. The test validates the AI's output and serves as a living specification.

### V. Human-in-the-Loop
The AI agent is a collaborator, not a replacement for the developer. The developer MUST review, approve, and guide the agent's work at each step of the process.

## Development Workflow

The standard workflow for using this repository is as follows:
1.  **Define**: Create a Product Requirement Document (PRD) using `create-prd.mdc`.
2.  **Plan**: Generate a detailed task list from the PRD using `generate-tasks-from-prd.mdc`.
3.  **Execute**: Process the task list step-by-step using `process-task-list.mdc`.

## Governance

This Constitution is the foundational document for the AI Dev Tasks project. It governs the principles and workflows to ensure consistent, reliable, and high-quality AI-assisted development. Amendments to this constitution require a proposal and review process to maintain the integrity of the system.

**Version**: 1.0.0 | **Ratified**: 2025-12-12 | **Last Amended**: 2025-12-12