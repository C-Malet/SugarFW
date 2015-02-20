<?php

    class TestView extends SugarView {

        public function __construct() {
            $this->addRootContent('web/html/headers/header.html');

            $subView = new SugarView;
            $subView->addRootContent('web/html/headers/subHeader.html');

            $this->mergeView($subView, 'subSubViewTest');
        }

    }

?>