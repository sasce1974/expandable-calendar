<?php
require_once("../includes/functions.php");
$_SESSION['initToken'] = token();
$invalidAccess = true;
if (isset($_GET['user']) && $_GET['user'] != "") {
    $invalidAccess = false;
    $hash = $_GET['user'];
}
//if they've attempted the form but had a problem, we need to allow them in.
if (isset($_SESSION['formAttempt']) && $_SESSION['formAttempt']== true) {
    $invalidAccess = false;
    $hash = $_SESSION['hash'];
}
if ($invalidAccess) {
    die(header("Location: ../login/"));
}
?>
<!doctype html>
<html>
<head>
<title>Reset Password</title>
    <meta charset = "UTF-8">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>/css/font-awesome.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/my_custom_style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/login-form.css">
</head>
<body>
<div id="video"></div>
<div class="card mx-auto ml-0 mr-0" style="background: rgba(250, 250, 250, 0.7)">
    <div class="card-header">
        Password reset
        <p class="float-right">
            To sign in, please click <a href="../login/">here</a>
        </p>
    </div>
    <div class="card-body pt-0">
        <div class="mx-3" id="errorDiv"></div>
        <div class="mx-3">
            <?php
            if (isset($_SESSION['error']) && isset($_SESSION['formAttempt'])) {
                unset($_SESSION['formAttempt']);
                print <<<HERE
                        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <b><i class="far fa-frown mx-2"></i></b>
HERE;
                if (is_array($_SESSION['error'])) {
                    print "<b>Error: </b>";
                    foreach ($_SESSION['error'] as $error) {
                        print $error . "<br> \n";
                    }
                } else {
                    print "<b>Error: </b>" . $_SESSION['error'];
                }
                unset($error, $_SESSION['error']);
            }
            print "</div>";
            ?>
        </div>
        <div class="m-3">
            <form id="loginForm" method="POST" action="reset-process.php">
                <input type="hidden" name="init" value="<?php print $_SESSION['initToken'];  ?>">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="width:10em" id="basic1">E-mail</span>
                        </div>
<!--                        <input placeholder="some@email.com" class="form-control p-2" aria-describedby="basic1" onblur ="checkIfUserExist()" id="email" name="email" type="email" required>-->
                        <input placeholder="some@email.com" class="form-control p-2" aria-describedby="basic1" id="email" name="email" type="email" required>
                    </div>
<!--                    <span id="user_check"></span>-->
<!--                    <small class="errorFeedback errorSpan" id="emailError">E-mail is required</small>-->
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="width:10em" id="basic2">New Password</span>
                        </div>
                        <input class="form-control p-2" aria-describedby="basic2" id="password1" name="password1" type="password"
                               pattern=".{1,20}" placeholder="Use strong password" title="1-20 characters" required>
                    </div>
<!--                    <small class="errorFeedback errorSpan" id="passwordError">Password required</small>-->
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="width:10em" id="basic3">Repeat Password</span>
                        </div>
                        <input placeholder="Repeat the password" class="form-control p-2" aria-describedby="basic3" id="password2" name="password2" type="password"
                               required>
                    </div>
<!--                    <small class="errorFeedback errorSpan" id="password2Error">Passwords don’t match</small>-->
                </div>
                <input type="hidden" name="hash" value="<?php print $hash; ?>">
                <div class="text-right">
                    <button class="btn btn-success" type="submit" id="submit" name="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>