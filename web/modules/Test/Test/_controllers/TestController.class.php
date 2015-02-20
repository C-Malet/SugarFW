<?php

    class TestController extends SugarModuleController {

        /* @var SugarView */
        private $view;

        /* @var array */
        protected $allowedActions = [
            'edit' => 'editAction'
        ];

        protected function control() {
            include 'web/globals/views/MainView.class.php';
            $this->view = new MainView;

            $subView = new TestView;
            $this->view->mergeView($subView, 'exampleMergeTag');

            $this->executeAction();

            $this->view->renderView();
        }

        protected function editAction() {

        }

    }

?>