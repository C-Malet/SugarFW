<?php

    class CouldNotLoadViewContentException extends Exception {

        /**
         * @param viewName
         * @param viewContentName
         * @param viewContentPath
         */
        public function __construct($viewName, $viewContentName, $viewContentPath) {
            parent::__construct('Could not load content of the view content "' . $viewContentName .
                                '" with the given path "' . $viewContentPath . '" for the view "' . $viewName . '".');
        }

    }

?>