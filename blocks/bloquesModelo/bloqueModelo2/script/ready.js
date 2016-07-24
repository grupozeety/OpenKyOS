
$(function() {
	$(document).tooltip({
		position : {
			my : "left+15 center",
			at : "right center"
		}
	},
	{ hide: { duration: 800 } }
	);
});


$(function() {
	$("button").button().click(function(event) {
		event.preventDefault();
	});
});
