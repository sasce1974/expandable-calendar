//Initialization of submit form by ID:"userForm" NOT to submit in case of errors, and to provide info
$(document).ready(function() {
    $("#newUserForm").submit(function(e) {
        removeFeedback();
        var errors = validateForm();
        if (errors == "") {
            return true;
        } else {
            provideFeedback(errors);
            e.preventDefault();
            return false;
        }
    });

    function validateForm() {
        var errorFields = new Array();
        //Check required fields have something in them
        if ($("#name").val() == "") {
            errorFields.push("name");
        }
        if($("#name").val()!="") {
            if (!($("#name").val()).match(/^[a-zA-Z .]+$/)) {
                errorFields.push("name1");
            }
        }

        if ($("#email").val() == "") {
            errorFields.push("email");
        }
        if (!($("#password1").val()).match(/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,40})/)) {
            errorFields.push("password1");
        }
        if ($("#password2").val() != $("#password1").val()) {
            errorFields.push("password2");
        }

        if (!(($("#email").val()).match(/^.+@.+\..{2,4}$/))) {
            errorFields.push("email");
        }
        if ($("#phone").val() != "") {
            var phoneNum = $("#phone").val();
            if (!(phoneNum.match(/^\d{8,14}$/))) {
                errorFields.push("phone");
            }
        }
        return errorFields;
    } //end function validateForm

    function provideFeedback(incomingErrors) {
        for (var i = 0; i < incomingErrors.length; i++) {
            $("#" + incomingErrors[i]).addClass("errorClass");
            $("#" + incomingErrors[i] + "Error"). removeClass("errorFeedback");
        }
        $("#errorDiv").html("Errors encountered");
    }

    function removeFeedback() {
        $("#errorDiv").html("");
        $("input").each(function() {
            $(this).removeClass("errorClass");
        });
        $(".errorSpan").each(function() {
            $(this).addClass("errorFeedback");
        });
    }
});