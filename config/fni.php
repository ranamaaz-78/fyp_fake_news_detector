<?php

return [
    'ml_api_url' => env('FNI_ML_API_URL', 'http://127.0.0.1:5000'),
    'min_input_chars' => (int) env('FNI_MIN_INPUT_CHARS', 20),
    'max_input_chars' => (int) env('FNI_MAX_INPUT_CHARS', 10000),
    'ml_timeout' => (int) env('FNI_ML_TIMEOUT', 10),
    'ml_train_timeout' => (int) env('FNI_ML_TRAIN_TIMEOUT', 30),
    'url_fetch_timeout' => (int) env('FNI_URL_FETCH_TIMEOUT', 10),
    'url_max_bytes' => (int) env('FNI_URL_MAX_BYTES', 524288),
];
