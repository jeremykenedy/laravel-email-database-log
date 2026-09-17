<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Tests\Browser;

use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class BrowserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $framework = $_GET['framework'] ?? 'standalone';
        $framework = in_array($framework, ['standalone', 'bootstrap5', 'tailwind'], true) ? $framework : 'standalone';
        $this->app['config']->set([
            'app.key'                                 => 'base64:MTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3ODkwMTI=',
            'app.debug'                               => true,
            'database.default'                        => 'sqlite',
            'database.connections.sqlite.database'    => ':memory:',
            'session.driver'                          => 'array',
            'laravel-email-database-log-ui.enabled'   => true,
            'laravel-email-database-log-ui.framework' => $framework,
            'laravel-email-database-log.per_page'     => 5,
            'laravel-email-database-log.stylesheet'   => $framework === 'standalone' ? null : '/'.$framework.'.css',
        ]);
    }

    public function boot(): void
    {
        require_once __DIR__.'/../../src/Database/Migrations/2023_02_26_001638_create_email_log.php';
        (new \CreateEmailLog)->up();
        $subjects = ['Weekly account summary', 'Your invoice is ready', 'Welcome to Northstar', 'Password reset request', 'Your order has shipped', 'Payment received', 'Team invitation'];
        foreach ($subjects as $index => $subject) {
            DB::table('email_log')->insert([
                'date'    => '2026-09-17 '.sprintf('%02d', 9 + $index).':24:00',
                'from'    => 'Northstar <hello@example.com>',
                'to'      => ['alex@example.com', 'sam@example.com', 'jordan@example.com'][$index % 3],
                'subject' => $subject,
                'body'    => '<h1>'.$subject.'</h1><p>Your account has been updated.</p><script>window.emailExecuted = true;</script>',
                'headers' => 'Content-Type: text/html; charset=utf-8',
            ]);
        }
        Gate::define('viewEmailLog', fn ($user) => $user->id === 1);
        if (! isset($_GET['guest'])) {
            Auth::guard()->setUser(new GenericUser(['id' => 1]));
        }
    }
}
