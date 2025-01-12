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
            WHERE z.id_zam = ?;";
    $params = [$uziv];
    $result = sqlsrv_query($conn, $sql, $params);
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
        <h2>Moje povolení</h2>
        <fieldset class="prehled-field">
            <form method="get">
                <div class="date-selection">
                    <select name="mesic" id="mesic" class="custom-select">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>" <?= ($m == $mesic) ? 'selected' : '' ?>>
                                <?= $mesicCZ[$m] ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <select name="rok" id="rok" class="custom-select">
                        <?php for ($y = date('Y') - 5; $y <= date('Y'); $y++): ?>
                            <option value="<?= $y ?>" <?= ($y == $rok) ? 'selected' : '' ?>>
                                <?= $y ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <input type="submit" value="Zobrazit" class="submit-button">
                </div>
            </form>
            <div class="prehledy">
                <?php 
                    $sql = "SELECT 
                                p.id_pov,
                                p.ev_cislo,
                                p.odeslano
                            FROM Povolenka as p 
                            WHERE p.id_zam = ? AND MONTH(p.odeslano) = ? AND YEAR(p.odeslano) = ?
                            ORDER BY p.odeslano ASC;";
                    $params = [$uziv, $mesic, $rok];
                    $result = sqlsrv_query($conn, $sql, $params);
                    if ($result === FALSE)
                        die(print_r(sqlsrv_errors(), true));
                ?>
                <?php 
                    if (!sqlsrv_has_rows($result)) 
                        echo '<p style="font-style: italic;">Nebyl nalezen žádný záznam!</p>';
                    else {
                        while ($zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){
                            echo'<div class="prehled">';
                            echo '<div class="prehled-head">';
                            echo '<p>Datum: <strong>' . $zaznam['odeslano']->format('d.m.Y') . '</strong></p>';
                            echo '</div>';
                            echo '<div class="prehled-body">';
                            echo '<div>';
                            echo '<p>Ev. č. ' . $zaznam['ev_cislo'] . '</p>';
                            echo '<div class="stav">';
                            echo '<p>Stav: <span class="status">Odesláno</span></p>';
                            echo '<span class="icon"></span>';
                            echo '</div>';
                            echo '</div>';
                            echo '<div>';
                            echo '<a href="#" class="link" id="' . $zaznam['id_pov'] . '">Podrobnosti &gt;</a>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    sqlsrv_free_stmt($result);
                ?>
            </div>
        </fieldset>
    </div>
    <style>
        h2 {
            font-size: 24px; 
            font-weight: bold; 
            color: #003366; 
            text-align: left;
            margin-bottom: 15px;
        }
        h2::after {
            content: "";
            display: block;
            width: 25%;
            height: 3px; 
            background-color: #d40000; 
            margin-top: 5px;
            border-radius: 2px;
        }

        .container {
            padding: 20px;
            background-color: #edf4fb;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 900px;
        }
        .prehled-field {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 800px;
        }

        .date-selection {
            display: flex;
            align-items: center;
        }
        .custom-select {
            background-color: #ffffff;
            border: 1px solid #cccccc;
            border-radius: 5px;
            padding: 10px;
            font-size: 14px;
            color: #2C3E50;
            cursor: pointer;
            margin-right: 20px;
            transition: border-color 0.3s ease;
        }
        .custom-select:hover {
            border-color: #003366;
        }

        .prehledy {
            margin-top: 20px;
            border: 1px solid #dddddd;
            border-radius: 8px;
            background-color: #fafafa;
            padding: 10px;
            max-height: 380px;
            overflow-y: auto;
        }

        .prehled {
            border-bottom: 1px solid #dddddd;
            padding: 10px 0;
            transition: background-color 0.3s ease;
        }
        .prehled:hover {
            background-color: #f1f1f1;
        }
        .prehled:last-child {
            border-bottom: none;
        }

        .prehled-head p {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .prehled-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }
        .prehled-body p {
            margin: 0;
        }
        .prehled-body img {
            margin-left: 10px;
            margin-bottom: -10px;
            width: 30px;
        }

        .stav{
            display: flex;
            align-items: center;
        }
        .icon {
            width: 16px;
            height: 16px;
            background-color: #39B54A;
            border-radius: 50%;
            position: relative;
            margin-left: 5px;
        }
        .icon::after {
            content: "";
            position: absolute;
            top: 2px;
            left: 5px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .status {
            color: #28a745;
            font-weight: bold;
        }
        .link {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        .link:hover {
            color: #0056b3;
        }
        
        form {
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