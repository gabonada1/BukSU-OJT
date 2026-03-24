<?php

namespace App\Support\Security;

class PasswordGenerator
{
    public function generate(int $length = 14): string
    {
        $alphabet = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $password = '';
        $maxIndex = strlen($alphabet) - 1;

        for ($index = 0; $index < $length - 1; $index++) {
            $password .= $alphabet[random_int(0, $maxIndex)];
        }

        return $password.'!';
    }
}
