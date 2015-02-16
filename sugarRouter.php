<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(-1);

    $ini = parse_ini_file('sugarConfig.ini');

    set_include_path(get_include_path() . PATH_SEPARATOR . $ini['SugarPath']);

    include 'functions/SugarFunctions.class.php';
    include 'routers/SugarRouter.class.php';

    // http://stackoverflow.com/a/10298907/2627459
    $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    include 'helpers/SugarGlobals.class.php';
    include 'helpers/SugarConfiguration.class.php';
    $globalConfiguration = new SugarConfiguration;
    _SUGAR::setGlobalConfiguration($globalConfiguration);

    $sugarRouter = new SugarRouter($url);

    try {
        $sugarRouter->route();
        $sugarRouter->launchController();
    } catch (Exception $e) {
        die ($e->getMessage());
    }

?>