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

        $hasError = $nameErr || $emailErr || $productIdErr || $purchaseDateErr || $issueErr;

        if ($hasError) {
            // Redirect back with values + errors
            $qs = http_build_query([
                'name' => $_POST['name'],       // original value
                'email' => $_POST['email'],
                'productId' => $_POST['productId'],
                'purchaseDate' => $_POST['purchaseDate'],
                'issue' => $_POST['issue'],
                'nameErr' => $nameErr,
                'emailErr' => $emailErr,
                'productIdErr' => $productIdErr,
                'purchaseDateErr' => $purchaseDateErr,
                'issueErr' => $issueErr
            ]);
            header("Location: index.html?$qs");
            exit;
        } else {
            // No error -> go to success page
            $qs = http_build_query([
                'name' => $_POST['name'],       // original value
                'email' => $_POST['email'],
                'productId' => $_POST['productId'],
                'purchaseDate' => $_POST['purchaseDate'],
                'issue' => $_POST['issue'],
            ]);
            header("Location: result.html?$qs");
            exit;
        }
    }
    
?>