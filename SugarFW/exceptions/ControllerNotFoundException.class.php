<?php

    class ControllerNotFoundException extends Exception {

        /**
         * @param controllerName the name of the controller that was not found
         */
        public function __construct($controllerName) {
            parent::__construct('Could not find the controller ' . $controllerName . '.');
        }

    }

?>