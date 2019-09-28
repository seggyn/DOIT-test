<!DOCTYPE>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    <title>TODO List - Sign up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <?php include "views/const.php"; ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row d-flex justify-content-center mt-2">
            <div class="col-md-5">
                <div class="form">

                    <h2>Sign Up</h2>
                    <form id="sign-up">
                        <p class="text-danger error"></p>
                        <div class="form-group">
                            <input name="email" name="email" id="login" type="text" class="form-control" placeholder="Login" autofocus="true" required/>
                        </div>
                        <div class="form-group">
                            <input name="password" name="password" id="password" type="password" class="form-control" placeholder="Password" required/>
                        </div>
                        <button class="btn btn-primary" type="submit">Sign Up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

    <script src="assets/js/cookie.js" type="application/javascript"></script>
    <script src="assets/js/script.js" type="application/javascript"></script>

</body>
</html>