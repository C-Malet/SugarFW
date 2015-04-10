<?php

    class Sugar {

        /**
         * @param $var       Variable to be dumped
         * @param $dump_type Function to dump the variable
         *
         * @return void
         */
        static public function pre_dump($var, $dump_type = 'var_dump') {
            echo '<pre>';
            $dump_type($var);
            echo '</pre>';
        }

    }

?>