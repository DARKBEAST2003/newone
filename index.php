<?php
    function filterName($field=""){
        $field = filter_var(trim($field), FILTER_SANITIZE_STRING);
        if (filter_var($field, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-zA-Z\s]+$/')))){
            return $field;
        } else {
            return FALSE;
        }
    }

    function filterId($field=""){
        $field = filter_var(trim($field), FILTER_SANITIZE_STRING);
        if (filter_var($field, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-zA-Z0-9\s]+$/')))){
            return $field;
        } else {
            return FALSE;
        }
    }
    
    function filterEmail($field=""){
        $field = filter_var(trim($field), FILTER_SANITIZE_EMAIL);
        if (filter_var($field, FILTER_VALIDATE_EMAIL)){
            return $field;
        } else {
            return FALSE;
        }
    }

    function filterString($field=""){
        $field = filter_var(trim($field), FILTER_SANITIZE_STRING);
        if (!empty($field)){
            return $field;
        } else {
            return FALSE;
        }
    }

    function filterDate($field = ""){
        if (empty($field)) {
            return false;
        }

        try {
            $date = new DateTime($field);   // Convert string to DateTime
            $today = new DateTime();        // Current date & time

            if ($date < $today) {
                return $field; // Return the original date string
            }

            return false; // Not a future date
        } catch (Exception $e) {
            return false; // Invalid date format
        }
    }

    $name = $email = $subject = $comments = "";
    $nameErr = $emailErr = $subjectErr = $commentsErr = "";

    if ($_SERVER['REQUEST_METHOD'] == "POST"){
        if (empty($_POST['name'])){
            $nameErr = "Please enter your full name";
        } else {
            $name = filterName($_POST['name']);
            if ($name == FALSE){
                $nameErr = "Please enter a valid full name";
            }
        }

        if (empty($_POST['email'])){
            $emailErr = "Please enter your email";
        } else {
            $email = filterEmail($_POST['email']);
            if ($email == FALSE){
                $emailErr = "Please enter a valid email";
            }
        }

        if (empty($_POST['productId'])){
            $productIdErr = "Please enter your product name or ID";
        } else {
            $productId = filterId($_POST['productId']);
            if ($productId == FALSE){
                $productIdErr = "Please enter a valid product name or ID";
            }
        }

        if (empty($_POST['purchaseDate'])) {
            $purchaseDateErr = "Please set your product purchase date";
        } else {
            $purchaseDate = filterDate($_POST['purchaseDate']);
            if ($purchaseDate === false) {
                $purchaseDateErr = "Please set a valid purchase date";
            }
        }

        if (empty($_POST['issue'])){
            $issueErr = "Please describe your issue";
        } else {
            $issue = filterString($_POST['issue']);
            if ($issue == FALSE){
                $issueErr = "Please describe a valid issue";
            }
        }

        if (empty($_FILES['productImg']['name'])) {
            $productImgErr = "Please upload your damaged product picture, format: .jpg file";
        } else {
            $fileTmp  = $_FILES['productImg']['tmp_name']; // Temp file path
            $fileName = $_FILES['productImg']['name'];     // Original name
            $fileType = mime_content_type($fileTmp);       // Detect MIME type
            $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // Extract extension

            // Check if it's a JPG
            if (($fileType !== "image/jpeg" && $fileType !== "image/pjpeg") || $fileExt !== "jpg") {
                $productImgErr = "Only .jpg files are allowed";
            } else {
                $productImgErr = ""; // Valid JPG
            }
        }
    }
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <style>
        body {
            background: rgb(218, 218, 218);
        }

        .container {
            background: white;
            padding: 1rem;
            margin: 2rem auto;
            border-radius: 1rem;
        }

        h1 {
            text-align: center;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }

        .form-group {
            margin: 1rem 0;
        }

        .form-group input {
            width: 100%;
        }
        
        .submit {
            background: rgb(0, 90, 224);
            color: white;
            border: none;
            padding: .35rem .7rem;
            border-radius: .35rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Product Complaint Form</h1>
        <form action="index.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name: <sup>*</sup></label>
                <input  class="form-control" type="text" name="name" id="name" value="<?php echo @$name ?>">
                <span class="error"><?= @$nameErr ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email Address: <sup>*</sup></label>
                <input class="form-control" type="email" name="email" id="email" value="<?php echo @$email ?>">
                <span class="error"><?= @$emailErr ?></span>
            </div>
            <div class="form-group">
                <label for="productId">Product Name or ID:</label>
                <input class="form-control" type="text" name="productId" id="productId" value="<?php echo @$productId ?>">
                <span class="error"><?= @$productIdErr ?></span>
            </div>
            <div class="form-group">
                <label for="purchaseDate">Date of Purchase:</label><br>
                <input class="purchaseDate" type="date" name="purchaseDate" id="purchaseDate" value="<?php echo @$purchaseDate ?>">
                <span class="error"><?= @$purchaseDateErr ?></span>
            </div>
            <div class="form-group">
                <label for="issue">Describe the issue: <sup>*</sup></label>
                <textarea class="form-control" name="issue" id="issue" rows="5" cols="30" value="<?php echo @$issue ?>"></textarea>
                <span class="error"><?= @$issueErr ?></span>
            </div>
            <div class="form-group">
                <label for="productImg">Upload Picture of Damaged Product:</label><br>
                <input class="productImg" type="file" name="productImg" id="productImg">
                <span class="error"><?= @$productImgErr ?></span>
            </div>
            <br>
            <input type="submit" class="submit" name="submit" value="Submit Complaint">
        </form>
    </div>
</body>
</html>