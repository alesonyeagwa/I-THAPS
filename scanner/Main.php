#!/usr/bin/php
<?php
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

define("THAPS_DIR",__DIR__."/");
define("THAPS_LIB_DIR",THAPS_DIR."Lib/");
define("THAPS_MODEL_DIR",THAPS_DIR."Models/");
define("THAPS_MODEL_EXT",".tpm");

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

require THAPS_LIB_DIR."Constants.php";
//require THAPS_DIR."PHP-Parser/lib/bootstrap.php";
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





try {
    if (!isset($_GET["file"])) {

        $options = getopt("fl:tw:cr:im:q",array("fulltree","loop:","showtree","watch:","cleangetpost","requestid:", "ignoreflow","module:","inqludes"));
        
        if ((!isset($options["r"]) && !isset($options["requestid"])) && ($argc == 1 || !is_file($file = $argv[$argc - 1]))) {
            die("Remember the filename!");
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

    if (substr($file,0,1) != "/") {
        $file = realpath(getcwd()."/".$file);
    }

    $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
    $nodeDumper = new PhpParser\NodeDumper;
    $prettyPrinter = new PhpParser\PrettyPrinter\Standard;

    $stmts = $parser->parse(file_get_contents($file));

    $fileNameTraverser = new PhpParser\NodeTraverser;
    $fileNameVisitor = new FileNameVisitor($file);
    $fileNameTraverser->addVisitor($fileNameVisitor);
    $stmts = $fileNameTraverser->traverse($stmts);


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

    if (isset($options["q"]) || isset($options["inqludes"])) {
        IncludeVisitor::$doIncludes = true;
    }

    $start = microtime(true);
    //echo(var_dump($stmts));
    
    $includeVisitor->setFileName($file);
    $stmts = $includeTraverser->traverse($stmts);
    //die(var_dump($stmts));
    echo "Includes done\n";

    if (isset($model) && isset($model["functionCallWrappers"])) {
        $modelVisitor = new ModelVisitor();
        $modelVisitor->setFunctionCallWrappers($model["functionCallWrappers"]);
        $modelTraverser = new PhpParser\NodeTraverser();
        $modelTraverser->addVisitor($modelVisitor);
        $stmts = $modelTraverser->traverse($stmts);
    }

    if (isset($options["t"]) || isset($options["showtree"])) {
        die($nodeDumper->dump($stmts));
    }

    $stmts = $classTraverser->traverse($stmts);
    echo "Classes done\n";

    if (isset($options["f"]) || isset($options["fulltree"])) {
        echo "Full tree in use!\n";
        BodyVisitor::useFullTree(true);
    }

    global $functionVisitior;
    $functionVisitior->setFileName($file);
    $stmts = $functionTraverser->traverse($stmts);
    echo "Functions done\n";


    




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

            $stmts = array_merge($stmts,$parser->parse($code));

        }
    }


    $bodyVisitor->setVScope($variableStorage);
    $bodyVisitor->setFileName($file);
    $bodyVisitor->setVulnerabilityStorage($globalVulnerabilities);
    echo "Preprocessing done\n";

    $nodeCounter = new NodeCounter();
    $nodeCTraverser = new PhpParser\NodeTraverser();
    $nodeCTraverser->addVisitor($nodeCounter);

    $nodeCTraverser->traverse($stmts);
    $totalNodeCount = $nodeCounter->getNodeCount();
    $stepSize = ceil($totalNodeCount / 100);

    

    $bodyTraverser->traverse($stmts);
    $vulnerabilities = $bodyVisitor->getVulnerabilities()->get();

    $timeUsed = microtime(true)-$start;
    //echo $nodeDumper->dump($stmts);

    // Lets group the vulnerabilities
    $groupedVulns = array();
    foreach ($vulnerabilities as $nr => $vulnerability) {
        if ($vulnerability->return) {
            unset($vulnerabilities[$nr]);
            continue;
        }

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
    echo "----------------------------------\n";

} catch (PhpParser\Error $e) {
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

?>
