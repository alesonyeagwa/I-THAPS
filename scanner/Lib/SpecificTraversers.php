<?php

use PhpParser\Node;

class FunctionCallResolver extends PhpParser\NodeVisitorAbstract {
    private $funcCallList = array();

    public function getFuncCallList() {
        return $this->funcCallList;
    }

    public function enterNode(PhpParser\Node $node) {
        if ($node instanceof PhpParser\Node\Expr\FuncCall) {
            $funcName = $node->name->parts[0];
            array_push($this->funcCallList,$funcName);
        }
    }
}


class GlobalResolver extends PhpParser\NodeVisitorAbstract {
    /**
     * @var string[] - keeps the names of the variables that are global
     */
    private $globalVars = array();
    public function getGlobalVars() {
        return $this->globalVars;
    }

    public function enterNode(PhpParser\Node $node) {
        if ($node instanceof PhpParser\Node\Stmt\Global_) {
            foreach ($node->vars as $var) {
                $this->globalVars[] = $var->name;
            }
        }
    }
}


class NodeCounter extends PhpParser\NodeVisitorAbstract {
    private $c;

    public function beforeTraverse(array $nodes) {
        $this->c = 0;
    }

    public function enterNode(PhpParser\Node $node) {
        $this->c++;
        $node->setAttribute("nodeNumber", $this->c);
    }

    public function afterTraverse(array $nodes) {
        return $nodes;
    }

    public function getNodeCount(){
        return $this->c;
    }
}

class ModelVisitor extends PhpParser\NodeVisitorAbstract {
    public $functionCallWrapper = array();
    public function setFunctionCallWrappers($wrappers) {
        $this->functionCallWrapper = $wrappers;
    }

    public function leaveNode(PhpParser\Node $node) {
        if ($node instanceof PhpParser\Node\Expr\FuncCall) {
            $funcName = $node->name->parts[0];
            if (array_key_exists($funcName,$this->functionCallWrapper)) {
                global $prettyPrinter;
                $old = printNode($node,true);
                // This only supports single function argugements, should be more.
                $newFuncName = $node->args[end($this->functionCallWrapper[$funcName])]->value->value;
                $node->name->parts[0] = $newFuncName;
                $node->args = array();
                $node->setAttribute("THAPSComment", "Model rewrite from: ".$old);

            }
        }
    }
}

class ConditionVisitor extends PhpParser\NodeVisitorAbstract {
    private $cleanVars = array();
    private $dirtyVars = array();
    public function getCleanedVars() {
        return $this->cleanVars;
    }
    public function getDirtyVars() {
        return $this->dirtyVars;
    }

    public function beforeTraverse(array $nodes) {
        $this->cleanVars = array();
        $this->dirtyVars = array();
    }

    public function enterNode(PhpParser\Node $node) {
        if ($node instanceof PhpParser\Node\Expr\FuncCall) {
            global $SECURING_IN_IFS;

            $funcName = $node->name->parts[0];
            if (in_array($funcName, $SECURING_IN_IFS)) {
                $arg = $node->args[0];
                $this->cleanVars[] = $arg->value;
            }
        }
        elseif ($node instanceof PhpParser\Node\Expr\BooleanNot) {
            $conditionVisitor = new ConditionVisitor();
            $conditionTranverser = new PhpParser\NodeTraverser();
            $conditionTranverser->addVisitor($conditionVisitor);
            $conditionTranverser->traverse(array($node->expr));

            $this->cleanVars = array_merge($this->cleanVars,$conditionVisitor->getDirtyVars());
            $this->dirtyVars = array_merge($this->dirtyVars,$conditionVisitor->getCleanedVars());
        }
        elseif ($node instanceof PhpParser\Node\Expr\BinaryOp\BooleanOr) {
            $conditionVisitor = new ConditionVisitor();
            $conditionTranverser = new PhpParser\NodeTraverser();
            $conditionTranverser->addVisitor($conditionVisitor);
            $conditionTranverser->traverse(array($node->left));
            $leftClean = $conditionVisitor->getCleanedVars();
            $leftDirty = $conditionVisitor->getDirtyVars();

            $conditionTranverser->traverse(array($node->right));
            $rightClean = $conditionVisitor->getCleanedVars();
            $rightDirty = $conditionVisitor->getDirtyVars();


            foreach ($leftClean as $cleanVar) {
                foreach ($rightClean as $cleanVar2) {
                    if ($this->compareVars($cleanVar,$cleanVar2)) {
                        $this->cleanVars = array_merge($this->cleanVars,array($cleanVar));
                    }
                }
            }
            foreach ($leftDirty as $dirtyVar) {
                foreach ($rightDirty as $dirtyVar2) {
                    if ($this->compareVars($dirtyVar,$dirtyVar2)) {
                        $this->dirtyVars = array_merge($this->dirtyVars,array($dirtyVar));
                    }
                }
            }
        }
        elseif ($node instanceof PhpParser\Node\Expr\BinaryOp\BooleanAnd) {
            $conditionVisitor = new ConditionVisitor();
            $conditionTranverser = new PhpParser\NodeTraverser();
            $conditionTranverser->addVisitor($conditionVisitor);
            $conditionTranverser->traverse(array($node->left));
            $this->cleanVars = array_merge($this->cleanVars,$conditionVisitor->getCleanedVars());
            $this->dirtyVars = array_merge($this->dirtyVars,$conditionVisitor->getDirtyVars());

            $conditionTranverser->traverse(array($node->right));
            $this->cleanVars = array_merge($this->cleanVars,$conditionVisitor->getCleanedVars());
            $this->dirtyVars = array_merge($this->dirtyVars,$conditionVisitor->getDirtyVars());
        }
        return PhpParser\NodeTraverser::DONT_TRAVERSE_CHILDREN;
    }

    private function compareVars($first,$second) {
        if ($first instanceof PhpParser\Node\Expr\ArrayDimFetch &&
            $second instanceof PhpParser\Node\Expr\ArrayDimFetch) {
            return $this->compareVars($first->var,$second->var) && $this->compareVars($first->dim,$second->dim);
        }
        elseif ($first instanceof PhpParser\Node\Expr\ConstFetch &&
                $second instanceof PhpParser\Node\Expr\ConstFetch &&
                $first->name->parts[0] == $second->name->parts[0]) {
            return true;
        }
        elseif ($first instanceof PhpParser\Node\Expr\Variable &&
                $second instanceof PhpParser\Node\Expr\Variable &&
                $first->name == $second->name) {
            return true;
        }
        elseif ($first instanceof PhpParser\Node\Scalar &&
                $second instanceof PhpParser\Node\Scalar &&
                $first->value == $second->value) {
            return true;
        }
        return false;
    }
}

class FileNameVisitor  extends PhpParser\NodeVisitorAbstract {
    private $filename;
    public function setFilename($fileName){
        $this->filename = $fileName;
    }

    public function __construct($fileName = "")
    {
        $this->filename = $fileName;
    }

    public function enterNode(Node $node)
    {
        $node->setAttribute("fileName", $this->filename);
        //die(var_dump($node->getSubNodeNames()));
        //echo(var_dump($node));
    }

    public function afterTraverse(array $nodes)
    {
        return $nodes;
    }
}