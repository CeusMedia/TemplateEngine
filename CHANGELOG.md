# Changelog

All notable changes to this project will be documented in this file.

## [0.6.1] - 2026-11-14

### Security Fixes
- **File plugin path traversal**: `getFileNameFromKey()` now validates resolved paths stay within the configured base directory using `realpath()`, blocking directory traversal attacks (`../`)
- **Output escaping**: Added `Escape` filter (`escape`, `htmlentities` keywords) using `htmlentities(ENT_QUOTES)` for XSS prevention when rendering user-controlled data

### Bug Fixes
- **`addArrayRecursive` overwrite propagation**: The `$overwrite` parameter is now correctly passed to recursive calls, so nested elements can be overwritten when requested
- **`checkLoadLimit` never incremented**: Replaced `in_array()` with `array_key_exists()` — the load counter was always reset to 1 instead of incrementing, so infinite inclusion loops were never detected
- **`Matrix::extractValue` depth comparison**: Cast string depth to `int` before strict comparison (`!==`), fixing a PHP 8.3 strict mode bug where `'2' !== 2` caused multi-depth matrix lookups to fail silently
- **`getTaggedComment` return type**: Method now returns `null` instead of `''` when no match is found, matching the declared `?string` return type
- **`getElementsFromComment` array offset guard**: Added `count($out) < 3` check before accessing regex match array offsets

### Added
- **`Template::clear()` static method**: Resets all static state (plugins, filters, cache, loaded templates) — essential for long-running processes and test isolation
- **`Escape` filter**: HTML escaping filter combinable with other filters via pipe syntax (e.g., `<%input|escape|strip:space%>`)
- **Test coverage**: Added tests for `File` plugin (7 tests), `Inclusion` plugin (3 tests), `Matrix` plugin (8 tests), `Strip` filter (8 tests), `Tidy` plugin (6 tests), `Escape` filter (8 tests), `renderString()` (4 tests), `setCache()` with PSR-16 cache, `checkLoadLimit()`, and `clear()`
- **`MockCache`**: Minimal PSR-16 `CacheInterface` implementation for testing cache integration
- **README documentation**: Added comprehensive template syntax reference, usage examples, built-in plugin/filter tables, filter combining guide, and security notes

### Changed
- **Branch merge**: `0.6.x-review` merged into `0.6.x`, preparing version 0.6.1
- **Code style**: Standardized `TRUE`/`FALSE`/`NULL` constants and tab-based indentation across all new code to match project conventions
- **`registerFilter` docblock**: Clarified that class name strings are lazily instantiated on first use via `ObjectFactory`
- **Placeholder docblocks**: Filled in 7 `* ...` placeholder docblocks with proper descriptions in `Template.php`

### Removed
- **`<xmp>` tag support**: Removed deprecated HTML5 `<xmp>` format from `Code` filter; `<code>` tag is now always used
- **`MockAntiProtection`**: Replaced `eval()`-based mock class generation with safe PHP `Reflection` helpers in `TemplateTest`

### Dependencies
- **`rector/rector`**: Pinned from `"*"` to `"^2"` in `composer.json` dev dependencies

## [0.6.0] - 2026-11-14

### Changed
- **PHPStan v2**: Upgraded from PHPStan v1 to v2, dropping analysis level to 9 + strict-rules
- **PHP 8.3 compatibility**: Reached PHPStan level max + strict on PHP 8.3
- **`addArrayRecursive` parameter type**: Changed `$name` parameter type from `string` to `string|int|float` to accept numeric keys
- **Static return types**: Added `: void` return type declarations to static methods (`addFilter`, `addPlugin`, `registerFilter`, `setCache`, `setTemplatePath`, `addElement`, `addTemplate`, `checkLoadLimit`)
- **`Matrix` null value handling**: `str_replace()` now uses `$value ?? ''` to handle null matrix values
- **Plugin keywords**: Added `$keyword` property to `Comments`, `Optional`, and `Tidy` plugins
- **Copyright**: Updated copyright year to 2026

## [0.5.1] - 2023-11-03

### Added
- **Packagist badges**: Added download and version badges to README
- **Plugin keywords**: Added `$keyword` property to `Comments`, `Optional`, and `Tidy` plugins for consistent plugin identification

