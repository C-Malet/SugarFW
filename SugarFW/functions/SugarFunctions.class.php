<?php

    class Sugar {

        /**
         * @param var       variable to be dumped
         * @param dump_type function to dump the variable
         *
         * @return void
         */
        static public function pretty_dump($var, $dump_type = 'var_dump') {
            echo '<pre>';
            $dump_type($var);
            echo '</pre>';
        }

        // http://stackoverflow.com/a/283094/2627459
        public static function childClass() {
            return get_called_class();
        }

    }

?>