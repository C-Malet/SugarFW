<?php

    class SugarRouter {

        /* array */
        private $path = [];

        /* array */
        private $controllerParams = [];

        /* string */
        private $controllerPath;

        /* string */
        private $controllerAction;

        /* SugarConfiguration */
        private $configuration = null;

        /* string */
        private $DEFAULT_CONTROLLER_NAME = 'Controller';

        /**
         * Initialize the route
         *
         * @param url           URL accessed
         * @param configuration SugarConfiguration object
         */
        public function __construct($url) {

            $parsedUrl = parse_url($url);

            $this->path = array_values(array_filter(explode('/', $parsedUrl['path'])));
            $rootPath = $this->getRootPathFromConfiguration();
            $this->path = array_merge($rootPath, $this->path);

            if (isset($parsedUrl['query'])) {
                $queries = explode('&', $parsedUrl['query']);
                foreach ($queries as $query) {
                    $parsedQuery = explode('=', $query);
                    $key = $parsedQuery[0];
                    $item = $parsedQuery[1];
                    $this->controllerParams[$key] = $item;
                }
            }
        }

        /**
         * Manage the initialized route, handling specials cases and error,
         * and validating the route
         *
         * @return void
         * @throw  ControllerNotFoundException
         */
        public function route() {
            $paths = [];
            $i = 0;

            // Create paths to check in order to load the deepest found controller
            foreach ($this->path as $directory) {
                $paths[$i] = isset($paths[$i - 1]) ?
                             $paths[$i - 1] . '/' . $directory :
                             $directory;
                ++$i;
            }

            // Reverse -> Starts from deepest
            $paths = array_reverse($paths);

            $actionAndPathParameters = [];

            $controllerPath = null;
            foreach ($paths as $path) {
                if (is_dir($path)) {
                    $controllerPath = $path;
                    break;
                } else {
                    $explode = explode('/', $path);
                    array_unshift($actionAndPathParameters, array_pop($explode));
                }
            }

            if (is_null($controllerPath)) {
                include 'exceptions/ControllerNotFoundException.class.php';
                throw new ControllerNotFoundException($parsedUrl['path']);
            }

            $this->controllerAction = array_shift($actionAndPathParameters);

            $actionAndPathParameters = array_chunk($actionAndPathParameters, 2);
            foreach ($actionAndPathParameters as $parameter) {
                if (isset($parameter[1])) {
                    $this->controllerParams[$parameter[0]] = $parameter[1];
                } else {
                    $this->controllerParams[uniqid()] = $parameter[0];
                }
            }

            $this->controllerPath = $controllerPath;
            $explode = explode('/', $controllerPath);
            $this->controllerName = array_pop($explode) . $this->DEFAULT_CONTROLLER_NAME;
        }

        /**
         * Instantiates the controller found during the routing process
         * and sets its initial values
         *
         * @return void
         * @throw  ControllerNotFoundException
         */
        public function launchController() {
            include 'controllers/SugarModuleController.class.php';

            $controllerClassFile = $this->controllerPath . '/_controllers/' . ucfirst($this->controllerName) . '.class.php';

            if (!is_file($controllerClassFile)) {
                // By default, the controller name is equals to (MODULE_NAME . 'Controller')
                // However, if it is not found, the generic 'Controller' name can be used
                $controllerClassFile = $this->controllerPath . '/_controllers/Controller.class.php';
                $this->controllerName = $this->DEFAULT_CONTROLLER_NAME;
                if (!is_file($controllerClassFile)) {
                    include 'exceptions/ControllerNotFoundException.class.php';
                    throw new ControllerNotFoundException($this->controllerPath);
                }
            }

            $controllerConfigFile = $this->controllerPath . '/sugarConfig.ini';
            $controllerConfiguration = new SugarConfiguration($controllerConfigFile);
            _SUGAR::setControllerConfiguration($controllerConfiguration);

            include $controllerClassFile;

            $controllerName = $this->controllerName;
            $controller = new $controllerName(
                $this->controllerName,
                $this->controllerAction,
                $this->controllerParams
            );
        }

        /**
         * @return array
         */
        private function getRootPathFromConfiguration() {
            return _SUGAR::getGlobalConfiguration()->getModulesRoot();
        }

    }

?>