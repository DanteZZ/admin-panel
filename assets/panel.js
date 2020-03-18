function sendToHandler(action, data, type) {
	if (type === undefined) {
		type = 'json';
	}
	res = $.ajax({
		url: '/panel-handler/' + action,
		method: 'post',
		data: data,
		async: false,
		dataType: type
	});

	if (type == 'json') {
		return res.responseJSON;
	} else {
		return res.responseText;
	}
}

/**
 * @param {String} action Название хэндлера
 * @param {Object} data Передаваемые данные
 * @param {*} type Тип данных
 * @param {*} success Каллбэк. Если не указан, то возвращается промис
 */
function sendToHandlerAsync(action, data, type, success) {
	if (type === undefined) {
		type = 'json';
	}

	if (!success) {
		return new Promise((resolve, reject) => {
			$.ajax({
				url: '/panel-handler/' + action,
				method: 'post',
				data: data,
				async: true,
				dataType: type,
				success: function(res, err) {
					resolve(res);
					reject(err);
				}
			});
		});
	}

	$.ajax({
		url: '/panel-handler/' + action,
		method: 'post',
		data: data,
		async: true,
		dataType: type,
		success: function(res) {
			success(res);
		}
	});
}

function responsive_filemanager_callback(field_id, el) {
	$('#' + field_id).change();
}

$(document).ready(function() {
	$('#select_panel_locale').change(function(el) {
		var val = this.value;
		$.ajax({
			url: '.',
			method: 'post',
			data: { set_panel_locale: val },
			async: false
		});
		location.reload();
	});
});
