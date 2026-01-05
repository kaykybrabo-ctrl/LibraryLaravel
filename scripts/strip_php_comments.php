<?php
if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "Run this script via CLI.\n");
    exit(1);
}
array_shift($argv); 
$scopes = $argv;
if (empty($scopes)) {
    $scopes = ['app', 'routes', 'database', 'config'];
}
$root = realpath(__DIR__ . '/..');
if ($root === false) { $root = __DIR__ . '/..'; }
$excluded = [
    realpath($root . '/vendor'),
    realpath($root . '/storage'),
    realpath($root . '/bootstrap/cache'),
];
function starts_with_path($path, $prefix) {
    if (!$path || !$prefix) return false;
    $path = str_replace('\\', '/', $path);
    $prefix = str_replace('\\', '/', $prefix);
    return str_starts_with($path, $prefix);
}
function is_excluded_dir($path, $excluded) {
    $rp = realpath($path);
    foreach ($excluded as $ex) {
        if ($ex && starts_with_path($rp, $ex)) return true;
    }
    return false;
}
$processed = 0;
$changed = 0;
foreach ($scopes as $scope) {
    $scopePath = realpath($root . '/' . $scope);
    if (!$scopePath || !is_dir($scopePath)) continue;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($scopePath, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->isDir()) continue;
        $path = $file->getPathname();
        if (is_excluded_dir(dirname($path), $excluded)) continue;
        if (pathinfo($path, PATHINFO_EXTENSION) !== 'php') continue;
        $code = @file_get_contents($path);
        if ($code === false) continue;
        $tokens = token_get_all($code);
        $out = '';
        foreach ($tokens as $token) {
            if (is_array($token)) {
                [$id, $text] = $token;
                if ($id === T_COMMENT || $id === T_DOC_COMMENT) {
                    $out .= str_repeat("\n", substr_count($text, "\n"));
                    continue;
                }
                $out .= $text;
            } else {
                $out .= $token;
            }
        }
        if ($out !== $code) {
            @file_put_contents($path, $out);
            $changed++;
        }
        $processed++;
    }
}
echo "[strip] processed={$processed} changed={$changed}\n";
