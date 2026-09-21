# Upgrading existing installations

## Logging-only applications

Run your normal Composer update and deployment checks. No new migration or setup command is required. Logging remains enabled through the original `MessageSending` listener; the dashboard remains disabled unless explicitly configured.

This release preserves:

- PHP `^8.0` and the existing Illuminate support constraints through Laravel 13.
- The `email_log` table and every existing column.
- Migration `2023_02_26_001638_create_email_log.php` and class `CreateEmailLog`.
- Publish tag `laravel-email-database-log-migration`.
- The registered provider, event provider, and `EmailLogger::handle` contract.
- The signatures of `formatAddressField` and `saveAttachments` for Symfony mail objects.
- Existing Symfony body, address, header, and MIME attachment storage behavior.
- Propagation of database errors to the sending application.

Laravel 8's SwiftMailer events now have a separate logging path. Previously the package allowed Laravel 8 in Composer but required Symfony mail objects at runtime. Missing subjects now use an empty string, which fits the unchanged non-null subject column.

There was no previous frontend, route, or published view set to replace. The new GUI is an explicit addition. None of the optional packages are installed by an update.

## Enabling a dashboard

1. Back up your database and review who should have access to the entire mail log.
2. Run `php artisan email-log:install --framework=standalone`, or choose Bootstrap 5 or Tailwind.
3. Define the `viewEmailLog` gate using your application's admin permission.
4. Supply compiled framework CSS if using Bootstrap 5 or Tailwind.
5. Rebuild application caches and visit `/email-log` as an authorized user.

Do not republish or rerun the table creation migration on an existing table. The installer recognizes migrations ending in `_create_email_log.php`; if you renamed it to a different suffix, use `email-log:update` to configure the dashboard without publishing migrations.

## Updating a configured dashboard

```bash
php artisan email-log:update --no-interaction
```

This retains the framework and optional integration choices, preserves application configuration and published views, and replaces package CSS/JavaScript assets. It clears application configuration, route, and compiled view caches; rebuild them as part of deployment.

Use explicit options to switch frameworks or disable the dashboard. `--force-views` overwrites view customizations and should only be used after reviewing and backing up those changes.

## Existing limitations retained

The logger runs before transport delivery and inside the current database connection/transaction. A rolled-back transaction may roll back the log. String column lengths and the original body column type are unchanged, including database-specific size limits. This update does not truncate oversized records or silently ignore database failures.

Changes to mail retention, queue behavior, delivery confirmation, database schema, and per-tenant access are outside this release. Test the package with your application's custom mail transports and logger subclasses before deployment.
