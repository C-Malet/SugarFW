<?php

    /**
     * Not sure yet where I am going with this ...
     */

    class SugarLogs {

        /* array */
        private $logs = [];

        private $logsType = [
            0 => 'FATAL',
            1 => 'ERROR',
            2 => 'WARNING',
            3 => 'DEBUG',
            4 => 'INFO'
        ];

        public function __construct() {}

        public function __call($name, $arguments) {
            if (count($arguments) === 1) {
                $this->logs[$name] = (string) $arguments[0];
            }
        }

        public function getLogs() {
            return implode(PHP_EOL, $this->logs);
        }

    }

?>