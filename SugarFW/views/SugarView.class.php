<?php

    class SugarView {

        private $rootView;
        private $views = [];

        public function addView($viewPath, $viewName = null) {
            $viewContent = file_get_contents($viewPath);
            if ($viewName === null) {
                if (isset($rootView)) {
                    die ('Root view already defined');
                }
                $this->rootView = $viewContent;
            } else {
                $this->views[$viewName] = $viewContent;
            }
        }

        public function renderView() {
            $this->formView();

            echo $this->rootView;
        }

        private function formView() {
            $change = null;
            while ($change !== false) {
                $change = false;
                foreach ($this->views as $tag => $content) {
                    $count = 0;
                    $this->rootView = str_replace('<$V$>' . $tag . '<$V$>', $content, $this->rootView, $count);
                    if ($count !== 0) {
                        $change = true;
                    }
                }
            }
        }

    }

?>