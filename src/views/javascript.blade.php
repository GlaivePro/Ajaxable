<script>
var token = $('meta[name="csrf-token"]').attr('content');

$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': token
	}
});

var ajaxableNewAttributes = $('.ajaxable-new-attribute');

ajaxableNewAttributes.change(function() {
	var attribute = $(this);
	var creator = $('#' + attribute.data('creator'));
	
	creator.data(attribute.data('key'), attribute.val());
});

var ajaxableCreators = $('.ajaxable-creator');

var list = $('#list-container');

ajaxableCreators.click(function() {
	var button = $(this);
	button.prop('disabled', true);
	
	var inputs = $('input[data-key=' + field + '][data-creator=' + button.attr('id') + ']');
	
	inputs.find('.error-block').remove();
	inputs.closest('.form-group').removeClass('has-error');
	
	$.ajax({
		url: "{{route('ajaxable.create')}}",
		type: 'post',
		dataType: 'json',
		data: button.data(),
		success: function(response) {
			if (1 == response['success'])
			{
				var newRow = $(response['row']);
				
				button.prop('disabled', false);
				
				var list = $('#' + button.data('ajaxable-list'));
				
				list.append(newRow);
				
				newRow.get(0).scrollIntoView();
				newRow.addClass('info').delay(1500).queue(function(){
					$(this).removeClass('info').dequeue();
				});
			}
		},
		error: function(rawResponse) {
			var response = $.parseJSON(rawResponse.responseText);
			
			$.each(response['errors'], function (field, error) {
				var input = $('input[data-key=' + field + '][data-creator=' + button.attr('id') + ']');
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

$('body').on('click', '.ajaxable-delete', function() {
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
	
	$.post(
		"{{route('ajaxable.delete')}}",
		data,
		function(response) {
			if (1 == response['success'])
				row.remove();
		}
	);
});

$('body').on('change', '.ajaxable-edit', function() {
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
		val: value
	};
	
	$.ajax({
		url: "{{route('ajaxable.update')}}",
		type: 'post',
		dataType: 'json',
		data: data,
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

$('body').on('click', '.ajaxable-control', function() {
	var button = $(this);
	button.prop('disabled', true);
	
	var action = button.data('action');
	var id = button.data('id');
	var model = button.data('model')
	
	var data = {
		model: model,
		action: action,
		id: id
	};
	
	$.post(
		"{{route('ajaxable.control')}}",
		data,
		function(response) {
			if (1 == response['success'])
			{
				if ('toggle' == action)
				{
					$('.ajaxable-control[data-action="toggle"][data-model=' + model + '][data-id=' + id + ']').toggleClass('hidden');
					button.prop('disabled', false);
				}
				else
				{
					var list = $('#' + button.data('ajaxable-list'));
					
					list.html(response['list']);
				}
			}
		}
	);
});
</script>
