<?php
    session_start();
    if (isset($_SESSION['uziv']) && isset($_SESSION['admin']) && $_SESSION['admin'] == true)
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

    $sql = "SELECT ochrana FROM Ochrana GROUP BY ochrana;";
    $result = sqlsrv_query($conn, $sql);
    if ($result === FALSE)
        die(print_r(sqlsrv_errors(), true));
    
    $ochrany = [];
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $ochrany[] = $row['ochrana'];
    }
    sqlsrv_free_stmt($result);

    $sql = "SELECT * FROM Ochrana;";
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
            <a href="uvod.php">
                <img src="home_icon.png" width="75%" style="cursor: pointer; margin-top: 15%;">
            </a>
            <div class="uziv">
                <img src="user_icon.png" width="28%" style="margin-right: 2%;">
                <div class="uziv_inf">
                    <p><?= $jmeno; ?></p>
                    <p style="font-size: 12px; margin-left: 1px;"><?= $funkce; ?></p>
                </div>
            </div>
            <a id="logout">
                <img src="logout_icon.png" width="78%" style="cursor: pointer;">
            </a>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th colspan="">Ochrané prostředky</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ochrany as $och): ?>
                <tr>
                    <td id="<?= $och ?>"><?= htmlspecialchars($och); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($ochrany)): ?>
                <tr>
                    <td colspan="1">Žádné ochranné prostředky nejsou k dispozici.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <style>
        table {
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        td {
            background-color: #f9f9f9;
            cursor: pointer;
        }
    </style>
</body>
</html>