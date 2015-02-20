<?php

    include 'exceptions/views/RootViewAlreadyDefinedException.class.php';
    include 'exceptions/views/ViewAlreadyDefinedException.class.php';
    include 'exceptions/views/CouldNotLoadViewContentException.class.php';
    include 'exceptions/views/ViewContentNotRenderedException.class.php';
    include 'exceptions/views/ValueAlreadyDefinedException.class.php';

    class SugarView {

        /* string */
        private $viewContent;

        /* array */
        private $viewsContent = [];

        /* array */
        private $viewValues = [];

        /* array */
        private $sugarTemplateIndicators = [
            'includeLeft' => '$$',
            'includeRight' => '$$',
            'valueLeft' => '{{',
            'valueRight' => '}}'
        ];

        /* string */
        private $includeIndicatorSeparator = '';
        private $valueIndicatorSeparator = '';

        /**
         * @param viewContentPath
         * @param $viewContentName
         * @throws CouldNotLoadViewContentException
         * @throws ViewAlreadyDefinedException
         */
        public function addView($viewContentPath, $viewContentName = null) {
            if (file_exists($viewContentPath) === false) {
                throw new CouldNotLoadViewContentException(Sugar::childClass(), $viewContentName, $viewContentPath);
            }
            $viewContent = file_get_contents($viewContentPath);
            if ($viewContent === false) {
                throw new CouldNotLoadViewContentException(Sugar::childClass(), $viewContentName, $viewContentPath);
            }

            if ($viewContentName === null) {
                $this->addRootView($viewContentPath, $viewContent);
            } else {
                if (isset($this->views[$viewContentName])) {
                    throw new ViewAlreadyDefinedException(Sugar::childClass(), $viewContentName);
                }
                $this->views[$viewContentName] = $viewContent;
            }
        }

        public function addRootView($viewContentPath, $viewContent = null) {
            if (isset($this->rootView)) {
                throw new RootViewAlreadyDefinedException(Sugar::childClass());
            }

            if ($viewContent === null) {
                if (file_exists($viewContentPath) === false) {
                    throw new CouldNotLoadViewContentException(Sugar::childClass(), $viewContentName, $viewContentPath);
                }
                $viewContent = file_get_contents($viewContentPath);
                if ($viewContent === false) {
                    throw new CouldNotLoadViewContentException(Sugar::childClass(), $viewContentName, $viewContentPath);
                }
            }
            $this->viewContent = $viewContent;
        }

        public function renderView() {
            $this->formView();
            $this->assignValues();

            $this->validateView();

            echo $this->viewContent;
        }

        /**
         * Checks if any SugarTemplateIndicator has been left unrendered
         */
        // TODO Rework this with a REGEX to catch XX_____XX patterns instead of XX patterns
        private function validateView() {
            foreach ($this->sugarTemplateIndicators as $indicator) {
                if (strpos($this->viewContent, $indicator) !== false) {
                    throw new ViewContentNotRenderedException(Sugar::childClass());
                }
            }
        }

        private function formView() {
            $leftIndicatorFromConfig = _SUGAR::getGlobalConfiguration()->getIncludeLeftIndicator();
            $rightIndicatorFromConfig = _SUGAR::getGlobalConfiguration()->getIncludeRightIndicator();
            $indicatorSeparatorFromConfig = _SUGAR::getGlobalConfiguration()->getIncludeIndicatorSeparator();

            $this->sugarTemplateIndicators['includeLeft'] = $leftIndicatorFromConfig !== null ? $leftIndicatorFromConfig : $this->sugarTemplateIndicators['includeLeft'];
            $this->sugarTemplateIndicators['includeRight'] = $rightIndicatorFromConfig !== null ? $rightIndicatorFromConfig : $this->sugarTemplateIndicators['includeRight'];
            $this->includeIndicatorSeparator = $indicatorSeparatorFromConfig !== null ? $indicatorSeparatorFromConfig : $this->includeIndicatorSeparator;

            $leftIndicator = $this->sugarTemplateIndicators['includeLeft'] . $this->includeIndicatorSeparator;
            $rightIndicator =  $this->includeIndicatorSeparator . $this->sugarTemplateIndicators['includeRight'];

            $change = null;
            while ($change !== false) {
                $change = false;
                foreach ($this->views as $tag => $content) {
                    $count = 0;
                    $this->viewContent = str_replace($leftIndicator . $tag . $rightIndicator, $content, $this->viewContent, $count);
                    if ($count !== 0) {
                        $change = true;
                    }
                }
            }
        }

        private function assignValues() {
            $leftIndicatorFromConfig = _SUGAR::getGlobalConfiguration()->getValueLeftIndicator();
            $rightIndicatorFromConfig = _SUGAR::getGlobalConfiguration()->getValueRightIndicator();
            $indicatorSeparatorFromConfig = _SUGAR::getGlobalConfiguration()->getValueIndicatorSeparator();

            $this->sugarTemplateIndicators['valueLeft'] = $leftIndicatorFromConfig !== null ? $leftIndicatorFromConfig : $this->sugarTemplateIndicators['valueLeft'];
            $this->sugarTemplateIndicators['valueRight'] = $rightIndicatorFromConfig !== null ? $rightIndicatorFromConfig : $this->sugarTemplateIndicators['valueRight'];
            $this->valueIndicatorSeparator = $indicatorSeparatorFromConfig !== null ? $indicatorSeparatorFromConfig : $this->valueIndicatorSeparator;

            $leftIndicator = $this->sugarTemplateIndicators['valueLeft'] . $this->valueIndicatorSeparator;
            $rightIndicator =  $this->valueIndicatorSeparator . $this->sugarTemplateIndicators['valueRight'];

            foreach ($this->viewValues as $tag => $value) {
                $this->viewContent = str_replace($leftIndicator . $tag . $rightIndicator, $value, $this->viewContent);
            }
        }

        public function assignValue($value, $viewValueName) {
            if (isset($this->viewValues[$viewValueName])) {
                throw new ValueAlreadyDefinedException(Sugar::childClass(), $viewValueName);
            }

            $this->viewValues[$viewValueName] = $value;
        }

    }

?>