# Test Suite for `generate-pdfs.sh`

This directory contains the comprehensive test suite for the main PDF generation script, built using the `shellspec` framework. The suite is designed to achieve 100% code coverage.

## Running Tests

To run the entire test suite, execute the following command from the project root:

```bash
shellspec
```

To run only a specific set of tests, you can provide a line number as an argument:

```bash
# Run a specific test block
shellspec spec/generate-pdfs.spec.sh:123
```

## Coverage Reports

The test suite is designed for high coverage. To generate a detailed, line-by-line coverage report using `kcov`, ensure `kcov` is installed (add `pkgs.kcov` to your `dev.nix`) and then run:

```bash
shellspec --kcov
```

After the run is complete, you can view the detailed HTML report in your browser:

```bash
open shellspec-coverage/index.html
```
