<?php


    class ValueAlreadyDefinedException extends Exception {

        public function __construct($className, $valueAlreadyDefined) {
            parent::__construct('The value "' . $valueAlreadyDefined . '" is already defined in view "' . $className . '".');
        }

    }

?>