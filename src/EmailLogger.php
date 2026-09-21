<?php

namespace jeremykenedy\LaravelEmailDatabaseLog;

use Carbon\Carbon;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\DB;
use jeremykenedy\LaravelEmailDatabaseLog\Models\EmailLog;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class EmailLogger
{
    /**
     * Handle the actual logging.
     */
    public function handle(MessageSending $event): void
    {
        $message = $event->message;

        if ($message instanceof \Swift_Message) {
            $this->logSwiftMessage($message);

            return;
        }

        DB::table((new EmailLog)->getTable())->insert(
            [
                'date'        => Carbon::now()->format('Y-m-d H:i:s'),
                'from'        => $this->formatAddressField($message, 'From'),
                'to'          => $this->formatAddressField($message, 'To'),
                'cc'          => $this->formatAddressField($message, 'Cc'),
                'bcc'         => $this->formatAddressField($message, 'Bcc'),
                'subject'     => $message->getSubject() ?? '',
                'body'        => $message->getBody()?->bodyToString() ?? '',
                'headers'     => $message->getHeaders()->toString(),
                'attachments' => $this->saveAttachments($message),
            ]
        );
    }

    /**
     * Format address strings for sender, to, cc, bcc.
     */
    public function formatAddressField(Email $message, string $field): ?string
    {
        $headers = $message->getHeaders();

        return $headers->get($field)?->getBodyAsString();
    }

    /**
     * Collect all attachments and format them as strings.
     */
    protected function saveAttachments(Email $message): ?string
    {
        if (empty($message->getAttachments())) {
            return null;
        }

        return collect($message->getAttachments())
            ->map(fn (DataPart $part) => $part->toString())
            ->implode("\n\n");
    }

    protected function logSwiftMessage(\Swift_Message $message): void
    {
        $address = fn (string $field) => $message->getHeaders()->get($field)?->getFieldBody();
        $attachments = collect($message->getChildren())
            ->filter(fn ($part) => $part instanceof \Swift_Attachment)
            ->map(fn ($part) => $part->toString());

        DB::table((new EmailLog)->getTable())->insert([
            'date'        => Carbon::now()->format('Y-m-d H:i:s'),
            'from'        => $address('From'),
            'to'          => $address('To'),
            'cc'          => $address('Cc'),
            'bcc'         => $address('Bcc'),
            'subject'     => $message->getSubject() ?? '',
            'body'        => $message->getBody() ?? '',
            'headers'     => $message->getHeaders()->toString(),
            'attachments' => $attachments->isEmpty() ? null : $attachments->implode("\n\n"),
        ]);
    }
}
