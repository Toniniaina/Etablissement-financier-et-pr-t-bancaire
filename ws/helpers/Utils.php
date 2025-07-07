<?php

class Utils {
    public static function formatDate($date) {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }
}