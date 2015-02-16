<?php

    class ActionNotHandledException extends Exception {

        public function __construct($message) {
            parent::__construct($message);
        }

    }

?>