@extends('email-log::layout')
@section('title', 'Email details')
@section('content')
    <a class="el-back" href="{{ route('email-log.index') }}">Back to email history</a>
    <div class="el-heading"><div><p class="el-eyebrow">MESSAGE #{{ $log->id }}</p><h1>{{ $log->subject !== '' ? $log->subject : '(No subject)' }}</h1><p class="el-muted">Recorded {{ $log->date }}</p></div><span class="el-badge">Read only</span></div>
    <section class="el-panel el-details" aria-label="Message details">
        <dl class="el-metadata">
            <div><dt>From</dt><dd>{{ $log->from ?: 'None' }}</dd></div>
            <div><dt>To</dt><dd>{{ $log->to ?: 'None' }}</dd></div>
            <div><dt>Cc</dt><dd>{{ $log->cc ?: 'None' }}</dd></div>
            <div><dt>Bcc</dt><dd>{{ $log->bcc ?: 'None' }}</dd></div>
        </dl>
        <div class="el-message"><h2>Message source</h2><p class="el-muted">Stored content is shown as text. Remote images and scripts are not loaded.</p><pre class="el-source">{{ $log->body }}</pre></div>
        <details class="el-disclosure"><summary>Headers</summary><pre class="el-source">{{ $log->headers ?: 'No headers recorded.' }}</pre></details>
        <details class="el-disclosure"><summary>Attachment source</summary><pre class="el-source">{{ $log->attachments ?: 'No attachments recorded.' }}</pre></details>
    </section>
@endsection
