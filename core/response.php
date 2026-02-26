<?php 

    class response 
    {
        public static function success($data = [], $message = 'Operación exitosa') {
            return [
                'success' => true,
                'message' => $message,
                'data' => $data
            ];
        }

        public static function error($message = 'Ocurrió un error', $data = []) {
            return [
                'success' => false,
                'message' => $message,
                'data' => $data
            ];
        }
    }
    