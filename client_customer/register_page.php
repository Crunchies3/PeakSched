<?php

require_once "./php/config.php";

$firstName = $lastName = $emailAddress = $mobileNumber = $password = $confirmPassword = $checkbox =  $customerId = $hashedPassword = "";

// variables that will hold error messages
$firstName_err = $lastName_err = $emailAddress_err = $mobileNumber_err = $password_err = $confirmPassword_err = $checkBox_err = "";

validateInputs();



function validateInputs()
{
    global $firstName_err, $lastName_err, $emailAddress_err, $mobileNumber_err, $password_err, $confirmPassword_err, $checkBox_err;
    global $firstName, $lastName, $emailAddress, $mobileNumber, $password, $confirmPassword, $checkbox, $customerId, $hashedPassword;

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        return;
    }

    $customerId = rand(100000, 200000);

    while (!isCustomerIdUnique($customerId)) {
        $customerId = rand(100000, 200000);
    }

    // validate firstname
    $firstName = trim($_POST["firstName"]);

    if (empty($firstName)) {
        $firstName_err = "Please enter your first name.";
    }

    $lastName = trim($_POST["lastName"]);

    if (empty($lastName)) {
        $lastName_err = "Please enter your last name.";
    }

    if (empty(trim($_POST["email"]))) {
        $emailAddress_err = "Please enter your email address.";
    } else if (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $emailAddress_err = "Please enter a valid email address";
    } else if (!isEmailUnique(trim($_POST["email"]))) {
        $emailAddress_err = "Email already exist";
    } else {
        $emailAddress = trim($_POST["email"]);
    }

    $mobileNumber = trim($_POST["mobile"]);

    if (empty($mobileNumber)) {
        $mobileNumber_err = "Please enter your mobile number.";
    } else if (!is_numeric($mobileNumber)) {
        $mobileNumber_err = "Please enter a valid mobile number.";
    }

    $password = trim($_POST["password"]);

    if (empty($password)) {
        $password_err = "Please enter a password.";
    } else if (strlen($password) < 8) {
        $password_err = "Please enter a password with atleast 8 characters.";
    } elseif (!preg_match("#[0-9]+#", $password)) {
        $password_err = "Your Password Must Contain At Least 1 Number!";
    } elseif (!preg_match("#[A-Z]+#", $password)) {
        $password_err = "Your Password Must Contain At Least 1 Capital Letter!";
    } elseif (!preg_match("#[a-z]+#", $password)) {
        $password_err = "Your Password Must Contain At Least 1 Lowercase Letter!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

    $confirmPassword = trim($_POST["confirmPassword"]);

    if (empty($confirmPassword)) {
        $confirmPassword_err = "Please enter a password.";
    } else if ($confirmPassword != $password) {
        $confirmPassword_err = "Password does not match.";
    }

    if (!isset($_POST['checkBox'])) {
        $checkBox_err = "Please Agree before submitting";
    } else {
        $checkbox = 'checked';
    }

    if (empty($firstName_err) && empty($lastName_err) && empty($emailAddress_err) && empty($mobileNumber_err) && empty($password_err) && empty($confirmPassword_err) && empty($checkBox_err)) {
        insertCustomerDetailstoDB($customerId, $firstName, $lastName, $emailAddress, $mobileNumber, $hashedPassword);
    }
}

function isEmailUnique($email)
{
    global $conn;

    try {
        $stmt = $conn->prepare("SELECT * FROM tbl_customer WHERE email = ?");
        $stmt->bind_param("s", $email);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt->close();
            return false;
        }
        $stmt->close();
        return true;
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}

function isCustomerIdUnique($customerId)
{
    global $conn;

    try {
        $stmt = $conn->prepare("SELECT * FROM tbl_customer WHERE customerid = ?");
        $stmt->bind_param("s", $customerId);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt->close();
            return false;
        }
        $stmt->close();
        return true;
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}

