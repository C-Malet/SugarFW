<?php

    class ViewRootContentNotSetException extends Exception {

        /**
         * @param $viewName Name of the view
         */
        public function __construct($viewName) {
            parent::__construct('Root content not set for the view "' . $viewName . '", cannot render anything.');
        }

    }

?>