<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">

    <!-- Custom CSS -->
    <link href="dist/css/style.min.css" rel="stylesheet">

    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <div class="main-wrapper">
        <!-- Preloader -->
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>

        <!-- Login Form -->
        <div class="auth-wrapper d-flex justify-content-center align-items-center bg-dark">
            <div class="auth-box bg-dark border-top border-secondary">
                <div id="loginform">
                    <div class="text-center p-4">
                        <h3 class="text-white">Mondol Motors</h3>
                    </div>

                    <form id="loginForm" class="form-horizontal">
                        <div class="row pb-3">
                            <div class="col-12">
                                <!-- Email Field -->
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-success text-white"><i class="ti-user"></i></span>
                                    </div>
                                    <input type="email" class="form-control form-control-lg" name="email" placeholder="Email" required>
                                </div>

                                <!-- Password Field -->
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-warning text-white"><i class="ti-pencil"></i></span>
                                    </div>
                                    <input type="password" class="form-control form-control-lg" name="password" placeholder="Password" required>
                                </div>
                            </div>
                        </div>

                        <div class="row border-top border-secondary">
                            <div class="col-12">
                                <div class="form-group text-center pt-3">
                                    <button type="button" class="btn btn-info" id="to-recover"><i class="fa fa-lock mr-2"></i>Lost password?</button>
                                    <button type="submit" class="btn btn-success float-right">Login</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Password Recovery Form -->
                <div id="recoverform" style="display: none;">
                    <div class="text-center">
                        <span class="text-white">Enter your email to receive password recovery instructions.</span>
                    </div>

                    <div class="row mt-3">
                        <form class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-danger text-white"><i class="ti-email"></i></span>
                                </div>
                                <input type="email" class="form-control form-control-lg" placeholder="Email Address" required>
                            </div>

                            <div class="row mt-3 pt-2 border-top border-secondary">
                                <div class="col-12 text-center">
                                    <button type="button" class="btn btn-success" id="to-login">Back To Login</button>
                                    <button type="submit" class="btn btn-info float-right">Recover</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <diV class="text-center">© Copyright <b>NirmaIT</b> All Rights Reserved</diV>

    <!-- Required JS -->
    <script src="assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize tooltip
            $('[data-toggle="tooltip"]').tooltip();

            // Hide preloader
            $(".preloader").fadeOut();

            // Toggle login & recover form
            $("#to-recover").click(function() {
                $("#loginform").slideUp();
                $("#recoverform").fadeIn();
            });

            $("#to-login").click(function() {
                $("#recoverform").fadeOut();
                $("#loginform").fadeIn();
            });

            // Handle Login Form Submission
            $("#loginForm").submit(function(event) {
                event.preventDefault(); // Prevent page reload

                let email = $("input[name='email']").val().trim();
                let password = $("input[name='password']").val().trim();

                if (!email || !password) {
                    alert("Please enter both email and password.");
                    return;
                }

                $.ajax({
                    url: "https://mondolmotors.com/api/login.php",

                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({
                        email: email,
                        password: password
                    }),
                    success: function(response) {
                        console.log("Server Response:", response);

                        if (response.status === "success") {
                            alert(response.message);
                            window.location.href = "index.php"; // Redirect to dashboard
                        } else {

                            alert("Login failed: " + response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error("Login Error:", xhr.responseText);
                        alert("An error occurred while logging in.");
                    }
                });
            });

        });
    </script>
</body>

</html>