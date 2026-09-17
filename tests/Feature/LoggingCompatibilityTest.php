<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use jeremykenedy\LaravelEmailDatabaseLog\EmailLogger;
use jeremykenedy\LaravelEmailDatabaseLog\Tests\Mail\TestMail;
use jeremykenedy\LaravelEmailDatabaseLog\Tests\TestCase;
use Symfony\Component\Mime\Email;

class LoggingCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_plain_text_mail_and_custom_headers_are_recorded_once(): void
    {
        Mail::raw('Plain text body', function ($message) {
            $message->to('reader@example.com')->subject('Résumé and café');
            $message->getHeaders()->addTextHeader('X-Reference', 'reference-123');
        });

        $this->assertSame(1, DB::table('email_log')->count());
        $log = DB::table('email_log')->first();
        $this->assertSame('Plain text body', $log->body);
        $this->assertSame('Résumé and café', $log->subject);
        $this->assertStringContainsString('X-Reference: reference-123', $log->headers);
    }

    public function test_multiple_attachments_retain_their_content_and_names(): void
    {
        $mail = (new TestMail)->attachData('First attachment', 'first.txt', ['mime' => 'text/plain'])
            ->attachData('Second attachment', 'second.txt', ['mime' => 'text/plain']);
        Mail::to('reader@example.com')->send($mail);

        $attachments = DB::table('email_log')->value('attachments');
        foreach (['first.txt', 'second.txt', base64_encode('First attachment'), base64_encode('Second attachment')] as $value) {
            $this->assertStringContainsString($value, $attachments);
        }
    }

    public function test_queued_mail_is_logged_when_the_queue_processes_it(): void
    {
        Mail::to('reader@example.com')->queue(new TestMail);
        $this->assertSame(1, DB::table('email_log')->count());
    }

    public function test_a_database_error_remains_visible_to_the_sending_application(): void
    {
        Schema::drop('email_log');
        $this->expectException(QueryException::class);
        Mail::to('reader@example.com')->send(new TestMail);
    }

    public function test_missing_subject_and_body_can_be_logged(): void
    {
        $message = class_exists(\Swift_Message::class) ? (new \Swift_Message)->setBody('') : (new Email)->text('');
        (new EmailLogger)->handle(new MessageSending($message));
        $this->assertDatabaseHas('email_log', ['subject' => '', 'body' => '', 'attachments' => null]);
    }

    public function test_symfony_address_method_keeps_its_public_contract(): void
    {
        if (! class_exists(Email::class)) {
            $this->markTestSkipped('Symfony Mailer is used by Laravel 9 and later.');
        }
        $logger = new EmailLogger;
        $message = (new Email)->from('sender@example.com')->to('reader@example.com');
        $this->assertSame('reader@example.com', $logger->formatAddressField($message, 'To'));
        $this->assertNull($logger->formatAddressField($message, 'Bcc'));
    }

    public function test_migration_schema_and_rollback_remain_compatible(): void
    {
        $this->assertSame(['id', 'date', 'from', 'to', 'cc', 'bcc', 'subject', 'body', 'headers', 'attachments'], Schema::getColumnListing('email_log'));
        $migration = new \CreateEmailLog;
        $migration->down();
        $this->assertFalse(Schema::hasTable('email_log'));
        $migration->up();
        $this->assertTrue(Schema::hasTable('email_log'));
    }
}
