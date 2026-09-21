<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Http\Controllers;

use Illuminate\Routing\Controller;
use jeremykenedy\LaravelEmailDatabaseLog\Actions\SearchEmailLogs;
use jeremykenedy\LaravelEmailDatabaseLog\Http\Requests\SearchEmailLogsRequest;
use jeremykenedy\LaravelEmailDatabaseLog\Models\EmailLog;

class EmailLogController extends Controller
{
    private SearchEmailLogs $searchEmailLogs;

    public function __construct(SearchEmailLogs $searchEmailLogs)
    {
        $this->searchEmailLogs = $searchEmailLogs;
    }

    public function index(SearchEmailLogsRequest $request)
    {
        $validated = $request->validated();
        $search = trim($validated['q'] ?? '');
        $logs = $this->searchEmailLogs->execute($search);

        return view('email-log::index', compact('logs', 'search'));
    }

    public function show($id)
    {
        $log = EmailLog::query()->findOrFail($id, ['id', 'date', 'from', 'to', 'cc', 'bcc', 'subject', 'body', 'headers', 'attachments']);

        return view('email-log::show', compact('log'));
    }
}
