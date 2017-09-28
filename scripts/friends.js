$( document ).ready(function() {
	$("#search-bar").hide();

	window.addEventListener('click', function(e){   
		if (document.getElementById('search-icon').contains(e.target) || document.getElementById('search-bar').contains(e.target)){
    		// Clicked in box
		} 
		else{
		    // Clicked outside the box
		    $("#search-bar").hide(400);
		    $("#search-bar").val('');
		}
	});

});

function show_bar(){
	$("#search-bar").show(400);
}