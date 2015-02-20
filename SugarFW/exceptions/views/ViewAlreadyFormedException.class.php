<?php

    class ViewAlreadyFormedException extends Exception {

        public function __construct($view) {
            parent::__construct('The view "' . $view . '" has already been formed properly.
                                 Set the first parameter as \'true\' if you want to attempt to form the view again.');
        }

    }

?>