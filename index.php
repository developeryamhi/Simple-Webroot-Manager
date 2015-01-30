<?php

//	Include
include_once 'helper.php';

//	Provided Path
$path = rtrim('/' . (isset($_GET['path']) ? trim($_GET['path'], '/') : ''), '/');

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Simple Webroot Manager</title>
<link href="css/bootstrap.min.css" rel="stylesheet" />
<link href="css/pnotify.min.css" rel="stylesheet" />
<link href="css/style.css" rel="stylesheet" />
</head>

<body>

	<nav class="navbar navbar-default navbar-inverse">
		<div class="container">
		    <div class="navbar-header">
				<a class="navbar-brand" href="javascript:void(0);">
					<i class="glyphicon glyphicon-th-large"></i>&nbsp;
					Simple Webroot Manager
				</a>
			</div>
		</div>
	</nav>

	<div class="container">
		<div id="main-container">
			<form class="folder-scanner-form" action="post" onsubmit="return false;">
				<div class="input-group">
					<span class="input-group-addon"><?php echo dirname(dirname(__FILE__)); ?>/</span>
					<input type="text" class="folder-path form-control" placeholder="please enter the folder path... e.g. '/' for base path" title="please enter the folder path... e.g. '/' for base path" value="<?php echo $path; ?>" />
					<span class="input-group-btn">
						<button class="btn btn-success" type="submit">Scan</button>
					</span>
				</div>
			</form>

			<div class="list-group folder-scanned-data"></div>
			<div class="no-folder-scanned-data label-danger">Invalid Path Specified or Path is Empty</div>
		</div>
	</div>

	<script id="scanned-item-row-template" type="text/x-handlebars-template">
	<div class="list-group-item scanned-item-row" data-name="{{name}}" data-ftype="{{ftype}}" data-rel-path="{{rel_path}}" data-size="{{size}}" data-permission="{{permission_display}}" data-owner="{{owner_display}}" data-is-file="{{is_file}}">
		<div class="row">
			<div class="col-sm-4 col-xs-5">
				{{#if is_file}}
				{{name}}
				{{else}}
				<a href="javascript:void(0);" class="step-into-file" title="{{title}}">{{name}}</a>
				{{/if}}
			</div>
			<div class="col-xs-2 hidden-xs">{{size_display}}</div>
			<div class="col-sm-2 col-xs-3">{{permission_display}}</div>
			<div class="col-xs-2 hidden-xs">{{owner_display}}</div>
			<div class="col-sm-2 col-xs-4">
				{{#if has_actions}}
				<div class="scanned-item-actions">
					<a href="javascript:void(0);" title="Rename" class="rename-file">
						<i class="glyphicon glyphicon-pencil"></i>
					</a>
					<!--<a href="javascript:void(0);" title="Delete" class="delete-file">
						<i class="glyphicon glyphicon-trash"></i>
					</a>-->
					<a href="javascript:void(0);" title="Change Permission" class="change-file-permission">
						<i class="glyphicon glyphicon-cog"></i> <span class="hidden-sm hidden-xs">Perm</span>
					</a>
					<a href="javascript:void(0);" title="Change Owner" class="change-file-owner">
						<i class="glyphicon glyphicon-cog"></i> <span class="hidden-sm hidden-xs">Owner</span>
					</a>
				</div>
				{{/if}}
			</div>
		</div>
	</div>
	</script>

	<script>var CURRENT_PATH = '<?php echo $path; ?>';</script>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/pnotify.min.js"></script>
	<script src="js/handlebars-v2.0.0.js"></script>
	<script src="js/script.js"></script>
</body>
</html>