<?php

namespace App\Helper;

abstract class Utils {

    /**
     * Check if class has trait
     * @param string $className
     * @return bool
     */
    public static function hasTrait($trait, $class){
        try {
            return in_array($trait, class_uses($class) ?? []);
        } catch (\Throwable $th) {
            return false;
        }
    }
}
