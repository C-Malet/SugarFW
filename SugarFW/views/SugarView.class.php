<?php

    include 'exceptions/views/RootViewAlreadyDefinedException.class.php';
    include 'exceptions/views/ViewAlreadyDefinedException.class.php';
    include 'exceptions/views/CouldNotLoadViewContentException.class.php';
    include 'exceptions/views/ViewContentNotRenderedException.class.php';
    include 'exceptions/views/ValueAlreadyDefinedException.class.php';
    include 'exceptions/SugarExecutionException.class.php';

    class SugarView {

        /* @var string */
        private $rootContent;

        /* @var array */
        private $partialContents = [];

        /* @var array */
        private $assignedValues = [];

        /**
         * List of sugarTemplateIndicators used to render the view
         * Even though these indicators are hard coded here `just in case`, they
         * are supposed to be retrieved from the global configuration file
         *
         * @var array
         */
        private $sugarTemplateIndicators = [
            'includeLeft'      => '$$',
            'includeRight'     => '$$',
            'includeSeparator' => '',
            'valueLeft'        => '{{',
            'valueRight'       => '}}',
            'valueSeparator'   => ''
        ];

        /* @var string */
        private $formedContent;

        /**
         * Adds a partial content that will be used to from the view
         *
         * @param viewContentPath
         * @param $viewContentName
         *
         * @throws CouldNotLoadViewContentException
         * @throws ViewAlreadyDefinedException
         */
        public function addPartialContent($contentPath, $contentName = null) {
            if (file_exists($contentPath) === false) {
                throw new CouldNotLoadViewContentException(Sugar::childClass(), $contentName, $contentPath);
            }
            $content = file_get_contents($contentPath);
            if ($content === false) {
                throw new CouldNotLoadViewContentException(Sugar::childClass(), $contentName, $contentPath);
            }

            if ($contentName === null) {
                $this->addRootContent($contentPath, $content);
            } else {
                if (isset($this->partialContents[$contentName])) {
                    throw new ViewAlreadyDefinedException(Sugar::childClass(), $contentName);
                }
                $this->partialContents[$contentName] = $content;
            }
        }

        /**
         * Adds a root content to the view, that will hold the main content that will be used to form the view
         *
         * @param $contentPath Path to the content to load
         * @param $content     A direct content to assign, to bypass the loading phase
         *
         * @throws RootViewAlreadyDefinedException
         * @throws CouldNotLoadViewContentException
         */
        public function addRootContent($contentPath, $content = null) {
            if (isset($this->rootContent)) {
                throw new RootViewAlreadyDefinedException(Sugar::childClass());
            }

            if ($content === null) {
                if (file_exists($contentPath) === false) {
                    throw new CouldNotLoadViewContentException(Sugar::childClass(), $contentName, $contentPath);
                }
                $content = file_get_contents($contentPath);
                if ($content === false) {
                    throw new CouldNotLoadViewContentException(Sugar::childClass(), $contentName, $contentPath);
                }
            }
            $this->rootContent = $content;
        }

        /**
         * Render the view, applying all the process of the view with assigned contents and outputting the resulting view
         */
        public function renderView() {
            $this->formView();

            $this->assignValues();

            $this->validateView();

            echo $this->rootContent;
        }

        /**
         * Checks if any SugarTemplateIndicator has been left unrendered
         */
        // TODO Rework this with a REGEX to catch XX_____XX patterns instead of XX patterns
        private function validateView() {
            /*
            foreach ($this->sugarTemplateIndicators as $indicator) {
                if (strpos($this->viewContent, $indicator) !== false) {
                    throw new ViewContentNotRenderedException(Sugar::childClass());
                }
            }
            */
        }

        /**
         * Sets the template indicators
         *
         * @param $type          Type of the value to which we assign a template indicator (should be in ['value', 'include'])
         * @param $indicatorType Type of the template indicator (should be in ['left', 'right', 'include'])
         *
         * @throws SugarExecutionException
         */
        private function setIndicator($type, $indicatorType) {
            $type = strtolower($type);
            $indicatorType = strtolower($indicatorType);
            $func = 'get' . ucfirst($type) . ucfirst($indicatorType) . 'Indicator';
            $indicator = _SUGAR::getGlobalConfiguration()->$func();

            $arrayKey = $type . ucfirst($indicatorType);
            if (!array_key_exists($arrayKey, $this->sugarTemplateIndicators)) {
                throw new SugarExecutionException;
            }

            $this->sugarTemplateIndicators[$arrayKey] = $indicator !== null ?
                                                        $indicator :
                                                        $this->sugarTemplateIndicators[$arrayKey];
        }

        /**
         * Form the view, using all the assigned content
         */
        private function formView() {
            $this->setIndicator('include', 'left');
            $this->setIndicator('include', 'right');
            $this->setIndicator('include', 'separator');

            $leftIndicator = $this->sugarTemplateIndicators['includeLeft'] . $this->sugarTemplateIndicators['includeSeparator'];
            $rightIndicator =  $this->sugarTemplateIndicators['includeSeparator'] . $this->sugarTemplateIndicators['includeRight'];

            $change = null;
            // TODO Would be really nice to find a more performance friendly method to form the view
            while ($change !== false) {
                $change = false;
                foreach ($this->partialContents as $tag => $content) {
                    $count = 0;
                    $this->rootContent = str_replace($leftIndicator . $tag . $rightIndicator, $content, $this->rootContent, $count);
                    if ($count !== 0) {
                        $change = true;
                    }
                }
            }
        }

        /**
         * Replace assigned variables in the view content
         */
        private function assignValues() {
            $this->setIndicator('value', 'left');
            $this->setIndicator('value', 'right');
            $this->setIndicator('value', 'separator');

            $leftIndicator = $this->sugarTemplateIndicators['valueLeft'] . $this->sugarTemplateIndicators['valueSeparator'];
            $rightIndicator =  $this->sugarTemplateIndicators['valueSeparator'] . $this->sugarTemplateIndicators['valueRight'];

            foreach ($this->assignedValues as $tag => $value) {
                $this->rootContent = str_replace($leftIndicator . $tag . $rightIndicator, $value, $this->rootContent);
            }
        }

        /**
         * Assigns a value, checking if it already exists
         *
         * @param $value         Value to assign
         * @param $viewValueName Tag/Key of the assigned value
         *
         * @throws ValueAlreadyDefinedException
         */
        public function assignValue($value, $viewValueName) {
            if (isset($this->assignedValues[$viewValueName])) {
                throw new ValueAlreadyDefinedException(Sugar::childClass(), $viewValueName);
            }

            if (is_object($value)) {
                $value = (string) $value;
            }

            $this->assignedValues[$viewValueName] = $value;
        }

    }

?>