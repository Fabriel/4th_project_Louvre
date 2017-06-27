$(document).ready(function() {
    $('.js-dob').keyup(function(){
        var nbChar = $(this).val().length;
        if(nbChar == 2 || nbChar == 5) {
            $(this).val($(this).val()+'/');
        }
    });
});
