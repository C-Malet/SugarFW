<?php

    trait ClassChild {

        // http://stackoverflow.com/a/283094/2627459
        public static function childClass() {
            return get_called_class();
        }

    }

?>