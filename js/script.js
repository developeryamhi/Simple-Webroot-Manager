var siSource, siTemplate;
jQuery(function($) {

	//	Scanned Item Template
	siSource = $("#scanned-item-row-template").html();
	siTemplate = Handlebars.compile(siSource);

	//	Listen for Scanner Form Submit
	$(".folder-scanner-form").on('submit', function(e) {

		//	Scanner Form
		var $scannerForm = $(this);

		//	Run Ajax
		$.ajax({
			url: 'ajax.php',
			data: {
				action: 'scan',
				path: $scannerForm.find('.folder-path').val()
			},
			dataType: 'json',
			type: 'POST',
			success: function(response) {

				//	Check
				if(response.success) {

					//	Store the Path
					CURRENT_PATH = response.path;

					//	Populate the Scanned Data
					populateScannedData(response.data);

					//	Show Scanned Data
					displayRespectiveSections(true);
				} else {

					//	Show No Data
					displayRespectiveSections();
				}
			},
			error: function() {

				//	Show No Data
				displayRespectiveSections();
			}
		});

		//	Prevent Default
		e.preventDefault();
		return false;
	}).submit();

	//	Listen for Step Into Folder
	$(".folder-scanned-data").on('click', ".step-into-file", function(e) {

		//	Row
		var $row = $(this).parent().parent().parent();

		//	Check
		if(!$row.data('isFile')) {

			//	Set Folder Path
			jQuery(".folder-scanner-form .folder-path").val($row.data('relPath'));

			//	Submit Scanner Form
			$(".folder-scanner-form").submit();
		}

		//	Prevent Default
		e.preventDefault();
		return false;
	});

	//	Listen for Rename File/Folder
	$(".folder-scanned-data").on('click', ".rename-file", function(e) {

		//	Row
		var $row = $(this).parent().parent().parent().parent();

		//	Prompt for New Name
		customPrompt('Rename ' + capilatize($row.data('ftype')), 'Please enter the new name for the ' + $row.data('ftype'), $row.data('name'), function(newName) {

			//	Run Ajax
			$.ajax({
				url: 'ajax.php',
				data: {
					action: 'rename',
					path: $row.data('relPath'),
					newName: newName
				},
				dataType: 'json',
				type: 'POST',
				success: function(response) {

					//	Check
					if(response.success) {

						//	Compile
						var newHTML = siTemplate(response.info);

						//	Replace
						$row.replaceWith(newHTML);
					}
				}
			});
		});

		//	Prevent Default
		e.preventDefault();
		return false;
	});

	//	Listen for Delete File/Folder
	$(".folder-scanned-data").on('click', ".delete-file", function(e) {

		//	Row
		var $row = $(this).parent().parent().parent().parent();

		//	Prompt for New Name
		customConfirm('Delete ' + capilatize($row.data('ftype')), 'Are you sure to delete ' + $row.data('ftype') + ": " + $row.data('name') + " ?", function() {

			//	Run Ajax
			$.ajax({
				url: 'ajax.php',
				data: {
					action: 'delete',
					path: $row.data('relPath')
				},
				dataType: 'json',
				type: 'POST',
				success: function(response) {

					//	Check
					if(response.success) {

						//	Fadeout
						$row.fadeOut(function() {

							//	Remove
							$(this).remove();
						});
					}
				}
			});
		});

		//	Prevent Default
		e.preventDefault();
		return false;
	});

	//	Listen for Rename File/Folder
	$(".folder-scanned-data").on('click', ".rename-file", function(e) {

		//	Row
		var $row = $(this).parent().parent().parent().parent();

		//	Prompt for New Name
		customPrompt('Rename ' + capilatize($row.data('ftype')), 'Please enter the new name for the ' + $row.data('ftype') + ": " + $row.data('name'), $row.data('name'), function(newName) {

			//	Run Ajax
			$.ajax({
				url: 'ajax.php',
				data: {
					action: 'rename',
					path: $row.data('relPath'),
					newName: newName
				},
				dataType: 'json',
				type: 'POST',
				success: function(response) {

					//	Check
					if(response.success) {

						//	Compile
						var newHTML = siTemplate(response.info);

						//	Replace
						$row.replaceWith(newHTML);
					}
				}
			});
		});

		//	Prevent Default
		e.preventDefault();
		return false;
	});

	//	Listen for Change Permission for File/Folder
	$(".folder-scanned-data").on('click', ".change-file-permission", function(e) {

		//	Row
		var $row = $(this).parent().parent().parent().parent();

		//	Prompt for New Name
		customPrompt('Change Permission for ' + capilatize($row.data('ftype')), 'Please enter the new permission for the ' + $row.data('ftype') + ": " + $row.data('name'), $row.data('permission'), function(newPermission) {

			//	Run Ajax
			$.ajax({
				url: 'ajax.php',
				data: {
					action: 'change_permission',
					path: $row.data('relPath'),
					newPermission: newPermission
				},
				dataType: 'json',
				type: 'POST',
				success: function(response) {

					//	Check
					if(response.success) {

						//	Compile
						var newHTML = siTemplate(response.info);

						//	Replace
						$row.replaceWith(newHTML);
					}
				}
			});
		});

		//	Prevent Default
		e.preventDefault();
		return false;
	});

	//	Listen for Change Owner for File/Folder
	$(".folder-scanned-data").on('click', ".change-file-owner", function(e) {

		//	Row
		var $row = $(this).parent().parent().parent().parent();

		//	Prompt for New Name
		customPrompt('Change Owner for ' + capilatize($row.data('ftype')), 'Please enter the new owner for the ' + $row.data('ftype') + ": " + $row.data('name'), $row.data('owner'), function(newOwner) {

			//	Run Ajax
			$.ajax({
				url: 'ajax.php',
				data: {
					action: 'change_owner',
					path: $row.data('relPath'),
					newOwner: newOwner
				},
				dataType: 'json',
				type: 'POST',
				success: function(response) {

					//	Check
					if(response.success) {

						//	Compile
						var newHTML = siTemplate(response.info);

						//	Replace
						$row.replaceWith(newHTML);
					}
				}
			});
		});

		//	Prevent Default
		e.preventDefault();
		return false;
	});
});

