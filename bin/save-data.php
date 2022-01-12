#!/usr/bin/env php
<?php
declare(strict_types = 1);

function validatePath(string $path): void {
  if (is_dir($path)) {
    return;
  }

  throw new RuntimeException(
    sprintf(
      'Invalid path "%s"',
      $path
    )
  );
}

function validateExtension(string $extension): void {
  if (preg_match('/^[a-zA-Z0-9_-]+$/', $extension)) {
    return;
  }

  throw new RuntimeException(
    sprintf(
      'Invalid extension name "%s"',
      $extension
    )
  );
}

function validateOutcome(string $outcome): void {
  if (in_array($outcome, ['success', 'failure', 'cancelled', 'skipped'])) {
    return;
  }

  throw new RuntimeException(
    sprintf(
      'Invalid outcome "%s"',
      $outcome
    )
  );
}

function saveData(
  string $path,
  string $extension,
  string $phpVersion,
  bool $phpZts,
  string $deps,
  string $build,
  string $tests
): void {
  $file = $path . '/' . $extension . '.json';
  $data = [];
  if (is_writable($file)) {
    $raw = file_get_contents($file);
    if ($raw === false) {
      throw new RuntimeException(
        sprintf(
          'Failed to read data from file "%s"',
          $file
        )
      );
    }

    $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
  }

  $data[$phpVersion][$phpZts ? 'zts' : 'nts'] = [
    'deps'  => $deps,
    'build' => $build,
    'tests' => $tests
  ];

  if (file_put_contents($file, json_encode($data), LOCK_EX) === false) {
    throw new RuntimeException(
      sprintf(
        'Failed to save data to file "%s"',
        $file
      )
    );
  }
}

function printHelp(string $script): void {
  echo 'Usage:', PHP_EOL;
  echo '  ', $script, ' <data-path> <extension> <deps> <build> <tests>', PHP_EOL;
  echo PHP_EOL;
  echo '  Values:', PHP_EOL;
  echo '    data-path: path to load/save data from/to', PHP_EOL;
  echo '    extension: a valid extension name', PHP_EOL;
  echo '    deps: success, failure, cancelled or skipped', PHP_EOL;
  echo '    build: success, failure, cancelled or skipped', PHP_EOL;
  echo '    tests: success, failure, cancelled or skipped', PHP_EOL;
}

if ($argc < 5) {
  printHelp($argv[0]);

  exit(1);
}

try {
  validatePath($argv[1]);
  validateExtension($argv[2]);
  validateOutcome($argv[3]);
  validateOutcome($argv[4]);
  validateOutcome($argv[5]);

  saveData(
    $argv[1],
    $argv[2],
    PHP_VERSION,
    (bool)PHP_ZTS,
    $argv[3],
    $argv[4],
    $argv[5]
  );
} catch (RuntimeException $exception) {
  echo 'Exception: ', $exception->getMessage(), PHP_EOL;

  exit(1);
}
