$(function() {


	 /* initialize tabs */
    $( "#tabs" ).tabs();
   
	
	/* Maken Menu Sticky on Scroll */
	var mainMenu = $("#main"),
		header = $("#header"),
		sidebar = $("#sidebar"),
		sidebarToggle = $(".sidebar-toggle"),
		scrollTop,
		headerOuterheight = header.outerHeight();

	$(window).on( 'scroll', function(){
		scrollTop = $(this).scrollTop();

		if(scrollTop > 0 && sidebar.hasClass("collapsed")){
			sidebarToggle.hide();
		} else {
			sidebarToggle.show();
		}

		if(scrollTop >= headerOuterheight &&
			window.matchMedia("(min-width: 1000px)").matches){
			mainMenu.addClass("fixed");
		}	else {

			mainMenu.removeClass("fixed");
		}
	});

	/* Check if mobile */
    
	function checkMobile(x) {
	    if (mob.matches) {
	        $mobil = false;
	    } else {
	       $mobil = true;
	    }
	}
	
	/* Set mobile width */
	var mob = window.matchMedia("(min-width: 1000px)")
	checkMobile(mob) 
	mob.addListener(checkMobile) 	
	
	

	
	/* Make Menu Merge on linebreak */
	
	var mainmenu = {
		menu : $('#mainmenu'),
		fullw : 0
	},
	submenu = {
		menu : $('#submenu'),
		fullw : 0
	};

	
	mainmenu.menu.attr('data-items', mainmenu.menu.children().length);
	submenu.menu.attr('data-items', submenu.menu.children().length);	

    
	function menu_merge(m){
		
		var childw = 0,
			menu = m.menu;
		
		menu.children().outerWidth(function(i,w){childw+=w+1;},true);


		if(childw >= menu.width()) {

			if(m.fullw == 0){
				menu.children().outerWidth(function(i,w){m.fullw+=w+1;},true);				
			}
			
			menu.addClass("merged");
		} 
		
		if(m.fullw != 0 && menu.width() >= m.fullw){
			menu.removeClass("merged");
		}
	}    


	/* run on load */
	if(!$mobil){
		menu_merge(mainmenu);
		menu_merge(submenu);
	}
	
	/* run on resize */
	$(window).on('resize', function(){
		
		if(!$mobil){
			menu_merge(mainmenu);
			menu_merge(submenu);
		} else {
			mainmenu.menu.removeClass("merged");
			submenu.menu.removeClass("merged");
		}
    });	
    
    $menuOpener = $("#header .menu-opener");
    $closem = $(".close-mobile");
    
    
    $menuOpener.on("click", function(){
	    mainmenu.menu.toggleClass("fixed");
	    $closem.addClass("display");
	    $("body").css("overflow", "hidden");
    });
    

    mainmenu.menu.find(" > li > a").on("click", function(){
	    submenu.menu.removeClass("fixed");
	 	$(this).siblings("ul.submenu").addClass("fixed");
	 	mainmenu.menu.find(" > li").removeClass("show").addClass("mobilehide");
	 	$(this).parent().removeClass("mobilehide").addClass("show");

    });
    
    
    $(".menu-wrapper").on("click", ".close-mobile", function(){
	   mainmenu.menu.removeClass("fixed").find("ul.submenu").removeClass("fixed");
	   mainmenu.menu.find(" > li").removeClass("mobilehide").removeClass("show");
	   $(this).removeClass("display");
	   $("body").css("overflow", "visible");
    }); 

    
});




