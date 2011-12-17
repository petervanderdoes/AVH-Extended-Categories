function avhecManualOrder() {
	jQuery("#avhecManualOrder").sortable({
		placeholder : "sortable-placeholder",
		revert : false,
		items : '.lineitem',
		opacity: 0.65,
		cursor: 'move',
		forcePlaceholderSize: true,
		tolerance : "pointer"
	});
};

addLoadEvent(avhecManualOrder);

function orderCats() {
	jQuery("#updateText").html("Updating Category Order...");
	jQuery("#hdnMyCategoryOrder").val(
			jQuery("#avhecManualOrder").sortable("toArray"));
}