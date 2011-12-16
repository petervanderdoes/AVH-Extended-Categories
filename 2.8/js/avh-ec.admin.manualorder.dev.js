function avhecManualOrderaddloadevent() {
	jQuery("#avhecManualOrder").sortable({
		placeholder : "sortable-placeholder",
		revert : false,
		tolerance : "pointer"
	});
};

addLoadEvent('avhecManualOrderaddloadevent');

function orderCats() {
	jQuery("#updateText").html(
			"' . __('Updating Category Order...', 'mycategoryorder') . '");
	jQuery("#hdnMyCategoryOrder").val(
			jQuery("#myCategoryOrderList").sortable("toArray"));
}