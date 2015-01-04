function toggleOffCanvasNavLeft() {
	if ($('#pageWrap').hasClass('navLeftOpen')) {
		$('#pageWrap').removeClass('navLeftOpen');
		$('#pageWrap').unbind('click');
	}
	else {
		$('#pageWrap').addClass('navLeftOpen');
		$('#pageWrap').bind('click', function() {
			$(this).removeClass('navLeftOpen');
		});
	}
}


$(function() {
	$('#showLeftNavigation').bind('click', function(e) {
		e.stopPropagation();
		toggleOffCanvasNavLeft();
	});
	//prevent closing when clicking on off canvas nav
	$('nav#leftMenu').bind('click', function(e) {
		e.stopPropagation();
	});


	$('.headline').slabText({
		// Don't slabtext the headers if the viewport is under 380px
		"viewportBreakpoint":380
	});
	$('.subheading').slabText({
		// Don't slabtext the headers if the viewport is under 380px
		"viewportBreakpoint":380
	});

});
