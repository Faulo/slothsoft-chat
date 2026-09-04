# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.7] - 2026-09-05

### Changed

- Updated Slothsoft dependencies and modernized the development, test, and CI configuration.

### Fixed

- Returned empty message ranges when database queries find no chat messages.
- Handled failed chat storage initialization in push and pull endpoints.

## [1.1.6] - 2026-04-02

### Changed

- Restored support for PHP 8.2.

## [1.1.5] - 2026-04-02

### Changed

- Raised the minimum PHP version to 8.3, added PHP 8.4 and 8.5 test coverage, and updated Slothsoft dependencies.

## [1.1.4] - 2026-01-09

### Changed

- Tracked Server-Sent Event progress through `lastEventId` instead of message payloads.

## [1.1.3] - 2026-01-09

### Changed

- Updated the Slothsoft Core, Farah, SSE, and Farah Testing dependencies.

## [1.1.2] - 2025-11-30

### Fixed

- Selected the embedded stylesheet before rendering Server-Sent Event messages.

## [1.1.1] - 2025-11-30

### Fixed

- Passed the embedded template content to the shoutbox renderer.

## [1.1.0] - 2025-11-30

### Changed

- Switched shoutbox translations to Farah Dictionary v1 and startup to the Farah Bootstrap API.

## [1.0.12] - 2025-11-16

### Changed

- Reduced shoutbox network requests by embedding the message-rendering stylesheet in page content.

### Removed

- Removed bundled chat dictionaries and obsolete frontend assets.

## [1.0.11] - 2025-10-24

### Changed

- Migrated the shoutbox frontend to the current Farah DOM and XSLT APIs.

## [1.0.10] - 2025-09-19

### Fixed

- Restored the archive data asset.

## [1.0.9] - 2025-09-19

### Changed

- Migrated source and test loading to PSR-4 and updated the Farah, SSE, and PHPUnit integrations.

### Fixed

- Fixed chat push and archive asset configuration and SSE server method compatibility.

## [1.0.8] - 2025-07-04

### Changed

- Moved generated API documentation publishing to GitHub Pages.

### Removed

- Removed generated API documentation from the repository.

## [1.0.7] - 2024-11-11

### Fixed

- Prevented empty message lists from causing frontend scrolling errors.
- Declared the Farah asset manifest version.

## [1.0.6] - 2024-10-05

### Changed

- Raised the minimum PHP version to 7.4 and moved the test suite to PHPUnit 8.5.

## [1.0.5] - 2024-09-29

### Changed

- Aligned Composer package metadata with the MIT license.

## [1.0.4] - 2024-09-29

### Changed

- Replaced the project license text with the MIT license.

## [1.0.3] - 2024-09-23

### Changed

- Refreshed package metadata, tests, the dependency lock file, and generated API documentation.

## [1.0.2] - 2024-04-01

### Changed

- Regenerated API documentation with the current documentation tooling.

## [1.0.1] - 2024-04-01

### Changed

- Updated public Farah asset URLs to use route-based paths.

## [1.0.0] - 2018-08-10

Initial release.

[unreleased]: https://github.com/Faulo/slothsoft-chat/compare/1.1.7...HEAD
[1.1.7]: https://github.com/Faulo/slothsoft-chat/compare/1.1.6...1.1.7
[1.1.6]: https://github.com/Faulo/slothsoft-chat/compare/1.1.5...1.1.6
[1.1.5]: https://github.com/Faulo/slothsoft-chat/compare/1.1.4...1.1.5
[1.1.4]: https://github.com/Faulo/slothsoft-chat/compare/1.1.3...1.1.4
[1.1.3]: https://github.com/Faulo/slothsoft-chat/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/Faulo/slothsoft-chat/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/Faulo/slothsoft-chat/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/Faulo/slothsoft-chat/compare/1.0.12...1.1.0
[1.0.12]: https://github.com/Faulo/slothsoft-chat/compare/1.0.11...1.0.12
[1.0.11]: https://github.com/Faulo/slothsoft-chat/compare/1.0.10...1.0.11
[1.0.10]: https://github.com/Faulo/slothsoft-chat/compare/1.0.9...1.0.10
[1.0.9]: https://github.com/Faulo/slothsoft-chat/compare/1.0.8...1.0.9
[1.0.8]: https://github.com/Faulo/slothsoft-chat/compare/1.0.7...1.0.8
[1.0.7]: https://github.com/Faulo/slothsoft-chat/compare/1.0.6...1.0.7
[1.0.6]: https://github.com/Faulo/slothsoft-chat/compare/1.0.5...1.0.6
[1.0.5]: https://github.com/Faulo/slothsoft-chat/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/Faulo/slothsoft-chat/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/Faulo/slothsoft-chat/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/Faulo/slothsoft-chat/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/Faulo/slothsoft-chat/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/Faulo/slothsoft-chat/releases/tag/1.0.0
