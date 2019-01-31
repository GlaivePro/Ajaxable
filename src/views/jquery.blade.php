<script>
if (null == xCsrfToken) 
	var xCsrfToken = $('meta[name="csrf-token"]').attr('content');

$(document).on('change', '.ajaxable-new-attribute', function() {
	var attribute = $(this);
	var creator = $(attribute.data('creator'));
	
	creator.data('attribute_' + attribute.data('key'), attribute.val());
});

$(document).on('click', '.ajaxable-creator', function() {
	var button = $(this);
	button.prop('disabled', true);
	
	var inputs = $('input, select').filter(function(index, element) {
		return $($(element).data('creator')).is(button);
	});
	
	inputs.find('.error-block').remove();
	inputs.closest('.form-group').removeClass('has-error');

	var positionForNewRow = 'last';
	if ('first' == button.data('ajaxable-list-position'))
		positionForNewRow = 'first';

	var scroll = false;
	if (button.data('ajaxable-scroll'))
		scroll = true;
	
	var data = {
		model: button.data().model,
		attributes: {}
	};
	
	if (button.data('ajaxable-list'))
		data.view = true;

	for (var key in button.data()) 
		if (0 === key.indexOf('attribute_'))
			data.attributes[key.substr(10)] = button.data(key);
	
	$.ajax({
		url: "{{route('ajaxable.create')}}",
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function(request) {
			request.setRequestHeader("X-CSRF-TOKEN", xCsrfToken);
		},
		success: function(response) {
			if (1 == response['success'])
			{
				button.prop('disabled', false);

				if (response['view'])
				{
					var newRow = $(response['view']);
					
					var list = $(button.data('ajaxable-list'));
		
					if ('first' == button.data('ajaxable-list-position'))
						list.prepend(newRow);
					else
						list.append(newRow);
					
					inputs.val('');
					
					if (button.data('ajaxable-scroll'))
						newRow.get(0).scrollIntoView();
					
					newRow.addClass('ajaxable-highlight').delay(1500).queue(function(){
						$(this).removeClass('ajaxable-highlight').dequeue();
					});
				}
			}
		},
		error: function(rawResponse) {
			var response = $.parseJSON(rawResponse.responseText);
			
			$.each(response['errors'], function (field, error) {
				var input = inputs.filter('[data-key="' + field + '"]')
				
				var formGroup = input.closest('.form-group');
				if (formGroup.length)
				{
					errorBlock = $('<span></span>');
					errorBlock.addClass('error-block').addClass('help-block');
					errorBlock.html(error);
					errorBlock.appendTo(formGroup);
					formGroup.addClass('has-error');
				}
				else
					alert(error); 
			});
			
			button.prop('disabled', false);
		}
	});
});

$(document).on('click', '.ajaxable-delete', function() {
	var button = $(this);
	button.prop('disabled', true);
	
	var test = confirm('Remove this entry?');
	if (!test)
	{
		button.prop('disabled', false);
		return false;
	}
	
	var row = button.closest('.ajaxable-row');
	
	var data = {
		model: button.data('model'),
		id: button.data('id')
	};
	
	$.ajax({
		url: "{{route('ajaxable.delete')}}",
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function(request) {
			request.setRequestHeader("X-CSRF-TOKEN", xCsrfToken);
		},
		success: function(response) {
			if (1 == response['success'])
				row.remove();
		}
	});
});

$(document).on('click', '.ajaxable-remove-media', function() {
	var button = $(this);
	button.prop('disabled', true);
	
	var test = confirm('Remove this entry?');
	if (!test)
	{
		button.prop('disabled', false);
		return false;
	}
	
	var row = button.closest('.ajaxable-row');
	
	var data = {
		model: button.data('model'),
		id: button.data('id'),
		media_id: button.data('media_id')
	};
	
	$.ajax({
		url: "{{route('ajaxable.deleteMedia')}}",
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function(request) {
			request.setRequestHeader("X-CSRF-TOKEN", xCsrfToken);
		},
		success: function(response) {
			if (1 == response['success'])
				row.remove();
		}
	});
});

$(document).on('change', '.ajaxable-edit', function() {
	var field = $(this);
	field.prop('disabled', true);
	
	var formGroup = field.closest('.form-group');
	if (formGroup.length)
	{
		formGroup.removeClass('has-error');
		formGroup.find('.error-block').remove();
	}
	
	value = field.val();
	if ('checkbox'  == field.prop('type'))
		value = field.is(':checked') ? 1 : 0;
	
	var data = {
		model: field.data('model'),
		id: field.data('id'),
		key: field.data('key'),
		value: value
	};
	
	$.ajax({
		url: "{{route('ajaxable.update')}}",
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function(request) {
			request.setRequestHeader("X-CSRF-TOKEN", xCsrfToken);
		},
		success: function(response) {
			if (1 == response['success'])
				field.prop('disabled', false);
		},
		error: function(rawResponse) {
			var response = $.parseJSON(rawResponse.responseText);
			
			var error = response['errors']['val'][0];
			
			if (formGroup.length)
			{
				errorBlock = $('<span></span>');
				errorBlock.addClass('error-block').addClass('help-block');
				errorBlock.html(error);
				errorBlock.appendTo(formGroup);
				formGroup.addClass('has-error');
			}
			else
				alert(error);
			
			field.prop('disabled', false);
		}
	});
});
</script>
