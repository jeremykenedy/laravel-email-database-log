# Changelog

## Unreleased

### Added

- Optional read-only Blade dashboard with search, pagination, and message source details.
- Standalone, Bootstrap 5, and Tailwind styling, with light, dark, and system appearance.
- Install and update commands with explicit framework selection and optional Laravel UI Kit support.
- Configuration, view, and asset publish tags, while retaining the original migration tag.
- Authentication and gate checks for dashboard access.
- PHP compatibility, console, browser, and accessibility tests, plus lint and dependency audit jobs.
- Light and dark README screenshots and installation, upgrade, dashboard, and testing guides.

### Fixed

- Handle SwiftMailer messages from Laravel 8 without changing Symfony logger method signatures.
- Store missing email subjects as empty strings in the existing schema.
- Align development dependencies and CI with the advertised PHP and Laravel versions.
- Correct the README's unsupported Laravel 5 through 7 claims.

### Compatibility

- Composer updates retain logging-only behavior unless the dashboard was explicitly enabled.
- Existing table schema, migration filename, migration publish tag, and Symfony mail storage format are unchanged.
- Setup commands preserve application configuration, existing migrations, and customized views by default.
- Optional integrations remain outside required dependencies.
- Update the MIT copyright year to 2026.
