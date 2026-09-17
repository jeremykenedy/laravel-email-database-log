<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class EmailLogController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate(['q' => ['nullable', 'string', 'max:200']]);
        $search = trim($validated['q'] ?? '');
        $query = DB::table('email_log')->select(['id', 'date', 'from', 'to', 'subject']);

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('subject', 'like', '%'.$search.'%')
                    ->orWhere('to', 'like', '%'.$search.'%')
                    ->orWhere('from', 'like', '%'.$search.'%');
            });
        }

        $logs = $query->orderByDesc('id')
            ->simplePaginate(max(1, min(100, (int) config('laravel-email-database-log.per_page', 25))))
            ->appends(['q' => $search]);

        return view('email-log::index', compact('logs', 'search'));
    }

    public function show($id)
    {
        $log = DB::table('email_log')->where('id', $id)->first();
        abort_unless($log, 404);

        return view('email-log::show', compact('log'));
    }
}
