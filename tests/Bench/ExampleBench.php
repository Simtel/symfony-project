<?php

declare(strict_types=1);

namespace App\Tests\Bench;

use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

class ExampleBench
{
    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchForeach()
    {
        $aHash = [];
        $i = 0;
        $tmp = '';
        while ($i < 100000) {
            $tmp .= 'a';
            ++$i;
        }
        $aHash = array_fill(10000000, 10000, $tmp);
        unset($i, $tmp);

        $t = microtime(true);
        reset($aHash);
        $sum = 0;
        foreach ($aHash as $val) {
            $sum += (int)$val;
        }

        return (microtime(true) - $t);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchForeachKeyAndTmp()
    {
        $aHash = [];
        $i = 0;
        $tmp = '';
        while ($i < 100000) {
            $tmp .= 'a';
            ++$i;
        }
        $aHash = array_fill(10000000, 10000, $tmp);
        unset($i, $tmp);

        $tmp = [];
        /* The Test */
        $t = microtime(true);
        reset($aHash);
        foreach ($aHash as $key => $val) {
            $tmp[] = $aHash[$key];
        }

        return (microtime(true) - $t);
    }

}
