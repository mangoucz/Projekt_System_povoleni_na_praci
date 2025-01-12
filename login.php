<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (isset($_POST['subLogin'])) {
            $uziv = $_POST['uziv'];
            $sql = "";
            $params = [];
            $error = "";        
            
            require_once 'server.php';

            $sql = "SELECT z.id_zam FROM Zamestnanci AS z WHERE z.uziv_jmeno = ?;";
            $params = [$uziv];
            $result = sqlsrv_query($conn, $sql, $params);    
            if ($result === FALSE)
                die(print_r(sqlsrv_errors(), true));
            $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

            if ($zaznam) {
                session_start();      
                $_SESSION['uziv'] = $zaznam['id_zam'];
                header("Location: uvod.php");
                exit();
            } 
            else 
                $error = "Uživatel nenalezen!";
            sqlsrv_free_stmt($result);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Systém povolení na práci</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>SYSTÉM POVOLENÍ NA PRÁCI</h1>
        <span class="separator">|</span>
        <form action="" method="post">
            <h2>Přihlášení</h2>
            <?php echo isset($error) ? '<p class="error">' . $error . '</p>' : "";?>
            <input type="text" name="uziv" placeholder="Přihlašovací jméno" required>
            <input type="submit" value="Přihlásit se!" name="subLogin"><br>
        </form>
    </div>
    <div class="footer">
        <img src="Indorama.png" width="200px">
    </div>
</body>
<style>
    .container {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin: 15% 0 0 0;
    }
    .separator {
        font-size: 100px;
        color: #003366;
        margin-right: 20px;
    }
    h1 {
        color: #003366; 
        font-size: 36px;
        margin: 0.9% 20px 0 0;
    }
    h2 {
        color: #003366;
        margin-bottom: 20px;
        font-size: 24px;
    }
    form {
        display: flex;
        flex-direction: column;
        background-color: #FFFFFF; 
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        max-width: 400px;
        width: 200%;
        text-align: center;
        align-items: center;
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

    .footer{
        display: none;
    }
    
    .error {
        width: 60%;
        color: #d40000;
        font-size: 16px;
        background-color: #ffe6e6;
        border: 1px solid #d40000;
        border-radius: 5px;
        padding: 10px;
        margin: 1px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 660px) {
        body{
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .logo{
            display: none;
        }
        .container {
            text-align: center;
            flex-direction: column;
            width: 90%;
        }
        .footer{
            display: flex;
        }
        h1{
            margin: 0 0 0 0;
        }
        .separator {
            display: none;
        }
        form {
            width: 90%;
            margin-top: 30%;
        }
    }
</style>
</html>