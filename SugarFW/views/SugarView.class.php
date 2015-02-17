<?php

    include 'exceptions/views/RootViewAlreadyDefinedException.class.php';
    include 'exceptions/views/ViewAlreadyDefinedException.class.php';
    include 'exceptions/views/CouldNotLoadViewContentException.class.php';
    include 'exceptions/views/ViewContentNotRenderedException.class.php';

    include 'traits/ClassChild.class.php';

    class SugarView {

        use ClassChild;

        /* string */
        private $viewContent;

        /* array */
        private $viewsContent = [];

        /* array */
        private $sugarTemplateIndicators = [
            '<$V$>'
        ];

        public function addView($viewContentPath, $viewContentName = null) {
            if (file_exists($viewContentPath) === false) {
                throw new CouldNotLoadViewContentException(self::childClass(), $viewContentName, $viewContentPath);
            }
            $viewContent = file_get_contents($viewContentPath);
            if ($viewContent === false) {
                throw new CouldNotLoadViewContentException(self::childClass(), $viewContentName, $viewContentPath);
            }

            if ($viewContentName === null) {
                $this->addRootView($viewContentPath, $viewContent);
            } else {
                if (isset($this->views[$viewContentName])) {
                    throw new ViewAlreadyDefinedException(self::childClass(), $viewContentName);
                }
                $this->views[$viewContentName] = $viewContent;
            }
        }

        public function addRootView($viewContentPath, $viewContent = null) {
            if (isset($this->rootView)) {
                throw new RootViewAlreadyDefinedException(self::childClass());
            }

            if ($viewContent === null) {
                if (file_exists($viewContentPath) === false) {
                    throw new CouldNotLoadViewContentException(self::childClass(), $viewContentName, $viewContentPath);
                }
                $viewContent = file_get_contents($viewContentPath);
                if ($viewContent === false) {
                    throw new CouldNotLoadViewContentException(self::childClass(), $viewContentName, $viewContentPath);
                }
            }
            $this->viewContent = $viewContent;
        }

        public function renderView() {
            $this->formView();

            $this->validateView();

            echo $this->viewContent;
        }

        /**
         * Checks if any SugarTemplateIndicator has been left unrendered
         */
        private function validateView() {
            foreach ($this->sugarTemplateIndicators as $indicator) {
                if (strpos($this->viewContent, $indicator) !== false) {
                    throw new ViewContentNotRenderedException(self::childClass());
                }
            }
        }

        private function formView() {
            $change = null;
            while ($change !== false) {
                $change = false;
                foreach ($this->views as $tag => $content) {
                    $count = 0;
                    $this->viewContent = str_replace('<$V$>' . $tag . '<$V$>', $content, $this->viewContent, $count);
                    if ($count !== 0) {
                        $change = true;
                    }
                }
            }
        }

    }

?>