<?php
$functionTraverser = new PhpParser\NodeTraverser;
$functions = array();

class FunctionDescription {
    public $alwaysVulnerable = array();
    public $returnAlwaysVulnerable = array();
    public $vulnerableParameters = array();
    public $returnVulnerableParameters = array();

    public $globalVulnerable = array();
    public $returnGlobalVulnerable = array();
    public $globalSideEffectParameter = array();
    public $globalSideEffectAlways = array();
}

class TempFunctionDescription {
    public $node;
    public $name;
    public $dependencies = array();
}

class FunctionVisitor extends PhpParser\NodeVisitorAbstract {
    /**
     * @var TempFunctionDescription[]
     */
    private $foundFunctions;
    /**
     * @var TempFunctionDescription
     */
    private $currentFunction;

    private $vScope = null;

    private $fileName = "";

    public function setVScope($scope) {
        $this->vScope = $scope;
    }

    public function setFileName($fileName){
        $this->fileName = $fileName;
    }

    public function beforeTraverse(array $nodes) {
        if ($this->vScope == null) {
            global $globalVScope;
            $this->vScope = $globalVScope =  new VariableStorage;
        }
        $this->foundFunctions = array();
    }

    public function enterNode(PHPParser\Node $node) {
        if ($node instanceof PhpParser\Node\Stmt\Function_) {
            $funcName = trim($node->name);
            $funcDesc = new TempFunctionDescription;
            $funcDesc->name = $funcName;
            $funcDesc->node = $node;
            $this->foundFunctions[$funcName] = $funcDesc;
            $this->currentFunction = $funcDesc;
        } else if ($node instanceof PhpParser\Node\Expr\FuncCall) {
            $funcParts = $node->name->parts;
            $funcName = $funcParts[0];

            if ($this->currentFunction != null)
                $this->currentFunction->dependencies[] = $funcName;
        }
    }
    public function leaveNode(PHPParser\Node $node) {
        if ($node instanceof PhpParser\Node\Stmt\Function_) {
            $this->currentFunction = null;
            return PhpParser\NodeTraverser::REMOVE_NODE;
        }
    }

    public function afterTraverse(array $nodes) {
        // Parse the functions
        foreach ($this->foundFunctions as $funcName => $funcDesc) {
            $this->parseFunction($funcDesc);
        }
    }

    private $workList = array();

    private function parseFunction(TempFunctionDescription $funcDesc) {
        global $functions;

        $funcName = $funcDesc->name;
        if (isset($functions[$funcName])) {
            return;
        }
        $this->workList[] = $funcName;
        foreach ($funcDesc->dependencies as $dep) {
            if (isset($this->foundFunctions[$dep]) && !in_array($dep,$functions) && !in_array($dep,$this->workList)) {
                $this->workList[] = $dep;
                $this->parseFunction($this->foundFunctions[$dep]);
                array_pop($this->workList);
            }
        }
        array_pop($this->workList);


        $node = $funcDesc->node;
        $vars = $this->vScope;
        $params = array(); // Save names of params;

        foreach ($node->params as $paramNr => $param) {
            $vars->setVariableValue(new VariableValue(true),$param->name);
            $params[$paramNr] = $param->name;
        }

        // Globals - same as params - side effects included
        $globalVisitor = new GlobalResolver();
        $globalTraverser = new PhpParser\NodeTraverser();
        $globalTraverser->addVisitor($globalVisitor);
        $globalTraverser->traverse($node->stmts);
        $globals = array_unique($globalVisitor->getGlobalVars());

        foreach ($globals as $global) {
            $vars->setVariableValue(new VariableValue(true),$global);
        }

        $bodyVisitor = new BodyVisitor;
        $bodyTraverser = new PhpParser\NodeTraverser;
        $bodyTraverser->addVisitor($bodyVisitor);
        $bodyVisitor->setVScope($vars);
        $bodyVisitor->setFileName($node->getAttribute("fileName"));
        $vs = new VulnerabilityStorage();
        $bodyVisitor->setVulnerabilityStorage($vs);
        $functionVulnerabilities = $bodyTraverser->traverse($node->stmts);
        $functionDescription = new FunctionDescription();
        $functions[$funcName] = $functionDescription;
        if (count($functionVulnerabilities) > 0) {
            // Vulnerabilities
            foreach ($functionVulnerabilities as $nr => $vuln) {
                $vulnParamName = vulnOriginsFromVar($vuln->flowpath);

                if (in_array($vulnParamName,$params)) {
                    if ($vuln->return) {
                        if (!isset($functionDescription->returnVulnerableParameters[array_search($vulnParamName,$params)]))
                            $functionDescription->returnVulnerableParameters[array_search($vulnParamName,$params)] = array();
                        $functionDescription->returnVulnerableParameters[array_search($vulnParamName,$params)][] = clone $vuln;
                    } else {
                        if (!isset($functionDescription->vulnerableParameters[array_search($vulnParamName,$params)]))
                            $functionDescription->vulnerableParameters[array_search($vulnParamName,$params)] = array();
                        $functionDescription->vulnerableParameters[array_search($vulnParamName,$params)][] = clone $vuln;
                    }
                }
                elseif (in_array($vulnParamName,$globals)) {
                    if ($vuln->return) {
                        if (!isset($functionDescription->returnGlobalVulnerable[$vulnParamName]))
                            $functionDescription->returnGlobalVulnerable[$vulnParamName] = array();
                        $functionDescription->returnGlobalVulnerable[$vulnParamName][] = clone $vuln;
                    } else {
                        if (!isset($functionDescription->globalVulnerable[$vulnParamName]))
                            $functionDescription->globalVulnerable[$vulnParamName] = array();
                        $functionDescription->globalVulnerable[$vulnParamName][] = clone $vuln;
                    }
                }
                else {
                    if ($vuln->return) {
                        $functionDescription->returnAlwaysVulnerable[] = clone $vuln;
                    } else {
                        $functionDescription->alwaysVulnerable[] = clone $vuln;
                    }
                }
            }
        }
        // Side effects of global
        foreach ($globals as $global) {
            foreach ($bodyVisitor->getVScope()->getDependencyStorage() as $conf) {
                $tmpTaint = $conf->getVariableValue($global);
                $tmpTaint = squeezeArrayTaints($tmpTaint);
                if ($tmpTaint->userInput) {
                    $origin = vulnOriginsFromVar($tmpTaint->flowpath);
                    if (in_array($origin,$params)) {
                        if (!isset($functionDescription->globalSideEffectParameter[array_search($origin,$params)]))
                            $functionDescription->globalSideEffectParameter[array_search($origin,$params)] = array();
                        $functionDescription->globalSideEffectParameter[array_search($origin,$params)][$global] = $tmpTaint;
                    } else if (count($tmpTaint->flowpath) > 0) {
                        $functionDescription->globalSideEffectAlways[$global] = $tmpTaint;
                    }
                }
            }
        }
    }
}
$functionVisitior = new FunctionVisitor;
$functionTraverser->addVisitor($functionVisitior);


function vulnOriginsFromVar($vulnerability) {
    $str = end($vulnerability);

    $c = preg_match_all('/\$[a-z0-9_\->]+/i',$str,$matches);

    $res = "";
    if ($c > 0)
        $res = substr($matches[0][$c - 1],1);

    return $res;
}
