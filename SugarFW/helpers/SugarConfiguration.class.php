<?php

    class SugarConfiguration {

        /* @var array */
        private $config;

        /**
         * Load a config file
         *
         * @param $iniFile file path of the ini file to load
         */
        public function __construct($iniFile = 'sugarConfig.ini') {
            if (is_file($iniFile)) {
                $this->config = parse_ini_file($iniFile);
            }
        }

        /**
         * Form an array made of directories to the root module from the root of the project
         *
         * @return array
         */
        public function getModulesRoot() {
            if (isset($this->config['ModulesRoot'])) {
                return array_filter(explode('/', $this->config['ModulesRoot']));
            } else {
                return array();
            }
        }

        public function __call($name, $arguments) {
            $name = ltrim($name, 'get');
            if (is_array($this->config) && isset($this->config[lcfirst($name)])) {
                return $this->config[lcfirst($name)];
            } else {
                return null;
            }
        }

    }

?>