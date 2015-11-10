$(document).ready(function() {
    if($.support.fileDrop){
            setupFiledrop();
	}else{
            alert('Your browser does not support file drag-n-drop :(');
	}
    $('#assets').sortable();
    $('.asset').mousedown(function() {
        $('#save-order').show();
    });
    $('#save-order').click(function() {
        saveOrder();
    });
});

function setupFiledrop(){
    var $outputArea = $("#dropzone");
    var $count = $("#drop-demo-count");

    //Add file drag-n-drop to the HTML element
    $('#dropzone').fileDrop({
        decodeBase64: false,
        removeDataUriScheme: false,
        onFileRead: function(fileCollection){

            if(console){
                console.clear();
                console.log("---File Collection---");
                console.log(fileCollection);
            }

            var newHtml='';

            //Loop through each file that was dropped
            $.each(fileCollection, function(i){

                $('#dropzone').css('height', 'auto');
                $('#new-asset > #info').css('display', 'block');
                
                if(this.type.indexOf('image')>=0){
                    newHtml += '<img src="' + this.data + '"/>';
                }else{
                    var noScheme = $.removeUriScheme(this.data);
                    var base64Decoded = window.atob(noScheme);
                    var htmlEncoded = htmlEncode(base64Decoded);
                    newHtml += '<p>'+ htmlEncoded + '</p>';
                }

                if(i !== fileCollection.length-1){
                    newHtml += "<hr />";
                }
                
                $('#image').val(this.data);
            });

            //Set the text about how many files were dropped. Make it plural when there is more than one!
            var countText = fileCollection.length + ' file' + ( fileCollection.length === 1 ? '' : 's' ) + ' dropped!';
            $count.text(countText);

            //Set the HTML
            $outputArea.html(newHtml);
        }
    });
}

//Helper function to HTML encode anything that gets dropped on the page
function htmlEncode(value) {
	var el = document.createElement('div');
	if (value) {
		el.innerText = el.textContent = value;
		return el.innerHTML;
	}
	return value;
}

function saveOrder() {
    var order = [];
    $('.asset').each(function(i) {
        order[i] = $(this).attr('id');
    });
    order = JSON.stringify(order);
    $('#orderAsset').val(order);
}