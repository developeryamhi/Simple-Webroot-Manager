<?php

//	Format File Permission
function representableFileperms($perms) {

	if (($perms & 0xC000) == 0xC000) {
	    // Socket
	    $info = 's';
	} elseif (($perms & 0xA000) == 0xA000) {
	    // Symbolic Link
	    $info = 'l';
	} elseif (($perms & 0x8000) == 0x8000) {
	    // Regular
	    $info = '-';
	} elseif (($perms & 0x6000) == 0x6000) {
	    // Block special
	    $info = 'b';
	} elseif (($perms & 0x4000) == 0x4000) {
	    // Directory
	    $info = 'd';
	} elseif (($perms & 0x2000) == 0x2000) {
	    // Character special
	    $info = 'c';
	} elseif (($perms & 0x1000) == 0x1000) {
	    // FIFO pipe
	    $info = 'p';
	} else {
	    // Unknown
	    $info = 'u';
	}

	// Owner
	$info .= (($perms & 0x0100) ? 'r' : '-');
	$info .= (($perms & 0x0080) ? 'w' : '-');
	$info .= (($perms & 0x0040) ?
	            (($perms & 0x0800) ? 's' : 'x' ) :
	            (($perms & 0x0800) ? 'S' : '-'));

	// Group
	$info .= (($perms & 0x0020) ? 'r' : '-');
	$info .= (($perms & 0x0010) ? 'w' : '-');
	$info .= (($perms & 0x0008) ?
	            (($perms & 0x0400) ? 's' : 'x' ) :
	            (($perms & 0x0400) ? 'S' : '-'));

	// World
	$info .= (($perms & 0x0004) ? 'r' : '-');
	$info .= (($perms & 0x0002) ? 'w' : '-');
	$info .= (($perms & 0x0001) ?
	            (($perms & 0x0200) ? 't' : 'x' ) :
	            (($perms & 0x0200) ? 'T' : '-'));

	//	Return
	return $info;
}

//  Format the Bytes
function formatBytes($bytes, $precision = 2) {

    //  Check
    if(!$bytes || intval($bytes) < 1)   return '0 B';

    //  Units
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    // Uncomment one of the following alternatives
    //$bytes /= pow(1024, $pow);
    $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
}

//	Run In Shell
function runInShell($cmd) {

	//	Output
	$output = null;

	//	Check
	if(function_exists('shell_exec')) {

		//	Output
		$output = shell_exec($cmd);
	}
	else if(function_exists('exec')) {

		//	Output
		exec($cmd, $output);

		//	Implode
		$output = implode("\n", $output);
	}
	else if(function_exists('system')) {

		//	Output
		$output = system($cmd);
	}

	//	Return
	return $output;
}

//	Get File Permissions
function file_perms($file, $octal = false) {
    if(!file_exists($file)) return false;
    $perms = fileperms($file);
    $cut = $octal ? 2 : 3;
    return substr(decoct($perms), $cut);
}

//	Get File Info
function extractFileInfo($file, $baseDir) {

	//	Basename
	$basename = basename($file);

	//	Get Size
	$fileSize = filesize($file);

	//	Get Owner
	$fileOwner = posix_getpwuid(fileowner($file));

	//	Store
	$info = array(
		'abs_path' => $file,
		'name' => $basename,
		'title' => $basename,
		'rel_path' => substr($file, strlen($baseDir)),
		'permission' => fileperms($file),
		'permission_display' => file_perms($file, true),
		'owner' => $fileOwner['gid'],
		'owner_display' => $fileOwner['name'],
		'size' => $fileSize,
		'size_display' => formatBytes($fileSize),
		'is_file' => is_file($file),
		'ftype' => (is_file($file) ? 'file' : 'folder'),
		'has_actions' => true
	);

	//	Check
	if(!$info['rel_path'])	$info['rel_path'] = '/';

	//	Return
	return $info;
}