# Functional Components

This document describes the main functional components of the `apply-doc-standards` tool.

## Core Components

### `Application`

The `Application` class is the main entry point of the application. It is responsible for:

-   Parsing the command-line arguments.
-   Loading the configuration.
-   Orchestrating the file scanning and processing.
-   Displaying the results to the user.

**Public API:**

-   `run(array $argv): int`: Runs the application.

## Utility Components

### `ConfigManager`

The `ConfigManager` class is responsible for managing the configuration of the application. It can load configuration from a file and provides methods to get and set configuration values.

**Public API:**

-   `loadFromFile(string $filePath): void`: Loads configuration from a file.
-   `get(string $key, $default = null)`: Gets a configuration value.
-   `set(string $key, $value): void`: Sets a configuration value.
-   `isDryRun(): bool`: Checks if the application is in dry-run mode.
-   `shouldForce(): bool`: Checks if the processing should be forced.

### `FileScanner`

The `FileScanner` class is responsible for scanning for files to be processed. It can scan a directory recursively and filter files by extension.

**Public API:**

-   `scanDirectory(string $directory, bool $recursive): array`: Scans a directory for files.

### `DocumentationProcessor`

The `DocumentationProcessor` class is responsible for processing the documentation files. It applies the documentation standards to the files, including:

-   Adding an anchored document title.
-   Creating or repositioning the Table of Contents.
-   Adding an introduction section.
-   Numbering the content sections.
-   Adding a navigation footer.

**Public API:**

-   `processFile(string $filePath, array $options): array`: Processes a single file.
