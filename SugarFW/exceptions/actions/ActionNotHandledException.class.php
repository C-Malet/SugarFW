<?php

    class ActionNotHandledException extends Exception {

        /**
         * @param $actionName Name of the action that is not handled
         */
        public function __construct($actionName) {
            parent::__construct('Action not handled : ' . $actionName);
        }

    }

?>