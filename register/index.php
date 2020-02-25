<?php
require_once("../includes/functions.php");
require (BASE_URI . 'controllers/UserClass.php');
$_SESSION['initToken'] = token();

?>
<!doctype html>
<html lang="en">
<head>
    <title>Eastern Therapies | Sign Up</title>
    <meta charset = "UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Register free for Planner / Scheduller for free days
    stay in a country without required visa. Plan - calculate 90 out of 180 allowed days.">
    <meta name="author" content="Aleksandar Ardjanliev">
    <meta name="keywords" content="Planner, Scheduler, Visa planner">


    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>/css/font-awesome.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/my_custom_style.css?x=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/login-form.css?x=<?php echo time(); ?>">
    <link rel="stylesheet" href="form.css?x=<?php echo time(); ?>">
    <script src="<?php echo BASE_URL; ?>/js/jquery.js"></script>
    <script src="<?php echo BASE_URL; ?>/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/js/check_new_user.js"></script>
    <script src="form.js"></script>
</head>
<body>

<div id="video"></div>
    <div id="errorDiv"></div>
    <div class="login-wrap">
        <div class="m-3">
            <h4>Sign Up For New Account - PLANNER</h4>

        <?php
            if (isset($_SESSION['error']) && isset($_SESSION['formAttempt'])) {
                unset($_SESSION['formAttempt']);
            ?>

            <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            <b><i class="far fa-frown mx-2"></i></b>

                <?php
                if (is_array($_SESSION['error'])) {
                    print "<b>Error: </b>";
                    foreach ($_SESSION['error'] as $error) {
                        print $error . "<br> \n";
                    }
                } else {
                    print "<b>Error: </b>" . $_SESSION['error'];
                }
                unset($error, $_SESSION['error']);
                print "</div> \n";
            }


            $user = new User;
            if($user->getAttribute('isLoggedInPlanner')){
                $user->logout();
            }
        ?>


            <form autocomplete="off" id="newUserForm" method="POST" action="register-process.php">
                <input type="hidden" name="init" value="<?php print $_SESSION['initToken'];  ?>">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic1"><i class="fa fa-envelope d-none d-md-block mr-1"></i> E-mail</span>
                        </div>
                        <input placeholder="some@email.com" class="form-control p-2" aria-describedby="basic1" onblur ="checkIfUserExist()" id="email" name="email" type="email" required>
                    </div>
                    <span id="user_check"></span>
                    <small class="errorFeedback errorSpan" id="emailError">E-mail is not valid</small>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic2"><i class="fa fa-key d-none d-md-block mr-1"></i> Password</span>
                        </div>
                        <input class="form-control p-2" aria-describedby="basic2" id="password1" name="password1" type="password"
                        placeholder="Use strong password" title="At least 1 small, 1 big letter and 1 number, and has to be 8-40 characters long" required>
                        <i class="fa fa-eye inside-input" style="top:10px" id="eye"  onclick="showPassword()"></i>
                    </div>
                    <small class="errorFeedback errorSpan" id="password1Error">Password must have at least 1 big letter, 1 small letter, 1 number and to be between 8 and 40 characters</small>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text py-1" id="basic3"><i class="fa fa-key d-none d-md-block mr-1"></i> Repeat<br>Password</span>
                        </div>
                        <input placeholder="Repeat the password" class="form-control p-2" aria-describedby="basic3" id="password2" name="password2" type="password"
                               required>
                    </div>
                    <small class="errorFeedback errorSpan" id="password2Error">Passwords don’t match</small>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic4"><i class="fa fa-user d-none d-md-block mr-1"></i> Name</span>
                        </div>
                        <input placeholder="John Doe" class="form-control p-2" aria-describedby="basic4" id="name" name="name" type="text"
                               required>
                    </div>
                    <small class="errorFeedback errorSpan" id="nameError">Name field cannot be empty</small>
                    <small class="errorFeedback errorSpan" id="name1Error">Name and surname can contain only letters</small>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic6"><i class="fa fa-phone d-none d-md-block mr-1"></i> Phone</span>
                        </div>
                        <input class="form-control p-2" aria-describedby="basic6" id="phone" name="phone" type="tel">
                    </div>
                    <small class="errorFeedback errorSpan" id="phoneError">Phone number can contain 8 to 14 digits</small>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <textarea placeholder="You can enter some notes about your self..." class="form-control" id="note" name="note"></textarea>
                    </div>
                </div>
                <div class="text-right">
                    <button class="btn btn-outline" type="submit" id="submit" name="submit">Submit</button>
                </div>
            </form>
        </div>

    </div> <!-- End of Login wrap -->
</div> <!-- End of div Video from layout -->

<script type="text/javascript">
    function checkIfUserExist() {
        var user = $("#email").val();
        if (user!=="") {
            $.post("check_new_user.php",{"check_email":user},check_info);
        }
    }
    function check_info(data, textStatus){
        $("#user_check").html(data);
    }
</script>

<script>
    function showPassword() {
        var p = document.getElementById("password1");
        var eye_icon = document.getElementById("eye");
        if(p.type === "password"){
            p.type = "text";
            eye_icon.className = "fa fa-eye-slash inside-input";
        }else{
            p.type = "password";
            eye_icon.className = "fa fa-eye inside-input";
        }
    }
</script>

</body>
</html>