<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'anthropic' => ['key' => env('ANTHROPIC_API_KEY')],
    'gemini' => ['key' => env('GEMINI_API_KEY')],
    'openai' => ['key' => env('OPENAI_API_KEY')],
    'deepseek' => ['key' => env('DEEPSEEK_API_KEY'), 'url' => env('DEEPSEEK_URL', 'https://api.deepseek.com'), 'timeout' => (int) env('DEEPSEEK_TIMEOUT', 120)],
    'ollama' => ['url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'), 'timeout' => (int) env('OLLAMA_TIMEOUT', 300)],
    'mizan3g_extraction' => ['provider' => env('MIZAN3G_EXTRACTION_PROVIDER', 'OPENAI'), 'openai_model' => env('MIZAN3G_OPENAI_MODEL', 'gpt-5-mini'), 'anthropic_model' => env('MIZAN3G_ANTHROPIC_MODEL', 'claude-sonnet-4-20250514'), 'gemini_model' => env('MIZAN3G_GEMINI_MODEL', 'gemini-3.8-flash'), 'deepseek_model' => env('MIZAN3G_DEEPSEEK_MODEL', 'deepseek-flash'), 'ollama_model' => env('MIZAN3G_OLLAMA_MODEL', 'qwen3:8b')],

];
