<?php
$includeTraverser = new PhpParser\NodeTraverser;
class IncludeVisitor extends PhpParser\NodeVisitorAbstract
{
    private $modelMode = false;
    public function useModel() {
        $this->modelMode = true;
    }
    private $requestMode = false;
    public function useRequest() {
        $this->requestMode = true;
    }

    private $dynamicIncludeInformation = array();
    private $includeFileStack = array();

    private $systemPath = "";

    /**
     * @var VariableStorage
     */
    private $vScope = null;
    public function setVScope(VariableStorage $scope) {
        $this->vScope = $scope;
    }

    public static $doIncludes = false;

    /**
     * @var string
     */
    private $fileName;

    public function setFileName($fileName){
        $this->fileName = $fileName;
    }

    public function getFileName(){
        return $this->fileName;
    }

    private $lastIncludeStatementLineNumber = 0;

    public function beforeTraverse(array $nodes) {
        if ($this->vScope == null) {
            $this->vScope = new VariableStorage();
        }
        //var_dump($nodes);
        
        foreach($nodes as $node){
            if($node instanceof PhpParser\Node\Stmt\Expression){
                if($node->expr instanceof PhpParser\Node\Expr\Include_){
                    $this->lastIncludeStatementLineNumber = $node->getLine();
                }
            }elseif($node instanceof PhpParser\Node\Expr\Include_){
                $this->lastIncludeStatementLineNumber = $node->getLine();
            }
        }
        //echo $this->lastIncludeStatementLineNumber;
        //die();
    }

    /**
     * @param $includeDescription PHPExtBridge_Include[]
     */
    public function addDynamicIncludeResolve($includeDescription) {
        foreach ($includeDescription as $descr) {
            $descr["current"] = realpath($descr["current"]);
            if (!isset($this->dynamicIncludeInformation[$descr["current"]]))
                $this->dynamicIncludeInformation[$descr["current"]] = array();
            if (!isset($this->dynamicIncludeInformation[$descr["current"]][$descr["line"]]))
                $this->dynamicIncludeInformation[$descr["current"]][$descr["line"]] = array();
            $this->dynamicIncludeInformation[$descr["current"]][$descr["line"]][] = $descr["included"];
        }
    }

    public function enterNode(PhpParser\Node $node_)
    {
        //#Issue 1 solution
        $node = $node_;
        if($node_ instanceof PhpParser\Node\Stmt\Expression){
            $node = $node->expr;
        }
        if($node->getLine() <= $this->lastIncludeStatementLineNumber && IncludeVisitor::$doIncludes){
            if ($node instanceof PhpParser\Node\Expr\FuncCall) {
                if($node->name->parts){
                    $funcName = $node->name->parts[0];
                    
                    if ($funcName == "define") {
                        $taintTraverser = new PhpParser\NodeTraverser();
                        $taintVisitor = new BodyVisitor();
                        $taintTraverser->addVisitor($taintVisitor);
                        $taintVisitor->setFileName($this->fileName);
                        $taintVisitor->setVScope($this->vScope);
                        $vs = new VulnerabilityStorage();
                        $taintVisitor->setVulnerabilityStorage($vs);
                        $taintTraverser->traverse(array($node));
                    }
                }
            }else if($node instanceof PhpParser\Node\Expr\Assign || 
                    $node instanceof PhpParser\Node\Expr\AssignOp\Concat ||
                    $node instanceof PhpParser\Node\Expr\Assignop\Plus){
                $taintTraverser = new PhpParser\NodeTraverser();
                $taintVisitor = new BodyVisitor();
                $taintTraverser->addVisitor($taintVisitor);
                $taintVisitor->setVScope($this->vScope);
                $taintVisitor->setFileName($this->fileName);
                $vs = new VulnerabilityStorage();
                $taintVisitor->setVulnerabilityStorage($vs);
                $taintTraverser->traverse(array($node));
            }
        }
        return PhpParser\NodeTraverser::DONT_TRAVERSE_CHILDREN;
    }

