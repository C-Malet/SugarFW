<?php

    class ViewContentNotRenderedException extends Exception {

        public function __construct($viewName) {
            parent::__construct('Some content could not be rendered on the view "' . $viewName . '".');
        }

    }

?>