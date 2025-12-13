#!/usr/bin/env bash

# Description: This file contains shared constants for the pdf-generator tool.
# It is intended to be sourced by both the main application and the test suite
# to ensure a single source of truth for values like ANSI color codes.

# --- ANSI Color Codes ---
# These variables are used to add color to the terminal output.

# The CSI (Control Sequence Introducer) is the common prefix for ANSI escape sequences.
# Using $'...' syntax to ensure the escape character is interpreted correctly.
CSI=$'\033['

# --- SGR (Select Graphic Rendition) Parameters ---
# These codes control text formatting like color and style.

# Text Colors
RED="${CSI}31m"

# Text Styles
BOLD="${CSI}1m"

# Reset all text attributes to the terminal's default.
RESET="${CSI}0m"

# --- Combined Codes for Convenience ---
# These are combinations of the above codes for common use cases.

# Bold Red for error messages
B_RED="${BOLD}${RED}"
