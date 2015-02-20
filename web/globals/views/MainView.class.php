<?php

    class MainView extends SugarView {

        public function __construct() {
            $this->addRootContent('web/html/html.html');
            $this->addPartialContent('web/html/heads/head.html', 'head');
            $this->assignValue('My val', 'var');
        }

    }

?>