    public function setSystemPath($path) {
        $this->systemPath = $path;
    }

    public function leaveNode(PhpParser\Node $node_) {
        //echo(var_dump($node));
        $node = $node_;
        if($node_ instanceof PhpParser\Node\Stmt\Expression){
            $node = $node->expr;
        }
        if ($node instanceof PhpParser\Node\Expr\Include_) {
            //echo(var_dump($node));

            $filename = null;

            if ($this->requestMode) {
                if (isset($this->dynamicIncludeInformation[realpath($this->getFilename())][$node->getLine()])) {
                    $filename = $this->dynamicIncludeInformation[realpath($this->getFilename())][$node->getLine()][0];
                }
            } else {
                if ($node->getLine() != -1) {
                    //print_r($node);
                    //die();
                }
                //echo(var_dump($node));
                $taintTraverser = new PhpParser\NodeTraverser();
                $taintVisitor = new BodyVisitor();
                $taintTraverser->addVisitor($taintVisitor);
                $taintVisitor->setVScope($this->vScope);
                $taintVisitor->setFileName($this->fileName);
                $vs = new VulnerabilityStorage();
                $taintVisitor->setVulnerabilityStorage($vs);
                $taintTraverser->traverse(array($node->expr));
                $val = $taintVisitor->getTaint();
                //echo "11\n";
                //die(var_dump($val));
                if ($val instanceof VariableValue) {
                    if ($val->fi) {
                        array_unshift($val->flowpath, printNode($node));
                        //$val->dependencies = array_merge($taintVisitor->dependencies, $val->dependencies);
                        global $globalVulnerabilities;
                        $vulnerability = new VulnerabilityDescription();
                        $vulnerability->fi = true;
                        $vulnerability->flowpath = $val->flowpath;
                        $vulnerability->dependencies = $val->dependencies;
                        $vulnerability->description = "LOCAL FILE INCLUSION VULNERABILITY FOUND IN FILE " . $this->getFilename() . " LINE " . $node->getLine();
                        $globalVulnerabilities->add($vulnerability);
                    }
                    $filename = $val->value;
                }
                if ($filename !== null && substr($filename,0,1) != "/") {
                    $filename = dirname($this->getFilename())."/".$filename;
                }
            }
            $filename = realpath($filename);
            //die($filename);
            if ($this->modelMode && $node->getLine() != -1) {
                //echo $filename."tried\n";
                if (strpos($filename,$this->systemPath) !== 0) {
                    $filename = null;
                }
            }

            if (is_file($filename) && IncludeVisitor::$doIncludes) {
                if (!in_array($filename,$this->includeFileStack)) {
                    global $parser, $includeTraverser;
                    try {
                        $stmts = $parser->parse(file_get_contents($filename));
                        array_push($this->includeFileStack, $filename);
                        $tmpIncludeTraverser = new PhpParser\NodeTraverser;
                        $tmpIncludeVisitor = new IncludeVisitor;
                        $fileNameVisitor = new FileNameVisitor($filename);
                        $tmpIncludeVisitor->setFileName($filename);
                        $tmpIncludeTraverser->addVisitor($fileNameVisitor);
                        $tmpIncludeTraverser->addVisitor($tmpIncludeVisitor);
                        $stmts = $tmpIncludeTraverser->traverse($stmts);
                        //$stmts = $includeTraverser->traverse($stmts);
                        //echo array_pop($this->includeFileStack). " was included correctly\n";

                        return $stmts;
                    } catch (PhpParser\Error $e) {
                        echo "Parse error: ".$e->getMessage();
                    }
                } else {
                    //echo $filename." has been included in this part of the tree! - Possible cycle?\n";
                }
            } else {
                //echo $filename." not found\n";
            }
        }
    }
}
$includeVisitor = new IncludeVisitor;
$includeTraverser->addVisitor($includeVisitor);
