# Laravel Email Database Log
A database logger for all outgoing emails sent by your Laravel application.

[![Total Downloads](https://poser.pugx.org/jeremykenedy/laravel-email-database-log/d/total.svg)](https://packagist.org/packages/jeremykenedy/laravel-email-database-log)
[![StyleCI](https://github.styleci.io/repos/606927458/shield?branch=master)](https://github.styleci.io/repos/606927458?branch=master)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![All Contributors](https://img.shields.io/badge/all_contributors-1-orange.svg?style=flat-square)](#contributors)
<a href="https://www.patreon.com/bePatron?u=10119959" title="Become a Patreon">
    <img src="https://c5.patreon.com/external/logo/become_a_patron_button.png" alt="Become a Patreon" width="85px" >
</a>

#### Table of contents
- [Requirements](#requirements)
- [Installation Instructions](#installation-instructions)
    - [Publish All Assets](#publish-all-assets)
- [Usage](#usage)
- [File Tree](#file-tree)
- [License](#license)

### Requirements
* [Laravel 5.5, 5.6, 5.7, 5.8, 6.0+, 7.0+, 8.0+, 9.0+, or 10.0+](https://laravel.com/docs/installation)

### Installation Instructions
1. From your projects root folder in terminal run:

    ```bash
        composer require jeremykenedy/laravel-email-database-log
    ```

2. Register the package

* Laravel 5.5 and up
Uses package auto discovery feature, no need to edit the `config/app.php` file.

* Laravel 5.4 and below
Register the package with laravel in `config/app.php` under `providers` with the following:

```php
    'providers' => [
        // ...
        jeremykenedy\LaravelEmailDatabaseLog\LaravelEmailDatabaseLogServiceProvider::class,
    ];
```

3. Publish the packages migration files by running the following from your projects root folder:

```bash
php artisan vendor:publish --tag=laravel-email-database-log-migration
```

4. From your projects root folder in terminal run the migration:

```bash
php artisan migrate
```

### Usage
After installation, any email sent by your application will be logged to `email_log` table in the site's database.

### File Tree
```bash
laravel-email-database-log
├── .all-contributorsrc
├── .github
│   └── workflows
│       └── master.yml
├── .gitignore
├── .styleci.yml
├── LICENSE.md
├── README.md
├── composer.json
├── phpunit.xml
└── src
    ├── database
    │   └── migrations
    │       ├── 2015_07_31_1_email_log.php
    │       ├── 2016_09_21_001638_add_bcc_column_email_log.php
    │       ├── 2017_11_10_001638_add_more_mail_columns_email_log.php
    │       └── 2018_05_11_115355_use_longtext_for_attachments.php
    └── jeremykenedy
        └── LaravelEmailDatabaseLog
            ├── EmailLogger.php
            ├── LaravelEmailDatabaseLogEventServiceProvider.php
            └── LaravelEmailDatabaseLogServiceProvider.php
```

* Tree command can be installed using brew: `brew install tree`
* File tree generated using command `tree -a -I '.git|node_modules|vendor|storage|tests'`

### License
Laravel Email Database Log is licensed under the [MIT license](https://opensource.org/licenses/MIT). Enjoy!
