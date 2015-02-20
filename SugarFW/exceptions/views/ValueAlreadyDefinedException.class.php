<?php


    class ValueAlreadyDefinedException extends Exception {

        /**
         * @param $className           Name of the view vith an already defined value
         * @param $valueAlreadyDefined Tag of the value that was already defined
         */
        public function __construct($className, $valueAlreadyDefined) {
            parent::__construct('The value "' . $valueAlreadyDefined . '" is already defined in view "' . $className . '".');
        }

    }

?>