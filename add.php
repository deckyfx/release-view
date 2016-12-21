<?php
    ini_set("error_reporting", E_ALL);
    date_default_timezone_set('Asia/Jakarta');

    require_once __DIR__ . '/vendor/autoload.php';
    
    // setup Propel
    require_once __DIR__ . '/generated-conf/config.php';

    use Models\Config;
    use Models\Build;
    use Models\BuildQuery;

    $build = new Build();
	$build->setName($_POST['build-name']);
	$build->setVersion($_POST['build-version']);
	$build->setUrl($_POST['build-url']);
	$build->setNote($_POST['build-note']);
	$build->save();

    print_r(json_encode($_POST));
?>