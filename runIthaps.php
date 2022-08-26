<?php
//$pluginDir = 'wpplugins/7_25/';
//$pluginDir = dirname(__FILE__) . "\..\wamp\www\wordpress\wp-content\plugins\\";
//$pluginDir = 'THAPS-master/scanner/Test/';
//$pluginDir = 'hmsp/';
//$pluginDir = 'mybb_1.8.14/';
//$pluginDir = 'wityCMS-master/';
//$pluginDir = 'DVWA-master/';
$options = getopt("fl:tw:cr:im:s",array("fulltree","loop:","showtree","watch:","cleangetpost","requestid:", "ignoreflow","module:","silent"));
        
if (($argc == 1 || (!is_file($file = $argv[$argc - 1]) && !is_dir($dir = $argv[$argc - 1])))) {
    die("Remember the filename or directory!");
}

if(is_file($file)){
    $files[] = realpath($file);
}else{
    $files = getDirContents($dir);
    $totalFiles = count($files);
}

$pluginDir = 'pp088/';
$logPath = "logs/";

function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);
    $_files = sortFiles($files, $dir);
    foreach ($_files as $key => $value) {
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

function sortFiles($files, $dir){
    $tmpFiles = array();
    foreach($files as &$value){
        $value = strtolower($value);
        $tmpFiles[] = $value;
    }
    sort($files);
    sort($tmpFiles);
    $folders = array();
    $files_ = array();
    foreach($tmpFiles as $ind => &$value){
        $value = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if(is_file($value)){
            array_push($files_, $files[$ind]);
        }elseif($value != "." && $value != ".."){
            array_push($folders, $files[$ind]);
        }
    }
    $indexPHPLocation = array_search("index.php", $files_);
    if($indexPHPLocation !== false){
        $indexPath = $files_[$indexPHPLocation];
        unset($files_[$indexPHPLocation]);
        array_unshift($files_, $indexPath);
    }
    $files = array_merge($files_, $folders);
    return $files;
}

//var_dump($files = getDirContents($pluginDir));
//$files = getDirContents($pluginDir);

$total = count($files);
$current = 0;
foreach ($files as $v_file) {
    $current++;
    $index = strrpos($v_file, str_replace("/", "\\", $pluginDir), 0);
    //echo (string)$index . "\n";
    $logName = str_replace("\\", "_", substr(pathinfo($v_file, PATHINFO_DIRNAME),$index + strlen($pluginDir))) . "_" . pathinfo($v_file, PATHINFO_BASENAME) . ".log";
    //echo $logName ."\n";
    echo "Processing file: " . realpath($v_file) . "\n";
    echo "----------------------------------------------------------------------------------\n";
    fwrite(STDERR, "Processing file: " . realpath($v_file) . " Progress: " . $current . "/" . $total . "\n");
    //system("php Main.php -m wordpress ". $v_file . " > " . $logPath . $logName);
    //system("php Main.php -m wordpress ". $v_file);
    //echo $v_file;
    //exit;
    $v_file = str_replace("\\", "/", str_replace(getcwd() . "\\", "", $v_file));

    //system("E:/wamp/bin/php/php5.6.31/php.exe THAPS-master/scanner/Main.php ". $v_file . " > E:/Thesis/thaps_results/" . $logName);
    //system("E:/wamp/bin/php/php5.6.31/php.exe I-THAPS/scanner/Main.php -m wordpress ". $v_file);
    system("php scanner/Main_vs.php -q ". $v_file);
    echo "Done file: " . realpath($v_file) . "\n";
    //system("php THAPS-master/scanner/Main.php ". $v_file);
    //echo "Processing " . $logName . " - " . $current . "/" . $total . "\r";
}