<?php

    class TestController extends SugarModuleController {

        /* @var array */
        protected $allowedActions = [
            'edit' => 'editAction'
        ];

        protected function control() {
            include 'web/globals/views/MainView.class.php';
            $view = new MainView();
            $view->renderView();
        }

        protected function editAction() {

        }

    }

?>