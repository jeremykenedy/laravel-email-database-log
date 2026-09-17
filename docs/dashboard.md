# Dashboard configuration

The dashboard is disabled by default. It uses Blade and shares one set of templates across standalone styling, Bootstrap 5, and Tailwind. No CDN, remote font, or frontend package is loaded automatically.

## Install and update commands

```bash
php artisan email-log:install
php artisan email-log:update
```

Interactive runs ask which framework to use and whether to use a separately installed Laravel UI Kit. Non-interactive runs retain current selections, defaulting to logging only on a fresh installation.

| Option | Effect |
| --- | --- |
| `--framework=none` | Disable the dashboard without affecting logging. |
| `--framework=standalone` | Enable included styling. |
| `--framework=bootstrap5` | Enable Bootstrap 5 markup. |
| `--framework=tailwind` | Enable Tailwind markup. |
| `--ui-kit` | Use the separately installed UI Kit alert. |
| `--without-ui-kit` | Turn off UI Kit integration. |
| `--publish-views` | Copy missing views without overwriting existing files. |
| `--force-views` | Replace published package views. Back up customizations first. |
| `--no-interaction` | Use explicit options and existing selections without prompts. |

The install command publishes the original migration if no `*_create_email_log.php` migration exists. The update command does not publish migrations. Neither command runs migrations, changes dependencies, or modifies the application's routes or layout files.

Both commands refresh package assets when the dashboard is enabled. Keep asset customizations in your application stylesheet; `public/vendor/email-log` contains replaceable package assets. Commands clear configuration, route, and compiled view caches. Rebuild deployment caches afterward using your normal deployment process.

## Configuration files

`config/laravel-email-database-log.php` holds application-owned settings and is never overwritten by these commands:

```php
return [
    'path' => 'email-log',
    'middleware' => ['web', 'auth'],
    'gate' => 'viewEmailLog',
    'per_page' => 25,
    'theme' => 'system',
    'stylesheet' => null,
];
```

`config/laravel-email-database-log-ui.php` holds the three installer selections: `enabled`, `framework`, and `ui_kit`. The commands update this file rather than rewriting your application-owned settings. Commit both configuration files when deploying from source control.

`per_page` is bounded to 1 through 100. `theme` accepts `light`, `dark`, or `system`. `stylesheet` is an optional URL to compiled CSS supplied by your application, for example `/build/assets/app.css`. Use a stable asset URL from your build or publish a custom layout that uses your application's `@vite` directive.

Route names are `email-log.index` and `email-log.show`. After manually changing routing or enabled settings, rebuild your route and configuration caches. The authorization middleware also checks `enabled` at request time to block previously cached routes when the dashboard is disabled.

## Access

Keep the `web` middleware and your application's authentication middleware, including the appropriate guard when needed. The package additionally requires an authenticated user and the configured gate for every request, even if `auth` is removed from the middleware list.

Define `viewEmailLog` in a service provider using the application's actual administrator permission. The gate is intentionally not granted automatically in local environments. It allows access to the whole log table; applications requiring tenant isolation should leave this dashboard disabled until they provide a suitably scoped implementation.

The database schema is unchanged. Email source is escaped, including addresses, subject, headers, and MIME attachments. Remote content is not rendered. The history query omits message bodies, headers, and attachments to keep list pages small. Search uses parameter-bound SQL `LIKE`; `%` and `_` act as wildcards. Substring searches can scan the log table, so apply your existing retention policy to large installations.

## Bootstrap 5

Install or use your application's Bootstrap 5.3 stylesheet and set `stylesheet` to its compiled URL. Bootstrap JavaScript and jQuery are not required.

```bash
php artisan email-log:update --framework=bootstrap5
```

The appearance selector sets `data-bs-theme="light"` or `data-bs-theme="dark"` on the dashboard document. The included package stylesheet handles the dashboard's surfaces, spacing, and contrast.

## Tailwind

Include package views in the application's content sources so Tailwind can find the utility classes.

For Tailwind 4, relative to the usual `resources/css/app.css`:

```css
@import "tailwindcss";
@source "../../vendor/jeremykenedy/laravel-email-database-log/resources/views";
@source "../views/vendor/email-log";
@custom-variant dark (&:where(.dark, .dark *));
```

For Tailwind 3, add these paths to `content` in `tailwind.config.js` and use `darkMode: 'class'`:

```js
content: [
    './resources/**/*.blade.php',
    './vendor/jeremykenedy/laravel-email-database-log/resources/views/**/*.blade.php',
],
darkMode: 'class',
```

Build your CSS, set `stylesheet` to its URL, and select Tailwind:

```bash
php artisan email-log:update --framework=tailwind
```

## Appearance

The default follows the operating system. The selector persists a choice in `localStorage` under `email-log-theme`, scoped to the application's origin. Storage failures do not disable the selector. System appearance responds to operating system changes. CSS still follows the configured appearance when JavaScript is unavailable.

The selector also sets the Tailwind `dark` class and Bootstrap's `data-bs-theme` attribute. Package colors are CSS variables prefixed with `--el-`. Override them in a published layout or stylesheet if needed.

## Custom views and optional components

```bash
php artisan email-log:update --publish-views
```

Views are copied to `resources/views/vendor/email-log`. Edit `layout.blade.php` to use an application layout, build assets, or optional components. The index and details share this layout. Framework switching does not erase these files.

If UI Kit is enabled, `partials/ui-kit-notice.blade.php` renders `<x-ui::alert>`. Its provider must be registered and its CSS configuration must match the host stylesheet. Removing the optional package falls back to the dashboard without this notice. Logging does not depend on it.

To return to package templates, remove only your published overrides after backing them up, or explicitly use `--force-views` to refresh them. A normal update only refreshes missing views when requested.

## Publish tags

- `laravel-email-database-log-migration`: original migration.
- `laravel-email-database-log-config`: application settings and installer selections.
- `laravel-email-database-log-views`: Blade overrides.
- `laravel-email-database-log-assets`: local CSS and JavaScript.

The standard `vendor:publish --force` option can overwrite files. Prefer the install/update commands when preserving configuration and views.
