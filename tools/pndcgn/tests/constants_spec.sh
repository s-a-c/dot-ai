#!/usr/bin/env shellspec

# Description: This test file serves as a "contract" to ensure that the
# shared constants are correctly defined and loaded into the test environment.

# Include the test helper, which sources the constants.
. "tools/pdf-generator/spec/spec_helper.sh"

Describe "Shared Constants Contract (src/constants.sh)"

    Context "when the constants file is sourced"

        It "defines the B_RED constant correctly"
            # The value should be the combination of BOLD and RED.
            # We use `eq` for an exact string match.
            The value "$B_RED" should eq "${BOLD}${RED}"
        End

        It "defines the RESET constant correctly"
            The value "$RESET" should eq "${CSI}0m"
        End

        It "ensures constants are not empty"
            The value "$CSI" should not be empty
            The value "$RED" should not be empty
            The value "$BOLD" should not be empty
            The value "$RESET" should not be empty
            The value "$B_RED" should not be empty
        End
    End
End
