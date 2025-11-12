# AI-Assisted Development Workflows

## 1. Introduction

This document outlines the standardized workflows for AI-assisted development, from feature conception to implementation and version control. These workflows ensure consistency, quality, and efficiency in AI-human collaboration.

## 2. Core Workflow Principle

**All AI-assisted workflows should be structured, systematic, and suitable for junior developers to understand, follow, and execute effectively.**

## 3. Feature Development Workflow: PRD to Tasks

This workflow provides a structured, step-by-step process for building features with AI assistance, ensuring clarity, control, and quality.

### 3.1. Step 1: Create a Product Requirements Document (PRD)

*   **Goal:** To create a detailed PRD in Markdown format that is clear enough for a junior developer to understand.
*   **Process:**
    1.  The user provides an initial feature description.
    2.  The AI assistant MUST ask clarifying questions to understand the "what" and "why" of the feature.
    3.  Based on the answers, the AI generates a PRD with sections for Overview, Goals, User Stories, Functional Requirements, Non-Goals, and Success Metrics.
*   **Tool:** Use the `create-prd.mdc` context file to guide this process.

### 3.2. Step 2: Generate a Hierarchical Task List from the PRD

*   **Goal:** To break down the PRD into a detailed, multi-level task list that provides comprehensive implementation guidance.
*   **Process:**
    1.  The AI analyzes the PRD.
    2.  It first generates 4-7 high-level parent tasks and asks for user confirmation ("Go").
    3.  Upon confirmation, it decomposes each parent task into 3-8 actionable sub-tasks.
    4.  For complex sub-tasks, it can further generate granular sub-sub-tasks (atomic, testable, and achievable in 1-3 hours).
*   **Tool:** Use the `generate-tasks.mdc` context file to guide this process.

### 3.3. Step 3: Process the Task List

*   **Goal:** To implement the feature by tackling one task at a time, with user verification at each step.
*   **Process:**
    1.  The AI starts with the first sub-task (e.g., `1.1.1`).
    2.  After completing the implementation for that single sub-task, it marks it as complete (`[✅]`).
    3.  The AI **MUST** stop and wait for the user's permission to proceed (e.g., "yes" or "y").
    4.  Once all sub-tasks for a parent task are complete, the parent task is also marked as complete.
*   **Tool:** Use the `process-task-list.mdc` context file to guide this process.

## 4. Git and Version Control Workflow

### 4.1. Branching Strategy

*   **GitHub Flow:** Use the GitHub flow model (feature branches from `main`, pull requests, code reviews).

### 4.2. Commit Messages

All commit messages MUST follow the **Conventional Commits** specification.

*   **Format:** `type(scope): description`
    *   `type`: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`.
    *   `scope` (optional): The part of the codebase the commit affects (e.g., `parser`, `api`).
    *   `description`: A concise, imperative-mood summary (max 50 characters).
*   **Body:** A longer, more detailed explanation is separated by a blank line, with lines wrapped at 72 characters.
*   **Breaking Changes:** MUST be indicated with a `!` after the type/scope (e.g., `feat(api)!: ...`) and a `BREAKING CHANGE:` footer.

**Example:**
```shell
git commit -m "feat(auth): implement multi-factor authentication" \
    -m "" \
    -m "Adds support for TOTP-based two-factor authentication using Laravel Fortify." \
    -m "This includes the necessary database migrations, UI components, and backend logic." \
    -m "" \
    -m "BREAKING CHANGE: The user authentication flow has been modified."
```

## 5. Local Development and CI/CD

### 5.1. Local Development

*   **Setup:** Use `composer dev` to start the local development environment (server, queue, etc.).
*   **Pre-commit Hooks:** A pre-commit hook is configured to run critical quality checks before every commit:
    1.  **Code Formatting:** Runs Laravel Pint.
    2.  **Static Analysis:** Runs PHPStan at Level 10.
    3.  **Tests:** Runs the test suite.
    *A commit will be blocked if any of these checks fail.*

### 5.2. Continuous Integration (CI)

*   **Automated Checks:** On every pull request, a GitHub Actions workflow runs a comprehensive suite of quality gates:
    1.  **Static Analysis:** PHPStan, Rector, Pint.
    2.  **Testing:** Unit, Feature, Integration, and Architecture tests, with a 90% minimum coverage requirement.
    3.  **Security:** Security audit and vulnerability scans.
*   **Branch Protection:** The `main` branch is protected. Merging is blocked until all CI checks pass and at least one code review is approved.

## 6. Quality Assurance Integration

### 6.1. Built-in Quality Gates

*   **Code Quality:** Automated formatting and static analysis
*   **Test Coverage:** Minimum 90% coverage requirement
*   **Security:** Automated vulnerability scanning
*   **Performance:** Basic performance benchmarks
*   **Documentation:** Automated documentation checks

### 6.2. Review Processes

*   **AI Review:** AI assistant performs initial code review
*   **Human Review:** Team member performs detailed review
*   **Automated Review:** Tools perform automated checks
*   **Integration Review:** Review of integration with existing codebase

## 7. Communication and Collaboration

### 7.1. AI-Human Communication

*   **Clear Instructions:** AI provides clear, actionable instructions
*   **Progress Updates:** Regular progress updates and status reports
*   **Decision Points:** Clear decision points requiring human input
*   **Error Handling:** Proper error handling and escalation procedures

### 7.2. Team Collaboration

*   **Role Clarity:** Clear understanding of AI and human roles
*   **Communication Protocols:** Standardized communication methods
*   **Knowledge Sharing:** Documentation and knowledge transfer
*   **Continuous Improvement:** Regular process improvements

## 8. Workflow Automation

### 8.1. Automated Processes

*   **Code Quality:** Automated formatting and analysis
*   **Testing:** Automated test execution and reporting
*   **Deployment:** Automated deployment processes
*   **Monitoring:** Automated monitoring and alerting

### 8.2. Tool Integration

*   **IDE Integration:** AI tools integrated with development environment
*   **Git Integration:** Automated Git operations and checks
*   **CI/CD Integration:** Seamless integration with CI/CD pipeline
*   **Documentation Integration:** Automated documentation generation

## 9. Best Practices

### 9.1. AI Assistant Best Practices

*   **Clarity:** Provide clear, unambiguous instructions
*   **Context:** Maintain context throughout the development process
*   **Validation:** Validate assumptions and decisions
*   **Documentation:** Document all decisions and changes

### 9.2. Human Developer Best Practices

*   **Review:** Review all AI-generated code carefully
*   **Testing:** Test all implementations thoroughly
*   **Feedback:** Provide clear, constructive feedback
*   **Learning:** Learn from AI suggestions and improvements

## 10. Troubleshooting

### 10.1. Common Workflow Issues

*   **Communication Breakdown:** Use standardized communication protocols
*   **Quality Issues:** Apply quality gates and review processes
*   **Tool Failures:** Use fallback procedures and manual processes
*   **Integration Issues:** Follow integration troubleshooting procedures

### 10.2. Support Resources

*   **Documentation:** Refer to workflow documentation
*   **Team Support:** Consult with experienced team members
*   **Tool Support:** Use tool-specific documentation and support
*   **Best Practices:** Apply established best practices

## 11. Navigation

[←  Workflows Index](000-index.md) | [↑ Top](#ai-assisted-development-workflows) |  [Workflow Guidelines →](020-workflow-guidelines.md)