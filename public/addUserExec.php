<?php include './session.php'; ?>
<?php

    //define variables
    $username = $image = $email = $phone_number = $password = "";

    //sanitize form data
    function sanitize($data) {
        global $conn;
        $data = @trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        
        return mysqli_real_escape_string($conn,$data);
    }

    // Validate the form data
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $dir = './uploads/';

    $fileName = $_FILES['image']['name'];
    $fileTmpName = $_FILES['image']['tmp_name'];
    $fileError = $_FILES['image']['error'];

    if (empty($fileName)) {
        $_SESSION['status'] = "Your photo is required";
        header("location: adduser.php");
        exit(0);
    }
    else if (empty($_POST['username'])) {
        $_SESSION['status'] = "Username is required";
        header("location: adduser.php");
        exit(0);
    } 
    else if (!preg_match('/^[a-zA-Z]+[a-zA-Z-_\'\s]*$/', $_POST['username'])) {
        $_SESSION['status'] = "Invalid username. Only alphabet characters, hyphens, and underscores are allowed.";
        header("location: adduser.php");
        exit(0);
      //array_push($errors, "Invalid username. Only alphanumeric characters, hyphens, and underscores are allowed.")
    } 
    else if (empty($_POST['email'])) {
        $_SESSION['status'] = "Email is required";
        header("location: adduser.php");
        exit(0);
    } 
    else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['status'] = "Invalid email format";
        header("location: adduser.php");
        exit(0);
    } 
    else if (empty($_POST['phone'])) {
        $_SESSION['status'] = "Phone no. is required";
        header("location: adduser.php");
        exit(0);
    } 
    else if (!preg_match('/^[+][0-9]{10,}$/', $_POST['phone'])) {
        $_SESSION['status'] = "Invalid phone number! Should start with +country code...";
        header("location: adduser.php");
        exit(0);
    } 
    else if (empty($_POST['password'])) {
        $_SESSION['status'] = "Password is required";
        header("location: adduser.php");
        exit(0);
      } 
      else if ($_POST['password'] != $_POST['confirm_password']) {
        $_SESSION['status'] = "Passwords do not match";
        header("location: adduser.php");
        exit(0);
      }
      else if (!preg_match('/^(?=.*[A-Za-z])(?=.*[@#$%^&*-+=]).{8,24}$/', $_POST['password'])) {
        $_SESSION['status'] = "Password should contain atleast one special character & be atleast 8 characters!";
        header("location: adduser.php");
        exit(0);
      } else {

        $username = sanitize($_POST['username']);

        $filename = basename($_FILES['image']['name']);
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        $imageExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
  
        $allowedImageTypes = array('jpg', 'jpeg', 'png', 'gif');
  
        if(in_array($filetype, $allowedImageTypes)) {
            $imgName = uniqid("IMG-", true).'.'.$imageExtension;
            if($fileError === 0){
                if(move_uploaded_file($fileTmpName, $dir.$imgName)) {
                    $image = $imgName;
                } else {
                    $_SESSION['status'] = "Failed to upload file";
                    header("location: adduser.php");
                    exit(0);
                }
            }else {
                $_SESSION['status'] = "There was an error uploading the image";
                header("location: adduser.php");
                exit(0);
            }

          } else {
            $_SESSION['status'] = "Invalid image format! Only jpg, jpeg, png & gif are allowed";
            header("location: adduser.php");
            exit(0);
          }
        
        $email = sanitize($_POST['email']);
        $phone_number = sanitize($_POST['phone']);
        $password = sanitize($_POST['password']);
      


    //hash password --> $password = password_hash($pass, PASSWORD_BCRYPT);
    
    //check whether an account with the email exist
    $queryuser = "SELECT email FROM users WHERE email = '$email'";
    $userResult = mysqli_query($conn,$queryuser);

    if(mysqli_num_rows($userResult) > 0){
        //$errors[] = "This email is already used by someone else. Try a different email kindly"; //-->
        $_SESSION['status'] = "This email is already used by someone else. Try a different email kindly";
        header("location: adduser.php");
        exit(0);
    } else {
        //insert data
        $query = "INSERT INTO users(user_name, user_image, email, phone_no, password) VALUES('$username','$image','$email','$phone_number','$password')";

        $insertData = mysqli_query($conn,$query);

        //check if query was successful
        if($insertData) {
            $_SESSION['status'] = "User added successfully";
            header("location: adduser.php");
		    exit(0);
	    }else {
		    die("Something went wrong.\n Our team is working on it.\n Please try again after some few minutes.");
            //echo "<h3 style='color:red'>User not registered. Please try again.</h3>".mysqli_error($connection);
	    }

        // Close the database connection
        $conn->close(); 
    }
}
}
?>