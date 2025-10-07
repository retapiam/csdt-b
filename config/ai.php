<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Inteligencia Artificial
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'enabled' => env('AI_CACHE_ENABLED', true),
        'duration' => env('AI_CACHE_DURATION_MINUTES', 60) * 60, // en segundos
        'max_entries' => env('AI_CACHE_MAX_ENTRIES', 1000),
        'strategy' => env('AI_CACHE_STRATEGY', 'lru'),
    ],

    'limits' => [
        'max_requests_per_minute' => env('AI_MAX_REQUESTS_PER_MINUTE', 60),
        'max_tokens_per_day' => env('AI_MAX_TOKENS_PER_DAY', 100000),
        'max_files_per_analysis' => env('AI_MAX_FILES_PER_ANALYSIS', 10),
        'max_file_size_mb' => env('AI_MAX_FILE_SIZE_MB', 10),
        'timeout_default' => env('AI_TIMEOUT_DEFAULT', 30000),
        'max_retries' => env('AI_MAX_RETRIES', 3),
    ],

    'security' => [
        'encrypt_data' => env('AI_ENCRYPT_DATA', true),
        'validate_inputs' => env('AI_VALIDATE_INPUTS', true),
        'sanitize_responses' => env('AI_SANITIZE_RESPONSES', true),
        'rate_limiting' => env('AI_RATE_LIMITING', true),
        'auth_required' => env('AI_AUTH_REQUIRED', true),
    ],

    // Nota: La configuración de proveedores está en config/services.php
    // Este archivo solo contiene configuración de límites y seguridad
    'cost_per_token' => [
        'openai' => env('OPENAI_COST_PER_TOKEN', 0.00003),
        'anthropic' => env('ANTHROPIC_COST_PER_TOKEN', 0.000015),
        'google_gemini' => env('GEMINI_COST_PER_TOKEN', 0.000001),
        'elevenlabs' => env('ELEVENLABS_COST_PER_REQUEST', 0.001),
        'lexisnexis' => env('LEXISNEXIS_COST_PER_REQUEST', 0.01),
        'huggingface' => env('HUGGINGFACE_COST_PER_TOKEN', 0.000001),
    ],
];

