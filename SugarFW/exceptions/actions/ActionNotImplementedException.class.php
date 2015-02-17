<?php

    class ActionNotImplementedException extends Exception {

        /**
         * @param actionName the name of the action that is not implemented
         */
        public function __construct($actionName) {
            parent::__construct('The following action is not implemented : ' . $actionName);
        }

    }

?>