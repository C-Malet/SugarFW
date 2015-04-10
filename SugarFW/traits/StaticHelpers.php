<?php

    trait StaticHelpers {

        /**
         * Find the `deepest` child class name
         *
         * @see http://stackoverflow.com/a/283094/2627459
         */
        public static function childClass() {
            return get_called_class();
        }

    }

?>