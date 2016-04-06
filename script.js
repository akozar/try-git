$(document).ready(function() {
	var timerDeviceTab1, timerDeviceTab2;
	/*
	Expanding order history
	$("#orderHistory tr").click(function(event){
		event.stopPropagation();
		if ($(this).next().hasClass('order-info')){
			$(this).next().toggle("fast");
		} else {
		var el = $(this)
		.closest('tr')
		.after("<tr class='info order-info'><td colspan='3'>Список списанных материалов:</td></tr>");
		el.next().hide().fadeIn();
		}
	})

	*/

	function delete_cookie(name) {
		document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
	}

	function showTab(numTab) {
		$("#tabMenu").nextAll().hide();
		$($("#tabMenu").nextAll()[numTab]).show();
		delete_cookie('numTab');
		document.cookie = "numTab=" + numTab;
	}

	$("#materialToListBtn").click(function(event) {
		event.preventDefault();
		var sortName = $("#sortPicker option:selected").val();
		var material = $("#addMaterialPicker option:selected").val();
		var quantity, unit;
		if ( $('#quantity').closest('.form-group').hasClass('hidden') ) {

			// check if table has this element
			if ( $('#deviceMaterialsTbl td').filter(function(){
				return $(this).text() == sortName;
			}).next().filter(function(){
				return $(this).text() == material;
			}).length !== 0 ) {
				$('#unitChooserDevTab').prop("disabled", true);
			}	else {
				$('#unitChooserDevTab').prop("disabled", false);
			}	
			quantity = $("#setQuantityDevTab").val();
			unit = $('#unitChooserDevTab').val();
			$("#setQuantityDevTab").val('');
			$('#unitChooserDevTab').prop("disabled", true);
		} else {
			quantity = $("#quantity").val();
			unit = $('#measure').html();
			$("#quantity").val('');
		}
		console.log('#materialToListBtn fired', quantity, sortName, material, unit);
		if (quantity && sortName && material && unit) {
			// Adding entered values to table
			addMaterialToTable(sortName, material, quantity, unit);			
		} else {
			// Output an error
			console.log('error');
		}
		console.log(sortName, quantity);
	});

	function addMaterialToTable(sortName, material, quantity, unit) {
		var sortName = $('#addNewSortName').val() || $('#sortPicker').val();
		var material = $('#addNewMaterialName').val() || $('#addMaterialPicker').val();		
		console.log('#addMaterialToTable fired', sortName, material);
		var tableRow = $('<tr><td class="text-center"><a href="#"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></td><td>'+sortName+'</td><td>'+material+'</td><td>'+quantity+' '+unit+'</td></tr>');
		$(tableRow).appendTo($("#materialPanelForDevice tbody"));
		// Add event handler for delete button
		$(tableRow).find('a').click(function(){
			event.preventDefault();		
			// Hide table if this row is the last one
			if ($("#materialPanelForDevice tbody tr").length===2){
				$('#materialPanelForDevice').addClass('hidden');
			}
			// Deleting Row
			$(tableRow).remove();

			if ( $('#deviceMaterialsTbl td').filter(function(){
				return $(this).text() == sortName;
			}).next().filter(function(){
				return $(this).text() == material;
			}).length !== 0 ) {
				$('#unitChooserDevTab').prop("disabled", true);
			} else {
				$('#unitChooserDevTab').prop("disabled", false);
			};

		});
		$('#materialPanelForDevice').removeClass('hidden');
	}

	var numTab = document.cookie.replace(/(?:(?:^|.*;\s*)numTab\s*\=\s*([^;]*).*$)|^.*$/, "$1");
	if (!numTab) {
		showTab(0);
	} else {
		showTab(numTab);
		$("ul li").removeClass("active");
		$($("ul li")[numTab]).addClass("active");
	}
	var numTab;

	$('#tabMenu li a').click(function(event) {
		event.preventDefault();
		$('#tabMenu li ').removeClass('active');
		numTab = $('#tabMenu li a').index(this);


		$(this).parent().addClass('active');
		showTab(numTab);
	});

	function setCookie() {
		document.cookie = "numTab=" + numTab;
	}
	window.addEventListener('beforeunload', setCookie, false);
	window.addEventListener('unload', setCookie, false);
	function setDisabledState(el){
		$("#changeMaterialCaption").fadeOut(400,function(){					
			$(this).html('Добавление нового сортамента').fadeIn();
		});
		// Поставить режим добавления нового сортамента при пустой таблице
		$('#currentQuantity').attr('placeholder', 'Сейчас: 0');
		$('#currentCriticalValue').attr('placeholder', 'Сейчас: 0');
		
	}
	// on input handler on newMaterialInput
	function disableSelectOnEl(el, select) {
		el.addEventListener("input", function (){
			var existStatus = $(select).parent().next().children()[0];
			var newInputStatus = $(el).parent().next().children()[0];
			if (el.value.length>0){
				if ($('#changeMaterialCaption').text != 'Добавление нового сортамента'){
					$("#changeMaterialCaption").fadeOut(400,function(){					
						$(this).html('Добавление нового сортамента').fadeIn();
					});
				}
				$('#currentQuantity').attr('placeholder', 'Сейчас: 0');
				$('#currentCriticalValue').attr('placeholder', 'Сейчас: 0');				
				$(select).prop('disabled',true);
				$(el).closest('.form-group').addClass('has-success');				
				$(newInputStatus).removeClass('red').addClass('green');
				$($(newInputStatus).children()[0]).removeClass('glyphicon-remove').addClass('glyphicon-ok');
				$(existStatus).removeClass('green').addClass('red');
				$($(existStatus).children()[0]).removeClass('glyphicon-ok').addClass('glyphicon-remove');				
				$("#createMode").removeClass('hidden');
				$("#editMode").addClass('hidden');
			} else {
				$(select).prop('disabled',false);
				$(el).closest('.form-group').removeClass('has-success');
				$(newInputStatus).removeClass('green').addClass('red');
				$($(newInputStatus).children()[0]).removeClass('glyphicon-ok').addClass('glyphicon-remove');				
				$(existStatus).removeClass('red').addClass('green');
				$($(existStatus).children()[0]).removeClass('glyphicon-remove').addClass('glyphicon-ok');
				$("#createMode").addClass('hidden');
				$("#editMode").removeClass('hidden');				
				initApp();
			}

		}, false);
	};
	disableSelectOnEl(document.getElementById('newMaterial'),document.getElementById('materialChooser'));
	disableSelectOnEl(document.getElementById('newAssortment'),document.getElementById('assortChooser'));	

	$('#addDevice').click(function() {
		event.preventDefault();
		if ($('#materialPanelForDevice').hasClass('hidden')) {
			alert('Вы не добавили ни одного элемента к станку');
		} else if ($('#deviceName').val()===''){
			alert('Вы не ввели название станка');
		} else {
			$('#addDeviceModal .modal-body').html('<br>');
			$('#materialPanelForDevice').clone().removeAttr('id').appendTo('#addDeviceModal .modal-body');
			$('<p class="bg-primary" style="padding: 5px 10px 5px 10px">Примечание:<br>Элементы, которых в данный момент не существует в базе данных, будут добавлены в БД со стандартными значениями. Вы можете их изменить на вкладке "Операции со складом".</p>').appendTo('#addDeviceModal .modal-body');
			$('#addDeviceModal').modal('toggle');
			$('#addDeviceModal .btn-primary').unbind("click");
			$('#addDeviceModal .btn-primary').click(function(){
				//Add device to MySQL table and clear table if success
				var device = {
					deviceName: '',
					contains: []
					};
				device.deviceName = $('#deviceName').val();
				var tableRows = $('#materialPanelForDevice #deviceMaterialsTbl tr');

				for (var i = 1; i < tableRows.length; i++) {
					var numberPattern = /\d+/g;
					var unitPattern = /\S*$/g;
					var unit;
					tableData = $(tableRows[i]).find('td');					
					assort = $(tableData[1]).text();
					material = $(tableData[2]).text();					
					quantity = $(tableData[3]).text().match(numberPattern).join('');
					unit = $(tableData[3]).text().match(unitPattern).join('');		
					var currentElement  = {
						assort: assort,
						material: material,
						quantity: quantity,
						unit: unit						
					};
					device.contains.push(currentElement);
				}

				console.log(JSON.stringify(device));

				// SEND POST REQUEST 
				$.ajax({
					method: "POST",
					url: "update_devices.php",
					data: {device: JSON.stringify(device)},
					dataType: "text",
					success: function (data) {
						console.log('otvet servera', data);
					}
				});

				// Reload page after timeout
				window.setTimeout(function(){
					document.location.reload(true);
				},500);
			})
		};



	});

	// CHECKING FORM INPUTS 

	$('.numbers-only').keyup(function() {
		this.value = this.value.replace(/[^\+\-0-9\.]/g, '');
	});

	$.getJSON("storagedb.json", function(json) {
    console.log(json); // this will show the info it in firebug console
	});
	$('select#item-name3').on('change',function(){
		console.log('fired')
	});

	function showObjOnAddDeviceTab(assortment, material){
		console.log('fired');
		$.post("quantity.php",{ assortment: assortment, material:  material}).done(function(data){			
			var obj = $.parseJSON(data);
			console.log(obj);
			if (obj.exist) {
				$('#disabledInput').attr('placeholder', 'Сейчас: '+obj.quantity);
				$('#measure').html(obj.unit);
				$("#setQuantityDevTab").closest('.form-group').addClass('hidden');
				$("#quantity").closest('.form-group').removeClass('hidden');
			} else {
				$('#disabledInput').attr('placeholder', 'ОШИБКА');
				$('#measure').html('ERR');
				$("#setQuantityDevTab").closest('.form-group').removeClass('hidden');
				$("#quantity").closest('.form-group').addClass('hidden');
			}
		}, "json");
	};
	$('#sellItemPicker').change(function(){
		var deviceName = $(this).val();
		console.log('#sellItemPicker fired');
		$.ajax({
			method: "POST",
			dataType: "text",
			data: {deviceName: JSON.stringify(deviceName)},
			url: "getItemDataHtml.php",
			success: function (data){
				$('#item-sell .warn-block').html(data);
			}
		});
	});
	$("#sortPicker").change(function(){
		var sortName = $("#sortPicker option:selected").val();
		var material = $("#addMaterialPicker option:selected").val();
		console.log('#sortPicker fired', sortName, material);
		if ( $('#deviceMaterialsTbl td').filter(function(){
			return $(this).text() == sortName;
		}).next().filter(function(){
			return $(this).text() == material;
		}).length !== 0 ) {
			$('#unitChooserDevTab').prop("disabled", true);
		}	else {
			$('#unitChooserDevTab').prop("disabled", false);
		}	
		showObjOnAddDeviceTab($("#sortPicker").val(), $("#addMaterialPicker").val());
	});

	$("#addMaterialPicker").change(function(){
		var sortName = $("#sortPicker option:selected").val();
		var material = $("#addMaterialPicker option:selected").val();
		console.log('#addMaterialPicker fired', sortName, material);
		if ( $('#deviceMaterialsTbl td').filter(function(){
			return $(this).text() == sortName;
		}).next().filter(function(){
			return $(this).text() == material;
		}).length !== 0 ) {
			$('#unitChooserDevTab').prop("disabled", true);
		} else {
			$('#unitChooserDevTab').prop("disabled", false);
		}
		showObjOnAddDeviceTab($("#sortPicker").val(), $("#addMaterialPicker").val());
	});

	function showObjOnInvTab(assortment, material){
		$.post("quantity.php",{ assortment: assortment, material:  material}).done(function(data){			
			var obj = $.parseJSON(data);
			$('#currentQuantity').attr('placeholder', 'Сейчас: '+obj.quantity);
			if (obj.criticalValue!==null) {
				$('#currentCriticalValue').attr('placeholder', 'Сейчас: '+obj.criticalValue);
			} else {
				$('#currentCriticalValue').attr('placeholder', 'Сейчас: значение отсутствует');
			}


			$('.unitstatus').html(obj.unit);
			if (obj.exist) {
				$("#changeMaterialCaption").fadeOut(400,function(){
					$(this).html('Изменение текущего сортамента').fadeIn();
				});
				$("#createMode").addClass('hidden');					
				$("#editMode").removeClass('hidden');	
			} else {
				$("#changeMaterialCaption").fadeOut(400,function(){					
					$(this).html('Добавление нового сортамента').fadeIn();
				});
				$("#editMode").addClass('hidden');				
				$("#createMode").removeClass('hidden');	
			}
		}, "json");
	};

	$(document).on('change', '#assortChooser', function (event) {
		if (!$('#assortChooser').prop('disabled') && !$('#materialChooser').prop('disabled')){
    		showObjOnInvTab($('#assortChooser').val(), $('#materialChooser').val());
   	}
	});
	$(document).on('change', '#materialChooser', function (event) {
		if (!$('#assortChooser').prop('disabled') && !$('#materialChooser').prop('disabled')){  	
 			showObjOnInvTab($('#assortChooser').val(), $('#materialChooser').val());    	
 		}
	});
	$(document).on('input', '#addNewSortName', function (event) {
		var assort = $('#addNewSortName').val() || $('#sortPicker').val();
		var material = $('#addNewMaterialName').val() || $('#addMaterialPicker').val();		
		if ($('#addNewSortName').val()!==''){  	
 			$('#addNewSortName').closest('.form-group').addClass('has-success');
 			$('#sortPicker').closest('.form-group').removeClass('has-success'); 		
 			// timeout func to show obj info
			if (timerDeviceTab1) {clearTimeout(timerDeviceTab1)}
			
			timerDeviceTab1 = setTimeout(function(){
				showObjOnAddDeviceTab(assort, material);
			}, 500);		
 		} else {
 			$('#addNewSortName').closest('.form-group').removeClass('has-success');
 			$('#sortPicker').closest('.form-group').addClass('has-success');

			if ($('#addNewMaterialName').val()==''){
				showObjOnAddDeviceTab($("#sortPicker").val(), $("#addMaterialPicker").val());
			} else {
				showObjOnAddDeviceTab($('#addNewSortName').val(), $("#addNewMaterialName").val());
			};
 		};

		if ( $('#deviceMaterialsTbl td').filter(function(){
			return $(this).text() == assort;
		}).next().filter(function(){
			return $(this).text() == material;
		}).length !== 0 ) {
			$('#unitChooserDevTab').prop("disabled", true);
		}	else {
			$('#unitChooserDevTab').prop("disabled", false);
		};	

	});	
	$(document).on('input', '#addNewMaterialName', function (event) {
		var assort = $('#addNewSortName').val() || $('#sortPicker').val();
		var material = $('#addNewMaterialName').val() || $('#addMaterialPicker').val();
		if ($('#addNewMaterialName').val()!==''){  	
 			$('#addNewMaterialName').closest('.form-group').addClass('has-success');
 			$('#addMaterialPicker').closest('.form-group').removeClass('has-success'); 
 			// timeout func to show obj info

			if (timerDeviceTab2) {clearTimeout(timerDeviceTab2)}
			
			timerDeviceTab2 = setTimeout(function(){
				showObjOnAddDeviceTab(assort, material);
			}, 500);				
 		} else {
 			$('#addNewMaterialName').closest('.form-group').removeClass('has-success');
 			$('#addMaterialPicker').closest('.form-group').addClass('has-success'); 	 

			if ($('#addNewSortName').val()==''){
				showObjOnAddDeviceTab($("#sortPicker").val(), $("#addMaterialPicker").val());
			} else {
				showObjOnAddDeviceTab($('#addNewSortName').val(), $("#addMaterialPicker").val());
			}
 		};

		if ( $('#deviceMaterialsTbl td').filter(function(){
			return $(this).text() == assort;
		}).next().filter(function(){
			return $(this).text() == material;
		}).length !== 0 ) {
			$('#unitChooserDevTab').prop("disabled", true);
		}	else {
			$('#unitChooserDevTab').prop("disabled", false);
		};	

	});		
	// Below a function that checks a form regarding of its name.	
	function submitForm(formName){
		switch (formName) {
			case "changeMaterial":
				var assortment = "", material="", quantity = 0, criticalValue = 0, unit = 'мм';
				var editMode = false;

				if ($("#changeMaterialCaption").text() == 'Изменение текущего сортамента'){
					editMode = true;
				} else {
					editMode = false;
				}

				if ($("#assortChooser").attr("disabled")){
					assortment = $("#newAssortment").val();
				} else {
					assortment = $("#assortChooser option:selected").text();
				}
				console.log(assortment);
				if ($("#materialChooser").attr("disabled")){
					material = $("#newMaterial").val();
				} else {
					material = $("#materialChooser option:selected").text();
				}				

				if (assortment == "" & material == ""){
					alert ('Не ввёден материал либо сортамент');
					return;					
				}
				console.log(assortment, material);
				if (editMode) {
					quantity = $('#changeQuantity').val(); // can be blank, with + or -
					quantity = quantity.replace(/\+/g, '');
					quantity = quantity.replace(/,/g, '.');
					criticalValue = $('#changeCriticalValue').val(); // can be a positive number
				} else {					
					quantity = $('#setQuantity').val();
					criticalValue = $('#setCriticalValue').val(); // can be a positive number
					unit = $('#unitChooser option:selected').text();
				}

				if (!quantity) {
					alert ('Введите количество!');
					return;
				}

				if (quantity && assortment && material) {
					$.ajax({
					type: "POST",
					url: "submitform.php",
					data: {formName:formName, editMode: editMode, assortment: assortment, material: material, quantity: quantity, criticalValue: criticalValue, unit: unit},
					dataType: "text",
					success: function (data){

						document.location.reload(true);
					}});
				} else {
					alert ('Введены не все поля');
				}

				break;
			default:
				throw Error('Form name handler is not set.');
		}
	}



	function initApp(){
		showObjOnInvTab($('#assortChooser').val(), $('#materialChooser').val());
		showObjOnAddDeviceTab($("#sortPicker").val(), $("#addMaterialPicker").val());

	}
	$("form[name] button[type='submit']").click(function(){
		event.preventDefault();
		submitFormName = $(this).closest('form[name]').attr('name');
		if (submitFormName) {
			submitForm(submitFormName);
		} else {
			throw Error('Form name is not set.');
		}
	});
	$("#comp-avail table span.glyphicon-trash").click(function(){
		var assortment = $(this).closest('td').text();
		var material = $(this).closest('td').next().text();
		$('#deleteItemModal .modal-body p').html("Вы действительно желате удалить сортамент <strong>"+assortment+"</strong> материала <strong>"+ material +"</strong>?");
		$('#deleteItemModal').modal('show');
		$('#deleteItemModal button.btn-danger').click(function(){			
			deleteRequest(assortment,material);
		});		
	});
	$("#comp-avail table span.glyphicon-pencil").click(function(){
		var assortment = $(this).closest('td').text();
		var material = $(this).closest('td').next().text();
		$($('#tabMenu li a')[3]).click();
		$('#assortChooser').val(assortment);
		$('#materialChooser').val(material);		
		showObjOnInvTab(assortment, material);
	});	
	if ($("#assortChooser option")){

	}
	function deleteRequest(assortment, material){
		$.ajax({
		type: "POST",
		url: "deleteitem.php",
		data: {assortment: assortment, material: material},
		dataType: "text",
		success: function(){
			document.location.reload(true);		
		}
		});
	}

// 	autocomplete on deviceName
	$("#deviceName").autocomplete({
		source: 'search_devices.php',
		change: function (event, ui) {
			var deviceName = $(this).val();
			console.log('change event on autocomplete: Performing an AJAX request on ', deviceName);		
			$.ajax({
			type: "POST",
			url: "getdevice.php",
			data: {deviceName: deviceName},
			dataType: "text",
			success: function(data){
				setTableOnDeviceTab($.parseJSON(data));				
				console.log('otvet servera: ', data);
			}
			});				
		}
	});

	$("#deviceName").change(function(){
		var deviceName = $(this).val();
		console.log('change event on def change: Performing an AJAX request on ', deviceName);		
		$.ajax({
		type: "POST",
		url: "getdevice.php",
		data: {deviceName: deviceName},
		dataType: "text",
		success: function(data){
			setTableOnDeviceTab($.parseJSON(data));				
			console.log('otvet servera: ', data);
		}
		});			
	})

// 	show existing scheme on deviceName's change
	function setTableOnDeviceTab (data) {
/*otvet servera:  [["Станок №1 тестовый","Сталь 30","Круг 20","360"],
				["Станок №1 тестовый","Сталь 50","Круг 20","444"],
				["Станок №1 тестовый","Сталь 50","Тестовый сортамент","333"]]	
*/
		if (data) {
			// maybe alert "do you wanna replace materials table?"
			$('#materialPanelForDevice').removeClass('hidden');
			$('#deviceMaterialsTbl tr:has(td)').remove();
			$('#add-machine .add-machine-title').text('Изменение станка');
			for (var i = 0; i<data.length; i++) {
				addMaterialToTable(data[i][2], data[i][1], data[i][3], data[i][4]);	
			}
		} else {
			$('#add-machine .add-machine-title').text('Добавление нового станка');
		};
	}

	$('#clearMaterialTable').on('click', function (){
		$('#deviceMaterialsTbl tr:has(td)').remove();
	});

	$('#item-name').autocomplete({
		source: 'search_devices.php',
		change: function (event, ui) {
			var deviceName = $(this).val();
			console.log('change event on autocomplete: Performing an AJAX request on ', deviceName);		
			$.ajax({
			type: "POST",
			url: "getdevice.php",
			data: {deviceName: deviceName},
			dataType: "json",
			success: function(data){
				$warnBlock = $('#order-material .warn-block');
				console.log(data);
				if (data){				

					if ($warnBlock.length==0){
						// create warn-block
						var $warnBlParent = $('#item-name').closest('.form-group').next().find('.text-right');
						$warnBlock = $('<div>',{class: 'warn-block'});
						$warnBlParent.append($warnBlock);
					} else {
						// clear warn-block
						$warnBlock.empty();
					}

					// iterate through data	
					for (var i = 0; i < data.length; i++){
						$warnBlock.append('<span><strong>' + data[i][3] + ' ' + data[i][4] + ' [' + data[i][5] 
						+ ' ' + data[i][4] + ' на складе] x ' + data[i][2] + ' [' + data[i][1] +']</span><br>');
					}	

				} else if (!data && $warnBlock.length==1){
					$warnBlock.remove();
				}


			}
			});				
		}
	});


	initApp();
});