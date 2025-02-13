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
    <script src="jquery-3.7.1.min.js"></script>
    <script src="script.js"></script>
</head>
<body>
    <div class="header">
        <img src="Indorama.png" class="logo">
        <h1>SYSTÉM POVOLENÍ NA PRÁCI</h1>
        <div class="headerB">
            <form action="" method="post" style="width: 60%;">
                <input type="submit" value="Nové povolení" name="subNove" class="defButt">
            </form>
            <div class="uziv">
                <img src="user_icon.png" width="28%" style="margin-right: 2%;">
                <div class="uziv_inf">
                    <p><?php echo $jmeno; ?></p>
                    <p style="font-size: 12px; margin-left: 1px;"><?php echo $funkce; ?></p>
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
        <fieldset>
            <form method="get" style="width: 60%;">
                <div class="date-selection">
                    <select name="mesic">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>" <?= ($m == $mesic) ? 'selected' : '' ?>>
                                <?= $mesicCZ[$m] ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <select name="rok">
                        <?php for ($y = date('Y') - 5; $y <= date('Y'); $y++): ?>
                            <option value="<?= $y ?>" <?= ($y == $rok) ? 'selected' : '' ?>>
                                <?= $y ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <input type="submit" value="Zobrazit" class="defButt">
                </div>
            </form>
            <div class="prehledy">
                <?php 
                    $sql = "SELECT 
                                p.id_pov,
                                p.ev_cislo,
                                p.odeslano,
                                p.povol_do,
                                p.povol_od,
                                Concat(z.jmeno, ' ', z.prijmeni) as Zam
                            FROM Povolenka as p JOIN Zamestnanci as z ON p.id_zam = z.id_zam 
                            WHERE p.id_zam = ? AND MONTH(p.odeslano) = ? AND YEAR(p.odeslano) = ?
                            ORDER BY p.odeslano DESC;";
                    $params = [$uziv, $mesic, $rok];
                    $result = sqlsrv_query($conn, $sql, $params);
                    if ($result === FALSE)
                        die(print_r(sqlsrv_errors(), true));
 
                    if (!sqlsrv_has_rows($result)) 
                        echo '<p style="font-style: italic;">Nebyl nalezen žádný záznam!</p>';
                    else {
                        while ($zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){
                            echo'<div class="prehled">';
                            echo '<div class="prehled-head">';
                            echo '<p>Ev. č. ' . $zaznam['ev_cislo'] . '</p>';
                            echo '</div>';
                            echo '<div class="prehled-body">';
                            echo '<div>';
                            echo '<p>Podal: ' . $zaznam['Zam'] . '</p>';
                            echo '<p>Na: '. $zaznam['povol_od']->format("d.m.Y") . ' - ' . $zaznam['povol_do']->format("d.m.Y") . '</p>';
                            echo '<div class="stav">';
                            echo '<p>Stav: <span class="status">Odesláno</span></p>';
                            echo '<span class="icon"></span>';
                            echo '<p class="odeslano">' . $zaznam['odeslano']->format('d.m.Y') . '</p>';
                            echo '</div>';
                            echo '</div>';
                            echo '<div>';
                            echo '<a class="link" id="' . $zaznam['id_pov'] . '" style="cursor: pointer;">Podrobnosti &gt;</a>';
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
    <div class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Podrobnosti povolenky</h2>
            </div>
            <div class="modal-body">
                <div class="info-row"><span class="label">Zadal:</span><span class="zadal obsah"></span></div>
                <div class="info-row"><span class="label">Povolení na:</span><span class="povoleni_na obsah"></span></div>
                <div class="info-row"><span class="label">Od:</span><span class="od obsah"></span></div>
                <div class="info-row"><span class="label">Do:</span><span class="do obsah"></span></div>
                <div class="info-row"><span class="label">Prodlouženo do:</span><span class="prodlDo obsah"></span></div>
                <div class="info-row"><span class="label">Odesláno:</span><span class="odeslano obsah"></span></div>
                <div class="info-row"><span class="label">Upraveno:</span><span class="upraveno obsah"></span></div>
                <div class="info-row"><span class="label">Prodlouženo:</span><span class="prodl obsah"></span></div>
                <div class="info-row"><span class="label">Popis práce:</span><span class="popis_prace obsah"></span></div>
            </div>
            <div class="modal-footer">
                <form method="post" action="nove.php">
                    <input type="submit" value="Editovat" name="subEdit" class="defButt edit">
                    <input type="button" value="Tisk" class="defButt print"></input>
                    <input type="submit" value="Prodloužit" name="subProdl" id="subProdl" class="defButt extend">
                    <input type="hidden" name="id" value="">
                </form>
            </div>
        </div>
    </div>
    <div class="footer">
        <img src="Indorama.png" width="200px">
    </div>
    <style>
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
            padding: 0.5% 1%;
            margin: 20px auto;
            max-width: 900px;
            background: rgba(255, 255, 255, 0.8); 
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        fieldset {
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
        select {
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
        select:hover {
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
            padding: 10px 0 5px 0;
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
            margin-top: 8px;
            align-items: flex-start; 
            position: relative;
        }
        .prehled-body p {
            margin: 0;
        }
        .prehled-body img {
            margin-left: 10px;
            margin-bottom: -10px;
            width: 30px;
        }
        .prehled-body .odeslano {
            position: absolute;
            right: 0;
            bottom: 0;
            color: #6c757d;
            font-weight: normal;
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
            color: #0056b3;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        .link:hover {
            color: #003366;
        }
        
        form {
            text-align: center;
        }
        .footer{
            display: none;
        }
        @media (max-width: 660px) {
            .header {
                flex-direction: column;
                align-items: center;
            }
            .container{
                background: unset;
                box-shadow: unset;
                margin: 20px 10px;
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