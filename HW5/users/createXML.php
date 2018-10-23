<?php

// $curfolder, $linkxml, $user_id

// scan folder user_id
$directory = $user_id;
$scanned_dir = array_diff(scandir($directory), array('..', '.'));

print_r($scanned_dir);

$di = new RecursiveDirectoryIterator($directory);
foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
    echo $filename . ' - ' . $file->getSize() . ' bytes <br/>';
}

$stack = array("");

$dir = new DirectoryIterator($directory . "/photo");
foreach ($dir as $fileinfo) {
    if ($fileinfo->isDir() && !$fileinfo->isDot()) {
        $name =  $fileinfo->getFilename();
		echo $name .'<br>';
		array_push($stack, $name);
    }
}

print_r($stack);



?>