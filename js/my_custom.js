

$("#user-label").click(function () {
    $("#login").fadeIn(500).toggleClass('d-none', 'd-block');
});

$(".close_me").click(function () {
    $(this).parent().fadeOut(500).toggleClass('d-none', 'd-block');
});

function showPassword2(){
    $("#password2").toggleClass('d-none', 'd-block');
    $("#password1").hide();
}


/*
function toggleText(){
    // $("#more_text_1").toggleClass('d-none', 'd-block');
    $("#more_text_1").fadeIn(500).toggleClass('d-none', 'd-block');
}*/

/* No need of this function, but can be used for other elements...
$(".alert-dismissible .close").onclick = function () {
    $(".alert-dismissible").css('display', 'none');
}*/

/*$(".preserve_text").text(function(){
    return $(this).text().replace(/\r?\n/g, "<br>");
});*/

/*var text = document.getElementsByClassName('preserve-text').value;
text = text.replace(/\r?\n/g, '<br>');*/


$("#select-section").change(function () {
    window.location = 'index.php?section=' + $(this).val();
});

/*
$("input[name='radio_icon']").click(function () {
    var radioValue = $("input[name='radio_icon']:checked").val();
    if(radioValue == 'icon') {

        var icons = [
            "fa fa-graduation-cap",
            "fa fa-support",
            "fa fa-certificate",
            "fab fa-apple",
            "fas fa-anchor",
            "fas fa-bath",
            "far fa-angry",
            "fab fa-angellist",
            "fas fa-baby",
            "fas fa-bed",
            "fas fa-beer",
            "fas fa-bicycle",
            "fas fa-bomb",
            "fas fa-bone",
        ];


        var option = "<option>Icon</option>";
        for(i=0;i<icons.length; i++){
            option += "<option value=\"<i class=\'" + icons[i] + "\'></i>\">" + icons[i] + "</option>"
            // option += "<option value='" + icons[i] + "'>" + icons[i] + "</option>"
        }

        $("#icon_or_image").html(
            "<select name='icon' class='form-control my-select'>" + option + "</select>"
        )
    }else{
        $("#icon_or_image").html(
            "<button type=\"button\" class=\"my-upload-btn mb-2\">Upload document</button><input class=\"form-control-file\" type=\"file\" name=\"image\" id=\"file\">"
        );
    }
});

*/

$("input[name='radio_icon']").click(function () {
    var radioValue = $("input[name='radio_icon']:checked").val();
    if(radioValue == 'icon') {
        //$("#icon_or_image").toggleClass("d-inline-block", "d-none");
        //$("#icon_or_image1").toggleClass("d-none", "d-inline-block");

        $("#icon_or_image").hide();
        $("#icon_or_image1").show();
        //$("#icon_or_image1").css("display", "block");
        //attr("display", "none");
    }else{
        //$("#icon_or_image1").toggleClass("d-inline-block", "d-none");
        //$("#icon_or_image").toggleClass("d-none", "d-inline-block");

        $("#icon_or_image").show();
        $("#icon_or_image1").hide();
    }
});

$(document).ready(function(){
    $(".owl-carousel").owlCarousel();
});

//AJAX call for autocomplete
$(document).ready(function () {
    $("#search").keyup(function () {
        $.ajax({
            type:"POST",
            url:"../../../controllers/documents/menageDocument.php",
            data: 'keyword=' + $(this).val(),
            beforeSend: function () {
                //$("#search").css("background", "#fff");
            },
            success: function (data) {
                $("#suggestion-box").show();
                $("#suggestion-box").html(data);
                //$("#suggestion-box").css("background", "transparent");
            }

        });
    });
});

//To select document
function selectDocument(id) {
    //$("#search").val(val);
    $("#suggestion-box").hide();
    window.open("index.php?document_id=" + id, "_self");
}

$(document).ready(function () {
    $("#clear-search").click(function () {
        window.open("/views/managing/documents/", "_self");
    });
});

$(document).ready(function () {
    $("#save-document-btn").click(function () {
        $("#loader").show();
    });
});


/**
 * Listen to scroll to change header opacity class
 */
function checkScroll(){
    var startY = $(".navbar").height() * 2; //The point where the navbar changes in px

    if($(window).scrollTop() > startY){
        $('.navbar').addClass("nav-scrolled");
    }else{
        $('.navbar').removeClass("nav-scrolled");
    }
}

if($('.navbar').length > 0){
    $(window).on("scroll load resize", function(){
        checkScroll();
    });
}