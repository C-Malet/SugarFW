<?php

    class RootViewAlreadyDefinedException extends Exception {

        public function __construct($viewName) {
            parent::__construct('Root view already defined for the view ' . $viewName);
        }

    }

?>