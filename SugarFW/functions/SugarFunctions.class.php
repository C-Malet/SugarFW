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

        /**
         * Loads a file containing some PHP content,
         * execute the PHP within the output_buffer
         * and return the resulting content.
         *
         * @param $filePath path of the file to retrieve
         *
         * @return string
         *
         * @see http://stackoverflow.com/a/8751222/2627459
         */
        static public function fileGetContentsExecPHP($filePath) {
            ob_start();
            include $filePath;
            return ob_get_clean();
        }

    }

?>