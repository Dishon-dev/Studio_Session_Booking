<?php include './session.php'; ?>
<?php
    //name spaces
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    //define variables
    $email = $email = $subject = $message = "";

    //sanitize form data
    function sanitize($data) {
        global $conn;
        $data = @trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        
        return mysqli_real_escape_string($conn,$data);
    }

    //isset($_POST['send'])
    if($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (empty($_POST['name'])) {
        $_SESSION['status'] = "Name is required!";
        echo $_SESSION['status'];
        unset($_SESSION['status']);
    } 
    else if (!preg_match('/^[a-zA-Z]+[a-zA-Z\'\s]*$/', $_POST['name'])) {
        $_SESSION['status'] = "Invalid name. Only alphabets characters are allowed.";
        echo $_SESSION['status'];
        unset($_SESSION['status']);
    } 
    else if (empty($_POST['email'])) {
        $_SESSION['status'] = "Email is required";
        echo $_SESSION['status'];
        unset($_SESSION['status']);
    } 
    else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['status'] = "Invalid email format";
        echo $_SESSION['status'];
        unset($_SESSION['status']);
    } 
    else if (empty($_POST['subject'])) {
        $_SESSION['status'] = "subject is required";
        echo $_SESSION['status'];
        unset($_SESSION['status']);
    } 
    else if (empty($_POST['message'])) {
        $_SESSION['status'] = "Message is required";
        echo $_SESSION['status'];
        unset($_SESSION['status']);
    }  else {
        unset($_SESSION['status']);

        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $subject = sanitize($_POST['subject']);
        $message = sanitize($_POST['message']);


        $mail = new PHPMailer(true); //new PHPMailer;

        //$mail->SMTPDebug = 3;

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls'; //ssl
        $mail->Port = 587; //465 --> ssl
        $mail->Username = 'boncestudios@gmail.com';
        $mail->Password = ''; //app password -> security -> generate app password

        $mail->setFrom($email); //email from input
        $mail->addAddress('boncestudios@gmail.com', 'Bounce Studio');

        $mail->isHTML(true);

        //$mail->addAttachment()
        $mail->Subject = $subject; //$subject
        $mail->Body = $message;

        if($mail->send()) {
            $mail = new PHPMailer(true); 

        //$mail->SMTPDebug = 3;

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587; 
        $mail->Username = 'boncestudios@gmail.com';
        $mail->Password = ''; 

        $mail->setFrom('boncestudios@gmail.com', 'Bounce Studio'); 
        $mail->addAddress($email);

        $mail->isHTML(true);
        //$mail->addAttachment()
        $mail->Subject = "Acknowlegement"; 

        $mail_template = "
          <h3>Hello $name,</h3>
          <h4>Thank you for choosing Bounce Studio.<br />We will get back to you in the meantime.</h4>
          <br /><br />
          <h4>Regards<br />Bounce Studio.<h4/>
          ";
        $mail->Body = $mail_template;
        $mail->send();

            $_SESSION['status'] = "Your message has been sent successfuly!";
            echo $_SESSION['status'];
            unset($_SESSION['status']);

        } else {
            $_SESSION['status'] = "Your message could not be delivered successfuly, please try again.";
            echo $_SESSION['status'];
            unset($_SESSION['status']);
        } 

        $mail->smtpClose();
    }
}
?>