$(document).ready(function() {
    $('#add-client').click(function() {
        $('.new-client').slideToggle();
    });
    $(function(){
	$('#name').friendurl({id : 'urlname'});
    });
});