jQuery(function($){
	function toggleCheckboxValue(value){
		if (value == "YES") { value = "NO"; }
		else { value = "YES"; }
		return value;
	}
	$(document).on( 'click', '.image_remove', function(event){
		event.preventDefault();
		$(this).parents('.form-group').children('#image-url').val('');
		$(this).parents('.previewWrapper').children('.image_preview').html('');
		$(this).remove();
	});
	$(document).on( 'change', '.formCheckbox', function(event){
		event.preventDefault();
		var currentValue = $(this).parents('.form-group').children('.formCheckboxHiddenField').val();
		var changedValue = toggleCheckboxValue(currentValue);
		$(this).parents('.form-group').children('.formCheckboxHiddenField').val(changedValue);
	});
});