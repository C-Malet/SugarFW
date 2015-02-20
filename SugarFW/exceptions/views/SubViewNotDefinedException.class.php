<?php

    class SubViewNotDefinedException extends Exception {

        /**
         * @param $viewName Name of the view in which the subView wasn't defined
         * @param $subView  Subview that was not defined
         */
        public function __construct($viewName, $subView) {
            if (is_object($subView)) {
                $subView = get_class($subView);
            }
            parent::__construct('The subview "' . $subView . '" was unexpectedly not defined in the view "' . $viewName . '".');
        }

    }

?>