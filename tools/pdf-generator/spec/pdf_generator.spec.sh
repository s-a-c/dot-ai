#!/usr/bin/env shellspec

# shellspec:ignore=SC2034 # (variable is referenced indirectly)

# Include the test helper (updated path)
. "tools/pdf-generator/spec/spec_helper.sh"

Describe "The pdf-generator script"
    # Setup the test environment before all tests
    BeforeAll 'setup_test_env'
    # Cleanup the test environment after all tests
    AfterAll 'cleanup_test_env'

    # Mock all external commands before each test
    BeforeEach 'mock_all_commands'

    # --- Test CLI Arguments and Basic Usage ---
    Context "when called with --help"
        It "displays the usage message"
            When run "$script" --help
            The status should be success
            The output should include "Usage:"
            The output should include "--force"
            The output should include "--clean"
        End
    End

    Context "when called with --clean"
        # Need to mock rm for this test
        rm() { printf "rm command called with: %s\n" "$*"; }

        It "calls rm and prints a clean message"
            When run "$script" --clean
            The status should be success
            The output should include "Clean complete"
            The output should include "rm command called with: -rf prerendered"
        End
    End

    Context "when called with an unknown option"
        It "fails with an error message"
            When run "$script" --unknown-flag
            The status should be failure
            The stderr should include "ERROR: Unknown option: --unknown-flag"
        End
    End

    # --- Test Core Execution Paths ---
    Context "when run normally (happy path)"
        It "processes all directories and generates a final report"
            When run "$script"
            The status should be success
            The output should include "› Initializing and checking prerequisites..."
            The output should include "Processing: dir1"
            The output should include "Processing: dir2"
            The output should include "Processing: dir3"
            The output should include "› Generating final run report..."
            The output should include "Success! Run"

            # Check that the final report was created
            The file "prerendered/pdf/_index.md" should be exist
            The contents of file "prerendered/pdf/_index.md" should include "# Run Report"
            The contents of file "prerendered/pdf/_index.md" should include "📊 Processing Statistics"
            The contents of file "prerendered/pdf/_index.md" should include "🗺️ Project Map"
        End
    End

    Context "when using --force"
        # Set the cache flag for the mock sqlite3
        BeforeEach 'touch prerendered/cache.sqlite.cache_flag'
        AfterEach 'rm -f prerendered/cache.sqlite.cache_flag'
        
        It "processes all directories, ignoring the cache"
            When run "$script" --force
            The status should be success
            The output should not include "Skipping (cached)"
            The output should include "Processing: dir1"
            The output should include "Processing: dir2"
        End
    End

    Context "when using --dry-run"
        It "shows what would be processed without executing"
            When run "$script" --dry-run
            The status should be success
            The output should include "Running in Dry-Run Mode"
            The output should include "Would process (dry-run): dir1"
            The output should not include "Generating PDF"
            The output should include "Dry-run complete"
            The file "prerendered/pdf/_index.md" should not be exist
        End
    End

    Context "when using -v (verbose)"
        It "prints the pandoc command"
            When run "$script" -v
            The status should be success
            The output should include "Running Pandoc:"
        End
    End

    # --- Test Caching and Resumption Logic ---
    Context "with a cached state"
        # Set the cache flag for the mock sqlite3
        Before 'touch prerendered/cache.sqlite.cache_flag'
        After 'rm -f prerendered/cache.sqlite.cache_flag'

        It "skips the cached directory"
            When run "$script"
            The status should be success
            The output should include "Skipping (cached from previous run): dir1"
            The output should include "Processing: dir2"
        End
    End

    Context "with an incomplete run"
        # Set the resume flag for the mock sqlite3
        Before 'touch prerendered/cache.sqlite.resume_flag'
        After 'rm -f prerendered/cache.sqlite.resume_flag'

        It "prompts the user to resume"
            # Simulate user typing 'y' and pressing enter
            When run "$script" <<< "y"
            The status should be success
            The output should include "Found an incomplete run"
            The output should include "Resuming incomplete run: test-run-123"
        End

        It "resumes automatically with --resume flag"
            When run "$script" --resume
            The status should be success
            The output should not include "Found an incomplete run"
            The output should include "Resuming incomplete run: test-run-123"
        End
    End
End