function insertCustomerDetailstoDB($customerId, $firstName, $lastName, $emailAddress, $mobileNumber, $hashedPassword)
{
    global $conn;

    try {
        $stmt = $conn->prepare("INSERT INTO tbl_customer (customerid, firstname, lastname, email, mobilenumber, password) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $customerId, $firstName, $lastName, $emailAddress, $mobileNumber, $hashedPassword);
        $stmt->execute();
        $stmt->close();
        header("location: index.php");
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <title>Create </title>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">

        <div class="row rounded-5 box-area shadow-lg">

            <div class="col-lg-6 d-flex justify-content-center align-items-center flex-column left-box" style="background: #FDFDFD;">
                <div class="feature-image mb-3">
                    <img src="./images/twin-peaks-logo.png" alt="Twin Peaks" style="width: 250px;">
                </div>
            </div>

            <div class="col-lg-6 right-box">
                <div class="row align-items-center">
                    <div class="header-text mb-4">
                        <h1>Create Account</h1>
                    </div>
                    <form class="row g-2" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
                        <div class="col-md-6 mb-2">
                            <input name="firstName" type="text" class="form-control input-field <?php echo (!empty($firstName_err)) ? 'is-invalid' : ''; ?>" placeholder="First name" aria-label="First name" value="<?php echo $firstName; ?>">
                            <div class="invalid-feedback">
                                <?php echo $firstName_err; ?>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <input name="lastName" type="text" class="form-control input-field <?php echo (!empty($lastName_err)) ? 'is-invalid' : ''; ?>" placeholder="Last name" aria-label="Last name" value="<?php echo $lastName; ?>">
                            <div class="invalid-feedback">
                                <?php echo $lastName_err; ?>
                            </div>
                        </div>
                        <div class="mb-2 col-12">
                            <input name="email" type="email" class="form-control fs-6 input-field <?php echo (!empty($emailAddress_err)) ? 'is-invalid' : ''; ?>" placeholder="Email Adress" value="<?php echo $emailAddress; ?>">
                            <div class="invalid-feedback">
                                <?php echo $emailAddress_err; ?>
                            </div>
                        </div>
                        <div class="mb-2 col-12">
                            <input name="mobile" type="text" class="form-control fs-6 input-field <?php echo (!empty($mobileNumber_err)) ? 'is-invalid' : ''; ?>" placeholder="Mobile Number" value="<?php echo $mobileNumber; ?>">
                            <div class="invalid-feedback">
                                <?php echo $mobileNumber_err; ?>
                            </div>
                        </div>
                        <div class="mb-2 col-12">
                            <input name="password" type="password" class="form-control fs-6 input-field <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" placeholder="Password" value="<?php echo $password; ?>">
                            <div class="invalid-feedback">
                                <?php echo $password_err; ?>
                            </div>
                        </div>
                        <div class="mb-2 col-12">
                            <input name="confirmPassword" type="password" class="form-control fs-6 input-field <?php echo (!empty($confirmPassword_err)) ? 'is-invalid' : ''; ?>" placeholder="Confirm Password" value="<?php echo $confirmPassword; ?>">
                            <div class="invalid-feedback">
                                <?php echo $confirmPassword_err; ?>
                            </div>
                        </div>
                        <div class="form-check mb-4">
                            <input name="checkBox" type="checkbox" id="formCheck" class="form-check-input chk-box <?php echo (!empty($checkBox_err)) ? 'is-invalid' : ''; ?>" <?php echo $checkbox; ?>>
                            <label class="lbl-chk">
                                <small>I agree to the <a href="#">terms of services</a> and <a href="#">privacy</a>
                                    policy
                                </small>
                            </label>
                            <div class="invalid-feedback">
                                <?php echo $checkBox_err; ?>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <button type="submit" name="submit" class="btn btn-lg w-100 fs-6" style="background-color: #1B75BB; color: whitesmoke; font-weight: 600;">Sign
                                In</button>
                        </div>
                        <div style="text-align: center;">
                            <small>Already have an account? <a href="./index.php" style="text-decoration: none; color: #1B75BB;">Sign In!</a></small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>