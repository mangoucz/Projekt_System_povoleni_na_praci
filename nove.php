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
    </div>
    <button class="accordion">Povolení k...</button>
    <div class="panel"><br>
        <label class="container">K práci na zařízení
            <input type="checkbox">
            <span class="checkmark"></span>
        </label>
        <label class="container">Ke svařování a práci s otevřeným ohněm
            <input type="checkbox">
            <span class="checkmark"></span>
        </label>
        <label class="container">Ke vstupu do zařízení nebo pod úroveň terénu
            <input type="checkbox">
            <span class="checkmark"></span>
        </label>
        <label class="container">K práci v prostředí s nebezpečím výbuchu
            <input type="checkbox">
            <span class="checkmark"></span>
        </label>
        <label class="container">K předání a převzetí zařízení do opravy a do provozu
            <input type="checkbox">
            <span class="checkmark"></span>
        </label>
        <!-- <input type="checkbox" name="povolení" value="zarizeni"><br>
        <input type="checkbox" name="povolení" value="svarovani"><br>
        <input type="checkbox" name="povolení" value="vstup"><br>
        <input type="checkbox" name="povolení" value="vybuch"><br>
        <input type="checkbox" name="povolení" value="oprava"><br> -->
    </div>
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
            text-align: center;
        }
        .header{
            background-color: #b6c7e2;
            width: 100%;
            padding: 0.5% 0;
        }
        /* Style the buttons that are used to open and close the accordion panel */
        .accordion {
            background-color: #eee;
            color: #444;
            cursor: pointer;
            padding: 18px;
            width: 100%;
            text-align: left;
            border: none;
            outline: none;
            transition: 0.4s;
        }

        /* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
        .active, .accordion:hover {
            background-color: #ccc;
        }

        /* Style the accordion panel. Note: hidden by default */
        .panel {
            padding: 0 18px;
            background-color: white;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.2s ease-out;
        }
        /* Customize the label (the container) */
            .container {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 22px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            }

            /* Hide the browser's default checkbox */
            .container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
            }

            /* Create a custom checkbox */
            .checkmark {
            position: absolute;
            top: 0;
            left: 28%;
            height: 25px;
            width: 25px;
            background-color: #eee;
            }

            /* On mouse-over, add a grey background color */
            .container:hover input ~ .checkmark {
            background-color: #ccc;
            }

            /* When the checkbox is checked, add a blue background */
            .container input:checked ~ .checkmark {
            background-color: #2196F3;
            }

            /* Create the checkmark/indicator (hidden when not checked) */
            .checkmark:after {
            content: "";
            position: absolute;
            display: none;
            }

            /* Show the checkmark when checked */
            .container input:checked ~ .checkmark:after {
            display: block;
            }

            /* Style the checkmark/indicator */
            .container .checkmark:after {
            left: 9px;
            top: 5px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
            }
    </style>
    <script>
        var acc = document.getElementsByClassName("accordion");

        for (var i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.maxHeight) 
                    panel.style.maxHeight = null;
                else 
                    panel.style.maxHeight = panel.scrollHeight + "px";
            });
        }
    </script>
</body>
</html>