### Fixed
- **`Matrix` null value handling**: `str_replace()` now uses `$value ?? ''` to handle null matrix values instead of type-coercing null to empty string
- **`getElementsFromComment` duplicate handling**: Changed from `$unique` flag to array-based duplicate detection, allowing multiple `@tag` entries per comment

### Improved
- **Unit tests**: Improved test coverage and code quality
- **Copyright**: Updated copyright year to 2023

## [0.5.0] - 2023-10-05

### Added
- **Unit tests**: Added initial unit test suite
- **Plugin keywords**: Added `$keyword` property to `Optional` and `Tidy` plugins
- **Static return types**: Added `: void` return type declarations to all static methods
- **`Traversable` support**: `addArrayRecursive()` now accepts `Traversable` objects in addition to arrays

### Changed
- **Dropped PHP 7 support**: Minimum PHP version raised to 8.0
- **Dropped CeusMedia Common 0.9**: Dependency updated to require CeusMedia Common 1.0+
- **`addArrayRecursive` `$data` parameter**: Changed from typed `array` to untyped `$data` to accept objects
- **README**: Updated with version notes

## [0.4.2] - 2023-11-01

### Added
- **Unit tests**: Added initial unit test suite
- **Packagist badges**: Added download and version badges to README

### Changed
- **README**: Updated with version notes

## [0.4.1] - 2023-06-18

### Fixed
- **`Matrix` null value handling**: `str_replace()` now uses `$value ?? ''` to handle null matrix values
- **`getElementsFromComment` duplicate handling**: Changed from `$unique` flag to array-based duplicate detection
- **`composer.json`**: Fixed configuration

### Added
- **Plugin keywords**: Added `$keyword` property to `Comments`, `Optional`, and `Tidy` plugins
- **PHPStan baseline**: Added baseline file for PHPStan analysis

### Changed
- **Copyright**: Updated copyright year to 2023

## [0.4.0] - 2023-03-21

### Changed
- **PSR version**: Updated PSR dependencies
- **Composer**: Updated `composer.json` configuration
- **PHP 7.4**: Targeted PHP 7.4 compatibility with PHPStan level 9
- **`declare(strict_types=1)`**: Added strict types declaration to all files
- **`addArrayRecursive` `$data` parameter**: Changed from typed `array` to untyped `$data` to accept objects
- **`Traversable` support**: `addArrayRecursive()` now checks `is_array() || instanceof Traversable` before iterating

### Fixed
- **`addObject` type hints**: Added `@var` PHPDoc annotations for `$value` in `addObject()` to satisfy PHPStan
- **Various bugs**: Fixed type declaration and iteration issues

## [0.3] - 2021-07-25

### Improved
- **PHPStan level 8**: Improved code quality to reach PHPStan level 8
- **PHPStan level 7**: Improved code quality to reach PHPStan level 7
- **Code documentation**: Improved code doc quality across all files
- **Code structure**: Ordered methods and properties for better readability

### Added
- **PHPStan**: Added PHPStan static analysis configuration
- **Makefile**: Added Makefile with `composer-install`, `composer-install-dev`, `composer-update`, `phpstan`, and `phpunit` targets

## [0.2] - 2021-02-06

### Improved
- **PHPStan analysis**: Improved code quality according to PHPStan analysis

### Added
- **PHPStan**: Added PHPStan static analysis tool
- **Makefile**: Added initial Makefile

## [0.1] - 2018-05-17

### Fixed
- **Label regex escaping**: Added `preg_quote()` around label names in regex pattern to prevent special characters in label names from breaking the regex

### Added
- **Demo**: Added demo files and `.gitignore`
- **Template exception**: Added `Template` exception class with error codes
- **README**: Created initial README documentation
- **Composer**: Added `composer.json` and `.gitignore`

### Changed
- **Namespace migration**: Migrated all classes (main template, plugins, filters) to use PSR-4 namespaces under `CeusMedia\TemplateEngine`
- **File structure**: Moved class files into `src/` folder and subfolders
- **Plugin rename**: Renamed `Include` plugin to `Inclusion` to avoid reserved keyword conflicts
- **Code documentation**: Improved code doc in filter classes, interfaces, and abstract classes

