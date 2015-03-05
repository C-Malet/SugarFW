<?php

    include_once 'exceptions/actions/ActionNotHandledException.class.php';
    include_once 'exceptions/actions/ActionNotImplementedException.class.php';
    include_once 'views/SugarView.class.php';

    abstract class SugarController {

        /* @var string */
        private $name;

        /* @var string */
        private $action;

        /* @var array */
        private $params;

        /* @var SugarConfiguration */
        private $configuration;

        /* @var array */
        protected $allowedActions;

        /**
         * At the moment, Sugar controllers are always meant to go through
         * the SugarModuleController
         * This is done to ensure that all fields are properly set directly
         * from the SugarRouter, and that some checks can be done before
         * the control execution.
         *
         * @param $name   Controller's name
         * @param $params Parameters of the query to the controller
         * @param $action Action of the controller
         */
        final public function __construct (
            $name,
            $action,
            array $params
        ) {
            $this->name   = $name;
            $this->params = $params;
            $this->action = $action;

            $this->control();
        }

        /**
         * Control function executed right after the controller has been instantiated
         */
        abstract protected function control();

        /**
         * Execute the controller action
         */
        public function executeAction() {
            if (!empty($this->action)) {
                if ($this->validateAction() === false) {
                    return false;
                }

                $actionFunction = $this->allowedActions[$this->action];
                $this->$actionFunction();
            }
        }

        /**
         * Checks if the wanted action is handled by the controller
         *
         * @throws ActionNotHandledException
         */
        private function validateAction() {
            if (!in_array($this->action, $this->allowedActions())
             || !$this->checkImplementedActions($this->allowedActions)
            ) {
                $configToUse = _SUGAR::getControllerConfiguration()->StrickCheckActions() !== null ?
                               _SUGAR::getControllerConfiguration() :
                               _SUGAR::getGlobalConfiguration();

                $strictCheck = $configToUse !== null ?
                               ($configToUse->StrickCheckActions() == 1 ? true : false) :
                               false;

                if ($strictCheck) {
                    throw new ActionNotHandledException($this->action);
                } else {
                    return false;
                }
            }
        }

        /**
         * List of actions allowed for this controller
         *
         * @return array
         */
        private function allowedActions() {
            if (isset($this->allowedActions)
            ) {
                return array_keys($this->allowedActions);
            } else {
                return [];
            }
        }

        /**
         * Adds an action as allowed for the current controller
         *
         * @param array $actionName
         * @param array $actionMethod May be ommited, actionMethod will then be $actionName . 'Action'
         */
        public function addAllowedAction($actionName, $actionMethod = null) {
            if ($actionMethod === null) {
                $actionMethod = $actionName . 'Action';
            }
            $this->allowedActions[$actionName] = $actionMethod;
        }

        /**
         * Adds several actions as allowed for the current controller
         *
         * @param array $actions Each entry of the array is an action :
         *                       key => 'actionName', value => 'actionMethod'
         */
        public function addAllowedActions(array $actions) {
            foreach ($actions as $actionName => $actionMethod) {
                $this->addAllowedAction($actionName, $actionMethod);
            }
        }

        /**
         * Checks if the actions declared as allowed are correctly implemented in the Controller
         *
         * @param $allowedActions List of allowed actions
         *
         * @throws ActionNotImplementedException
         * @return true;
         */
        private function checkImplementedActions($allowedActions) {
            foreach ($allowedActions as $action) {
                if (!method_exists($this, $action)) {
                    throw new ActionNotImplementedException($action);
                }
            }
            return true;
        }

        public function setControllerConfiguration(SugarConfiguration $configuration) {
            $this->configuration = $configuration;
        }

        /**
         * @return string
         */
        public function getAction() {
            return $this->action;
        }

        /**
         * @return string
         */
        public function getName() {
            return $this->name;
        }

        /**
         * @return array
         */
        public function getParams() {
            return $this->params;
        }

    }

?>