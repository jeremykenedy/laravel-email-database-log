<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Tests\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
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
            ->html('<p>Some random string.</p>');
    }
}
