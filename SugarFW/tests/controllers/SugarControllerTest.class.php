<?php

    include 'controllers/SugarController.class.php';
    include 'tests/controllers/data/TestController.class.php';

    class SugarControllerTest extends PHPUnit_Framework_TestCase {

        /** @var SugarController */
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