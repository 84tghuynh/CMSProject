<?php
     require('common.php');
     $errorFlag = false;
     $authFailure = false;


            /**
         * Process POST
         */
        if (
                isset($_POST['login'])                           &&
                !empty($_POST['password'])                       &&
                strlen(trim($_POST['password']))!=0              &&
                strlen($_POST['password'])>=1                    &&
                (validateEmail() == 'validemail' )
            )
            {
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                if(authenticateUser($email,$password))
                {
                    //if(isset($_SESSION['email'])) echo $_SESSION['email'];
                    header("Location: admin.php");
                    exit();
                }else{
                    $authFailure = true;
                }

            }else{
                if(isset($_POST['login']) && (empty($_POST['password']) || strlen(trim($_POST['password'])) == 0))   $errorFlag = true;
                if(isset($_POST['login']) && (validateEmail() != 'validemail' ))  $errorFlag = true;
            }
?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="utf-8">
    <title>Admin Login - CMS for Roy's Florist</title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <!-- <script src="js/main.js" type="text/javascript"></script> -->
</head>
<body>
    <div id="header">
        <div id="header-left">
            <div><img src="img/ninja.png" alt="Florist"></div>
            <div><a href="#"><h1>CMS For Roy's Florist - Admin Login ! </h1></a></div>
        </div>
        <div class="header-right">
            <div><a href="userregister.php">Register</a></div>
        </div>
        <div class="header-right">
            <div><a href="index.php">Home</a></div>
        </div>
    </div>
    <div class="clear"></div>
    <div id="content">
        <div class="register">
            <div id="reg-title">
                <h2>Admin Login CMS Roy's Florist</h2>
                <p>to continue with Roy's CMS </p>
            </div>
            <form id="loginForm" action="adminlogin.php"   method="post">
                <fieldset id="userInfo">
                <ul>
                    <li>
                    <?php if($authFailure): ?>
                        <p class="error" style="display: block;" >You logged in unsuccessfully. Please check your email or password!</p>
                    <?php endif ?>
                    </li>
                    <li>
                        <label for="email">Email</label>
                        <?php if($errorFlag): ?>
                            <?php if( validateEmail() == 'invalidemail'): ?>
                                <input id="email" name="email" type="text" value="<?= trim($_POST['email']) ?>" />
                                <p class="registerError error" id="emailformat_error" style="display: block;">* Invalid email address</p>
                            <?php elseif(validateEmail() == 'noemail'): ?>
                                <input id="email" name="email" type="text" />
                                <p class="registerError error" id="email_error" style="display: block;">* Required field</p>
                            <?php else: ?>
                                <input id="email" name="email" type="text" value="<?= trim($_POST['email']) ?>" />
                                <p class="registerError error" id="email_error">* Required field</p>
                                <p class="registerError error" id="emailformat_error">* Invalid email address</p>
                            <?php endif ?>
                        <?php else: ?>
                            <input id="email" name="email" type="text" />
                            <p class="registerError error" id="email_error">* Required field</p>
                            <p class="registerError error" id="emailformat_error">* Invalid email address</p>
                        <?php endif ?>
                    </li>
                    <li>
                        <label for="password">Password</label>
                        <?php if($errorFlag): ?>
                                <?php  if((empty($_POST['password'])  || strlen(trim($_POST['password'])) == 0)): ?>
                                    <input id="password" name="password" type="password" />
                                    <p class="registerError error"  id="password_error" style="display: block;">* Required field</p>
                                <?php  else: ?>
                                    <input id="password" name="password" type="password" value="<?=trim($_POST['password'])?>" />
                                    <p class="registerError error"  id="password_error" >* Required field</p>
                                <?php endif ?>
                        <?php else: ?>
                                <input id="password" name="password" type="password" />
                                <p class="registerError error"  id="password_error" >* Required field</p>
                        <?php endif ?>

                    </li>
                </ul>
                <div class="clear"></div>
				<p>
                    <!-- <button type="submit" id="submit">Register</button> -->
                    <input type="submit" name="login" id="login" value="Login">
				</p>
                </fieldset>
            </form>
        </div>
   </div>
</body>
</html>
