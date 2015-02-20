<?php

    class SugarExecutionException extends Exception {

        public function __construct() {
            parent::__construct('An unexpected error occured during the execution of the SugarFW process.');
        }

    }

?>