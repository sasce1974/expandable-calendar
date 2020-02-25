<?php
require_once("../includes/functions.php");
require (BASE_URI . 'controllers/UserClass.php');
$_SESSION['initToken'] = token();


if(isset($_SESSION['isLoggedInPlanner']) && $_SESSION['isLoggedInPlanner'] == true){
    header("Location: ../");
    exit();
}

?>

<!--  <script type="text/javascript" src="../form.js"></script>-->

<!doctype html>
<html lang="en">
<head>
    <title>Planner | Login</title>
    <meta charset = "UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Plan stayed days in country without visa. Plan 90 days
     out of the last 180 days.">
    <meta name="author" content="Aleksandar Ardjanliev">
    <meta name="keywords" content="Planner, Scheduler, Plan stayed days in country without visa.">


    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/planner.css?x=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/login-form.css?x=<?php echo time(); ?>">
    <link rel="stylesheet" href="form.css?x=<?php echo time(); ?>">

    <script src="<?php echo BASE_URL; ?>/js/jquery.js"></script>
    <script src="<?php echo BASE_URL; ?>/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/js/form.js"></script>
</head>
<body>

<div id="video"></div>
<div class = "login-wrap">
    <div class="card mx-auto" style="background: transparent">
        <div class="card-header">
<!--            <img src="../images/positivessl_trust_seal_sm_124x32.png" class="mx-auto mb-2">-->
            <h4>Login to <b>PLANNER</b></h4>
        </div>
        <div class="card-body">
            <div class="m-3">
            <?php
                if (isset($_SESSION['error']) && isset($_SESSION['formAttempt'])) {
                    unset($_SESSION['formAttempt']);
                    print <<<HERE
                    <div class="alert alert-danger alert-dismissible fade show ml-auto mr-auto mt-5" role="alert">
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
                    print "</div> \n";
                    unset($error, $_SESSION['error']);
                }
            if(!empty($_SESSION['message'])){
                print <<<HERE
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            <i class="fa fa-check mx-2"></i>
HERE;
                print $_SESSION['message'];
                unset($_SESSION['message']);
                print "</div>"; //end div alert success
            }
                print "</div>"; //end div m-3

                $user = new User;
                if($user->getAttribute('isLoggedInPlanner')){
                    $user->logout();
                }


?>

            <div class="m-3">
            <form id="loginForm" method="POST" action="login-process.php">
                <input type="hidden" name="init" value="<?php if(isset($_SESSION['initToken'])) print $_SESSION['initToken'];  ?>">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="width: 4em" id="basic1" aria-label="email"><i class="fa fa-envelope fa-2x"></i> </span>
                        </div>
                        <input class="form-control" aria-describedby="basic1" id="email" name="email" type="email" required>
                    </div>
                    <span id="info_login"></span>
                    <small class="errorFeedback errorSpan" id="emailError">E-mail is required</small>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="width: 4em" id="basic2"><i class="fa fa-key fa-2x"></i> </span>
                        </div>
                        <input class="form-control" aria-describedby="basic2" id="password" name="password" type="password" required>
                    </div>
                    <small class="errorFeedback errorSpan" id="passwordError">Password required</small>
                </div>
                <div class="text-right">
                    <button class="btn btn-outline" type="submit" id="submit" name="submit"><i class="fa fa-paper-plane"></i> Submit</button>
                </div>
            </form><hr>
            <p class="card-text">
                <a class="card-link" href="../reset/emailpass.php">Forgotten password? Click here</a> <br>
                <a class="card-link" href="../register/">If no account, sign up for free here</a>
            </p>
            </div>
        </div>
    </div>

</div> <!-- End of Login wrap -->


</body>
</html>