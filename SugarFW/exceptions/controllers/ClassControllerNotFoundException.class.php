<?php

    class ClassControllerNotFoundException extends Exception {

        /**
         * @param className name of the class that should have been present
         * @param classFile path of the file in which the class should have been present
         */
        public function __construct($className, $classFile) {
            parent::__construct('Expected "' . $className . '" to be defined, but could not be found in the file "' . $classFile . '".');
        }

    }

?>