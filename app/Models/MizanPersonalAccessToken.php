<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken;

class MizanPersonalAccessToken extends PersonalAccessToken
{
    protected $table = 'mizan3g_personal_access_tokens';
}
