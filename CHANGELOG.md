# Changelog

All notable changes to `laravel-api-response-helpers` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Added

- `declare(strict_types=1)` across the codebase, enforced by Pint (`declare_strict_types`) and a Pest architecture test.
- Laravel Boost support: auto-discovered AI guidelines (`resources/boost/guidelines/core.blade.php`) and the `api-response-helper` agent skill (`resources/boost/skills/api-response-helper/SKILL.md`).
- `PostTooLargeException` (413) to pair with the existing `postTooLargeResponse()` helper.
- `okWithPaginateResponse()` now supports `simplePaginate()` (meta with `has_more`, without `total`/`last_page`) and `cursorPaginate()` (meta with `next_cursor`/`prev_cursor`).
- GitHub Actions CI: test matrix across PHP 8.1–8.4 and Laravel 10–13, plus a Pint code-style job.
- Laravel 13 support (`illuminate/contracts ^13`, `orchestra/testbench ^11`, `pestphp/pest ^4`).

### Changed

- All helper functions are now wrapped in `function_exists()` guards to avoid fatal redeclaration errors when a project defines a function with the same name.
- `apiResponse()` signature tightened: `mixed $data = null` (previously `[]`) and `int $httpStatus` (previously untyped), so the `data` key defaults to `null` consistently across all helpers.
- `errorResponse()` `$httpStatus` parameter is now `int` instead of `?int` (passing `null` previously caused a `TypeError`).
- `extraData` can no longer overwrite the envelope keys (`msg`, `error`, `success`, `data`); it only adds new top-level keys.
- Minimum PHP version is now 8.1 (`^8.1`), which also covers PHP 8.4+.

### Fixed

- `okWithPaginateResponse()` crashed with `Call to undefined method` when given the result of `simplePaginate()` or `cursorPaginate()`.
