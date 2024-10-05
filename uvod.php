<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Systém povolení na práci</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>
    <?php
        session_start();

        require_once 'server.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subLogin'])) {
            $uziv = $_POST['uziv'];
            $_SESSION['uziv'] = $uziv;
        } 
        else{
            if (isset($_SESSION['uziv'])) 
                $uziv = $_SESSION['uziv'];
            else{
                header("Location: login.html");
                exit();
            }
        }
    ?>
    <div class="footer"></div>
        <p>Přihlášený uživatel: <?php echo $uziv ?> </p>
        <img src="Indorama.png">
        <a href="login.html">Odhlásit se</a>
    </div>
    <style>
        img {
            width: 12%;
        }
    </style>
</body>
</html>