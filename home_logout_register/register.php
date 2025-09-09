<?php
include("../config/connectdb.php"); // เชื่อมต่อ DB

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    // ตรวจสอบ email ซ้ำ
    $verify_query = mysqli_query($conn, "SELECT * FROM users WHERE user_email ='$email'");

    if (mysqli_num_rows($verify_query) != 0) {
        $error_message = "This email is already used, try another one.";
    } else {
        // เพิ่มข้อมูลผู้ใช้ใหม่
        $insert = mysqli_query($conn, "INSERT INTO users(user_name,user_email,user_password,user_status,user_phone) 
            VALUES('$username','$email','$password','user','$phone')");

        if ($insert) {
            // เพิ่มเสร็จแล้วไปหน้า login
            header("Location: ../index.php");
            exit;
        } else {
            $error_message = "Error occurred. Please try again.";
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>Register</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="loginstyle.css">
</head>

<body>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <h2 class="text-center text-dark mt-5">Register Form</h2>
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
                                <input type="text" class="form-control" name="username" id="username"
                                    placeholder="Username" required>
                            </div>
                             <div class="mb-3">
                                <input type="text" class="form-control" name="phone" id="phone"
                                    placeholder="Phone" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" name="email" id="email"
                                    placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" name="password" id="password"
                                    placeholder="Password" required>
                            </div>

                            <div class="text-center">
                                <button type="submit" name="submit"
                                    class="btn btn-success px-5 mb-5 w-100">Register</button>
                            </div>

                            <div class="form-text text-center mb-5 text-dark">
                                Already Registered?
                                <a href="../index.php" class="text-dark fw-bold">Login Here</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
</body>

</html>
