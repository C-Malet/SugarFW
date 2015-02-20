<?php

    class RootViewAlreadyDefinedException extends Exception {

        /**
         * @param $viewName Name of the view that already have a root content defined
         */
        public function __construct($viewName) {
            parent::__construct('Root content already defined for the view ' . $viewName);
        }

    }

?>