(function($) {
  "use strict"; // Start of use strict

  // Show the navbar when the page is scrolled up
  var MQL = 992;

  //primary navigation slide-in effect
  if ($(window).width() > MQL) {
    var headerHeight = $('#mainNav').height();
    $(window).on('scroll', {
        previousTop: 0
      },
      function() {
        var currentTop = $(window).scrollTop();
        //check if user is scrolling up
        if (currentTop < this.previousTop) {
          //if scrolling up...
          if (currentTop > 0 && $('#mainNav').hasClass('is-fixed')) {
            $('#mainNav').addClass('is-visible');
          } else {
            $('#mainNav').removeClass('is-visible is-fixed');
          }
        } else if (currentTop > this.previousTop) {
          //if scrolling down...
          $('#mainNav').removeClass('is-visible');
          if (currentTop > headerHeight && !$('#mainNav').hasClass('is-fixed')) $('#mainNav').addClass('is-fixed');
        }
        this.previousTop = currentTop;
      });
  }

})(jQuery); // End of use strict

function act(act) {	
	$("input[class=user_images").each(function()
	{
		this.checked = ((this.checked == 1) ? 0 : 1);
	});      
}

function Confirm(){
	var checkedfiles = "";
	$("input[class=user_images]").each(function()
	{
		if (this.checked == 1) {
			checkedfiles += (this.value + ",");
		}
	});  
	if (checkedfiles !== "") {
        var result = confirm("Do you want to do this?");
        if(result){
            return true;
        }else{
            return false;
        }
	} else {
	//	alert("Please select at least one file");
    //    return false;
	}    
}