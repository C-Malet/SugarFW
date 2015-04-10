<?php

    class ModuleNotFoundSugarException extends Exception {

        /**
         * @param $moduleName Name of the module that was not found
         */
        public function __construct($moduleName) {
            // TODO give the modules root and given path
            parent::__construct('Could not find the module ' . $moduleName . ' in directories.');
        }

    }

?>