<?php
    session_start();
    if (isset($_SESSION['uziv']))
        $uziv = $_SESSION['uziv'];
    else{
        header("Location: login.php");
        exit();    
    }
    require_once 'server.php';

    $sql = "SELECT 
                CONCAT(z.jmeno, ' ', z.prijmeni) AS jmeno,
                z.funkce
            FROM Zamestnanci AS z
            WHERE uziv_jmeno = '$uziv';";
    
    $result = sqlsrv_query($conn, $sql);
    if ($result === FALSE)
        die(print_r(sqlsrv_errors(), true));

    $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($result);

    $jmeno = $zaznam['jmeno'];
    $funkce = $zaznam['funkce'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_POST['subNove']) {
            header("Location: nove.php");
            exit();
        }
        else {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
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
    <div class="header">
        <img src="Indorama.png" class="logo">
        <h1>SYSTÉM POVOLENÍ NA PRÁCI</h1>
        <div class="headerB">
            <form action="" method="post">
                <input type="submit" value="Nové povolení" name="subNove">
            </form>
            <div class="uziv">
                <img src="user_icon.png" width="28%" style="margin-right: 2%;">
                <div class="uziv_inf">
                    <p><?php echo $jmeno; ?></p>
                    <p style="font-size: 12px; margin-left: 2px;"><?php echo $funkce; ?></p>
                </div>
            </div>
            <a href="login.php">
                <img src="logout_icon.png" width="78%" style="cursor: pointer;">
            </a>
        </div>
    </div>
    <div class="container">
        <?php
            $mesic = isset($_GET['mesic']) ? $_GET['mesic'] : date('n');
            $rok = isset($_GET['rok']) ? $_GET['rok'] : date('Y');
            $mesicCZ = [
                1 => 'Leden',
                2 => 'Únor',
                3 => 'Březen',
                4 => 'Duben',
                5 => 'Květen',
                6 => 'Červen',
                7 => 'Červenec',
                8 => 'Srpen',
                9 => 'Září',
                10 => 'Říjen',
                11 => 'Listopad',
                12 => 'Prosinec'
            ];
        ?>
        <fieldset class="prehled-field">
            <legend>Moje povolení:</legend>
            <form method="get">
                <div class="form-container">
                    <div class="date-selection">
                        <select name="mesic" id="mesic" class="custom-select">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= ($m == $mesic) ? 'selected' : '' ?>>
                                    <?= $mesicCZ[$m] ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <select name="rok" id="rok" class="custom-select">
                            <?php for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?= $y ?>" <?= ($y == $rok) ? 'selected' : '' ?>>
                                    <?= $y ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <div class="button-container">
                            <input type="submit" value="Zobrazit" class="submit-button">
                        </div>
                    </div>
                </div>
            </form>
            <div class="prehledy"  style="max-height: 380px; overflow-y: auto;">
                <div class="prehled">
                    <div class="prehled-head">
                        <p>Datum: <strong>18. 12. 2024</strong></p>
                    </div>
                    <div class="prehled-body">
                        <div>
                            <p>Ev. č. 33868</p>
                            <p>Stav: <span class="status">Čeká na schválení</span></p>
                        </div>
                        <div>
                            <a href="#" class="link">Podrobnosti &gt;</a>
                        </div>
                    </div>
                </div>
                <div class="prehled">
                    <div class="prehled-head">
                        <p>Datum: <strong>18. 12. 2024</strong></p>
                    </div>
                    <div class="prehled-body">
                        <div>
                            <p>Ev. č. 33868</p>
                            <p>Stav: <span class="status">Čeká na schválení</span></p>
                        </div>
                        <div>
                            <a href="#" class="link">Podrobnosti &gt;</a>
                        </div>
                    </div>
                </div>
                <div class="prehled">
                    <div class="prehled-head">
                        <p>Datum: <strong>18. 12. 2024</strong></p>
                    </div>
                    <div class="prehled-body">
                        <div>
                            <p>Ev. č. 33868</p>
                            <p>Stav: <span class="status">Čeká na schválení</span></p>
                        </div>
                        <div>
                            <a href="#" class="link">Podrobnosti &gt;</a>
                        </div>
                    </div>
                </div>
                <div class="prehled">
                    <div class="prehled-head">
                        <p>Datum: <strong>18. 12. 2024</strong></p>
                    </div>
                    <div class="prehled-body">
                        <div>
                            <p>Ev. č. 33868</p>
                            <p>Stav: <span class="status">Čeká na schválení</span></p>
                        </div>
                        <div>
                            <a href="#" class="link">Podrobnosti &gt;</a>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
    <style>
        .container {
            padding: 20px;
            background-color: #edf4fb;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 900px;
        }
        .date-selection {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .custom-select {
            background-color: #ffffff;
            border: 1px solid #808080;
            border-radius: 5px;
            padding: 10px;
            font-size: 14px;
            color: #2C3E50;
            cursor: pointer;
            margin-right: 20px;
        }
        .prehled-field {
            border: 2px solid #003366;
            border-radius: 8px;
            padding: 20px;
            background-color: #ffffff;
        }

        legend {
            font-size: 1.2em;
            font-weight: bold;
            color: #003366;
        }

        .prehled {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-top: 3%;
            background-color: #fefefe;
        }

        .prehled-head {
            border-bottom: 1px solid #ccc;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }

        .prehled-head p {
            margin: 0;
            font-size: 0.9em;
            color: #555555;
        }

        .prehled-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .prehled-body p {
            margin: 0;
            font-weight: bold;
        }

        .prehled-body .status {
            color: #ff9900;
            font-weight: bold;
        }

        .link {
            color: #003366;
            text-decoration: none;
            font-weight: bold;
        }
        form {
            background-color: #FFFFFF; 
            width: 60%;
            text-align: center;
        }
        input[type="submit"] {
            background-color: #003366; 
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            margin: 2% 0;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #d40000;
        }

        @media (max-width: 660px) {
            .header {
                flex-direction: column;
                align-items: center;
            }
            .footer{
                display: flex;  
            }
            .logo {
                display: none;
            }
            h1 {
                margin: 5% 0;
                font-size: 1.5em;
            }
        }
    </style>
</body>
</html>