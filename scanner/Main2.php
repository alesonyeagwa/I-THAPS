#!/usr/bin/php
<?php
error_reporting(0);

$isDirScan = false;
$silent = false;

require_once __DIR__ . '/vendor/autoload.php';

define("THAPS_DIR",__DIR__."/");
define("THAPS_LIB_DIR",THAPS_DIR."Lib/");
define("THAPS_MODEL_DIR",THAPS_DIR."Models/");
define("THAPS_MODEL_EXT",".tpm");

require THAPS_LIB_DIR."Constants.php";
require THAPS_DIR."PHP-Parser/lib/bootstrap.php";
require THAPS_LIB_DIR . "PHPExtBridge.php";

require THAPS_LIB_DIR."VulnerabilitySourcesAndSinks.php";
require THAPS_LIB_DIR."VariableStorage.php";
require THAPS_LIB_DIR."VulnerabilityStorage.php";
require THAPS_LIB_DIR."VulnerabilityDescription.php";
require THAPS_LIB_DIR."VulnerabilityScanner.php";

require THAPS_LIB_DIR . "SpecificTraversers.php";
require THAPS_LIB_DIR."IncludeResolver.php";
require THAPS_LIB_DIR."FunctionResolver.php";
require THAPS_LIB_DIR . "ClassResolver.php";



$parser = new PHPParser_Parser;
$nodeDumper = new PHPParser_NodeDumper;
$prettyPrinter = new PHPParser_PrettyPrinter_Zend;

