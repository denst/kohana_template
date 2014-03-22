var messages = {
    
    hideMessage: function(){
        $(".alert").show().delay(5000).fadeOut();
    }
}
$(function(){
    messages.hideMessage();
});