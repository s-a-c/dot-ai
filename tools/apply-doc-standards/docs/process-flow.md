# Process and Data Flows

This document describes the main process and data flows of the `apply-doc-standards` tool.

## Main Process Flow

The main process flow of the application is as follows:

1.  The `Application` class is instantiated.
2.  The `run` method is called with the command-line arguments.
3.  The command-line arguments are parsed.
4.  The configuration is loaded.
5.  The specified paths are processed.
    -   If a path is a directory, the `FileScanner` is used to scan for files.
    -   Each file is processed by the `DocumentationProcessor`.
6.  The results are displayed to the user.

## Mermaid Diagram: Main Process Flow

```mermaid
sequenceDiagram
    participant User
    participant Application
    participant FileScanner
    participant DocumentationProcessor

    User->>Application: Runs the tool with arguments
    Application->>Application: Parses arguments
    Application->>Application: Loads configuration
    Application->>FileScanner: Scans for files
    FileScanner-->>Application: Returns list of files
    loop for each file
        Application->>DocumentationProcessor: Processes file
        DocumentationProcessor-->>Application: Returns processing result
    end
    Application->>User: Displays results
```

## Data Flow

The main data flow of the application is as follows:

1.  The command-line arguments are passed to the `Application` class.
2.  The configuration is loaded into the `ConfigManager`.
3.  The `FileScanner` returns an array of file paths.
4.  The `DocumentationProcessor` reads the content of each file, processes it, and writes the new content back to the file.
5.  The processing results are collected and displayed to the user.

## Mermaid Diagram: Data Flow

```mermaid
graph TD
    A[Command-line arguments] --> B(Application);
    B --> C{ConfigManager};
    B --> D{FileScanner};
    D --> E[File paths];
    E --> F{DocumentationProcessor};
    F --> G[File content];
    G --> F;
    F --> H[New file content];
    H --> I[File system];
    B --> J[Results];

    style A fill:#f9f,stroke:#333,stroke-width:2px
    style B fill:#ccf,stroke:#333,stroke-width:2px
    style C fill:#cfc,stroke:#333,stroke-width:2px
    style D fill:#cfc,stroke:#333,stroke-width:2px
    style E fill:#fcf,stroke:#333,stroke-width:2px
    style F fill:#cfc,stroke:#333,stroke-width:2px
    style G fill:#fcf,stroke:#333,stroke-width:2px
    style H fill:#fcf,stroke:#333,stroke-width:2px
    style I fill:#f99,stroke:#333,stroke-width:2px
    style J fill:#9cf,stroke:#333,stroke-width:2px
```
