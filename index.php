<?php

class ParentClass {
    private $parent;
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'parent' => $this->parent,
        ];
    }
}
class SomeClass extends ParentClass {
    private $assembly_id;
    private $from;
    private $to;
    /**
     * @return array
     */
    public function toArray(): array
    {
        // return array_merge(parent::toArray(), [
        //     'date' => $this->date ? $this->date->format('Y-m-d H:i:s') : null,
        // ]);
        return [
            'assembly_id' => [$this->assembly_id],
            "from" => $this->from ? $this->from->format('') : null,
            'to' => $this->from->format(''),
        ];
    }
}


$oReflectionClass = new ReflectionClass('\SomeClass');

$fileName = $oReflectionClass->getFileName();
$method = $oReflectionClass->getMethod('toArray');
$from = $method->getStartLine();
$to = $method->getEndLine();
$lines = file($fileName);

$lineArray = array_slice($lines, $from, ($to-$from));

$code = implode('', $lineArray);
$tokens = token_get_all('<?php '. $code.' ?>');




$begin = null;
$brackets = 0;
$inValueMode = false;
$statements = [];
$statementsCounter = 0;

for($i=0; $i<count($tokens); $i++) {
    if (is_array($tokens[$i]) && token_name($tokens[$i][0]) === 'T_WHITESPACE') {
        continue;
    }
    if (is_array($tokens[$i]) && token_name($tokens[$i][0]) === 'T_RETURN') {
        $begin = $i;
        continue;
    }

    if ($begin !== null && $tokens[$i] === '[') {
        $brackets += 1;
        continue;
    }
    if ($begin !== null && $tokens[$i] === ']') {
        $brackets -= 1;

        if ($brackets === 0) break;
        continue;
    }
    // if ($begin !== null) {
    //     if (is_array($tokens[$i]) && token_name($tokens[$i][0]) === 'T_CONSTANT_ENCAPSED_STRING') {
    //         $inValueMode = true;
    //         echo "- - - - - - - - - - - \n";
    //     }
    // }
    if ($tokens[$i] === ',') {
        $statementsCounter += 1;
        continue;
    };

    if($begin !== null) {
        if (!array_key_exists($statementsCounter, $statements)) {
            $statements[$statementsCounter] = [];
        }
        $statements[$statementsCounter][] = $tokens[$i];
        // if(is_array($tokens[$i])) echo token_name($tokens[$i][0]). " {$tokens[$i][1]}\n";
        // if(is_string($tokens[$i])) echo $tokens[$i] . "\n";
    }
}

print_r($statements);
