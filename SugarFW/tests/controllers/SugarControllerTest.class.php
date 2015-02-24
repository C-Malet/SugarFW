<?php

    include 'controllers/SugarModuleController.class.php';
    include 'tests/controllers/data/TestController.class.php';

    class SugarControllerTest extends PHPUnit_Framework_TestCase {

        /** @var SugarModuleController */
        private $controller;

        public function setUp() {
            $this->controller = new TestController (
                'name',
                'action',
                [] // params
            );
        }

    }

?>