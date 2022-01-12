#!/usr/bin/env php
<?php
declare(strict_types = 1);

$path = __DIR__ . '/../dist';

if (is_dir($path) === true && rmdir($path) === false) {
  throw new RuntimeException(
    sprintf(
      'Failed to remove output folder (%s)',
      $path
    )
  );
}

if (mkdir($path) === false) {
  throw new RuntimeException(
    sprintf(
      'Failed to create output folder (%s)',
      $path
    )
  );
}

$content = sprintf(
  '<code>Input: %s%sList: %s</code>',
  $argv[1],
  PHP_EOL,
  implode(PHP_EOL, scandir($argv[1]))
);

$file = $path . '/index.html';

if (file_put_contents($file, $content, LOCK_EX) === false) {
  throw new RuntimeException(
    sprintf(
      'Failed to save html content (%s)',
      $file
    )
  );
}

echo 'Saved ', strlen($content), ' bytes to ', $file, PHP_EOL;
