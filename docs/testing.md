# Testing

## PHP suite

```bash
composer install
composer check
composer test-coverage
```

The suite sends actual mail through Laravel's in-memory array transport. It checks address formatting, multiple recipients, named Cc/Bcc, Unicode subjects, custom headers, plain text, MIME attachments, synchronous queue processing, missing subjects, database failure propagation, and migration rollback.

Dashboard tests exercise routes with authentication and gates, every framework option, pagination, search validation, escaped untrusted fields, missing IDs, empty states, and disabling previously registered routes. Console tests use temporary application directories to verify that config, migrations, and customized views survive installation and updates. They also cover invalid options, interactive prompts, and missing optional dependencies.

PHPUnit test method names work across PHPUnit 9 through 13. Xdebug or PCOV is required for coverage. CI publishes a Clover coverage artifact from its quality job; SwiftMailer-specific execution is covered by Laravel 8 matrix jobs.

## CI matrix

The workflow tests Laravel 8 through 13 on their compatible PHP versions, including PHP 8.0 through 8.5. Each Laravel major has a lowest-dependency job as well as current compatible dependencies. Windows jobs cover Laravel 8 and 12. Database jobs run the suite against MySQL 8.4 and PostgreSQL 16 in addition to SQLite. A separate job installs the real optional Laravel UI Kit package and reruns the suite.

Legacy Laravel 8 through 11 jobs permit Composer to resolve retired dependencies with known advisories so compatibility can still be tested. This exception exists only in those CI jobs, not in package Composer configuration. Current dependency jobs retain blocking, and a separate audit checks the current resolved dependency set. Passing legacy tests is not a claim of upstream security support.

Other jobs run Laravel Pint, Composer validation, dependency audits, CSS/JavaScript formatting checks, browser interactions, and accessibility checks. Actions use pinned commit references, read-only repository permissions, timeouts, and cancellation of superseded runs. Dependabot proposes dependency and Action updates monthly.

## Browser suite

```bash
npm ci
npx playwright install chromium
npm run lint
npm test
```

Node dependencies are development tools only. The browser suite compiles Tailwind test CSS and loads real Bootstrap CSS locally. It starts a disposable Testbench application at `127.0.0.1:18765` with an in-memory SQLite database and deterministic sample emails. The test server is not an application entry point and must not be deployed.

Chromium checks each framework in light and dark mode, searching and paging through mail, reading escaped message source, theme persistence, blocked browser storage, operating system theme changes, mobile overflow, keyboard navigation, and WCAG A/AA rules through axe. Tests use no production mail or database credentials.

Run the test server manually for visual review:

```bash
npm run build:test-css
php -S 127.0.0.1:18765 tests/Browser/router.php
```

Visit `/email-log`, `/email-log?framework=bootstrap5`, or `/email-log?framework=tailwind`. The sample server signs in a fixture administrator. Production authorization is tested separately with guests and unauthorized users.

Refresh the README screenshots from the actual dashboard:

```bash
UPDATE_ART=1 npm test
```

Normal test runs leave the committed screenshots untouched. Browser traces are retained on failures.
