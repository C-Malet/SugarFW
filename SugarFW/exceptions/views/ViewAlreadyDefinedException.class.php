<?php

    class ViewAlreadyDefinedException extends Exception {

        /**
         * @param viewName        Name of the view that already has a viewContentName defined
         * @param viewContentName Name of the content that already has been defined
         */
        public function __construct($viewName, $viewContentName) {
            parent::__construct('View content "' . $viewContentName . '" already defined in the view ' . $viewName);
        }

    }

?>