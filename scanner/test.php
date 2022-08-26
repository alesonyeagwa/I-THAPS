<?php
$pluginDir = 'wordpress/wp-content/plugins/';
$logPath = "logs/";

function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if($ext == "php"){
                $results[] = $path;
            }
        } else if ($value != "." && $value != "..") {
            getDirContents($path, $results);
            //$results[] = $path;
        }
    }

    return $results;
}

//var_dump($files = getDirContents($pluginDir));
$files = getDirContents($pluginDir);
foreach ($files as $v_file) {
    $index = strrpos($v_file, str_replace("/", "\\", $pluginDir), 0);
    //echo (string)$index . "\n";
    $logName = str_replace("\\", "_", substr(pathinfo($v_file, PATHINFO_DIRNAME),$index + strlen($pluginDir))) . "_" . pathinfo($v_file, PATHINFO_BASENAME) . ".log";
    echo $logName ."\n";
    echo "----------------------------------------------------------------------------------\n";
    //system("php Main.php -m wordpress ". $v_file . " > " . $logPath . $logName);
    //system("php Main.php -m wordpress ". $v_file);
    system("php Main.php ". $v_file);
    echo "\n\n";
}