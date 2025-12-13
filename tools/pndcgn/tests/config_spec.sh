#!/usr/bin/env shellspec

# shellspec:ignore=SC2034

# Include the test helper, which also sources the shared constants.
. "tools/pdf-generator/spec/spec_helper.sh"

Describe "Configuration Handling (pdf-generator.toml)"
    # Setup/cleanup the test environment
    BeforeAll 'setup_test_env'
    AfterAll 'cleanup_test_env'

    # --- Mock Setup and Teardown ---

    # Helper function to mock mkdir and export it.
    setup_mkdir_mock() {
        mkdir() { printf "mkdir called with: %s\n" "$*"; }
        export -f mkdir
    }

    # Helper function to clean up the specific mkdir mock.
    cleanup_mkdir_mock() {
        unset -f mkdir &>/dev/null || true
    }

    # Set up all mocks before each test.
    BeforeEach 'mock_all_commands; setup_mkdir_mock'
    # Clean up all mocks and temporary files after each test.
    AfterEach 'cleanup_mocks; cleanup_mkdir_mock; rm -f pdf-generator.toml*'

    # --- Test Cases ---

    Context "when pdf-generator.toml does not exist"
        It "uses the default output directory"
            When run "$script"
            The status should be success
            The output should include "mkdir called with: -p prerendered/pdf"
        End
    End

    Context "when pdf-generator.toml is present and valid"
        setup_custom_config() {
            cat > pdf-generator.toml <<-'EOF'
[paths]
output_root = "my-custom-output"
EOF
        }
        Before 'setup_custom_config'

        It "loads the custom output directory from the config"
            When run "$script"
            The status should be success
            The output should include "mkdir called with: -p my-custom-output/pdf"
        End
    End

    Context "when called with --init"
        It "creates a default config and populates it"
            When run "$script" --init
            The status should be success
            The file "pdf-generator.toml" should be exist
            The output should include "Created default configuration at: pdf-generator.toml"
            The contents of file "pdf-generator.toml" should include 'output_root = "prerendered"'
        End
    End

    Context "when pdf-generator.toml is malformed"
        setup_malformed_config() {
            echo '[paths] output_root = ' > pdf-generator.toml
        }
        Before 'setup_malformed_config'

        It "fails with a clear, colorized error message"
            When run "$script"
            The status should be failure
            # This assertion now uses the shared constants, making it both robust and readable.
            # It checks for the exact, raw output including ANSI codes.
            The stderr should eq "\n${B_RED}ERROR:${RESET} Failed to parse configuration file: 'pdf-generator.toml' is malformed.\n"
        End
    End
End
