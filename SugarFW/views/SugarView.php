<?php

    include_once 'exceptions/views/RootViewAlreadyDefinedException.class.php';
    include_once 'exceptions/views/ViewAlreadyDefinedException.class.php';
    include_once 'exceptions/views/CouldNotLoadViewContentException.class.php';
    include_once 'exceptions/views/ViewContentNotRenderedException.class.php';
    include_once 'exceptions/views/ValueAlreadyDefinedException.class.php';
    include_once 'exceptions/views/ViewRootContentNotSetException.class.php';
    include_once 'exceptions/views/SubViewAlreadyDefinedException.class.php';
    include_once 'exceptions/views/ViewAlreadyFormedException.class.php';
    include_once 'exceptions/views/SubViewNotDefinedException.class.php';

    include_once 'exceptions/SugarExecutionException.class.php';

    include_once 'traits/StaticHelpers.class.php';

    class SugarView {

        use StaticHelpers;

        /* @var SugarLayout */
        private $layout = null;

        /* @var string */
        private $rootContent;

        /* @var array */
        private $partialContents = [];

        /* @var array */
        private $assignedValues = [];

        /* @var array */
        private $subViews = [];

        /* @var boolean */
        private $formed = false;

        /* @var boolean */
        private $rendered = false;

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
            'valueSeparator'   => '',
            'viewLeft'         => '[[',
            'viewRight'        => ']]',
            'viewSeparator'    => ''
        ];

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
                throw new CouldNotLoadViewContentException(self::childClass(), $contentName, $contentPath);
            }

            $content = $this->fileGetContentsExecPHP($contentPath);

            if ($content === false) {
                throw new CouldNotLoadViewContentException(self::childClass(), $contentName, $contentPath);
            }

            if ($contentName === null) {
                $this->addRootContent($contentPath, $content);
            } else {
                if (isset($this->partialContents[$contentName])) {
                    throw new ViewAlreadyDefinedException(self::childClass(), $contentName);
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
                throw new RootViewAlreadyDefinedException(self::childClass());
            }

            if ($content === null) {
                if (file_exists($contentPath) === false) {
                    throw new CouldNotLoadViewContentException(self::childClass(), 'rootContent', $contentPath);
                }

                $content = $this->fileGetContentsExecPHP($contentPath);

                if ($content === false) {
                    throw new CouldNotLoadViewContentException(self::childClass(), 'rootContent', $contentPath);
                }
            }
            $this->rootContent = $content;
        }

        /**
         * Render the view, applying all the process of the view with assigned contents and outputting the resulting view
         *
         * @param $isSubView boolean Wheter or not this is a subview of a main view
         *
         * @return string
         */
        public function renderView($isSubView = false) {
            $this->formView($isSubView);

            $this->assignValues();

            $this->validateView();

            if ($isSubView === false) {
                echo $this->rootContent;
            } else {
                return $this->rootContent;
            }
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
         * Sets all the indicators at once for a type
         *
         * @param $type Type of value of the indicators to set
         */
        private function setIndicators($type) {
            $this->setIndicator($type, 'left');
            $this->setIndicator($type, 'right');
            $this->setIndicator($type, 'separator');
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
         *
         * @param $reform Wheter or not should we try to reform the view
         *
         * @throws ViewAlreadyFormException
         */
        private function formView($reform = false) {
            if ($this->formed === true && $reform !== true) {
                throw new ViewAlreadyFormedException(self::childClass());
            }

            if ($this->rootContent === null) {
                throw new ViewRootContentNotSetException(self::childClass());
            }

            // Forms subviews and includes until there is nothing left to form
            // from successive inclusions
            while ($this->formSubViews() !== 0 || $this->formIncludes() !== 0);

            $this->formed = true;
        }

        /**
         * Forms includes loaded
         *
         * @return integer
         */
        private function formIncludes() {
            $totalChanges = 0;

            $this->setIndicators('include');

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
                        ++$totalChanges;
                        $change = true;
                    }
                }
            }

            return $totalChanges;
        }

        /**
         * Forms subviews loaded
         *
         * @return integer
         */
        private function formSubViews() {
            $totalChanges = 0;

            if (!empty($this->subViews)) {
                $this->setIndicators('view');

                $leftIndicator = $this->sugarTemplateIndicators['viewLeft'] . $this->sugarTemplateIndicators['viewSeparator'];
                $rightIndicator =  $this->sugarTemplateIndicators['viewSeparator'] . $this->sugarTemplateIndicators['viewRight'];

                foreach ($this->subViews as $tag => $view) {
                    $count = 0;
                    $content = $view->renderView(true);
                    $this->rootContent = str_replace($leftIndicator . $tag . $rightIndicator, $content, $this->rootContent, $count);
                    if ($count !== 0) {
                        ++$totalChanges;
                    }
                }
            }

            return $totalChanges;
        }

        /**
         * Replace assigned variables in the view content
         */
        private function assignValues() {
            $this->setIndicators('value');

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
                throw new ValueAlreadyDefinedException(self::childClass(), $viewValueName);
            }

            if (is_object($value)) {
                $value = (string) $value;
            }

            $this->assignedValues[$viewValueName] = $value;
        }

        /**
         * Merge the given view into the current one
         *
         * @param $view View to merge within the current one
         * @param $tag  Replacement tag for the given view
         *
         * @throws SubViewAlreadyDefinedException
         */
        public function mergeView(SugarView $view, $tag) {
            if (isset($this->subViews[$tag])) {
                throw new SubViewAlreadyDefinedException(self::childClass(), $view::childClass(), $tag);
            }
            $this->subViews[$tag] = $view;
        }

        /**
         * Remove a given subView, given its tag or the View object itself
         *
         * @param $view The SugarView object or view tag to remove from the subviews
         *
         * @throws SubViewNotDefinedException
         */
        public function removeView($view) {
            if (is_string($view)) {
                if (!isset($this->subViews[$view])) {
                    throw new SubViewNotDefinedException(self::childClass(), $view);
                }

                unset($this->subViews[$view]);
            } else if (is_object($view) && $view instanceof SugarView) {
                $keysToRemove = array_keys($this->subViews, $view, true);

                if (empty($keysToRemove)) {
                    throw new SubViewNotDefinedException(self::childClass(), $view);
                }

                foreach ($keysToRemove as $key) {
                    unset($this->subViews[$key]);
                }
            } else {
                throw new InvalidArgumentException('Invalid argument for function SugarView::removeView, expected SugarView or string.');
            }
        }

        /**
         * Loads a file containing some PHP content,
         * execute the PHP within the output_buffer
         * and return the resulting content.
         *
         * @param $filePath path of the file to retrieve
         *
         * @return string
         *
         * @see http://stackoverflow.com/a/8751222/2627459
         */
        public function fileGetContentsExecPHP($filePath) {
            ob_start();
            include $filePath;
            return ob_get_clean();
        }

    }

?>