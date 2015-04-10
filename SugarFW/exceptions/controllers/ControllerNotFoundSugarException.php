<?php

    class ControllerNotFoundSugarException extends Exception {

        /**
         * @param $controllerName Name of the controller that was not found
         */
        public function __construct($controllerName) {
            parent::__construct('Could not find the controller ' . $controllerName . '.');
        }

    }

?>