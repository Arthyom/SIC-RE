$(document).ready(function(){

	// Menu principal
	$('#button-menu').click(function(){
		if($('#button-menu').attr('class') == 'fa fa-caret-down' ){

			$('.navegacion').css({'width':'100%'});
			$('#button-menu').removeClass('fa fa-caret-down').addClass('fa fa-caret-up');
			$('.navegacion .menu').css({'top':'0px'});

		} else{

			$('.navegacion').css({'width':'0%', 'background':'rgba(0,0,0,.0)'});
			$('#button-menu').removeClass('fa fa-caret-up').addClass('fa fa-caret-down');
			$('.navegacion .submenu1').css({'top':'-100vh'});
			$('.navegacion .submenu2').css({'top':'-100vh'});
			$('.navegacion .submenu3').css({'top':'-100vh'});
			$('.navegacion .submenu4').css({'top':'-100vh'});
			$('.navegacion .submenu5').css({'top':'-100vh'});
			$('.navegacion .menu').css({'top':'-100vh'});
		}
	});
	// Mostrar submenus
	$('.navegacion .menu > .item-submenu a').click(function(){
		
		var positionMenu = $(this).parent().attr('menu');
		var nivel = $(this).parent().attr('level');
		$('.item-submenu[menu='+positionMenu+'] .submenu'+nivel+'').css({'top':'0', 'height':'100%'});
	});

	// Ocultar submenus
	$('.navegacion .submenu1 li.go-back').click(function(){
		$(this).parent().css({'top':'-100vh'});
	});
	$('.navegacion .submenu2 li.go-back').click(function(){
		$(this).parent().css({'top':'-100vh'});
	});
	$('.navegacion .submenu3 li.go-back').click(function(){
		$(this).parent().css({'top':'-100vh'});
	});
	$('.navegacion .submenu4 li.go-back').click(function(){
		$(this).parent().css({'top':'-100vh'});
	});
	$('.navegacion .submenu5 li.go-back').click(function(){
		$(this).parent().css({'top':'-100vh'});
	});

});