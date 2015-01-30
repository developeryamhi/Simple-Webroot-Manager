<?php

//	Include
include_once 'helper.php';

//	Base Dir
$baseDir = dirname(dirname(__FILE__));

//	Response
$response = array('success' => false);

//	Check Valid
if($_SERVER['REQUEST_METHOD'] == 'POST'
	&& $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'
	&& isset($_POST['path'])) {

	//	Get Action
	$action = (isset($_POST['action']) ? trim(strtolower($_POST['action'])) : null);

	//	Provided Path
	$path = rtrim('/' . (isset($_POST['path']) ? trim($_POST['path'], '/') : ''), '/');

	//	Full Path
	$fullPath = $baseDir . $path;

	//	Switch Action
	switch($action) {

		//	Case 'scan'
		case 'scan':

			//	Set Success
			$response['success'] = (file_exists($fullPath) && is_dir($fullPath));

			//	Set the Path
			$response['path'] = $path;

			//	Files and Folders
			$allData = array();

			//	Check
			if($response['success']) {

				//	Folders Data
				$foldersData = array();

				//	Get Folders
				$allFolders = glob($fullPath . '/*', GLOB_ONLYDIR);

				//	Loop Each
				foreach($allFolders as $folder) {

					//	Store
					$foldersData[basename($folder)] = extractFileInfo($folder, $baseDir);
				}

				//	Sort Folders
				uasort($foldersData, function($b, $a) {
					return ($b['name'] - $a['name']);
				});

				//	Check
				if($fullPath != $baseDir) {

					//	Base Info
					$baseInfo = extractFileInfo(dirname($fullPath), $baseDir);

					//	Add Base Path
					$foldersData = array('..' => array_merge($baseInfo, array(
						'name' => '..',
						'title' => 'Go to parent directory',
						'has_actions' => false
					))) + $foldersData;
				} else {

					//	Script Folder
					$scriptFolder = basename(dirname(__FILE__));

					//	Remove the Script Dir from Indexing
					if(isset($foldersData[$scriptFolder])) {

						//	Remove
						unset($foldersData[$scriptFolder]);
					}
				}


				//	Files Data
				$filesData = array();

				//	Get Files
				$allFiles = array_filter(glob($fullPath . '/.*'), 'is_file');

				//	Loop Each
				foreach($allFiles as $file) {

					//	Store
					$filesData[basename($file)] = extractFileInfo($file, $baseDir);
				}

				//	Sort Files
				uasort($filesData, function($b, $a) {
					return ($b['name'] - $a['name']);
				});


				//	Files Data
				$filesData2 = array();

				//	Get Files
				$allFiles2 = array_filter(glob($fullPath . '/*'), 'is_file');

				//	Loop Each
				foreach($allFiles2 as $file) {

					//	Store
					$filesData2[basename($file)] = extractFileInfo($file, $baseDir);
				}

				//	Sort Files
				uasort($filesData2, function($b, $a) {
					return ($b['name'] - $a['name']);
				});


				//	Set All Data
				$allData = $foldersData + $filesData + $filesData2;
			}

			//	Store
			$response['data'] = $allData;

			break;

		//	Case 'rename'
		case 'rename':

			//	New Name
			$newName = (isset($_POST['newName']) ? trim($_POST['newName']) : null);

			//	Check
			if($newName && !empty($newName)) {

				//	New Path
				$newPath = $baseDir . pathinfo($path, PATHINFO_DIRNAME) . $newName;

				//	Rename File
				runInShell("mv -f '{$fullPath}' '{$newPath}'");

				//	Set Success
				$response['success'] = true;

				//	Store New Info
				$response['info'] = extractFileInfo($newPath, $baseDir);
			}

			break;

		//	Case 'delete'
		case 'delete':

			//	Info
			$info = extractFileInfo($fullPath, $baseDir);

			//	Check
			if($info['is_file']) {

				//	Unlink File
				runInShell("unlink '{$fullPath}'");
			} else {

				//	Delete Folder
				runInShell("rm -rf '{$fullPath}'");
			}

			//	Set Success
			$response['success'] = true;

			break;

		//	Case 'change_permission'
		case 'change_permission':

			//	New Permission
			$newPermission = (isset($_POST['newPermission']) ? trim($_POST['newPermission']) : null);

			//	Check
			if($newPermission && !empty($newPermission)) {

				//	Change Permission for File
				runInShell("sudo chmod -R {$newPermission} '{$fullPath}'");

				//	Clear Stat Cache
				clearstatcache();

				//	Set Success
				$response['success'] = true;

				//	Store New Info
				$response['info'] = extractFileInfo($fullPath, $baseDir);
			}

			break;

		//	Case 'change_owner'
		case 'change_owner':

			//	New Owner
			$newOwner = (isset($_POST['newOwner']) ? trim($_POST['newOwner']) : null);

			//	Check
			if($newOwner && !empty($newOwner)) {

				//	Change Owner for File
				runInShell("sudo chown -R {$newOwner}:{$newOwner} '{$fullPath}'");

				//	Clear Stat Cache
				clearstatcache();

				//	Set Success
				$response['success'] = true;

				//	Store New Info
				$response['info'] = extractFileInfo($fullPath, $baseDir);
			}

			break;
	}
}

//	Send Response
header('Content-Type: application/json');
echo json_encode($response);
exit;
