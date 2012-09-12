<?php

require __DIR__.'/src/Firework/Firework.php';

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Firework - Benchmark</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">

<!-- Le styles -->
<link href="http://rc.getbootstrap.com/assets/css/bootstrap.css" rel="stylesheet">
<link href="http://rc.getbootstrap.com/assets/css/bootstrap-responsive.css" rel="stylesheet">

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!-- Le fav and touch icons -->
<link rel="shortcut icon" href="assets/ico/favicon.ico">
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="http://rc.getbootstrap.com/assets/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="http://rc.getbootstrap.com/assets/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="http://rc.getbootstrap.com/assets/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="http://rc.getbootstrap.com/assets/ico/apple-touch-icon-57-precomposed.png">

</head>

<body>
<div class="container">
    <div class="hero-unit">
        <h1>Firework - Benchmark</h1>
        <p>A powerfull component that helps you benchmark your code easily</p>
        <a class="btn btn-primary btn-large">Learn more</a>
    </div>
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>Label</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Elapsed Time</th>
            </tr>
        </thead>
        <tbody>
            <?php Benchmark::toHTML(); ?>
        </tbody>
    </table>
</div>
</body>
</html>