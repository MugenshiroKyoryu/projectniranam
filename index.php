<?php
session_start();
include("config/connectdb.php"); // เชื่อมต่อ DB

// ตรวจสอบว่า user กดปุ่ม login หรือไม่
if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // ตรวจสอบ user ในตาราง users
    $result = mysqli_query($conn, "SELECT * FROM users WHERE user_email ='$username' AND user_password ='$password'") or die("Select Error");
    $row = mysqli_fetch_assoc($result);

    if (is_array($row) && !empty($row)) {
        // กำหนด session
        $_SESSION['valid'] = $row['user_email'];
        $_SESSION['id'] = $row['user_id'];
        $_SESSION['username'] = $row['user_name'];
        $_SESSION['password'] = $row['user_password'];

        // ไปหน้า home.php
        header("Location: home_logout_register\home.php");
        exit;
    } else {
        $error_message = "Wrong Email or Password";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>Login</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.3.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="css/loginstyle.css">
</head>

<body>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <h2 class="text-center text-dark mt-5">Login Form</h2>
                    <div class="text-center mb-5 text-dark">Made with Bootstrap</div>
                    <div class="card my-5">

                        <form class="card-body cardbody-color p-lg-5" method="post" action="">
                            <div class="text-center">
                                <img src="https://cdn.pixabay.com/photo/2016/03/31/19/56/avatar-1295397__340.png"
                                    class="img-fluid profile-image-pic img-thumbnail rounded-circle my-3" width="200px"
                                    alt="profile">
                            </div>

                            <!-- แสดงข้อความ error -->
                            <?php if (!empty($error_message)) { ?>
                                <div class="alert alert-danger text-center">
                                    <?php echo $error_message; ?>
                                </div>
                            <?php } ?>

                            <div class="mb-3">
                                <input type="email" class="form-control" name="username" id="username"
                                    placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" name="password" id="password"
                                    placeholder="Password" required>
                            </div>

                            <div class="text-center">
                                <button type="submit" name="submit"
                                    class="btn btn-primary px-5 mb-5 w-100">Login</button>
                            </div>

                            <div class="form-text text-center mb-5 text-dark">
                                Not Registered?
                                <a href="home_logout_register/register.php" class="text-dark fw-bold">Create an Account</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
</body>

</html>
