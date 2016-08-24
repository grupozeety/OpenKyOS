$(document).ready(function($){
	$('#mega-menu-1').dcMegaMenu({
		rowItems: '5',
		speed: 'slow',
                effect: 'fade',
                event: 'click'
	});
});

(function($){
	$(document).ready(function(){
		$('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
			event.preventDefault(); 
			event.stopPropagation(); 
			$(this).parent().siblings().removeClass('open');
			$(this).parent().toggleClass('open');
		});
	});
})(jQuery);

