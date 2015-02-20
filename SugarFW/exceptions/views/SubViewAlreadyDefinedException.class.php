<?php

    class SubViewAlreadyDefinedException extends Exception {

        public function __construct($view, $subView, $tag) {
            parent::__construct('Could not merge the view "' . $subView . '" into "' . $view . '" :
                                 a subview with the tag "' . $tag . '" has already been defined.');
        }

    }

?>