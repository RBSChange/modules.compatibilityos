function ecomextended_complete(field, fieldName, lang, pageId) {
	var limit = 30;
	var opField = document.getElementById('ecomextended_op');
	var op = (opField == null) ? "AND" : opField.options[opField.selectedIndex].value;
	var url = '/index.php?module=ecomextended&action=Complete&lang='+lang+'&pageId='+pageId+'&op='+op+'&limit='+limit;
	if (fieldName != null) {
		url += "&fieldName="+fieldName;
	}
	var jField = jQuery(field); 
	jField.autocomplete(url, {delay: 150, cacheLength : 1, max : limit, matchSubset: false, formatItem : function(result, index, count, term) { return result[0]+" ("+result[1]+")"; } });
	
	if (opField != null) {
		jQuery(opField).change(function() {
			jField.unautocomplete();
			ecomextended_complete(field, "aggregateText", lang, pageId);
		});
	}
	return jField;
}