jQuery(function () {
	jQuery("[id^='productVisualBlock']").click(function () {
		var blockId = jQuery(this).attr('id');			
		var blockId = blockId.substring(blockId.indexOf('_')+1);
		jQuery("[id^='mainVisualBlock']").hide();
		jQuery('#mainVisualBlock_'+blockId).show();
		return false;
	});
});