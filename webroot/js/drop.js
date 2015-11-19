var dropper;
var results;

$(document).ready(function() {
    droper();
    $('#save-order').click(function() {
        saveOrder();
    });
});

function saveOrder() {
    var order = [];
    $('.asset').each(function(i) {
        order[i] = $(this).attr('id');
    });
    order = JSON.stringify(order);
    $('#orderAsset').val(order);
}

function droper() {
    if (typeof window.FileReader === 'undefined')
        alert('File API & FileReader not supported');

    dropper = document.getElementById("dropzone");
    results = document.getElementById("dropzone");

    dropper.ondragover = function () { dropper.className = 'hover'; return false; };
    dropper.ondragend = function () { dropper.className = ''; return false; };
    dropper.ondrop = function (e) {
        e.preventDefault();
        var files = [].slice.call(e.dataTransfer.files);
        files.forEach(function (file) {
            var reader = new FileReader();
            reader.onload = function(event) {
                fileLoaded(file.name, event.target.result);
            };
            reader.readAsDataURL(file);
            dropper.className = '';
        });
        return false;
      };
}
    
function fileLoaded(filename, dataUri) {
    
    results.innerHTML = '';
    $('#new-asset > #info').slideDown('slow');

    if(/^data:image/.test(dataUri)) {
        results.style.height = 'auto';
        var img = document.createElement("img");
        img.src = dataUri;
        results.appendChild(img);
        $('#image').val(dataUri);
    } else {
        var name = document.createElement("p");
        name.innerHTML = filename;
        results.appendChild(name);
        $('#zipfile').val(dataUri);
    }
}