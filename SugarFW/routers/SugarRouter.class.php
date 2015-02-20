<?php

    include 'controllers/SugarModuleController.class.php';

    include 'exceptions/controllers/ControllerNotFoundException.class.php';
    include 'exceptions/controllers/ClassControllerNotFoundException.class.php';

    class SugarRouter {

        /* @var array */
        private $path = [];

        /* @var array */
        private $controllerParams = [];

        /* @var string */
        private $controllerPath;

        /* @var string */
        private $controllerAction;

        /* @var SugarConfiguration */
        private $configuration = null;

        /* @var string */
        private $DEFAULT_CONTROLLER_NAME = 'Controller';

        /* @var string */
        private $DEFAULT_VIEW_NAME = 'View';

        /**
         * Initialize the route
         *
         * @param $url URL accessed
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
         * @throws ControllerNotFoundException
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
            $name = array_pop($explode);
            $this->viewName = $name . $this->DEFAULT_VIEW_NAME;
            $this->controllerName = $name . $this->DEFAULT_CONTROLLER_NAME;
        }

        /**
         * Instantiates the controller found during the routing process
         * and sets its initial values
         *
         * @throws ControllerNotFoundException
         */
        public function launchController() {
            $controllerClassFile = $this->controllerPath . '/_controllers/' . ucfirst($this->controllerName) . '.class.php';
            $viewClassFile = $this->controllerPath . '/_views/' . ucfirst($this->viewName) . '.class.php';

            if (!is_file($controllerClassFile)) {
                // By default, the controller name is equals to (MODULE_NAME . 'Controller')
                // However, if it is not found, the generic 'Controller' name can be used
                $controllerClassFile = $this->controllerPath . '/_controllers/Controller.class.php';
                $this->controllerName = $this->DEFAULT_CONTROLLER_NAME;
                if (!is_file($controllerClassFile)) {
                    throw new ControllerNotFoundException($this->controllerPath);
                }
            }

            $controllerConfigFile = $this->controllerPath . '/sugarConfig.ini';
            $controllerConfiguration = new SugarConfiguration($controllerConfigFile);
            _SUGAR::setControllerConfiguration($controllerConfiguration);

            include $controllerClassFile;

            // Auto include of view files is not mandatory
            if (is_file($viewClassFile)) {
                include $viewClassFile;
            }

            if (!class_exists($this->controllerName)) {
                throw new ClassControllerNotFoundException($this->controllerName, $controllerClassFile);
            }

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