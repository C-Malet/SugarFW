<?php

    include 'exceptions/actions/ActionNotHandledException.class.php';
    include 'exceptions/actions/ActionNotImplementedException.class.php';

    abstract class SugarModuleController {

        /* string */
        private $name;

        /* array */
        private $action;

        /* array */
        private $params;

        /* SugarConfiguration */
        private $configuration;

        /**
         * @param name   controller's name
         * @param params parameters of the query to the controller
         * @param action action of the controller
         */
        public function __construct (
            $name,
            $action,
            array $params
        ) {
            $this->name = $name;
            $this->params = $params;
            $this->action = $action;

            include 'views/SugarView.class.php';

            $this->executeAction();

            $this->control();
        }

        /**
         * control function executed right after the controller has been instantiated
         */
        abstract protected function control();

        /**
         * execute the controller action
         */
        protected function executeAction() {
            if (!empty($this->action)) {


                try {
                    if ($this->validateAction() === false) {
                        return false;
                    }

                    $actionFunction = $this->allowedActions[$this->action];
                    $this->$actionFunction();
                } catch (ActionNotHandledException $e) {
                    die($e->getMessage());
                } catch (ActionNotImplementedException $e) {
                    die($e->getMessage());
                }
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