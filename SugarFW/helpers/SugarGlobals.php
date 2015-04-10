<?php

    class _SUGAR {

        /* array */
        private static $_GLOBALS = [];

        /**
         * Translate getters and setters into direct methods to use
         * and store them in as Sugar globals
         */
        public static function __callStatic($name, $arguments) {
            if (strpos($name, 'get') === 0) {
                $var = ltrim($name, 'get');
                return isset(self::$_GLOBALS[$var]) ?
                       self::$_GLOBALS[$var] :
                       null;
            }
            if (strpos($name, 'set') === 0) {
                if (count($arguments) === 0) {
                    return false;
                }
                $var = ltrim($name, 'set');
                self::$_GLOBALS[$var] = count($arguments) === 1 ?
                                        array_shift($arguments) :
                                        $arguments;
                return true;
            }
            return false;
        }

    }

?>