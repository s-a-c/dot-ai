# Application Architecture

The `apply-doc-standards` tool is a command-line application built with PHP. It is designed to be a standalone executable that can be run from the command line.

## High-Level Architecture

The application is composed of three main layers:

1.  **Core Layer**: This layer is responsible for the main application logic, including command-line argument parsing, configuration management, and orchestrating the documentation standards application process.
2.  **Utils Layer**: This layer provides utility classes for file scanning, documentation processing, and configuration management.
3.  **Vendor Layer**: This layer contains the third-party dependencies managed by Composer.

## Mermaid Diagram: High-Level Architecture

```mermaid
graph TD
    A[CLI Entry Point] --> B{Core: Application};
    B --> C{Utils: FileScanner};
    B --> D{Utils: DocumentationProcessor};
    B --> E{Utils: ConfigManager};
    C --> F[File System];
    D --> F;

    subgraph "Core Layer"
        B
    end

    subgraph "Utils Layer"
        C
        D
        E
    end

    style A fill:#f9f,stroke:#333,stroke-width:2px
    style B fill:#ccf,stroke:#333,stroke-width:2px
    style C fill:#cfc,stroke:#333,stroke-width:2px
    style D fill:#cfc,stroke:#333,stroke-width:2px
    style E fill:#cfc,stroke:#333,stroke-width:2px
    style F fill:#fcf,stroke:#333,stroke-width:2px
```

## Directory Structure

-   `apply-doc-standards`: The main executable file.
-   `src/`: Contains the source code of the application.
    -   `Core/`: Contains the core application logic.
        -   `Application.php`: The main application class.
    -   `Utils/`: Contains the utility classes.
        -   `ConfigManager.php`: Manages the configuration of the application.
        -   `DocumentationProcessor.php`: Processes the documentation files.
        -   `FileScanner.php`: Scans for files to be processed.
-   `docs/`: Contains the documentation of the application.
-   `tests/`: Contains the tests of the application.
-   `vendor/`: Contains the third-party dependencies.
