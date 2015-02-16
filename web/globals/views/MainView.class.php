<?php

    class MainView extends SugarView {

        public function __construct() {
            $this->addView('web/html/html.html');
            $this->addView('web/html/heads/head.html', 'head');
        }

    }

?>