<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(-1);

    $ini = parse_ini_file('sugarConfig.ini');
    set_include_path(get_include_path() . PATH_SEPARATOR . $ini['SugarPath']);
    include 'InitSugarFW.php';

    $globalConfiguration = new SugarConfiguration;
    $logger = new SugarLogs;
    _SUGAR::setGlobalConfiguration($globalConfiguration);
    _SUGAR::setLogger($logger);

    // http://stackoverflow.com/a/10298907/2627459
    $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $sugarRouter = new SugarRouter($url);

    try {
        $sugarRouter->route();
    } catch (Exception $e) {
        die ($e->getMessage()); // Debug at the moment
    }

?>