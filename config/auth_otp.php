<?php

return [
    'ttl_minutes' => (int) env('AUTH_OTP_TTL_MINUTES', 10),
    'max_attempts' => (int) env('AUTH_OTP_MAX_ATTEMPTS', 5),
    'send_cooldown_seconds' => (int) env('AUTH_OTP_SEND_COOLDOWN_SECONDS', 60),
];
