$(document).ready(function(){

	// Menu principal
	$('#button-menu').click(function(){
		if($('#button-menu').attr('class') == 'fa fa-bars' ){

			$('.navegacion').css({'width':'100%', 'background':'rgba(0,0,0,.5)'});
			$('#button-menu').removeClass('fa fa-bars').addClass('fa fa-close');
			$('.navegacion .menu').css({'left':'0px'});

		} else{

			$('.navegacion').css({'width':'0%', 'background':'rgba(0,0,0,.0)'});
			$('#button-menu').removeClass('fa fa-close').addClass('fa fa-bars');
			$('.navegacion .submenu1').css({'left':'-320px'});
			$('.navegacion .submenu2').css({'left':'-320px'});
			$('.navegacion .submenu3').css({'left':'-320px'});
			$('.navegacion .submenu4').css({'left':'-320px'});
			$('.navegacion .submenu5').css({'left':'-320px'});
			$('.navegacion .menu').css({'left':'-320px'});
		}
	});
	// Mostrar submenus
	$('.navegacion .menu > .item-submenu a').click(function(){
		
		var positionMenu = $(this).parent().attr('menu');
		var nivel = $(this).parent().attr('level');
		$('.item-submenu[menu='+positionMenu+'] .submenu'+nivel+'').css({'left':'0px'});
	});

	// Ocultar submenus
	$('.navegacion .submenu1 li.go-back').click(function(){
		$(this).parent().css({'left':'-320px'});
	});
	$('.navegacion .submenu2 li.go-back').click(function(){
		$(this).parent().css({'left':'-320px'});
	});
	$('.navegacion .submenu3 li.go-back').click(function(){
		$(this).parent().css({'left':'-320px'});
	});
	$('.navegacion .submenu4 li.go-back').click(function(){
		$(this).parent().css({'left':'-320px'});
	});
	$('.navegacion .submenu5 li.go-back').click(function(){
		$(this).parent().css({'left':'-320px'});
	});

});