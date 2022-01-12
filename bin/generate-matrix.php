#!/usr/bin/env php
<?php
declare(strict_types = 1);

$raw = file_get_contents('https://www.php.net/releases/active.php');
if ($raw === false) {
  echo 'Failed to retrieve the list of Active PHP Releases', PHP_EOL;

  exit(1);
}

$json = json_decode($raw, true);
if ($json === null) {
  echo 'Failed to decode the list of Active PHP Releases', PHP_EOL;

  exit(1);
}

$php = [];
foreach ($json as $major => $majorDetails) {
  foreach ($majorDetails as $minor => $release) {
    $php[] = $release['version'];
  }
}

$matrix = [
  'php' => $php,
  'ts' => ['ts', 'nts'],
  'extension' => [
    'ext-gpio'
  ]
];

echo json_encode($matrix);
