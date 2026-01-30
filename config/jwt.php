<?php

return [
    'secret' => env('JWT_SECRET', 'your-default-secret-key'),
    'algo' => env('JWT_ALGO', 'HS256'),
];