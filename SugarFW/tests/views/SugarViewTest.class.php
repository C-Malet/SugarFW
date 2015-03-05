<?php

    include 'views/SugarView.class.php';
    include 'helpers/SugarGlobals.class.php';
    include 'helpers/SugarConfiguration.class.php';
    include 'views/SugarLayout.class.php';

    class SugarViewTest extends PHPUnit_Framework_TestCase {

        /* @var SugarView */
        private $view;

        public function setUp() {
            $this->view = new SugarView;
            $globalConfiguration = new SugarConfiguration('tests/data/sugarConfig.ini');
            _SUGAR::setGlobalConfiguration($globalConfiguration);
        }

        /**
         * @expectedException ViewRootContentNotSetException
         */
        public function testRootContentNotSet() {
            $this->view->renderView();
        }

        /**
         * @expectedException RootViewAlreadyDefinedException
         */
        public function testRootContentAlreadySet() {
            $this->view->addRootContent('tests/views/data/testRootContentAlreadySet');
            $this->view->addRootContent('tests/views/data/testRootContentAlreadySet');
        }

        /**
         * @expectedException CouldNotLoadViewContentException
         */
        public function testCouldNotLoadViewContent() {
            $this->view->addRootContent('undefined');
        }

        /**
         * @expectedException ViewAlreadyDefinedException
         */
        public function testViewAlreadyDefined() {
            $this->view->addPartialContent('tests/views/data/testViewAlreadyDefined', 'test');
            $this->view->addPartialContent('tests/views/data/testViewAlreadyDefined', 'test');
        }

        /**
         * @expectedException ViewAlreadyFormedException
         */
        public function testViewAlreadyFormed() {
             $this->view->addRootContent('tests/views/data/testRootContent');
             $this->view->renderView();
             $this->view->renderView();
         }

        /**
         * @expectedException SubViewAlreadyDefinedException
         */
        public function testSubViewAlreadyDefined() {
            $subview = new SugarView;
            $this->view->mergeView($subview, 'subview');
            $this->view->mergeView($subview, 'subview');
        }

        /**
         * @expectedException SubViewNotDefinedException
         */
        public function testSubViewNotDefined() {
            $this->view->removeView('test');
        }

        /**
         * @expectedException ValueAlreadyDefinedException
         */
        public function testValueAlreadyDefined() {
            $this->view->assignValue('myVal', 'val');
            $this->view->assignValue('anotherVal', 'val');
        }
    }

?>