<?php

    include 'controllers/SugarController.class.php';
    include 'tests/controllers/data/TestController.class.php';
    include 'helpers/SugarGlobals.class.php';
    include 'helpers/SugarConfiguration.class.php';

    class SugarActionTest extends PHPUnit_Framework_TestCase {

        /** @var SugarController */
        private $controller;

        public function setUp() {
            $this->controller = new TestController (
                'name',
                'action',
                [] // params
            );
            $globalConfiguration = new SugarConfiguration('tests/data/sugarConfig.ini');
            _SUGAR::setGlobalConfiguration($globalConfiguration);
            $controllerConfiguration = new SugarConfiguration('tests/data/sugarControllerConfig.ini');
            _SUGAR::setControllerConfiguration($globalConfiguration);
        }

        /**
         * @expectedException ActionNotHandledException
         */
        public function testActionNotHandled() {
            $this->controller->executeAction();
        }

    }

?>