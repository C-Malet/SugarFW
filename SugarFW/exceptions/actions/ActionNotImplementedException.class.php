<?php

    class ActionNotImplementedException extends Exception {

        /**
         * @param actionName the name of the action that is not implemented
         */
        public function __construct($actionName) {
            parent::__construct('Action not implemented : ' . $actionName);
        }

    }

?>