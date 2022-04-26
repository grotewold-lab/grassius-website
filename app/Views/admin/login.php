
<html lang="en">
    
   <?php require_once "header.php"; ?>

    <body class="text-center">
        <form method="POST" class="form-signin" action="/attempt_login">
            <h1 class="h3 mb-3 font-weight-normal">enter admin password

                <?php 
                    $session = \Config\Services::session();
                    $message = $session->getFlashdata('login_message');
                    if($message)
                    {
                        echo '<p style="color:red">'.$message.'</p>';
                    }
                ?>

            </h1>
            <label for="inputPassword" class="sr-only">Password</label>
            <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password">
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        </form>
    </body>
</html>