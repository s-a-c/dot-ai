#!/usr/bin/env bash

# Description: This is the test helper for the pdf-generator tool.
# It sets up a clean test environment, provides mocking capabilities,
# and sources the shared constants file to ensure tests match application output.

# --- Shared Constants ---
# Source the single source of truth for constants like ANSI codes.
# The path is relative to the project root, as that's where shellspec runs from.
. "tools/pdf-generator/src/constants.sh"

# --- Test Environment Setup ---

# Creates a temporary directory for tests and sets the script path.
setup_test_env() {
    # Create a temporary directory and store its name.
    if ! temp_dir=$(mktemp -d); then
        printf "FATAL: Failed to create temporary directory for tests.\n" >&2
        exit 1
    fi
    
    # Set the path to the main script for use in tests.
    # The PWD variable will be the project root when shellspec is run.
    script="$PWD/tools/pdf-generator/bin/pdf-generator"
    
    # Move into the temporary directory for test isolation.
    cd "$temp_dir"
}

# Removes the temporary directory and mock functions.
cleanup_test_env() {
    # Exit the temporary directory and remove it.
    # The 'cd -' command returns to the previous directory.
    cd - >/dev/null
    rm -rf "$temp_dir"
}

# --- Mocking Framework ---

# This function mocks all external commands used by the main script.
# The mocks simply print their name and arguments to stdout for inspection.
mock_all_commands() {
    # List of commands to be mocked.
    local commands_to_mock=("pandoc" "sqlite3" "uv" "yq")

    # Iterate through the list and create a mock function for each.
    for cmd in "${commands_to_mock[@]}"; do
        eval "$cmd() { printf '%s called with: %s\\n' '$cmd' \"\$*\"; }"
        export -f "$cmd"
    done
}

# This function cleans up all mocks created by mock_all_commands.
cleanup_mocks() {
    # Same list of commands as in the setup function, plus any ad-hoc mocks.
    local commands_to_mock=("pandoc" "sqlite3" "uv" "yq" "mkdir")

    # Iterate and unset each function.
    for cmd in "${commands_to_mock[@]}"; do
        # Unset the function, redirecting errors to /dev/null in case it wasn't set.
        unset -f "$cmd" &>/dev/null || true
    done
}
