<?php

    class ViewContentNotRenderedException extends Exception {

        /**
         * @param $viewName Name of the view that could not be totally rendered
         */
        public function __construct($viewName) {
            parent::__construct('Some content could not be rendered on the view "' . $viewName . '".');
        }

    }

?>