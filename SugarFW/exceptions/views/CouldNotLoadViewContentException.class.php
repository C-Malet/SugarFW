<?php

    class CouldNotLoadViewContentException extends Exception {

        /**
         * @param $viewName        Name of the view in which we wanted to load the content
         * @param $viewContentName Tag of the content to load
         * @param $viewContentPath Path of the content to load
         */
        public function __construct($viewName, $viewContentName, $viewContentPath) {
            parent::__construct('Could not load content of the view content "' . $viewContentName .
                                '" with the given path "' . $viewContentPath . '" for the view "' . $viewName . '".');
        }

    }

?>