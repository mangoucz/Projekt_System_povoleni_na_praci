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
    <div class="container">
        <h1>SYSTÉM POVOLENÍ NA PRÁCI</h1>
        <span class="separator">|</span>
        <div>
            <form action="" method="post">
                <input type="submit" value="Nové povolení" name="subLogin"><br>
                <input type="submit" value="Přehed a editace" name="subLogin"><br>
                <input type="submit" value="Archiv" name="subLogin"><br>
            </form>
        </div>
    </div>
    <div class="footer">
        <p>Přihlášený uživatel: <?php echo $uziv ?> </p>
        <img src="Indorama.png">
        <a href="login.html">Odhlásit se</a>
    </div>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Arial', sans-serif;
            background-color: #F0F8FF; 
            color: #003366;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
            padding: 0 17%;
        }
        .footer{
            margin-top: auto;
            padding: 10px 20px;
            font-size: 12px;
            color: #666666;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            position: fixed;
            bottom: 0;
            width: 100%;
            gap: 630px;
        }
        h1 {
            color: #003366; 
            font-size: 36px;
            margin-right: 20px;
        }
        h2 {
            color: #003366;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .separator {
            font-size: 100px;
            color: #003366;
            margin-right: 20px;
        }
        form {
            background-color: #FFFFFF; 
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        input[type="text"] {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #003366; 
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #003366; 
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 60%;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #d40000;
        }
        img {
            width: 16%;
            margin-top: 20px;
        }
        @media (max-width: 600px) {
            .container {
                flex-direction: column;
                align-items: center;
            }

            .separator {
                display: none;
            }

            img {
                width: 70%;
            }

            form {
                width: 90%;
            }
        }
    </style>
</body>
</html>