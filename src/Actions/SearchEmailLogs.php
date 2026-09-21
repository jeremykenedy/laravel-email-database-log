<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Actions;

use Illuminate\Contracts\Pagination\Paginator;
use jeremykenedy\LaravelEmailDatabaseLog\Models\EmailLog;

class SearchEmailLogs
{
    public function execute(string $search): Paginator
    {
        return EmailLog::query()->select(['id', 'date', 'from', 'to', 'subject'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('subject', 'like', '%'.$search.'%')
                        ->orWhere('to', 'like', '%'.$search.'%')
                        ->orWhere('from', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('id')
            ->simplePaginate(max(1, min(100, (int) config('laravel-email-database-log.per_page', 25))))
            ->appends(['q' => $search]);
    }
}
