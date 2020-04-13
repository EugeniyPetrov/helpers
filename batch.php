<?php

function cat_csv($f, $skipEmptyLines = true)
{
    while (!feof($f)) {
        $row = fgetcsv($f);
        if ($row === false) {
            continue;
        }
        if (count($row) === 0 && $skipEmptyLines === true) {
            continue;
        }
        yield $row;
    }
}

function cat($f, $skipEmptyLines = true)
{
    while (!feof($f)) {
        $line = trim(fgets($f));
        if ($line === "" && $skipEmptyLines === true) {
            continue;
        }
        yield $line;
    }
}

function grep(\Generator $items, $callback)
{
    foreach ($items as $item) {
        if ($callback($item)) {
            yield $item;
        }
    }
}

function batches(\Generator $items, $batchSize)
{
    $batch = [];
    foreach ($items as $item) {
        $batch[] = $item;
        if (count($batch) === $batchSize) {
            yield $batch;
            $batch = [];
        }
    }

    if (count($batch) > 0) {
        yield $batch;
    }
}

function extract_keys($array, $keys)
{
    return array_intersect_key($array, array_flip($keys));
}

function extract_from_to($array)
{
    return array_intersect_key($array, [
        "from" => true,
        "to" => true,
    ]);
}

function extract_slice($list, $key)
{
    $slice = [];
    foreach ($list as $item) {
        $slice[] = array_key_exists($key, $item) ? $item[$key] : null;
    }
    return $slice;
}

function ranges($min, $max, $step, $maxTime = 0, $maxStep = 10000, $usleep = 0, $maxIterations = 0): \Generator
{
    $startTime = microtime(true);

    $inverted = false;
    if ($min > $max) {
        $min = -$min;
        $max = -$max;
        $inverted = true;
    }

    $execTime = 0;
    for ($from = $min, $i = 0; $from <= $max && ($maxIterations === 0 || $i < $maxIterations); $i++) {
        $to = $from + $step - 1;
        if ($to > $max) {
            $to = $max;
        }
        if ($to < $from) {
            return;
        }

        $start = microtime(true);
        yield [
            'min' => $inverted ? -$min : $min,
            'max' => $inverted ? -$max : $max,
            'step' => $step,
            'execTime' => round($execTime, 4),
            'from' => $inverted ? -$to : $from,
            'to' => $inverted ? -$from : $to,
            'startTime' => $startTime,
        ];
        $execTime = microtime(true) - $start;

        $from = $to + 1;

        if ($maxTime > 0) {
            if ($execTime > $maxTime) {
                $step *= 0.6;
            } else {
                $step *= 1.2;
            }
            $step = ceil($step);
            if ($step < 2) {
                $step = 2;
            }
            if ($step > $maxStep) {
                $step = $maxStep;
            }
        }

        usleep($usleep);
    }
}

function process($chunk)
{
    $processed = $chunk["to"] - $chunk["min"];
    $total = $chunk["max"] - $chunk["min"];

    if ($total === 0 && $processed === 0) {
        $process = 1;
    } else {
        $process = $processed / $total;
    }

    $spent = microtime(true) - $chunk['startTime'];
    $eta = ($spent / $process) - $spent;

    return round($process * 100, 2) . "% ETA: " . eta($eta);
}

function map(\Generator $items, $callback)
{
    foreach ($items as $i => $item) {
        yield $callback($item, $i);
    }
}

function get_args()
{
    $args = [];
    foreach ($_SERVER["argv"] as $value) {
        if (substr($value, 0, 2) === "--") {
            $items = explode("=", $value, 2);
            $args[$items[0]] = isset($items[1]) ? $items[1] : true;
        }
    }
    return $args;
}

function render_console_line($message)
{
    echo "\r$message                ";
}

function eta($eta)
{
    $days = floor($eta / 86400);
    $hours = floor(($eta - ($days * 86400)) / 3600);
    $minutes = floor(($eta - ($days * 86400 + $hours * 3600)) / 60);
    $seconds = floor($eta - ($days * 86400 + $hours * 3600 + $minutes * 60));

    $label = [];
    if ($days > 0) {
        $label[] = "{$days}d";
    }
    if ($hours > 0) {
        $label[] = "{$hours}h";
    }
    if ($minutes > 0) {
        $label[] = "{$minutes}m";
    }
    if ($seconds > 0) {
        $label[] = "{$seconds}s";
    }
    return join(" ", $label);
}

function measure($name = null, callable $callback = null)
{
    static $metrics = [];

    if ($name === null && $callback === null) {
        return $metrics;
    }

    isset($metrics[$name]) or $metrics[$name] = [
        'utime' => 0,
        'count' => 0,
    ];

    $result = null;
    $start = microtime(true);
    if ($callback !== null) {
        $result = $callback();
    }
    $metrics[$name]['utime'] += (microtime(true) - $start);
    $metrics[$name]['count']++;

    return $result;
}
