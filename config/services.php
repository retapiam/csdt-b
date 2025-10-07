<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Servicios de Inteligencia Artificial
    |--------------------------------------------------------------------------
    |
    | Configuración para todos los servicios de IA disponibles en el sistema
    |
    */

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
        'timeout' => env('OPENAI_TIMEOUT', 30),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 1000),
        'temperature' => env('OPENAI_TEMPERATURE', 0.3),
        'is_active' => env('OPENAI_ACTIVE', true),
        'rate_limit' => env('OPENAI_RATE_LIMIT', 1000), // requests per minute
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'base_url' => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com'),
        'model' => env('ANTHROPIC_MODEL', 'claude-3-opus-20240229'),
        'timeout' => env('ANTHROPIC_TIMEOUT', 30),
        'max_tokens' => env('ANTHROPIC_MAX_TOKENS', 1000),
        'is_active' => env('ANTHROPIC_ACTIVE', true),
        'rate_limit' => env('ANTHROPIC_RATE_LIMIT', 500),
    ],

    'google_gemini' => [
        'api_key' => env('GOOGLE_GEMINI_API_KEY'),
        'base_url' => env('GOOGLE_GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'model' => env('GOOGLE_GEMINI_MODEL', 'gemini-pro'),
        'timeout' => env('GOOGLE_GEMINI_TIMEOUT', 30),
        'is_active' => env('GOOGLE_GEMINI_ACTIVE', true),
        'rate_limit' => env('GOOGLE_GEMINI_RATE_LIMIT', 1000),
    ],

    'elevenlabs' => [
        'api_key' => env('ELEVENLABS_API_KEY'),
        'base_url' => env('ELEVENLABS_BASE_URL', 'https://api.elevenlabs.io/v1'),
        'model' => env('ELEVENLABS_MODEL', 'eleven_multilingual_v1'),
        'voice_id' => env('ELEVENLABS_VOICE_ID', '21m00Tcm4TlvDq8ikWAM'),
        'is_active' => env('ELEVENLABS_ACTIVE', true),
        'rate_limit' => env('ELEVENLABS_RATE_LIMIT', 100),
    ],

    'lexisnexis' => [
        'api_key' => env('LEXISNEXIS_API_KEY'),
        'base_url' => env('LEXISNEXIS_BASE_URL', 'https://api.lexisnexis.com/v1'),
        'is_active' => env('LEXISNEXIS_ACTIVE', true),
        'rate_limit' => env('LEXISNEXIS_RATE_LIMIT', 100),
        'jurisdictions' => ['colombia', 'estados_unidos', 'espana', 'mexico', 'internacional'],
    ],

    'huggingface' => [
        'key' => env('HUGGINGFACE_API_KEY'),
        'model_llama' => env('HUGGINGFACE_MODEL_LLAMA', 'meta-llama/Llama-2-70b-chat-hf'),
        'model_legal_bert' => env('HUGGINGFACE_MODEL_LEGAL_BERT', 'nlpaueb/legal-bert-base-uncased'),
        'is_active' => env('HUGGINGFACE_ACTIVE', false), // Deshabilitado por defecto
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Optimización de IA
    |--------------------------------------------------------------------------
    */

    'voice_optimization' => [
        'auto_update_models' => env('IA_AUTO_UPDATE_MODELS', true),
        'cache_duration' => env('IA_CACHE_DURATION', 7200), // 2 hours
        'fallback_enabled' => env('IA_FALLBACK_ENABLED', true),
        'circuit_breaker_enabled' => env('IA_CIRCUIT_BREAKER_ENABLED', true),
        'rate_limiting_enabled' => env('IA_RATE_LIMITING_ENABLED', true),
        'monitoring_enabled' => env('IA_MONITORING_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Proveedores Legales Especializados
    |--------------------------------------------------------------------------
    */

    'legal_ai_providers' => [
        'constitutional_ai' => [
            'is_active' => env('CONSTITUTIONAL_AI_ACTIVE', true),
            'specialization' => 'derecho_constitucional',
            'capabilities' => ['tutela_analysis', 'constitutional_review', 'fundamental_rights'],
        ],
        'territorial_ai' => [
            'is_active' => env('TERRITORIAL_AI_ACTIVE', true),
            'specialization' => 'derecho_territorial',
            'capabilities' => ['mining_rights', 'environmental_impact', 'community_rights'],
        ],
        'veeduria_ai' => [
            'is_active' => env('VEEDURIA_AI_ACTIVE', true),
            'specialization' => 'veeduria_ciudadana',
            'capabilities' => ['social_control', 'transparency', 'citizen_participation'],
        ],
    ],

];
