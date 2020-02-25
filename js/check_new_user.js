//Check if there is new registered user...
function checkNewUser(){
    $.post("../controllers/users/check_new_user.php", {"check_new_user":1}, message);
    //setTimeout(checkNewUser, 600000);
}

//Activate the new user from the message...
function activateUser(id) {
    var token = $('#token').val();
    $.post("../controllers/users/UsersController.php",{"activate_user":1,"id":id, "token":token}, message_autoHide);
}

//Update the new user that is checked and not to be shown again
function updateCheckNewUser(id) {
    var checked;
    var token = $('#token').val();
    if ($("#chk-new-user-info:checked")) {
        checked = 0;
    }else{
        checked = 1;
    }
    $.post("../controllers/users/UsersController.php",{"checked_user":1,"id":id, "checked":checked, "token":token},message_autoHide);
}

function message (data, textStatus) {
    //hideHidden("#loader");
    $("#message_check").html(data, textStatus).css("visibility", "visible");
    centerMessage("#message_check");
    if (($("#message_check").html()==" ") || ($("#message_check").html()=="")) {
        $("#message_check").css("visibility", "hidden");
    }
    // setTimeout(hideMessage, 5000);
}

function message_autoHide (data, textStatus) {
    //hideHidden("#loader");
    $("#message_check").html(data, textStatus).css("visibility", "visible");
    centerMessage("#message_check");
    if ($("#message_check").html()=="") {
        $("#message_check").css("visibility", "hidden");
    }
    setTimeout(hideMessage, 3000);
}

function hideMessage(){
    $("#message_check").html("").css("visibility", "hidden");
    //location.reload();
}


//Center given element in the middle of the window...
function centerMessage(objectId) {
    var windHeight = $(window).height();
    var windWidth = $(window).width();
    var centerV = windHeight/2;
    var centerH = windWidth/2;
    var objHeight = $(objectId).height();
    var objWidth = $(objectId).width();
    var top = centerV-objHeight/2;
    var left = centerH-objWidth/2;
    if(top<0) top = 0;
    if(left<0) left = 0;
    $(objectId).css({"top":(top)+"px", "left":(left)+"px"});
}