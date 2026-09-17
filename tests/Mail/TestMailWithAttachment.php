<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Tests\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMailWithAttachment extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('The e-mail subject')
            ->attach(__DIR__.'/../stubs/demo.txt')
            ->html('<p>Some random string.</p>');
    }
}
