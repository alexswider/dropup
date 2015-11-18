$(document).ready(function() {
    $('#add-project').click(function() {
        $('.new-project').slideToggle();
    });
    $(function(){
	$('#name').friendurl({id : 'urlname'});
    });
});