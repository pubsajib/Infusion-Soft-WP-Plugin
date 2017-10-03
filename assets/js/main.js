jQuery(function($) {
	$("#edit_setting").click(function(){
		$(this).addClass("hidden");
		$(".setting-save").removeClass("hidden");
		$("#Configaration").find(".form-control").removeAttr( "readonly");
	});
});

