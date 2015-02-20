<?php

    include 'exceptions/actions/ActionNotHandledException.class.php';
    include 'exceptions/actions/ActionNotImplementedException.class.php';
    include 'views/SugarView.class.php';

    abstract class SugarModuleController {

        /* @var string */
        private $name;

        /* @var array */
        private $action;

        /* @var array */
        private $params;

        /* @var SugarConfiguration */
        private $configuration;

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
        protected function executeAction() {
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

        public function getAction() {
            return $this->action;
        }

        public function getName() {
            return $this->name;
        }

        public function getParams() {
            return $this->params;
        }

    }

?>