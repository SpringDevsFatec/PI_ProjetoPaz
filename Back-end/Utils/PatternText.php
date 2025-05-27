<?php
namespace App\Backend\Utils;

use Exception;

class PatternText {
    public static function processText($data) {
        if (isset($data->name)) {
            $data->name = ucfirst($data->name);
        }
        if (isset($data->email)) {
            $data->email = mb_strtolower($data->email);
        }
        // ... another Pattners for other attributes
        return $data;
    }

    public static function cryptPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}

