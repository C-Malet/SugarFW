<?php

    include 'helpers/SugarGlobals.php'; // Special case

    define ('CONTROLLER', 'Controller');

    function __autoload ($className) {

        // Sugar classes autoload
        if (strpos($className, 'Sugar') === 0) {
            $sugarDirs = [
                'controllers',
                'functions',
                'helpers',
                'routers',
                'traits',
                'views'
            ];

            foreach ($sugarDirs as $sugarDir) {
                if (file_exists(stream_resolve_include_path($sugarDir . DIRECTORY_SEPARATOR . $className . '.php'))) {
                    include $sugarDir . DIRECTORY_SEPARATOR . $className . '.php';
                    return true;
                }
            }
        }

        // Sugar exceptions autoload
        if (strpos($className, 'SugarException') !== false) {
            $sugarExceptionDirs = [
                'controllers',
                'actions',
                'modules',
                'views'
            ];

            foreach ($sugarExceptionDirs as $sugarExceptionDir) {
                if (file_exists(stream_resolve_include_path('exceptions' . DIRECTORY_SEPARATOR . $sugarExceptionDir . DIRECTORY_SEPARATOR . $className . '.php'))) {
                    include 'exceptions' . DIRECTORY_SEPARATOR . $sugarExceptionDir . DIRECTORY_SEPARATOR . $className . '.php';
                    return true;
                }
            }
        }

        $sugarComponentsMap = [
            '_controllers' => 'Controller'
        ];

        foreach ($sugarComponentsMap as $componentDir => $sugarComponent) {
            if (strpos($className, $sugarComponent) !== false) {
                include $componentDir . DIRECTORY_SEPARATOR . $className . '.php';
                return true;
            }
        }

        return false;

    }

?>