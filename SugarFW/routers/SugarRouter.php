<?php

    class SugarRouter {

        /* @var array */
        private $path = [];

        /* @var string */
        private $routePath;

        /* @var array */
        private $routeParams = [];

        /* @var array */
        private $requestParams = [];

        /* @var string */
        private $targetModule = null;

        /* @var string */
        private $targetController;

        /* @var SugarConfiguration */
        private $configuration = null;

        /**
         * Initialize the route
         * The given URL can be a simple string url, which will be decomposed,
         * or an already decomposed array representating the route
         *
         * /!\ The array route is not yet handled though /!\
         *
         * @param $url URL accessed
         */
        public function __construct($url) {
            if (!is_array($url)) {
                $parsedUrl = parse_url($url);

                $this->path = array_values(array_filter(explode(DIRECTORY_SEPARATOR, $parsedUrl['path'])));

                // Adds the root path to modules before the desired path
                $rootPath = $this->getRootPathFromConfiguration();
                $this->path = array_merge($rootPath, $this->path);

                $this->requestParams = $_REQUEST;

                // Handle request variables with no value (i.e http://url.com/?value)
                // gives them a new unique key and assign the value
                foreach ($this->requestParams as $key => $val) {
                    if ($val === '' || $val === null) {
                        $this->requestParams[uniqid()] = $key;
                        unset($this->requestParams[$key]);
                    }
                }
            } else {
                // Routing from code, given an array
            }
        }

        /**
         * Manage the initialized route, handling specials cases and error,
         * and validate the route up to a module
         * Then, call the controller
         *
         * @throws ControllerNotFoundException
         */
        public function route() {
            $paths = [];

            // Create paths to check in order to load the deepest found module
            $i = 0;
            foreach ($this->path as $directory) {
                $paths[$i] = isset($paths[$i - 1]) ?
                             $paths[$i - 1] . DIRECTORY_SEPARATOR . $directory :
                             $directory;
                ++$i;
            }

            // Reverse -> Starts from deepest
            $paths = array_reverse($paths);

            $routeTargets = [];

            $modulePath = null;
            foreach ($paths as $path) {
                if (is_dir($path)) {
                    $modulePath = $path;

                    $explodedPath = explode(DIRECTORY_SEPARATOR, $path); // Strict standards ZzzZzz ...
                    $this->targetModule = array_pop($explodedPath);
                    break;
                } else {
                    $explode = explode(DIRECTORY_SEPARATOR, $path);

                    // If it's not a module, it is a "route target"
                    array_unshift($routeTargets, array_pop($explode));
                }
            }

            if (is_null($this->targetModule) ||
                $modulePath === implode(DIRECTORY_SEPARATOR, $this->getRootPathFromConfiguration())
            ) {
                $this->targetModule = 'index';
                $modulePath .= DIRECTORY_SEPARATOR . 'index';
            }

            $this->routePath = $modulePath;
            $this->targetController = array_shift($routeTargets); // May be false
            $this->routeParams = $routeTargets;

            $this->launchController();
        }

        /**
         * Instantiates the controller found during the routing process
         * and sets its initial values
         *
         * @throws ControllerNotFoundException
         */
        private function launchController() {
            // If there is no given controller, the default controller name is :
            // MODULE_NAME . 'Controller.php'
            $baseControllerName = $this->targetController === false ?
                                  $this->targetController :
                                  $this->targetModule;

            $controllerClassFile = $this->routePath .
                                   DIRECTORY_SEPARATOR . '_controllers' . DIRECTORY_SEPARATOR .
                                   ucfirst($baseControllerName) . CONTROLLER . '.php';

            if (!is_file($controllerClassFile)) {
                throw new ControllerNotFoundSugarException($controllerClassFile);
            }

            set_include_path(get_include_path() . PATH_SEPARATOR .
                             $this->routePath);

            $controllerName = ucfirst($baseControllerName) . CONTROLLER;
            $controller = new $controllerName(
                $baseControllerName,
                $this->routeParams,
                $this->requestParams
            );
        }

        /**
         * Returns the absolute path to the modules root
         *
         * @return array
         */
        private function getRootPathFromConfiguration() {
            return _SUGAR::getGlobalConfiguration()->getModulesRoot();
        }

    }

?>