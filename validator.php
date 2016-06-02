<?php

$version = ''; // 4.5.2
$local = ''; // en_US
$installDir = ''; // /var/www/wordpress

$checksums = json_decode(file_get_contents(sprintf('https://api.wordpress.org/core/checksums/1.0/?version=%s&locale=%s', $version, $local)), true);
$checksums = $checksums['checksums'];

if($checksums === false)
{
	echo sprintf('No checksum found for wordpress version \'%s\' and locale \'%s\'', $version, $local);
	exit;
}

$changes = array();
foreach (getDirContents($installDir) as $fullPath) {
	$shortFile = str_replace($installDir . '/', '', $fullPath);

	if (!array_key_exists($shortFile, $checksums)) {
		continue;
	}

	if (md5_file($fullPath) == $checksums[$shortFile]) {
		continue;
	}

	$changes[] = $fullPath;
}

if (count($changes) == 0) {
	echo 'No changes found :)';
} else {
	foreach ($changes as $change) {
		echo sprintf('Changed checksum in %s<br>', $change);
	}
}


function getDirContents($dir, &$results = array())
{
	foreach (scandir($dir) as $value) {
		$path = realpath($dir . DIRECTORY_SEPARATOR . $value);
		if (!is_dir($path)) {
			$results[] = $path;
		} else if ($value != '.' && $value != '..') {
			getDirContents($path, $results);
			$results[] = $path;
		}
	}

	return $results;
}