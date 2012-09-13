<?php

require dirname(__FILE__).'/src/Firework/Benchmark.php';

use \Firework\Benchmark;

Benchmark::start('Starting Benchmark');

    Benchmark::start('For\'s Benchmark');

        Benchmark::start('Pos-increment');
        for ($i=1;$i<10000;$i++) {
            // Do something
        }
        Benchmark::end();

        Benchmark::start('Pre-increment');
        for ($i=1;$i<10000;++$i) {
            // Do something
        }
        Benchmark::end();

    Benchmark::end();

    Benchmark::start('While\'s Benchmark');

        Benchmark::start('Pos-increment');
        $i = 1;
        while ($i<10000) {
            $i++;
        }
        Benchmark::end();

        Benchmark::start('Pre-increment');
        $i = 1;
        while ($i<10000) {
            ++$i;
        }
        Benchmark::end();

    Benchmark::end();

    Benchmark::start();
    Benchmark::end();

Benchmark::end();
echo Benchmark::toHTML();
?>