try {
    if (!isset($_GET["file"])) {

        $options = getopt("fl:tw:cr:im:s",array("fulltree","loop:","showtree","watch:","cleangetpost","requestid:", "ignoreflow","module:","silent"));
        
        if ((!isset($options["r"]) && !isset($options["requestid"])) && ($argc == 1 || (!is_file($file = $argv[$argc - 1]) && !is_dir($dir = $argv[$argc - 1])))) {
            die("Remember the filename or directory!");
        }
    } else {
        $file = $_GET["file"];
    }

    if (isset($options["r"]) || isset($options["requestid"])) {
        $requestId = isset($options["r"])?$options["r"]:$options["requestid"];
        //PHPExt::setCustomField("test", "includeTest");
        PHPExt::setRequestId($requestId);
        if (PHPExt::exists()) {
            $includeVisitor->useRequest();
            $includeVisitor->addDynamicIncludeResolve( PHPExt::getIncludes() );
            $file = PHPExt::getFilename();
            PHPExt::disconnect();
        } else {
            PHPExt::disconnect();
            die("Request Id does not exist\n");
        }
    }

    if(isset($options["s"]) || isset($options["silent"])){
        $silent = true;
    }

    $files = array();
    
    if(is_file($file)){
        $files[] = realpath($file);
    }else{
        $files = getDirContents($dir);
        $totalFiles = count($files);
    }
    //die(var_dump($files));
    // if (substr($file,0,1) != "/") {
    //     $file = realpath(getcwd()."/".$file);
    // }

    //echo(getcwd());
    //die($file);

    // $stmts = $parser->parse(new PHPParser_LexerFile($file));

    $modelScope = null;
    if (isset($options["m"]) || isset($options["module"])) {
        $modelName = isset($options["m"])?$options["m"]:$options["module"];

        if (!file_exists(THAPS_MODEL_DIR.$modelName.THAPS_MODEL_EXT)) {
            die(("Model not found"));
        }

        $model = json_decode(file_get_contents(THAPS_MODEL_DIR.$modelName.THAPS_MODEL_EXT),true);

        $includeVisitor->useModel();
        if (isset($model["pluginFolder"]) && strlen($model["pluginFolder"]) > 0) {

            $fileDir = dirname($file);
            $index = strrpos(str_replace("\\", "/",$fileDir), $model["pluginFolder"]);
            //die(str_replace("\\", "/",$fileDir));
            //die((string) $index);
            $moduleDir = substr($fileDir,0,$index+strlen($model["pluginFolder"]));
            //die($moduleDir);
            $includeVisitor->setSystemPath($moduleDir);
        }
        if (isset($model["model"])) {
            $include = array(new PHPParser_Node_Expr_Include(new PHPParser_Node_Scalar_String(THAPS_MODEL_DIR.$model["model"]),PHPParser_Node_Expr_Include::TYPE_INCLUDE));
            $modelScope = new VariableStorage();
            $bodyVisitor->setVScope($modelScope);
            $bodyVisitor->setVulnerabilityStorage(new VulnerabilityStorage);
            $include = $includeTraverser->traverse($include);
            $include = $classTraverser->traverse($include);
            $include = $functionTraverser->traverse($include);
            $include = $bodyTraverser->traverse($include);
        }
        if (isset($model["constants"])) {
            if ($modelScope === null) {
                $modelScope = new VariableStorage();
            }
            foreach ($model["constants"] as $constant => $value) {
                if (substr($value,0,1) == "!") {

                    switch (substr($value,1,1)) {
                        case "D":
                            $fileDir = dirname($file);
                            $value = substr($fileDir,0,strrpos($fileDir, substr($value,2))+strlen(substr($value,2)));
                            break;
                    }
                }
                $val = new VariableValue();
                $val->value = $value;
                $modelScope->setVariableValue($val,VAR_REP_CONST.$constant);
            }
        }
        if ($modelScope !== null) {
            $includeVisitor->setVScope(clone $modelScope);
            $functionVisitior->setVScope(clone $modelScope);
            $classVisitor->setVScope(clone $modelScope);
        }
    }
    if (isset($options["c"]) || isset($options["cleangetpost"])) {
        BodyVisitor::useCleanGetPost(true,true);
    }
    if (isset($options["i"]) || isset($options["ignoreflow"])) {
        VariableStorage::ignoreFlow();
    }
    if (isset($options["l"]) || isset($options["loop"])) {
        $bodyVisitor->setLoopExpandTimes(isset($options["l"])? $options["l"]:$options["loop"]);
    }

    $start = microtime(true);

    foreach ($files as $file) {

        echo "Processing file: " . realpath($file) . "\n";

        $globalVulnerabilities = new VulnerabilityStorage();
        $functions = array();
        $classVisitor = new ClassVisitor();
        $classTraverser->addVisitor($classVisitor);

        if ($modelScope !== null) {
            $includeVisitor->setVScope(clone $modelScope);
            $functionVisitior->setVScope(clone $modelScope);
            $classVisitor->setVScope(clone $modelScope);
        }

        $stmts = $parser->parse(new PHPParser_LexerFile($file));
        //echo(var_dump($stmts));
        $stmts = $includeTraverser->traverse($stmts);
        //echo(var_dump($stmts));
        silentEcho("Includes done\n");

        if (isset($model) && isset($model["functionCallWrappers"])) {
            $modelVisitor = new ModelVisitor();
            $modelVisitor->setFunctionCallWrappers($model["functionCallWrappers"]);
            $modelTraverser = new PHPParser_NodeTraverser();
            $modelTraverser->addVisitor($modelVisitor);
            $stmts = $modelTraverser->traverse($stmts);
        }

        if (isset($options["t"]) || isset($options["showtree"])) {
            die($nodeDumper->dump($stmts));
        }

        $stmts = $classTraverser->traverse($stmts);

        silentEcho("Classes done\n");

        $stmts = $functionTraverser->traverse($stmts);

        silentEcho("Functions done\n");


        if (isset($options["f"]) || isset($options["fulltree"])) {
            silentEcho("Full tree in use!\n");
            BodyVisitor::useFullTree(true);
        }




        if ($modelScope !== null) {
            $variableStorage = $modelScope;
        } else {
            $variableStorage = new VariableStorage;
        }

        if (isset($options["w"]) || isset($options["watch"])) {
            if (isset($options["w"])) {
                $varString = $options["w"];

            } else {
                $varString = $options["watch"];
            }

            $vars = explode(",",$varString);
            foreach ($vars as $var) {
                $variableStorage->addWatch($var);
            }
        }


        if (isset($model) && isset($model["callAllClassMembers"])) {
            $tokens = explode(" ",$model["callAllClassMembers"]);

            $callClasses = array();

            if ($tokens[0] == "*") {
                $callClasses = ClassStorage::getClasses();
            }
            elseif ($tokens[0] == "extends") {
                $classes = ClassStorage::getClasses();
                if (count($classes) > 0) {
                    foreach (ClassStorage::getClasses() as $class) {
                        if ($class->extends == $tokens[1]) {
                            $callClasses[] = $class;
                        }
                    }
                }
            }

            if (count($callClasses) > 0) {
                $i = 0;
                $code = '<?php '."\n";
                foreach ($callClasses as $class) {

                    $code .= '$THAPS_'.$i.' = new '.$class->name.";\n";
                    foreach ($class->getMethods() as $method) {
                        if ($method->name == "main") { // Midlertidigt hack til typo3
                            $code .= 'echo $THAPS_'.$i.'->'.$method->name."();\n";
                        } else {
                            $code .= '$THAPS_'.$i.'->'.$method->name."();\n";
                        }

                    }
                    $i++;
                }

                $stmts = array_merge($stmts,$parser->parse(new PHPParser_Lexer($code)));

            }
        }


        $bodyVisitor->setVScope($variableStorage);
        $bodyVisitor->setVulnerabilityStorage($globalVulnerabilities);
        silentEcho("Preprocessing done\n");

        $nodeCounter = new NodeCounter();
        $nodeCTraverser = new PHPParser_NodeTraverser();
        $nodeCTraverser->addVisitor($nodeCounter);

        $totalNodeCount = $nodeCTraverser->traverse($stmts);
        $stepSize = ceil($totalNodeCount / 100);

        $bodyTraverser->traverse($stmts);
        $vulnerabilities = $bodyVisitor->getVulnerabilities()->get();

        $timeUsed = microtime(true)-$start;
        //echo $nodeDumper->dump($stmts);

        // Lets group the vulnerabilities
        $groupedVulns = array();
        $dubVulns = array();
        foreach ($vulnerabilities as $nr => $vulnerability) {
            if ($vulnerability->return) {
                unset($vulnerabilities[$nr]);
                continue;
            }

            if(in_array($vulnerability, $dubVulns)){
                continue;
            }
            $dubVulns[] = $vulnerability;

            if (!array_key_exists($vulnerability->description,$groupedVulns)) {
                $groupedVuln = array();
                $groupedVuln["description"] = $vulnerability->description;
                $groupedVuln["sql"] = $vulnerability->sql;
                $groupedVuln["xss"] = $vulnerability->xss;
                $groupedVuln["fi"] = $vulnerability->fi;
                $groupedVulns[$vulnerability->description] = $groupedVuln;
            }
            $newFlowDep = array();
            $newFlowDep["dependencies"] = array_unique($vulnerability->dependencies);
            $newFlowDep["flowpath"] = array_unique($vulnerability->flowpath);
            $groupedVulns[$vulnerability->description]["flowanddependencies"][] = $newFlowDep;

        }
        // Lets write em out
        echo "----------------------------------\n";

        if (isset($options["r"]) || isset($options["requestid"])) {
            $requestId = isset($options["r"])?$options["r"]:$options["requestid"];
            //PHPExt::setCustomField("test", "includeTest");
            PHPExt::setRequestId($requestId);
            if (PHPExt::exists()) {
                $vulnerabilities = array();
                foreach ($groupedVulns as $type => $vulnerability) {
                    $vulnerabilities[] = array(
                        "type" => $type,
                        "descriptions" => $vulnerability["flowanddependencies"]
                    );
                }
                PHPExt::setVulnerabilities($vulnerabilities);
            }
        } else {
            foreach ($groupedVulns as $nr => $vulnerability) {
                vulnerabiltyPrinter($vulnerability);
            }
        }
        echo "Vulnerabilities: ".count($groupedVulns)."\n";
        echo "Seconds: ".$timeUsed."\n";
        echo "--------------------------------------------------------------------\n\n\n\n\n";
    }
    

} catch (PHPParser_Error $e) {
    echo 'Parse Error: ',$e->getMessage();
}


function vulnerabiltyPrinter($vuln) {
    echo $vuln["description"]."\n\n";
    foreach ($vuln["flowanddependencies"] as $flowdeppath) {
        echo "FLOWPATH:\n";
        foreach($flowdeppath["flowpath"] as $flow) {
            echo $flow."\n";
        }

        echo "\nDEPENDENCIES:\n";
        foreach($flowdeppath["dependencies"] as $dependency) {
            echo $dependency."\n";
        }
        echo "\n";
    }

    echo "-----------------------------------\n";
}

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

function silentEcho($str){
    global $silent;
    if(!$silent){
        echo $str;
    }
}

function resetScanner(){
    global $functions;
    $functions = array();;
}

?>
