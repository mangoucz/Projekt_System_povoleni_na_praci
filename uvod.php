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

    $sql = "SELECT s.id_schval FROM Schvalovani as s WHERE s.id_schval = ?;";
    $result = sqlsrv_query($conn, $sql, $params);
    if ($result === FALSE)
        die(print_r(sqlsrv_errors(), true));

    $schval = sqlsrv_has_rows($result);
    sqlsrv_free_stmt($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Systém povolení na práci</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="jquery-ui-1.14.1/jquery-ui.css">
    <script src="jquery-3.7.1.min.js"></script>
    <script src="jquery-ui-1.14.1/jquery-ui.js"></script>
    <script src="script.js"></script>
</head>
<body>
    <div class="header">
        <img src="Indorama.png" class="logo">
        <h1>SYSTÉM POVOLENÍ NA PRÁCI</h1>
        <div class="headerB">
            <form action="nove.php" method="post" style="width: 60%;">
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
    <form method="get">
        <div class="container" id="my">
            <?php
                $mesic = isset($_GET['mesic']) ? $_GET['mesic'] : date('n');
                $rok = isset($_GET['rok']) ? $_GET['rok'] : date('Y');
                $archiv = isset($_GET['archiv']) ? $_GET['archiv'] : 0;
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
                <div class="date-selection">
                    <div class="date-selection-A">
                        <select name="mesic" <?= isset($_GET['archiv']) ? '' : 'disabled' ?>>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= ($m == $mesic) ? 'selected' : '' ?>>
                                    <?= $mesicCZ[$m] ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <select name="rok" <?= isset($_GET['archiv']) ? '' : 'disabled' ?>>
                            <?php for ($y = date('Y') - 5; $y <= date('Y'); $y++): ?>
                                <option value="<?= $y ?>" <?= ($y == $rok) ? 'selected' : '' ?>>
                                    <?= $y ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <div class="panel">
                            <label class="container-check">Zahrnout archiv
                                <input type="checkbox" name="archiv" id="archiv" value="1" <?= ($archiv == 1) ? 'checked' : '' ?>>
                                <span class="checkbox"></span>
                            </label>
                        </div>
                    </div>
                    <div class="date-selection-B">
                        <input type="submit" value="Zobrazit" class="defButt">
                    </div>
                </div>
                <div class="prehledy">
                    <?php
                        if($archiv == 0){
                            $sql = "SELECT 
                                        p.id_pov,
                                        p.ev_cislo,
                                        p.odeslano,
                                        p.povol_do,
                                        p.povol_od,
                                        CONCAT(z.jmeno, ' ', z.prijmeni) AS Zam,
                                        prdZ.prodlZarDo,
                                        prdO.prodlOhDo
                                    FROM Povolenka AS p JOIN Zamestnanci AS z ON p.id_zam = z.id_zam
                                    OUTER APPLY (
                                        SELECT MAX(prd.do) AS prodlZarDo
                                        FROM Prodlouzeni AS prd
                                        WHERE prd.id_pov = p.id_pov AND prd.typ = 'zařízení') AS prdZ
                                    OUTER APPLY (
                                        SELECT MAX(prd.do) AS prodlOhDo
                                        FROM Prodlouzeni AS prd
                                        WHERE prd.id_pov = p.id_pov AND prd.typ = 'oheň') AS prdO
                                    WHERE p.id_zam = ? AND (COALESCE(prdZ.prodlZarDo, p.povol_do) >= GETDATE() OR COALESCE(prdO.prodlOhDo, p.povol_do) >= GETDATE())
                                    ORDER BY p.odeslano DESC;";
                            $params = [$uziv];
                        } 
                        else{
                            $sql = "SELECT 
                                        p.id_pov,
                                        p.ev_cislo,
                                        p.odeslano,
                                        p.povol_do,
                                        p.povol_od,
                                        CONCAT(z.jmeno, ' ', z.prijmeni) AS Zam,
                                        prdZ.prodlZarDo,
                                        prdO.prodlOhDo
                                    FROM Povolenka AS p JOIN Zamestnanci AS z ON p.id_zam = z.id_zam
                                    OUTER APPLY (
                                        SELECT MAX(prd.do) AS prodlZarDo
                                        FROM Prodlouzeni AS prd
                                        WHERE prd.id_pov = p.id_pov AND prd.typ = 'zařízení') AS prdZ
                                    OUTER APPLY (
                                        SELECT MAX(prd.do) AS prodlOhDo
                                        FROM Prodlouzeni AS prd
                                        WHERE prd.id_pov = p.id_pov AND prd.typ = 'oheň') AS prdO
                                    WHERE p.id_zam = ? AND (
                                            (MONTH(p.povol_od) = ? AND YEAR(p.povol_od) = ?)
                                            OR (MONTH(p.povol_do) = ? AND YEAR(p.povol_do) = ?)
                                            OR (MONTH(prdZ.prodlZarDo) = ? AND YEAR(prdZ.prodlZarDo) = ?)
                                            OR (MONTH(prdO.prodlOhDo) = ? AND YEAR(prdO.prodlOhDo) = ?))
                                    ORDER BY p.odeslano DESC;";
                            $params = [$uziv, $mesic, $rok, $mesic, $rok, $mesic, $rok, $mesic, $rok];
                        }
                        $result = sqlsrv_query($conn, $sql, $params);
                        if ($result === FALSE)
                            die(print_r(sqlsrv_errors(), true));
    
                        if (!sqlsrv_has_rows($result)) 
                            echo '<p style="font-style: italic;">Nebyl nalezen žádný záznam!</p>';
                        else {
                            while ($zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){
                                if (isset($zaznam['prodlZarDo']) && isset($zaznam['prodlOhDo'])) {
                                    $povolDo = $zaznam['prodlZarDo'] < $zaznam['prodlOhDo'] ? $zaznam['prodlOhDo'] : $zaznam['prodlZarDo'];
                                }
                                elseif(isset($zaznam['prodlZarDo'])){
                                    $povolDo = $zaznam['prodlZarDo'];
                                }
                                elseif (isset($zaznam['prodlOhDo'])) {
                                    $povolDo = $zaznam['prodlOhDo'];
                                }
                                else 
                                    $povolDo = $zaznam['povol_do'];

                                echo'<div class="prehled">';
                                echo '<div class="prehled-head">';
                                echo '<p>Ev. č. ' . $zaznam['ev_cislo'] . '</p>';
                                echo '</div>';
                                echo '<div class="prehled-body">';
                                echo '<div>';
                                echo '<p>Podal: ' . $zaznam['Zam'] . '</p>';
                                echo '<p>Na: '. $zaznam['povol_od']->format("d.m.Y") . ' - ' . $povolDo->format("d.m.Y") . '</p>';
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
        <?php if ($schval) : ?>
            <div class="container" id="team">
                <?php
                    $mesic = isset($_GET['mesicT']) ? $_GET['mesicT'] : date('n');
                    $rok = isset($_GET['rokT']) ? $_GET['rokT'] : date('Y');
                    $archiv = isset($_GET['archivT']) ? $_GET['archivT'] : 0;
                ?>
                <h2>Povolení týmu</h2>
                <fieldset>
                    <div class="date-selection">
                        <div class="date-selection-A">
                            <select name="mesicT" <?= isset($_GET['archivT']) ? '' : 'disabled' ?>>
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?= $m ?>" <?= ($m == $mesic) ? 'selected' : '' ?>>
                                        <?= $mesicCZ[$m] ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <select name="rokT" <?= isset($_GET['archivT']) ? '' : 'disabled' ?>>
                                <?php for ($y = date('Y') - 5; $y <= date('Y'); $y++): ?>
                                    <option value="<?= $y ?>" <?= ($y == $rok) ? 'selected' : '' ?>>
                                        <?= $y ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <div class="panel">
                                <label class="container-check">Zahrnout archiv
                                    <input type="checkbox" name="archivT" id="archivT" value="1" <?= ($archiv == 1) ? 'checked' : '' ?>>
                                    <span class="checkbox"></span>
                                </label>
                            </div>
                        </div>
                        <div class="date-selection-B">
                            <input type="submit" value="Zobrazit" class="defButt">
                        </div>
                    </div>
                    <div class="prehledy">
                        <?php
                            if($archiv == 0){
                                $sql = "SELECT 
                                            p.id_pov,
                                            p.ev_cislo,
                                            p.odeslano,
                                            p.povol_do,
                                            p.povol_od,
                                            CONCAT(z.jmeno, ' ', z.prijmeni) AS Zam,
                                            prdZ.prodlZarDo,
                                            prdO.prodlOhDo
                                        FROM (Povolenka AS p JOIN Zamestnanci AS z ON p.id_zam = z.id_zam) JOIN Schvalovani as zs ON z.id_zam = zs.id_zam
                                        OUTER APPLY (
                                            SELECT MAX(prd.do) AS prodlZarDo
                                            FROM Prodlouzeni AS prd
                                            WHERE prd.id_pov = p.id_pov AND prd.typ = 'zařízení') AS prdZ
                                        OUTER APPLY (
                                            SELECT MAX(prd.do) AS prodlOhDo
                                            FROM Prodlouzeni AS prd
                                            WHERE prd.id_pov = p.id_pov AND prd.typ = 'oheň') AS prdO
                                        WHERE zs.id_schval = ? AND (COALESCE(prdZ.prodlZarDo, p.povol_do) >= GETDATE() OR COALESCE(prdO.prodlOhDo, p.povol_do) >= GETDATE())
                                        ORDER BY p.odeslano DESC;;";
                                $params = [$uziv];
                            } 
                            else{
                                $sql = "SELECT 
                                            p.id_pov,
                                            p.ev_cislo,
                                            p.odeslano,
                                            p.povol_do,
                                            p.povol_od,
                                            CONCAT(z.jmeno, ' ', z.prijmeni) AS Zam,
                                            prdZ.prodlZarDo,
                                            prdO.prodlOhDo
                                        FROM (Povolenka AS p JOIN Zamestnanci AS z ON p.id_zam = z.id_zam) JOIN Schvalovani as zs ON z.id_zam = zs.id_zam
                                        OUTER APPLY (
                                            SELECT MAX(prd.do) AS prodlZarDo
                                            FROM Prodlouzeni AS prd
                                            WHERE prd.id_pov = p.id_pov AND prd.typ = 'zařízení') AS prdZ
                                        OUTER APPLY (
                                            SELECT MAX(prd.do) AS prodlOhDo
                                            FROM Prodlouzeni AS prd
                                            WHERE prd.id_pov = p.id_pov AND prd.typ = 'oheň') AS prdO
                                        WHERE zs.id_schval = ? AND (
                                                (MONTH(p.povol_od) = ? AND YEAR(p.povol_od) = ?)
                                                OR (MONTH(p.povol_do) = ? AND YEAR(p.povol_do) = ?)
                                                OR (MONTH(prdZ.prodlZarDo) = ? AND YEAR(prdZ.prodlZarDo) = ?)
                                                OR (MONTH(prdO.prodlOhDo) = ? AND YEAR(prdO.prodlOhDo) = ?))
                                        ORDER BY p.odeslano DESC;";
                                $params = [$uziv, $mesic, $rok, $mesic, $rok, $mesic, $rok, $mesic, $rok];
                            }
                            $result = sqlsrv_query($conn, $sql, $params);
                            if ($result === FALSE)
                                die(print_r(sqlsrv_errors(), true));
        
                            if (!sqlsrv_has_rows($result)) 
                                echo '<p style="font-style: italic;">Nebyl nalezen žádný záznam!</p>';
                            else {
                                while ($zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){
                                    if (isset($zaznam['prodlZarDo']) && isset($zaznam['prodlOhDo'])) {
                                        $povolDo = $zaznam['prodlZarDo'] < $zaznam['prodlOhDo'] ? $zaznam['prodlOhDo'] : $zaznam['prodlZarDo'];
                                    }
                                    elseif(isset($zaznam['prodlZarDo'])){
                                        $povolDo = $zaznam['prodlZarDo'];
                                    }
                                    elseif (isset($zaznam['prodlOhDo'])) {
                                        $povolDo = $zaznam['prodlOhDo'];
                                    }
                                    else 
                                        $povolDo = $zaznam['povol_do'];

                                    echo'<div class="prehled">';
                                    echo '<div class="prehled-head">';
                                    echo '<p>Ev. č. ' . $zaznam['ev_cislo'] . '</p>';
                                    echo '</div>';
                                    echo '<div class="prehled-body">';
                                    echo '<div>';
                                    echo '<p>Podal: ' . $zaznam['Zam'] . '</p>';
                                    echo '<p>Na: '. $zaznam['povol_od']->format("d.m.Y") . ' - ' . $povolDo->format("d.m.Y") . '</p>';
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
        <?php endif; ?>
    </form>
    <div class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span id="closeBtn" class="close">&times;</span>
                <h2>Povolení č. </h2>
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
                <form action="nove.php" method="post">
                    <input type="submit" value="Editovat" name="subEdit" class="defButt edit">
                    <input type="hidden" name="id" value="">
                </form>
                <form action="print_form.php" method="post" target="printFrame">
                    <input type="hidden" name="id" value="">
                    <input type="submit" class="defButt print" name="subTisk" value="Tisk"></button>
                </form>
                <iframe id="frame" name="printFrame" style="display: none;"></iframe>
                <form action="nove.php" method="post">
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
            background: #d40000; 
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
            background: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 800px;
        }

        .panel {
            padding: 10px 10px;
            background: #ffffff;
            border: 1px solid #cccccc;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .container-check {
            display: inline-flex;
            align-items: center;
            position: relative;
            padding-left: 35px;
            margin: 0;
            cursor: pointer;
            font-size: 14px;
            color: #003366;
            user-select: none;
            transition: color 0.3s ease;
        }
        .container-check input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }
        .container-check .checkbox {
            position: absolute;
            left: 0;
            height: 22px;
            width: 22px;
            background: #ffffff;
            border: 2px solid #cccccc;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        .container-check:hover input ~ .checkbox {
            background: #EAF3FF;
            border-color: #003366;
        }
        .container-check input:checked ~ .checkbox {
            background: #2196F3;
            border-color: #2196F3;
        }
        .container-check .checkbox:after {
            content: '';
            position: absolute;
            display: none;
            left: 8px;
            top: 3px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
            transition: all 0.2s ease;
        }
        .container-check input:checked ~ .checkbox:after {
            display: block;
        }

        .date-selection {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 20px;
        }
        .date-selection-A, .date-selection-B{
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .prehledy {
            margin-top: 20px;
            border: 1px solid #dddddd;
            border-radius: 8px;
            background: #fafafa;
            padding: 10px;
            max-height: 380px;
            overflow-y: auto;
        }

        .prehled {
            border-bottom: 1px solid #dddddd;
            padding: 10px 0 5px 0;
            transition: background 0.3s ease;
        }
        .prehled:hover {
            background: #f1f1f1;
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
            background: #39B54A;
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
                margin: 0 10px;
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
            .date-selection {
                display: block;
                width: 100%;                
            }
            .date-selection-A, .date-selection-B{
                width: 100%;
                justify-content: center;
            }
            .date-selection-A select{
                margin: 0;
            }
            .date-selection-B{
                margin-top: 10px;
            }
            form {
                width: 100%;
            }
        }
    </style>
</body>
</html>