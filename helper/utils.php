<?php 

    class utils 
    {
        public static function validateRequiredFields($fields, $data) {
            foreach ($fields as $field) {
                if (empty($data[$field]) || trim($data[$field]) === '') {
                    return false;
                }
            }
            return true;
        }

        public static function sanitizeInput($input) {
            return trim($input);
        }
    }
    