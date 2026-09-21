<?php

return [
    'app.key'          => 'base64:'.base64_encode(random_bytes(32)),
    'database.default' => env('DB_CONNECTION', 'sqlite'),
];
