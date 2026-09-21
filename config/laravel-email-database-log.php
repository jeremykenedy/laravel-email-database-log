<?php

return [
    'path'       => 'email-log',
    'middleware' => ['web', 'auth'],
    'gate'       => 'viewEmailLog',
    'per_page'   => 25,
    'theme'      => 'system',
    'stylesheet' => null,
];
