<?php

return [
    'credentials' => base_path(env('FIREBASE_CREDENTIALS')), // <- ganti dengan path ke file json kamu
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET') // <- ganti dengan bucket kamu
];  