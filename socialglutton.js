$(document).ready(function(){
	$('#socialg-shell > div').hide();
	$('#socialg-shell > div:first').show();
	$('#socialg-shell > ul li:first').addClass('active');
 
	$('#socialg-shell > ul li a').click(function(){
		$('#socialg-shell > ul li').removeClass('active');
		$(this).parent().addClass('active');
		var currentTab = $(this).attr('href');
		$('#socialg-shell > div').hide();
		$(currentTab).fadeIn();
		return false;
	});
});
