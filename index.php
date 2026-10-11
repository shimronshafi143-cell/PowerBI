<?php
session_start();

$error = "";
$success = "";

/* =================================
   GOOGLE RECAPTCHA KEYS
   ================================= */

$siteKey = "YOUR_SITE_KEY";
$secretKey = "YOUR_SECRET_KEY";


/* =================================
   FORM SUBMIT
   ================================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    /* -------------------------------
       CHECK RECAPTCHA
       ------------------------------- */

    if (!isset($_POST["g-recaptcha-response"]) ||
        empty($_POST["g-recaptcha-response"])) {

        $error = "Please verify that you are not a robot.";

    } else {

        $captchaResponse = $_POST["g-recaptcha-response"];

        // Verify captcha with Google
        $verifyUrl = "https://www.google.com/recaptcha/api/siteverify";

        $data = [
            "secret" => $secretKey,
            "response" => $captchaResponse
        ];

        $options = [
            "http" => [
                "header"  => "Content-type: application/x-www-form-urlencoded\r\n",
                "method"  => "POST",
                "content" => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);

        $result = file_get_contents($verifyUrl, false, $context);

        $resultData = json_decode($result);


        /* -------------------------------
           CAPTCHA SUCCESS
           ------------------------------- */

        if ($resultData->success) {

            /* -------------------------------
               EMAIL & PASSWORD CHECK
               ------------------------------- */

            if ($email == "admin@gmail.com" &&
                $password == "123456") {

                $_SESSION["login"] = true;
                $_SESSION["email"] = $email;

                $success = "Login successful!";

                // অন্য page এ নিতে চাইলে:
                // header("Location: home.php");
                // exit();

            } else {

                $error = "Invalid email or password.";
            }

        } else {

            $error = "Captcha verification failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login</title>


    <!-- =================================
         GOOGLE RECAPTCHA SCRIPT
         ================================= -->

    <script src="https://www.google.com/recaptcha/api.js"
            async
            defer>
    </script>


    <style>

        * {
            box-sizing: border-box;
        }

        body {

            margin: 0;

            font-family: Arial, sans-serif;

            background: #f2f4f7;

            display: flex;

            justify-content: center;

            align-items: center;

            min-height: 100vh;
        }


        /* =========================
           LOGIN BOX
           ========================= */

        .login-box {

            width: 420px;

            background: white;

            padding: 35px 40px;

            border-radius: 12px;

            box-shadow:
                0 5px 25px rgba(0,0,0,0.12);
        }


        /* =========================
           TITLE
           ========================= */

        .login-box h1 {

            text-align: center;

            margin-bottom: 30px;

            color: #222;
        }


        /* =========================
           FORM GROUP
           ========================= */

        .form-group {

            margin-bottom: 20px;
        }


        label {

            display: block;

            margin-bottom: 8px;

            font-weight: bold;

            color: #333;
        }


        /* =========================
           INPUT
           ========================= */

        input[type="email"],
        input[type="password"] {

            width: 100%;

            padding: 13px;

            border: 1px solid #bbb;

            border-radius: 6px;

            font-size: 16px;

            outline: none;
        }


        input[type="email"]:focus,
        input[type="password"]:focus {

            border-color: #333;
        }


        /* =========================
           CAPTCHA
           ========================= */

        .captcha-box {

            margin-top: 15px;

            margin-bottom: 25px;
        }


        /* =========================
           SUBMIT BUTTON
           ========================= */

        .submit-btn {

            width: 100%;

            padding: 13px;

            background: #222;

            color: white;

            border: none;

            border-radius: 6px;

            font-size: 17px;

            cursor: pointer;
        }


        .submit-btn:hover {

            background: #444;
        }


        /* =========================
           ERROR
           ========================= */

        .error {

            background: #ffe0e0;

            color: #c00000;

            padding: 10px;

            border-radius: 5px;

            margin-bottom: 20px;

            text-align: center;
        }


        /* =========================
           SUCCESS
           ========================= */

        .success {

            background: #dff5e1;

            color: #16852a;

            padding: 10px;

            border-radius: 5px;

            margin-bottom: 20px;

            text-align: center;
        }

    </style>

</head>


<body>


<div class="login-box">

    <h1>Login</h1>


    <!-- =========================
         ERROR MESSAGE
         ========================= -->

    <?php if ($error != "") { ?>

        <div class="error">

            <?php echo $error; ?>

        </div>

    <?php } ?>


    <!-- =========================
         SUCCESS MESSAGE
         ========================= -->

    <?php if ($success != "") { ?>

        <div class="success">

            <?php echo $success; ?>

        </div>

    <?php } ?>


    <!-- =========================
         LOGIN FORM
         ========================= -->

    <form method="POST">


        <!-- EMAIL -->

        <div class="form-group">

            <label>Email:</label>

            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                required
            >

        </div>


        <!-- PASSWORD -->

        <div class="form-group">

            <label>Password:</label>

            <input
                type="password"
                name="password"
                placeholder="Enter your password"
                required
            >

        </div>


        <!-- =========================
             GOOGLE RECAPTCHA
             ========================= -->

        <div class="captcha-box">

            <div
                class="g-recaptcha"
                data-sitekey="<?php echo $siteKey; ?>">
            </div>

        </div>


        <!-- SUBMIT -->

        <button
            type="submit"
            class="submit-btn">

            Submit

        </button>


    </form>

</div>


</body>

</html>