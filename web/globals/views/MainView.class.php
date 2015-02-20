<?php

    class MainView extends SugarView {

        public function __construct() {
            $this->addRootView('web/html/html.html');
            $this->addView('web/html/heads/head.html', 'head');
            $this->assignValue('Ma valeur', 'var');
        }

    }

?>