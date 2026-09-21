@php
    $classes = [
        'standalone' => ['container' => '', 'button' => '', 'input' => '', 'table' => ''],
        'bootstrap5' => ['container' => 'container', 'button' => 'btn btn-primary', 'input' => 'form-control', 'table' => 'table align-middle'],
        'tailwind' => ['container' => 'mx-auto max-w-7xl px-6', 'button' => 'rounded-lg px-4 py-2 font-semibold', 'input' => 'w-full rounded-lg border px-4 py-2', 'table' => 'w-full text-left text-sm'],
    ][$framework] ?? ['container' => '', 'button' => '', 'input' => '', 'table' => ''];
@endphp
@extends('email-log::layout')
@section('content')
    <div class="el-heading">
        <div><p class="el-eyebrow">MAIL HISTORY</p><h1>Outgoing emails</h1><p class="el-muted">Browse messages recorded by your application before delivery.</p></div>
        <span class="el-badge">Read only</span>
    </div>
    @if(config('laravel-email-database-log-ui.ui_kit') && class_exists('Jeremykenedy\LaravelUiKit\Providers\UiKitServiceProvider'))
        @component('email-log::components.ui-kit-notice') @endcomponent
    @endif
    <section class="el-panel" aria-label="Email history">
        <form class="el-search" action="{{ route('email-log.index') }}" method="get" role="search">
            <div class="el-search-field"><label for="search">Search email history</label><input id="search" class="el-input {{ $classes['input'] }}" type="search" name="q" value="{{ $search }}" maxlength="200" placeholder="Subject, sender, or recipient"></div>
            <button class="el-button {{ $classes['button'] }}" type="submit">Search</button>
            @if($search !== '')<a class="el-link" href="{{ route('email-log.index') }}">Clear</a>@endif
            @error('q')<p class="el-error" role="alert">{{ $message }}</p>@enderror
        </form>
        @if($search !== '')<p class="el-result">Results for <strong>{{ $search }}</strong></p>@endif
        @if($logs->isEmpty())
            <div class="el-empty"><h2>{{ $search !== '' ? 'No matching emails' : 'No emails recorded yet' }}</h2><p>{{ $search !== '' ? 'Try a different subject, sender, or recipient.' : 'Messages will appear here after your application sends an email.' }}</p></div>
        @else
            <div class="el-table-scroll" role="region" aria-label="Recorded emails" tabindex="0">
                <table class="el-table {{ $classes['table'] }}">
                    <caption class="el-sr-only">Recorded outgoing emails, newest first</caption>
                    <thead><tr><th scope="col">Subject</th><th scope="col">Recipient</th><th scope="col">Recorded</th><th scope="col"><span class="el-sr-only">Details</span></th></tr></thead>
                    <tbody>
                    @foreach($logs as $log)
                        <tr><td><a class="el-subject" href="{{ route('email-log.show', $log->id) }}">{{ $log->subject !== '' ? $log->subject : '(No subject)' }}</a><span class="el-sender">{{ $log->from ?: 'No sender' }}</span></td><td>{{ $log->to ?: 'No direct recipient' }}</td><td class="el-date"><time>{{ $log->date->format('Y-m-d H:i:s') }}</time></td><td><a class="el-link" href="{{ route('email-log.show', $log->id) }}" aria-label="View email {{ $log->id }}">View</a></td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <nav class="el-pagination" aria-label="Email history pages">
            <span>Page {{ $logs->currentPage() }}</span>
            <div>
                @if($logs->previousPageUrl())<a class="el-button el-secondary" rel="prev" href="{{ $logs->previousPageUrl() }}">Previous</a>@endif
                @if($logs->nextPageUrl())<a class="el-button el-secondary" rel="next" href="{{ $logs->nextPageUrl() }}">Next</a>@endif
            </div>
        </nav>
    </section>
@endsection
