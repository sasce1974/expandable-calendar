<?php
require_once("../includes/functions.php");
$_SESSION['initToken'] = token();
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
  <title>Password recovery</title>
    <meta charset = "UTF-8">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>/css/font-awesome.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>views/css/my_custom_style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>views/css/login-form.css">
</head>
<body>
    <div id="video"></div>
    <div class="card col-md-6 mt-5 mx-auto p-0 text-dark" style="background: rgba(250, 250, 250, 0.7)">
        <div class="card-header">
            <h4>Request New Password</h4>
        </div>
        <div class="card-body">

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

            <div class="m-3">
                <form class="text-dark" id="newUserForm" method="POST" action="email-process.php">
                    <input type="hidden" name="init" value="<?php print $_SESSION['initToken'];  ?>">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="width:3em" id="basic1"><i class="fa fa-envelope"></i></span>
                            </div>
                            <input placeholder="Your registered email here" class="form-control p-2 text-dark" aria-describedby="basic1" id="email" name="email" type="email" required>
                        </div>
                        <div id="errorDiv">
                            <small class="errorFeedback errorSpan" id="emailError">E-mail is required</small>
                        </div>
                    </div>
                    <div class="text-right">
                        <button class="btn btn-success" type="submit" id="submit" name="submit"><i class="fa fa-paper-plane"></i> Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
