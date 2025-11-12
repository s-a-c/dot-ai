<?php

use SAC\ApplyDocStandards\Utils\DocumentationProcessor;
use SAC\ApplyDocStandards\Utils\ConfigManager;

beforeEach(function () {
    $this->config = new ConfigManager();
    $this->config->set('standards.require_toc', true);
    $this->config->set('content.toc_min_sections', 2);
    $this->config->set('standards.toc_collapsible', false);
    $this->config->set('standards.require_navigation', false);
    $this->processor = new DocumentationProcessor($this->config);
    $this->tempDir = sys_get_temp_dir() . '/apply-doc-standards-tests';
    if (!file_exists($this->tempDir)) {
        mkdir($this->tempDir, 0777, true);
    }
});

afterEach(function () {
    // Clean up the temporary directory
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($this->tempDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }

    rmdir($this->tempDir);
});

test('TOC is moved to after H1 if it exists', function () {
    $filePath = $this->tempDir . '/test-toc-move.md';
    $content = <<<MARKDOWN
# My Test Document

## 2. Some Other Section

Some content here.

## Table of Contents

- [1. Introduction](#1-introduction)
- [2. Some Other Section](#2-some-other-section)

## 1. Introduction

This is the introduction.
MARKDOWN;

    file_put_contents($filePath, $content);

    $this->processor->processFile($filePath, []);

    $newContent = file_get_contents($filePath);

    $expected = <<<MARKDOWN
# <a id="my-test-document"></a>My Test Document

## Table of Contents

- [1. Introduction](#1-introduction)
- [2. Some Other Section](#2-some-other-section)

## 2. Some Other Section

Some content here.

## 1. Introduction

This is the introduction.

MARKDOWN;

    expect(trim($newContent))->toBe(trim($expected));
});

test('TOC is created if it does not exist', function () {
    $filePath = $this->tempDir . '/test-toc-create.md';
    $content = <<<MARKDOWN
# My Test Document

## 1. Introduction

This is the introduction.

## 2. Some Other Section

Some content here.
MARKDOWN;

    file_put_contents($filePath, $content);

    $this->processor->processFile($filePath, []);

    $newContent = file_get_contents($filePath);

    $expected = <<<MARKDOWN
# <a id="my-test-document"></a>My Test Document

## Table of Contents

- [1. Introduction](#1-introduction)
- [2. Some Other Section](#2-some-other-section)

## 1. Introduction

This is the introduction.

## 2. Some Other Section

Some content here.

MARKDOWN;

    expect(trim($newContent))->toBe(trim($expected));
});
