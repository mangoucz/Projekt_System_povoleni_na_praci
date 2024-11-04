<?php
    session_start();

    if (isset($_SESSION['uziv']))
        $uziv = $_SESSION['uziv'];
    else{
        header("Location: login.html");
        exit();    
    }
    require_once 'server.php';
?>
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
    <div class="header">
        <h1>NOVÉ POVOLENÍ</h1>
    </div><br>
    <form action="" method="post">
        <table>
            <tr>
                <th>Rizikovost</th>
                <th>Interní</th>
                <th>Externí</th>
                <th>Počet osob</th>
                <th>Od</th>
                <th>Do</th>
                <th>Povolení</th>
            </tr>
            <tr>
                <td><input type="range" name="rizikovost"></td>
                <td><input type="text" name="interni"></td>
                <td><input type="text" name="externi"></td>
                <td><input type="text" name="pocetOs"></td>
                <td><input type="date" name="povolOd"></td>
                <td><input type="date" name="povolDo"></td>
                <td rowspan="5">
                    <div class="panel">
                        <label class="container">K práci na zařízení
                            <input type="checkbox">
                            <span class="checkbox"></span>
                        </label>
                        <label class="container">Ke svařování a práci s otevřeným ohněm
                            <input type="checkbox">
                            <span class="checkbox"></span>
                        </label>
                        <label class="container">Ke vstupu do zařízení nebo pod úroveň terénu
                            <input type="checkbox">
                            <span class="checkbox"></span>
                        </label>
                        <label class="container">K práci v prostředí s nebezpečím výbuchu
                            <input type="checkbox">
                            <span class="checkbox"></span>
                        </label>
                        <label class="container">K předání a převzetí zařízení do opravy a do provozu
                            <input type="checkbox">
                            <span class="checkbox"></span>
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <th>Provoz</th>
                <td><input type="text" name="provoz"></td>
                <th>Název(číslo) objektu</th>
                <td><input type="text" name="objekt"></td>
                <td><input type="time" name="hodOd"></td>
                <td><input type="time" name="hodDo"></td>
                <!--<th>Číslo zařízení</th>
                <td><input type="text" name="CZarizeni"></td>-->
            </tr>
            <tr>
                <th>Název zařízení</th>
                <td colspan="5"><input type="text" name="NZarizeni"></td>
            </tr>
            <tr>
                <th>Popis, druh a rozsah práce</th>
                <td colspan="5"><input type="text" name="prace"></td>
            </tr>
            <tr>
                <th>Seznámení s riziky pracoviště dle karty č.</th>
                <td colspan="5"><input type="text" name="rizikaPrac"></td>
            </tr>   
        </table>
    </form>
    <div class="footer">
        <p style="margin-left: 1%;">Přihlášený uživatel: <?php echo $uziv ?> </p>
        <img src="Indorama.png" style="margin-right: 5.7%;">
        <a href="login.html">
            <img src="logout_icon.png" width="78%" style="cursor: pointer;">
        </a>
    </div>
    <style>
        body {
            align-items: center;
        }
        .header{
            background-color: #b6c7e2;
            width: 100%;
            padding: 0.5% 0;
            text-align: center;
        }
        table{
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #bcd4ef;
            margin: 0 auto;
            width: 95%;
        }
        input[type="text"],
        input[type="date"],
        input[type="time"],
        input[type="range"] {
            width: 90%;
            padding: 5px;
            border: 1px solid #cccccc;
            border-radius: 3px;
        }
        .panel {
            padding: 0 18px;
        }
        .container {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 22px;
        }
        .container input {
            display: none;
        }
        .checkbox {
            position: absolute;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
        }
        .container:hover input ~ .checkbox {
            background-color: #ccc;
        }
        .container input:checked ~ .checkbox {
            background-color: #2196F3;
        }
        .checkbox:after {
            content: "";
            position: absolute;
            display: none;
        }
        .container input:checked ~ .checkbox:after {
            display: block;
        }
        .container .checkbox:after {
            left: 9px;
            top: 5px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }
    </style>
</body>
</html>