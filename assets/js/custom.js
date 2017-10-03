$("#Country").change(function(){
	if($(this).prop('selected', true)){
		$("#State").prop("disabled",false);
	}
});

 