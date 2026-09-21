<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $table = 'email_log';

    public $timestamps = false;

    protected $fillable = [];

    protected $casts = ['date' => 'datetime'];
}
