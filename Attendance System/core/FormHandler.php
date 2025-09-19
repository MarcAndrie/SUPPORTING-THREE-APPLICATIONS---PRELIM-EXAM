<?php
class FormHandler {
    public static function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
?>