//	Custom Confirm
function customConfirm(title, title2, confirmed, unconfirmed, pType) {

	//	Check
	if(!pType || pType == undefined)	pType = 'warning';

	//	Return
	return (new PNotify({
	    title: title,
	    text: title2,
	    icon: 'glyphicon glyphicon-question-sign',
	    hide: false,
	    type: pType,
	    killer: true,
	    confirm: {
	        confirm: true
	    },
	    buttons: {
	        closer: false,
	        sticker: false
	    },
	    history: {
	        history: false
	    }
	})).get().on('pnotify.confirm', function() {

		//	Check
		if(typeof confirmed == 'function') {

			//	Callback
			confirmed.apply(this);
		}
	}).on('pnotify.cancel', function() {

		//	Check
		if(typeof unconfirmed == 'function') {

			//	Callback
			unconfirmed.apply(this);
		}
	});
}

//	Custom Prompt
function customPrompt(title, title2, defVal, confirmed, unconfirmed, pType) {

	//	Check
	if(!pType || pType == undefined)	pType = 'info';

	//	Return
	return (new PNotify({
	    title: title,
	    text: title2,
	    icon: 'glyphicon glyphicon-question-sign',
	    hide: false,
	    type: pType,
	    killer: true,
	    confirm: {
	        prompt: true,
	        prompt_default: defVal
	    },
	    buttons: {
	        closer: false,
	        sticker: false
	    },
	    history: {
	        history: false
	    }
	})).get().on('pnotify.confirm', function(e, notice, val) {

		//	Check
		if(val && val != undefined && val != ''
			&& typeof confirmed == 'function') {

			//	Check
			if(defVal != val) {

				//	Callback
				confirmed.apply(this, [val]);
			}
		} else {

			//	Check
			if(typeof unconfirmed == 'function') {

				//	Callback
				unconfirmed.apply(this);
			}
		}
	}).on('pnotify.cancel', function(e, notice) {

		//	Check
		if(typeof unconfirmed == 'function') {

			//	Callback
			unconfirmed.apply(this);
		}
	});
}

//	Capitalize
function capilatize(str) {
	return String(str.substr(0, 1)).toUpperCase() + str.substr(1);
}

//	Populate Scanned Data
function populateScannedData(data) {

	//	Output HTML
	var outputHTML = '';

	//	Loop Each
	for(var i in data) {

		//	Compile and Append
		outputHTML += siTemplate(data[i]);
	}

	//	Set Contents
	jQuery(".folder-scanned-data").html(outputHTML);
}

//	Display Sections
function displayRespectiveSections(hasData) {

	//	Check Has Data
	hasData = !!hasData;

	//	Hide the Sections
	jQuery(".folder-scanned-data").stop(true, true).slideUp('slow');
	jQuery(".no-folder-scanned-data").stop(true, true).slideUp('slow');

	//	Check
	if(!hasData) {

		//	Set the Value
		//jQuery(".folder-scanner-form .folder-path").val(CURRENT_PATH);
	}

	//	Check
	if(hasData) {

		//	Show Scanned Data
		jQuery(".folder-scanned-data").stop(true, true).slideDown('slow');
	} else {

		//	Show No Data
		jQuery(".no-folder-scanned-data").stop(true, true).slideDown('slow');
	}
}