<?php
    function inputVal($el, $typ) : string {
        if (!isset($el)) {
            return "";
        } 
        
        if ($typ === "dat") {
            return $el->format("d. m. Y") ?? "";
        } 
        else if ($typ === "cas") {
            return $el->format("H:i") ?? "";
        }
        else if ($typ === "check"){
            return $el === 1 ? 'checked' : '';
        }
        return "";
    }
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
        $ochrana = [];
        $ochrana_typy = ['nohy', 'telo', 'hlava', 'oci', 'dychadel', 'pas', 'rukavice', 'hasicak'];
        $edit = false;

        if(isset($_POST['subEdit']) || isset($_POST['subProdl'])){
            $id = $_POST['id'];
            $sql = [];
            $svareci = [];
            $rozbory = [];
            
            if (isset($_POST['subProdl'])) {
                $sql[0] = "SELECT
                            p.id_pov as id,
                            p.ev_cislo,
                            p.prace_na_zarizeni as zar,
                            p.svarovani_ohen as oh,
                            p.povol_do as povolDo,
                            (select MAX(prd.do) from Prodlouzeni as prd where prd.id_pov = p.id_pov AND prd.typ = 'zařízení') as prodlZarDo,
                            (select MAX(prd.do) from Prodlouzeni as prd where prd.id_pov = p.id_pov AND prd.typ = 'oheň') as prodlOhDo
                        FROM Povolenka as p
                        WHERE id_pov = ?;";
            }
            else{
                $sql[0] = "SELECT 
                                Povolenka.*, 
                                (SELECT MAX(prd.do) FROM Prodlouzeni AS prd WHERE prd.id_pov = Povolenka.id_pov AND prd.typ = 'zařízení') AS prodlZarDo,
                                (SELECT MAX(prd.do) FROM Prodlouzeni AS prd WHERE prd.id_pov = Povolenka.id_pov AND prd.typ = 'oheň') AS prodlOhDo    
                            FROM Povolenka 
                            WHERE Povolenka.id_pov = ?;";
                $sql[1] = "SELECT * FROM Pov_Svar as ps LEFT JOIN Svareci AS s ON s.id_svar = ps.id_svar WHERE ps.id_pov = ?;"; 
                $sql[2] = "SELECT * FROM Pov_Roz as pr LEFT JOIN Rozbory AS r ON r.id_roz = pr.id_roz WHERE pr.id_pov = ?;"; 
                $sql[3] = "SELECT o.id_och, o.typ FROM Pov_Ochran as po LEFT JOIN Ochrana AS o ON o.id_och = po.id_och WHERE po.id_pov = ?;";
            }
            $params = [$id];

            for ($i=0; $i < count($sql); $i++) { 
                $result = sqlsrv_query($conn, $sql[$i], $params);
                if ($result === false) 
                    die(print_r(sqlsrv_errors(), true));
    
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    if ($i === 0) {
                        $zaznam = $row;                  
                    }
                    else if ($i === 1) {
                        $svareci[] = $row;
                    }
                    else if ($i === 2) {
                        $rozbory[] = $row;
                    }
                   else if ($i === 3) {
                        $ochrana[rtrim($row['typ'])] = $row['id_och'];
                    }

                }
                sqlsrv_free_stmt($result);
            }
            if (isset($_POST['subEdit'])) {
                $poleDat = [$zaznam['povol_do'], $zaznam['prodlZarDo'], $zaznam['prodlOhDo']];
                $nejDo = max(array_filter($poleDat));
                
                isset($ochrana['nohy']) ? $edit = true : $edit = false;
            }
        }
        $sql = "SELECT * FROM Ochrana WHERE typ = ?;";
        for ($i=0; $i < count($ochrana_typy); $i++) {   
            $params = [$ochrana_typy[$i]];
            $result = sqlsrv_query($conn, $sql, $params);
            if ($result === false) 
                die(print_r(sqlsrv_errors(), true));

            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                $ochrany[$ochrana_typy[$i]][] = $row;
            }
            sqlsrv_free_stmt($result);
        }  
        if ($edit) {
            $ochranaZDB = [$ochrana['nohy'], $ochrana['telo'], $ochrana['hlava'], $ochrana['oci'], $ochrana['dychadel'], $ochrana['pas'], $ochrana['rukavice']];
        }
        else
            $ochranaZDB = [null, null, null, null, null, null, null];
    }
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
            <a href="login.php">
                <img src="logout_icon.png" width="78%" style="cursor: pointer;">
            </a>
        </div>
    </div>
    <form action="" method="POST" id="form">
        <div class="firstPage">
            <table id="intro">
                <thead class="origo">
                    <tr>
                        <th>Rizikovost</th>
                        <th>Interní</th>
                        <th>Externí</th>
                        <th>Počet osob</th>
                        <th>Od</th>
                        <th>Do</th>
                        <th>Povolení</th>
                    </tr>
                </thead>
                <thead class="respons">
                    <tr>
                        <th>POVOLENÍ k práci</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Rizikovost">
                            <div class="riziko-container">
                                <div id="riziko" class="slider-range"></div>
                                    <input type="hidden" name="riziko" id="rizikoInput" value="<?= $zaznam['rizikovost'] ?? 5 ?>">
                                <b id="rizikoValue"><?= isset($zaznam['rizikovost']) ? $zaznam['rizikovost'] : 5 ?></b>
                            </div>
                        </td>
                        <td data-label="Interní"><input type="text" name="interni" title="Interní" value="<?= $zaznam['interni'] ?? null ?>"></td>
                        <td data-label="Externí"><input type="text" name="externi" title="Externí" value="<?= $zaznam['externi'] ?? null ?>"></td>
                        <td data-label="Počet osob"><input type="number" name="pocetOs" title="Zadejte počet os." value="<?= $zaznam['pocet_osob'] ?? null ?>"></td>
                        <td data-label="Od"><input type="text" name="povolOd" id="povolOd" class="date" title="Datum začátku platnosti povolení" value="<?= inputVal($zaznam['povol_od'] ?? null, 'dat'); ?>" <?= isset($zaznam['povol_od']) ? 'disabled' : '' ?> placeholder="Vyberte datum"></td>
                        <td data-label="Do"><input type="text" name="povolDo" id="povolDo" class="date" title="Datum konce platnosti povolení (lze prodloužit)" value="<?= inputVal($nejDo ?? null, "dat") ?>" <?= isset($zaznam['povol_do']) ? 'disabled' : '' ?> placeholder="Vyberte datum"></td>
                        <td data-label="Povolení" rowspan="5">
                            <div class="panel">
                                <label class="container">K práci na zařízení
                                    <input type="checkbox" name="prace_na_zarizeni" id="prace_na_zarizeni" value="1" <?= inputVal($zaznam['prace_na_zarizeni'] ?? null, "check") ?>>
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">Ke svařování a práci s otevřeným ohněm
                                    <input type="checkbox" name="svarovani_ohen" id="svarovani_ohen" value="1" <?= inputVal($zaznam['svarovani_ohen'] ?? null, "check") ?>>
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">Ke vstupu do zařízení nebo pod úroveň terénu
                                    <input type="checkbox" name="vstup_zarizeni_teren" id="vstup_zarizeni_teren" value="1" <?= inputVal($zaznam['vstup_zarizeni_teren'] ?? null, "check") ?>>
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">K práci v prostředí s nebezpečím výbuchu
                                    <input type="checkbox" name="prostredi_vybuch" id="prostredi_vybuch" value="1" <?= inputVal($zaznam['prostredi_vybuch'] ?? null, "check") ?>>
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">K předání a převzetí zařízení do opravy a do provozu
                                    <input type="checkbox" name="predani_prevzeti_zarizeni" id="predani_prevzeti_zarizeni" value="1" <?= inputVal($zaznam['predani_prevzeti_zarizeni'] ?? null, "check") ?>>
                                    <span class="checkbox"></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr >
                        <th>Provoz</th>
                        <td data-label="Provoz"><input type="text" name="provoz" title="Provoz" value="<?= $zaznam['provoz'] ?? null ?>"></td>
                        <th>Název(číslo) objektu</th>
                        <td data-label="Název(číslo) objektu"><input type="text" name="objekt" title="Název nebo č. objektu" value="<?= $zaznam['objekt'] ?? null ?>"></td>
                        <td data-label="Od"><input type="text" name="hodOd" class="time" id="hodOd" title="Čas začátku platnosti" maxlength="5" placeholder="00:00" value="<?= inputVal($zaznam['povol_od'] ?? null, "cas") ?>" <?= isset($zaznam['povol_od']) ? 'disabled' : '' ?>></td>
                        <td data-label="Do"><input type="text" name="hodDo" class="time" id="hodDo" title="Čas konce platnosti" maxlength="5" placeholder="00:00" value="<?= inputVal($nejDo ?? null, "cas") ?>" <?= isset($zaznam['povol_do']) ? 'disabled' : '' ?>></td>
                    </tr>
                    <tr>
                        <th>Název zařízení</th>
                        <td data-label="Název zařízení" colspan="2"><input type="text" name="NZarizeni" title="Název zařízení" value="<?= $zaznam['nazev_zarizeni'] ?? null ?>"></td>
                        <th>Číslo zařízení</th>
                        <td data-label="Číslo zařízení" colspan="2"><input type="text" name="CZarizeni" title="Číslo zařízení" value="<?= $zaznam['c_zarizeni'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <th>Popis, druh a rozsah práce</th>
                        <td data-label="Popis, druh a rozsah práce" colspan="5"><input type="text" name="prace" title="Popis, druh a rozsah práce" value="<?= $zaznam['popis_prace'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <th>Seznámení s riziky pracoviště dle karty č.</th>
                        <td data-label="Seznámení s riziky pracov. dle karty č." colspan="5"><input type="number" name="rizikaPrac" title="Číslo karty" value="<?= $zaznam['c_karty'] ?? null ?>"></td>
                    </tr>   
                </tbody>
            </table>
            <table id="first">
                <thead class="origo">
                    <tr>
                        <th colspan="2">1. Příprava zařízení k opravě</th>
                        <th colspan="5">Bližší určení</th>
                    </tr>
                </thead>
                <thead class="respons">
                    <th>1. Příprava zařízení k opravě</th>
                </thead>
                <tbody>
                    <tr>
                        <td class="podnadpis" colspan="7">Zařízení bylo</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.1 Vyčištění od zbytků
                                <input type="checkbox" name="vycisteni" value="1" <?= inputVal($zaznam['vycisteni'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="vycisteni_kom" value="<?= $zaznam['vycisteni_kom'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.2 Vypařené
                                <input type="checkbox" name="vyparene" value="1" <?= inputVal($zaznam['vyparene'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="vyparene_hod"  value="<?= inputVal($zaznam['vyparene_hod'] ?? null, 'cas') ?>"></td>
                        <td colspan="4"><input type="text" name="vyparene_kom" value="<?= $zaznam['vyparene_kom'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.3 Vypláchnuté vodou
                                    <input type="checkbox" name="vyplachnute" value="1" <?= inputVal($zaznam['vyplachnute'] ?? null, "check") ?>>
                                    <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="vyplachnute_kom" value="<?= $zaznam['vyplachnute_kom'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.4 Plyn vytěsnen vodou
                                <input type="checkbox" name="plyn_vytesnen" value="1" <?= inputVal($zaznam['plyn_vytesnen'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="plyn_vytesnen_kom" value="<?= $zaznam['plyn_vytesnen_kom'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.5 Vyvětrané
                                <input type="checkbox" name="vyvetrane" value="1" <?= inputVal($zaznam['vyvetrane'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="vyvetrane_hod" value="<?= inputVal($zaznam['vyvetrane_hod'] ?? null, 'cas') ?>"></td>
                        <td colspan="4"><input type="text" name="vyvetrane_kom" value="<?= $zaznam['vyvetrane_kom'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.6 Profoukané dusíkem
                                <input type="checkbox" name="profouk_dusik" value="1" <?= inputVal($zaznam['profouk_dusik'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="profouk_dusik_hod" value="<?= inputVal($zaznam['profouk_dusik_hod'] ?? null, 'cas') ?>"></td>
                        <td colspan="4"><input type="text" name="profouk_dusik_kom" value="<?= $zaznam['profouk_dusik_kom'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.7 Profoukané vzduchem
                                <input type="checkbox" name="profouk_vzd" value="1" <?= inputVal($zaznam['profouk_vzd'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="profouk_vzd_hod" value="<?= inputVal($zaznam['profouk_vzd_hod'] ?? null, 'cas') ?>"></td>
                        <td colspan="4"><input type="text" name="profouk_vzd_kom" value="<?= $zaznam['profouk_vzd_kom'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.8 Odpojeno od elektrického proudu
                                <input type="checkbox" name="odpojeno_od_el" value="1" <?= inputVal($zaznam['odpojeno_od_el'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Kým</th>
                        <td data-label="Kým" colspan="5"><input type="text" name="odpojeno_od_el_kym" value="<?= $zaznam['odpojeno_od_el_kym'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.9 Oddělené záslepkami
                                <input type="checkbox" name="oddelene_zaslep" value="1" <?= inputVal($zaznam['oddelene_zaslep'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Kým</th>
                        <td data-label="Kým" colspan="5"><input type="text" name="oddelene_zaslep_kym" value="<?= $zaznam['oddelene_zaslep_kym'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.10 Jinak zapezpečené
                                <input type="checkbox" name="jinak_zab" value="1" <?= inputVal($zaznam['jinak_zab'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Jak</th>
                        <td data-label="Jak" colspan="6"><input type="text" name="jinak_zab_jak" value="<?= $zaznam['jinak_zab_jak'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td class="podnadpis"colspan="7">Podmínky BP a PO</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.11 Použít nejiskřivého nářadí
                                <input type="checkbox" name="nejiskrive_naradi" value="1" <?= inputVal($zaznam['nejiskrive_naradi'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="nejiskrive_naradi_kom"  value="<?= $zaznam['nejiskrive_naradi_kom'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.12 Po dobu oprav - zkrápět, větrat
                                <input type="checkbox" name="zkrapet_vetrat" value="1" <?= inputVal($zaznam['zkrapet_vetrat'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <td data-label="Krát za"><input type="text" name="zkrapet_vetrat_pocet" value="<?= $zaznam['zkrapet_vetrat_pocet'] ?? null ?>"></td>
                        <th>Krát za</th>
                        <td data-label="Hodin"><input type="text" name="zkrapet_vetrat_hod" value="<?= $zaznam['zkrapet_vetrat_hod'] ?? null ?>"></td>
                        <th>Hodin</th>
                        <th>V místě</th>
                        <td data-label="V místě"><input type="text" name="zkrapet_vetrat_misto" value="<?= $zaznam['zkrapet_vetrat_misto'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.13 Provést rozbor ovzduší
                                <input type="checkbox" name="rozbor_ovzdusi" value="1" <?= inputVal($zaznam['rozbor_ovzdusi'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Místo</th>
                        <td data-label="Místo"><input type="text" name="rozbor_ovzdusi_misto" value="<?= $zaznam['rozbor_ovzdusi_misto'] ?? null ?>"></td>
                        <th>Čas</th>
                        <td data-label="Čas"><input type="text" class="time" maxlength="5" placeholder="00:00" name="rozbor_ovzdusi_cas" value="<?= inputVal($zaznam['rozbor_ovzdusi_cas'] ?? null, 'cas') ?>"></td>
                        <th>Výsledek</th>
                        <td data-label="Výsledek"><input type="text" name="rozbor_ovzdusi_vysl" value="<?= $zaznam['rozbor_ovzdusi_vysl'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.14 Zabezpečit dozor dalšími osobami
                                <input type="checkbox" name="zab_dozor" value="1" <?= inputVal($zaznam['zab_dozor'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Počet</th>
                        <td data-label="Počet"><input type="text" name="zab_dozor_pocet" value="<?= $zaznam['zab_dozor_pocet'] ?? null ?>"></td>
                        <th colspan="4">Jména uvést v bodě 7</th>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.15 Požární hlídka provozu
                                <input type="checkbox" name="pozar_hlidka" value="1" <?= inputVal($zaznam['pozar_hlidka'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Počet</th>
                        <td data-label="Počet"><input type="text" name="pozar_hlidka_pocet" value="<?= $zaznam['pozar_hlidka_pocet'] ?? null ?>"></td>
                        <th>Jméno</th>
                        <td data-label="Jméno" colspan="3"><input type="text" name="pozar_hlidka_jmeno" value="<?= $zaznam['pozar_hlidka_jmeno'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.16 Hasící přístroj
                                <input type="checkbox" name="hasici_pristroj" value="1" <?= inputVal($zaznam['hasici_pristroj'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Počet</th>
                        <td data-label="Počet"><input type="text" name="hasici_pristroj_pocet" value="<?= $zaznam['hasici_pristroj_pocet'] ?? null ?>"></td>
                        <th>Druh</th>
                        <td data-label="Druh"><input type="text" name="hasici_pristroj_druh" value="<?= $zaznam['hasici_pristroj_druh'] ?? null ?>"></td>
                        <th>Typ</th>
                        <td data-label="Typ"><input type="text" name="hasici_pristroj_typ"  value="<?= $zaznam['hasici_pristroj_typ'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.17 Jiné zabezpečení požární ochrany
                                <input type="checkbox" name="jine_zab_pozar" value="1" <?= inputVal($zaznam['jine_zab_pozar'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="jine_zab_pozar_kom" value="<?= $zaznam['jine_zab_pozar_kom'] ?? null ?>"></td>
                    </tr>
                </tbody>
            </table>
            <table id="sec">
                <thead>
                    <tr>
                        <th colspan="6">2. Vlastní zabezpečení prováděné práce</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="podnadpis" colspan="6">Osobní ochranné pracovní prostředky</td>
                    </tr>
                    <tr>
                        <th>2.1 Ochrana nohou - jaká</th>
                        <td data-label="2.1 Ochrana nohou - jaká" colspan="5"><select name="ochran_nohy">
                            <?php foreach ($ochrany['nohy'] as $item): ?>
                                <option value="<?= $item['id_och'] ?>" <?= ($item['id_och'] == $ochranaZDB[0]) ? 'selected' : $item['ochrana'][0] ?>>
                                    <?= $item['ochrana'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select></td>
                    </tr>
                    <tr>
                        <th>2.2 Ochrana těla - jaká</th>
                        <td data-label="2.2 Ochrana těla - jaká" colspan="6"><select name="ochran_telo">
                            <?php foreach ($ochrany['telo'] as $item): ?>
                                <option value="<?= $item['id_och'] ?>" <?= ($item['id_och'] == $ochranaZDB[1]) ? 'selected' : $item['ochrana'][0] ?>>
                                    <?= $item['ochrana'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select></td>
                    </tr>
                    <tr>
                        <th>2.3 Ochrana hlavy - jaká</th>
                        <td data-label="2.3 Ochrana hlavy - jaká" colspan="6">  
                            <select name="ochran_hlava">
                                <?php foreach ($ochrany['hlava'] as $item): ?>
                                    <option value="<?= $item['id_och'] ?>" <?= ($item['id_och'] == $ochranaZDB[2]) ? 'selected' : $item['ochrana'][0] ?>>
                                        <?= $item['ochrana'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>2.4 Ochrana oči - jaká - druh</th>
                        <td data-label="2.4 Ochrana oči - jaká - druh" colspan="6">
                            <select name="ochran_oci">
                                <?php foreach ($ochrany['oci'] as $item): ?>
                                    <option value="<?= $item['id_och'] ?>" <?= ($item['id_och'] == $ochranaZDB[3]) ? 'selected' : $item['ochrana'][0] ?>>
                                        <?= $item['ochrana'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>2.5 Ochrana dýchadel - jaká</th>
                        <td data-label="2.5 Ochrana dýchadel - jaká" colspan="6">
                            <select name="ochran_dychadel">
                                <?php foreach ($ochrany['dychadel'] as $item): ?>
                                    <option value="<?= $item['id_och'] ?>" <?= ($item['id_och'] == $ochranaZDB[4]) ? 'selected' : $item['ochrana'][0] ?>>
                                        <?= $item['ochrana'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>2.6 Ochranný pás - druh</th>
                        <td data-label="2.6 Ochranný pás - druh" colspan="6">
                            <select name="ochran_pas">
                                <?php foreach ($ochrany['pas'] as $item): ?>
                                    <option value="<?= $item['id_och'] ?>" <?= ($item['id_och'] == $ochranaZDB[5]) ? 'selected' : $item['ochrana'][0] ?>>
                                        <?= $item['ochrana'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>2.7 Ochranné rukavice - druh</th>
                        <td data-label="2.7 Ochranné rukavice - druh" colspan="6">
                            <select name="ochran_rukavice">
                                <?php foreach ($ochrany['rukavice'] as $item): ?>
                                    <option value="<?= $item['id_och'] ?>" <?= ($item['id_och'] == $ochranaZDB[6]) ? 'selected' : $item['ochrana'][0] ?>>
                                        <?= $item['ochrana'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>2.8 Dozor jmenovitě</th>
                        <td data-label="2.8 Dozor jmenovitě" colspan="5"><input type="text" name="ochran_dozor" value="<?= $zaznam['dozor'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td class="podnadpis" colspan="6">Jiné příkazy</td>
                    </tr>
                    <tr>
                        <th>2.9 Jiné</th>
                        <td data-label="2.9 Jiné" colspan="5"><input type="text" name="jine_prikazy" value="<?= $zaznam['jine_prikazy'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td rowspan="2">
                            <div class="panel">
                                <label class="container">2.10 Napětí 220 V
                                    <input type="checkbox" name="U_220" value="1" <?= inputVal($zaznam['U_220V'] ?? null, 'check') ?>>
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">2.11 Napětí 24 V
                                    <input type="checkbox" name="U_24" value="1" <?= inputVal($zaznam['U_24V'] ?? null, "check") ?>>
                                    <span class="checkbox"></span>
                                </label>
                            </div>
                        </td>
                        <td rowspan="2">
                            <div class="panel">
                                <label class="container">S krytem
                                    <input type="checkbox" name="kryt" value="1" <?= inputVal($zaznam['kryt'] ?? null, "check") ?>>
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">Bez krytu
                                    <input type="checkbox" name="bez_krytu" value="1" <?= inputVal($zaznam['bez_krytu'] ?? null, "check") ?>>
                                    <span class="checkbox"></span>
                                </label>
                            </div>
                        </td>
                        <th>Bez krytu</th>
                        <td data-label="Bez krytu" colspan="3"><input type="text" name="bez_krytu1" value="<?= $zaznam['bez_krytu_kom'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <th>Bez krytu</th>
                        <td data-label="Bez krytu" colspan="3"><input type="text" name="bez_krytu2" value="<?= $zaznam['bez_krytu_kom2'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td rowspan="2" colspan="2">
                            2.12 Prohlášení: Prohlašuji, že zajistím dodržení výše uvedených <br>
                            podmínek, jakož i bezpečný provoz a postup práce, mně podřízených <br>
                            pracovníků. Při jakékoliv změně podmínek práci přeruším a požádám <br>
                            o okamžité prověření prostředí. Po skončení práce s otevřeným ohněm <br>
                            zabezpečím pracoviště ve smyslu ČSN 05 06 10.
                        </td>
                        <th>Za práci čety odpovídá</th>
                        <td data-label="Za práci čety odpovídá" colspan="3"><input type="text" name="za_praci_odpovida" value="<?= $zaznam['odpovida'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <th>Datum</th>
                        <td data-label="Datum"><input type="text" class="date" name="odpovednost_dat" value="<?= inputVal($zaznam['dat_odpovedny'] ?? null, 'dat') ?>" placeholder="Vyberte datum"></td>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="odpovednost_cas" value="<?= inputVal($zaznam['dat_odpovedny'] ?? null, 'cas') ?>" ></td>
                        <th>Hodin</th>
                    </tr>
                    <tr>
                        <td colspan="6" class="podnadpis">2.13 Sváření provedou</td>
                    </tr>
                    <tr>
                        <th>Jméno</th>
                        <th>Příjmení</th>
                        <th colspan="4">Č. svář. průkazu</th>
                    </tr>
                    <?php $svarecCount = !empty($svareci) ? count($svareci) : 0; ?>
                    <?php for($i = 0; $i < $svarecCount; $i++) : ?>
                    <tr class="svareciTR" data-index="<?= $i ?>">
                        <td data-label="Jméno"><input type="text" name="svarec[<?= $i ?>][jmeno]" value="<?= $svareci[$i]['jmeno'] ?? null ?>"></td>
                        <td data-label="Příjmení"><input type="text" name="svarec[<?= $i ?>][prijmeni]" value="<?= $svareci[$i]['prijmeni'] ?? null ?>"></td>
                        <td colspan="3" data-label="Č. svář. průkazu"><input type="text" name="svarec[<?= $i ?>][prukaz]" value="<?= $svareci[$i]['c_prukazu'] ?? null ?>"></td>
                    </tr>
                    <?php endfor; ?>
                    <tr id="svarecAdd">
                        <td colspan="6"><button type="button" id="svarecAddBut" class="add">+</button></td>
                        <input type="hidden" name="svareciPocet" value="0">
                    </tr>
                    <tr>
                        <th colspan="3">2.14 Osvědčení o způsobilosti k práci a sváření na plynové zařízení má pracovník:</th>
                        <td data-label="2.14 Osvědčení o způsobilosti k práci a sváření na plyn. zař. má pracovník:" colspan="3"><input type="text" name="osvedceny_prac" value="<?= $zaznam['osvedceni_ma'] ?? null ?>"></td>
                    </tr>
                </tbody>
            </table>
            <table id="third">
                <thead>
                    <tr>
                        <th colspan="7">3. Prohlašuji, že jsem se přesvědčil, že výše uvededené zajištění je provedeno.</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Datum</th>
                        <td data-label="Datum"><input type="text" class="date" name="prohl_prac_dat" placeholder="Vyberte datum" value="<?= inputVal($zaznam['dat_odpov_provoz'] ?? null, 'dat')?>"></td>
                        <th>Datum</th>
                        <td data-label="Datum"><input type="text" class="date" name="prohl_exter_dat" placeholder="Vyberte datum" value="<?= inputVal($zaznam['dat_odpov_GB_exter'] ?? null, 'dat')?>"></td>
                        <th>Vyjádření přilehlého obvodu</th>
                        <td data-label="Vyjádření přilehlého obvodu" colspan="2"><input type="text" name="prohl_obvod" value="<?= $zaznam['prohl_obvod'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <th rowspan="2" colspan="2">Podpis odpovědného pracovníka provozu: </th>
                        <th rowspan="2" colspan="2">Podpis odpovědného pracovníka provádějícího útvaru GB nebo externí firmy:</th>
                        <th rowspan="2">Podpis vedoucího přilehlého obvodu:</th>
                        <th>Datum</th>
                        <td data-label="Datum"><input type="text" class="date" name="prohl_vedouci_dat" placeholder="Vyberte datum" value="<?= inputVal($zaznam['dat_vedouci'] ?? null, 'dat')?>"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="secPage">
            <table id="fourth">
                <thead>
                    <tr>
                        <th colspan="5">4. Podmínky pro práci s otevřeným ohněm</th>
                    </tr>
                    <tr>
                        <td class="podnadpis" colspan="5">Vystavovatel</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th colspan="5">4.1 Podmínky (doplňující bod č. 1)</th>
                        <td class="respons" data-label="4.1 Podmínky (doplňující bod č. 1)"></td>
                    </tr>
                    <tr>
                        <td colspan="5"><textarea name="podminky" rows="5" style="resize: none; width: 100%;"><?= $zaznam['podminky_ohen']  ?? null ?></textarea></td>
                    </tr>
                    <tr>
                        <th>4.2 Výše uvedené podmínky stanovil - jméno:</th>
                        <td colspan="4" data-label="4.2 Výše uvedené podmínky stanovil - jméno:"><input type="text" name="podminky_jm" value="<?= $zaznam['ohen_jmeno'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <th>4.3 Pracoviště připraveno pro práci s otevřeným ohněm:</th>
                        <th>Dne</th>
                        <td data-label="4.3 Pracoviště připraveno pro práci s otevřeným ohněm dne"><input type="text" class="date" name="ohen_dat" placeholder="Vyberte datum" value="<?= inputVal($zaznam['dat_ohen'] ?? null, 'dat')?>"></td>
                        <th>Hodin</th>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="ohen_cas" value="<?= inputVal($zaznam['dat_ohen'] ?? null, 'cas')?>"></td>
                    </tr>
                    <tr>
                        <th>4.4 Osobně zkontroloval - jméno</th>
                        <td colspan="4" data-label="4.4 Osobně zkontroloval - jméno"><input type="text" name="zkontroloval_jm" value="<?= $zaznam['zkontroloval'] ?? null ?>"></td>
                    </tr>
                </tbody>
            </table>
            <table id="fifth">
                <thead class="origo">
                    <tr>
                        <th>5. Rozbor ovzduší</th>
                        <th>Datum</th>
                        <th>Čas</th>
                        <th>Místo odběru vzorku ovzduší</th>
                        <th>Naměřená hodnota</th>
                        <th></th>
                    </tr>
                </thead>
                <thead class="respons">
                    <th>5. Rozbor ovzduší</th>
                </thead>
                <tbody>
                    <?php $rozborCount = !empty($rozbory) ? count($rozbory) : 0; ?>
                    <?php for($i = 0; $i < $rozborCount; $i++) : ?>
                    <tr class="rozboryTR" data-index="<?= $i ?>">
                        <td data-label="Rozbor ovzduší"><input type="text" name="rozbor[<?= $i ?>][nazev]" value="<?= $rozbory[$i]['nazev'] ?? null ?>"></td>
                        <td data-label="Datum"><input type="text" class="date" placeholder="Vyberte datum" name="rozbor[<?= $i ?>][dat]" value="<?= inputVal($rozbory[$i]['dat'] ?? null, 'dat') ?>"></td>
                        <td data-label="Čas"><input type="text" class="time" maxlength="5" placeholder="00:00" name="rozbor[<?= $i ?>][cas]" value="<?= inputVal($rozbory[$i]['cas'] ?? null , 'cas') ?>"></td>
                        <td data-label="Místo odběru vzorku ovzduší"><input type="text" name="rozbor[<?= $i ?>][misto]" value="<?= $rozbory[$i]['misto'] ?? null ?>"></td>
                        <td data-label="Naměřená hodnota"><input type="text" name="rozbor[<?= $i ?>][hodn]" value="<?= $rozbory[$i]['hodn'] ?? null ?>"></td>
                    </tr>
                    <?php endfor; ?>
                    <tr id="rozborAdd">
                        <td colspan="6"><button type="button" id="rozborAddBut" class="add">+</button></td>
                        <input type="hidden" name="rozboryPocet" value="0">
                    </tr>
                </tbody>
            </table>
            <table id="sixth">
                <thead>
                    <tr>
                        <th colspan="5">6. Další jiné podmínky práce na zařízení</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="podnadpis">Vystavovatel nebo kontrolní orgán</td>
                    </tr>
                    <tr>
                        <td rowspan="4"><textarea name="dalsi_jine" rows="10" style="resize: none; width: 95%;"><?= $zaznam['dalsi_jine']  ?? null?></textarea></td>
                        <th>Stanovil</th>
                        <td data-label="Stanovil" colspan="3"><input type="text" name="dalsi_jine_stanovil" value="<?= $zaznam['dalsi_jine_stanovil']  ?? null?>"></td>
                    </tr>
                    <tr>
                        <th>Jméno</th>
                        <td data-label="Jméno" colspan="3"><input type="text" name="dalsi_jine_jm" value="<?= $zaznam['dalsi_jine_jm']  ?? null?>"></td>
                    </tr>
                    <tr>
                        <th>Dne</th>
                        <td data-label="Dne"><input type="text" class="date" name="dalsi_jine_dat" placeholder="Vyberte datum" value="<?= inputVal($zaznam['dalsi_jine_dat'] ?? null, 'dat')?>"></td>
                        <th>Hodin</th>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="dalsi_jine_cas" value="<?= inputVal($zaznam['dalsi_jine_dat'] ?? null, 'cas')?>"></td>
                    </tr>
                </tbody>
            </table>
            <table id="seventh">
                <thead>
                    <tr>
                        <th>7. Další nutná opatření - případně viz protokol ze dne</th>
                        <th class="origo"><input type="text" class="date" name="nutna_dat" placeholder="Vyberte datum" value="<?= inputVal($zaznam['nutna_dat'] ?? null, 'dat') ?? null ?>"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="respons"><input type="text" class="date" name="nutna_dat" placeholder="Vyberte datum" value="<?= inputVal($zaznam['nutna_dat'] ?? null, 'dat')?>"></td>
                        <td colspan="2"><textarea name="nutna_opatreni" title="Nutná opatření" rows="4" style="resize: none; width: 100%;"><?= $zaznam['nutna_opatreni'] ?? null ?></textarea></td>
                    </tr>
                </tbody>
            </table>
            <table id="eighth">
                <thead class="origo">
                    <tr>
                        <th colspan="3">8. Předání do opravy - protokol č.</th>
                        <th><input type="text" name="oprava_protokol" value="<?= $zaznam['oprava_protokol'] ?? null ?>"></th>
                        <th colspan="4">10. Práce svářečské ukončeny</th>
                        <th colspan="4">12. Kontrola BT, PO, jiného orgánu</th>
                    </tr>
                </thead>
                <thead class="respons">
                    <th>8. Předání do opravy - protokol č.</th>
                </thead>
                <tbody class="origo">
                    <tr>
                        <th>Dne</th>
                        <td><input type="text" class="date" placeholder="Vyberte datum" name="oprava_dat" value="<?= inputVal($zaznam['oprava_dat'] ?? null, 'dat')?>"></td>
                        <th>Hodina</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="oprava_cas" value="<?= inputVal($zaznam['oprava_dat'] ?? null, 'cas')?>"></td>
                        <th>Dne</th>
                        <td><input type="text" class="date" placeholder="Vyberte datum" name="svarec_ukon_dat" value="<?= inputVal($zaznam['svarec_ukon_dat'] ?? null, 'dat')?>"></td>
                        <th>Hodina</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="svarec_ukon_cas" value="<?= inputVal($zaznam['svarec_ukon_dat'] ?? null, 'cas')?>"></td>
                        <th>Kontrola dne</th>
                        <td><input type="text" class="date" placeholder="Vyberte datum" name="kontrola_dat" value="<?= inputVal($zaznam['kontrola_dat'] ?? null, 'dat')?>"></td>
                        <th>Hodina</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="kontrola_cas" value="<?= inputVal($zaznam['kontrola_dat'] ?? null, 'cas')?>"></td>
                    </tr>
                    <tr>
                        <th>Předal</th>
                        <td colspan="3"><input type="text" name="oprava_predal" value="<?= $zaznam['oprava_predal'] ?? null ?>"></td>
                        <th>Předal</th>
                        <td colspan="3"><input type="text" name="svarec_predal" value="<?= $zaznam['svarec_ukon_predal'] ?? null ?>"></td>
                        <th rowspan="4">Zjištěno</th>
                        <td colspan="3" rowspan="4"><textarea name="kontrola_zjisteno" rows="12" style="resize: none; width: 100%;"><?= $zaznam['kontrola_zjisteno'] ?? null ?></textarea></td>
                    </tr>
                    <tr>
                        <th>Převzal</th>
                        <td colspan="3"><input type="text" name="oprava_prevzal" value="<?= $zaznam['oprava_prevzal'] ?? null ?>"></td>
                        <th>Převzal</th>
                        <td colspan="3"><input type="text" name="svarec_prevzal" value="<?= $zaznam['svarec_ukon_prevzal'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <th colspan="4">9. Převzání z opravy</th>
                        <th colspan="4">11. Následný dozor</th>
                    </tr>
                    <tr>
                        <th>Dne</th>
                        <td><input type="text" class="date" placeholder="Vyberte datum" name="z_opravy_dat" value="<?= inputVal($zaznam['z_opravy_dat'] ?? null, 'dat')?>"></td>
                        <th>Hodina</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="z_opravy_cas" value="<?= inputVal($zaznam['z_opravy_dat'] ?? null, 'cas')?>"></td>
                        <th>Od</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="dozor_od" value="<?= inputVal($zaznam['dozor_od'] ?? null, 'cas')?>"></td>
                        <th>Do</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="dozor_do" value="<?= inputVal($zaznam['dozor_do'] ?? null, 'cas')?>"></td>
                    </tr>
                    <tr>
                        <th>Předal</th>
                        <td colspan="3"><input type="text" name="z_opravy_predal" value="<?= $zaznam['z_opravy_predal'] ?? null ?>"></td>
                        <th rowspan="2">Jméno</th>
                        <td rowspan="2" colspan="3"><input type="text" name="dozor_jm" value="<?= $zaznam['dozor_jm'] ?? null ?>"></td>
                        <th rowspan="2">Jméno</th>
                        <td rowspan="2" colspan="3"><input type="text" name="kontrola_jm" value="<?= $zaznam['kontrola_jm'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <th>Převzal</th>
                        <td colspan="3"><input type="text" name="z_opravy_prevzal" value="<?= $zaznam['z_opravy_prevzal'] ?? null ?>"></td>
                    </tr>
                </tbody>
                <tbody class="respons">
                    <tr>
                        <td><input type="text" name="oprava_protokol" value="<?= $zaznam['oprava_protokol'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td data-label="Dne"><input type="text" class="date" placeholder="Vyberte datum" name="oprava_dat" value="<?= inputVal($zaznam['oprava_dat'] ?? null, 'dat')?>"></td>
                        <td data-label="Hodina"><input type="text" class="time" maxlength="5" placeholder="00:00" name="oprava_cas" value="<?= inputVal($zaznam['oprava_dat'] ?? null, 'cas')?>"></td>
                    </tr>
                    <tr>
                        <td data-label="Předal" colspan="3"><input type="text" name="oprava_predal" value="<?= $zaznam['oprava_predal'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td data-label="Převzal" colspan="3"><input type="text" name="oprava_prevzal" value="<?= $zaznam['oprava_prevzal'] ?? null ?>"></td>
                    </tr>
                </tbody>
            </table>
            <table id="ninth" class="respons">
                <thead>
                    <th>9. Předání z opravy</th>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Dne"><input type="text" class="date" placeholder="Vyberte datum" name="oprava_dat" value="<?= inputVal($zaznam['z_opravy_dat'] ?? null, 'dat')?>"></td>
                        <td data-label="Hodina"><input type="text" class="time" maxlength="5" placeholder="00:00" name="z_opravy_cas" value="<?= inputVal($zaznam['z_opravy_dat'] ?? null, 'cas')?>"></td>
                    </tr>
                    <tr>
                        <td data-label="Předal"><input type="text" name="z_opravy_predal" value="<?= $zaznam['z_opravy_predal'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td data-label="Převzal"><input type="text" name="z_opravy_prevzal" value="<?= $zaznam['z_opravy_prevzal'] ?? null ?>"></td>
                    </tr>
                </tbody>
            </table>
            <table id="tenth" class="respons">
                <thead>
                    <th>10. Práce svářečské ukončeny</th>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Dne"><input type="text" class="date" placeholder="Vyberte datum" name="svarec_ukon_dat" value="<?= inputVal($zaznam['svarec_ukon_dat'] ?? null, 'dat')?>"></td>
                        <td data-label="Hodina"><input type="text" class="time" maxlength="5" placeholder="00:00" name="svarec_ukon_cas" value="<?= inputVal($zaznam['svarec_ukon_dat'] ?? null, 'cas')?>"></td>
                    </tr>
                    <tr>
                        <td data-label="Předal"><input type="text" name="svarec_predal" value="<?= $zaznam['svarec_ukon_predal'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <td data-label="Převzal"><input type="text" name="svarec_prevzal" value="<?= $zaznam['svarec_ukon_prevzal'] ?? null ?>"></td>
                    </tr>
                </tbody>
            </table>
            <table id="eleventh" class="respons">
                <thead>
                    <th>11. Následný dozor</th>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Od"><input type="text" class="time" maxlength="5" placeholder="00:00" name="dozor_od" value="<?= inputVal($zaznam['dozor_od'] ?? null, 'cas')?>"></td>
                        <td data-label="Do"><input type="text" class="time" maxlength="5" placeholder="00:00" name="dozor_do" value="<?= inputVal($zaznam['dozor_do'] ?? null, 'cas')?>"></td>
                    </tr>
                    <tr>
                        <td data-label="Jméno"><input type="text" name="dozor_jm" value="<?= $zaznam['dozor_jm'] ?? null ?>"></td>
                    </tr>
                </tbody>
            </table>
            <table id="twelfth" class="respons">
                <thead>
                    <th>12. Kontrola BT, PO, jiného orgánu</th>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Dne"><input type="text" class="date" placeholder="Vyberte datum" name="kontrola_dat" value="<?= inputVal($zaznam['kontrola_dat'] ?? null, 'dat')?>"></td>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="kontrola_cas" value="<?= inputVal($zaznam['kontrola_dat'] ?? null, 'cas')?>"></td>
                    </tr>
                    <tr>
                        <td data-label="Zjištěno"></td>
                    </tr>
                    <tr>
                        <td><textarea name="kontrola_zjisteno" rows="12" style="resize: none; width: 100%;"><?= $zaznam['kontrola_zjisteno'] ?? null ?></textarea></td>
                    </tr>
                    <tr>
                        <td data-label="Jméno"><input type="text" name="kontrola_jm" value="<?= $zaznam['kontrola_jm'] ?? null ?>"></td>
                    </tr>
                </tbody>
            </table>
            <table id="thirteenth">
                <?php 
                    $prodlZarDo = isset($zaznam['prodlZarDo']) && $zaznam['prodlZarDo']->format("Y-m-d") > date("Y-m-d") ? $zaznam['prodlZarDo']->format("Y-m-d") : date("Y-m-d");
                    $prodlZarHodDo = isset($zaznam['prodlZarDo']) && $zaznam['prodlZarDo']->format("H:i") > date("H:i") ? $zaznam['prodlZarDo']->format("H:i") : date("H:i");
                    $prodlOhDo = isset($zaznam['prodlOhDo']) && $zaznam['prodlOhDo']->format("Y-m-d") > date("Y-m-d") ? $zaznam['prodlOhDo']->format("Y-m-d") : date("Y-m-d"); 
                    $prodlOhHodDo = isset($zaznam['prodlOhDo']) && $zaznam['prodlOhDo']->format("H:i") > date("H:i") ? $zaznam['prodlOhDo']->format("H:i") : date("H:i");
                    $povolDo = isset($zaznam['povolDo']) && $zaznam['povolDo']->format("Y-m-d") > date("Y-m-d") ? $zaznam['povolDo']->format("Y-m-d") : date("Y-m-d");
                    $povolHodDo = isset($zaznam['povolDo']) && $zaznam['povolDo']->format("H:i") > date("H:i") ? $zaznam['povolDo']->format("H:i") : date("H:i");
                    
                    $zarMin = max($prodlZarDo, $povolDo);
                    $ohMin = max($prodlOhDo, $povolDo);
                    $zarHodMin = max($prodlZarHodDo, $povolHodDo);
                    $ohHodMin = max($prodlOhHodDo, $povolHodDo);
                ?>
                <thead>
                    <tr>
                        <th colspan="6">13. Prodloužených za podmínek stanovených tímto povolením</th>
                    </tr>
                </thead>
                <tbody class="origo">
                    <tr>
                        <td class="podnadpis" colspan="4">Prodlužuje provozovatel</td>
                    </tr>
                    <tr>
                        <th colspan="4">13.1 Pro práci na zařízení</th>
                    </tr>
                    <tr>
                        <th>Od</th>
                        <th>Do</th>
                        <th colspan="2"></th>
                    </tr>
                    <tr class="prodlZarTR">
                        <td data-label="Od" rowspan="2">
                            <input type="text" name="prodluzZarOd" id="prodluzZarOd" class="date" data-min="<?= $zarMin ?>" placeholder="Vyberte datum" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzZarhodOd" class="time" id="prodluzZarhodOd" data-min="<?= $zarHodMin ?>" maxlength="5" placeholder="00:00" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>>
                        </td>
                        <td data-label="Do" rowspan="2">
                            <input type="text" name="prodluzZarDo" id="prodluzZarDo" class="date" placeholder="Vyberte datum" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzZarhodDo" class="time" id="prodluzZarhodDo" maxlength="5" placeholder="00:00" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>>
                        </td>
                        <th>Přestávka</th>
                        <td data-label="Přestávka"><input type="text" name="prodluz_zar_prestavka" id="prodluzZarPrestavka" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>></td>
                    </tr>
                    <tr>
                        <th>Počet osob</th>
                        <td data-label="Počet osob"><input type="text" name="prodluz_zar_os" id="prodluzZarOs" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>></td>
                    </tr>
                    <tr>
                        <th colspan="4">13.2 Pro práci s otevřeným ohněm</th>
                    </tr>
                    <tr>
                        <th>Od</th>
                        <th>Do</th>
                        <th colspan="2"></th>
                    </tr>
                    <tr class="prodlOhTR">
                        <td data-label="Od" rowspan="2">
                            <input type="text" name="prodluzOhOd" id="prodluzOhOd" class="date" data-min="<?= $ohMin ?>" placeholder="Vyberte datum" min="<?= $ohMin; ?>" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzOhHodOd" class="time" id="prodluzOhHodOd" data-min="<?= $ohHodMin ?>" maxlength="5" placeholder="00:00" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?>>
                        </td>
                        <td data-label="Do" rowspan="2">
                            <input type="text" name="prodluzOhDo" id="prodluzOhDo" class="date" placeholder="Vyberte datum" min="<?= date("Y-m-d") ?>" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzOhHodDo" class="time" id="prodluzOhHodDo" maxlength="5" placeholder="00:00" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?>>
                        </td>
                        <th>Přestávka</th>
                        <td data-label="Přestávka"><input type="text" name="prodluz_oh_prestavka" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?>></td>
                    </tr>
                    <tr>
                        <th>Počet osob</th>
                        <td data-label="Počet osob"><input type="text" name="prodluz_oh_os" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?>></td>
                    </tr>
                </tbody>
                <tbody class="respons">
                    <tr>
                        <td class="podnadpis">Prodlužuje provozovatel</td>
                    </tr>
                    <tr class="prodlZarTR">
                        <td data-label="13.1 Pro práci na zařízení"></td>
                        <td data-label="Od" rowspan="2">
                            <input type="text" name="prodluzZarOd" id="prodluzZarOd" class="date" data-min="<?= $zarMin ?>" placeholder="Vyberte datum" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzZarhodOd" class="time" id="prodluzZarhodOd" data-min="<?= $zarHodMin ?>" maxlength="5" placeholder="00:00" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>>
                        </td>
                        <td data-label="Do" rowspan="2">
                            <input type="text" name="prodluzZarDo" id="prodluzZarDo" class="date" placeholder="Vyberte datum" min="<?= date("Y-m-d") ?>" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzZarhodDo" class="time" id="prodluzZarhodDo" maxlength="5" placeholder="00:00" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>>
                        </td>
                        <td data-label="Přestávka"><input type="text" name="prodluz_zar_prestavka" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>></td>
                        <td data-label="Počet osob"><input type="text" name="prodluz_zar_os" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>></td>
                    </tr>
                    <tr class="prodlOhTR">
                        <td data-label="13.2 Pro práci s otevřeným ohněm"></td>
                        <td data-label="Od" rowspan="2">
                            <input type="text" name="prodluzOhOd" id="prodluzOhOd" class="date" data-min="<?= $ohMin ?>" placeholder="Vyberte datum" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzOhHodOd" class="time" id="prodluzOhHodOd" data-min="<?= $ohHodMin ?>" maxlength="5" placeholder="00:00" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?>>
                        </td>
                        <td data-label="Do" rowspan="2">
                            <input type="text" name="prodluzOhDo" id="prodluzOhDo" class="date" placeholder="Vyberte datum" min="<?= date("Y-m-d") ?>" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzOhHodDo" class="time" id="prodluzOhHodDo" maxlength="5" placeholder="00:00" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?>>
                        </td>
                        <td data-label="Přestávka"><input type="text" name="prodluz_oh_prestavka" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?>></td>
                        <td data-label="Počet osob"><input type="text" name="prodluz_oh_os" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?>></td>
                    </tr>
                </tbody>
            </table>
            <table id="fourteenth">
                <thead>
                    <tr>
                        <th>14. Doplňky, poznámky</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5"><textarea name="doplnky" rows="5" style="resize: none; width: 100%;"><?= $zaznam['doplnky'] ?? null ?></textarea></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="submit-container">
            <input type="hidden" name="id_pov" value="<?= isset($id) ? $id : ''?>">
            <input type="hidden" name="ev_cislo" value="<?= isset($zaznam['ev_cislo']) ? $zaznam['ev_cislo'] : ''?>">
            <input type="hidden" name="prodl" value="<?= isset($_POST['subProdl']) ? 1 : 0?>">
            <input type="button" class="add" id="odeslat" value="Odeslat" name="subOdeslat" style="font-size: 16px;">
        </div>
    </form>
    <div class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span id="closeBtn" class="close">&times;</span>
                <h2>Povolení č.</h2>
            </div>
            <div class="modal-body">
                <h3>Povolení bylo úspěšně odesláno!</h3>
                <p>Chcete ho rovnou vytisknout?</p>
            </div>
            <div class="modal-footer">
                <form action="print_form.php" method="post" target="printFrame">
                    <input type="hidden" name="id" value="">
                    <input type="submit" value="Tisk" id="printBtn" class="defButt print"></button>                    
                </form>
                <iframe id="frame" name="printFrame" style="display: none;"></iframe>
                <button id="closeBtn" class="defButt">Zavřít</button>
            </div>
        </div>
    </div>
    <div class="footer">
        <img src="Indorama.png" width="200px">
    </div>
    <style>
        body{
            background: unset;
            background-color: #F0F8FF;
        }
        table{
            background: #FFFFFF;
            padding: 20px;
            border: 1px solid #BCD4EF;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        thead th {
            background: #EAF3FF;
            text-align: center;
            font-weight: bold;
            padding: 10px;
            border-bottom: 2px solid #BCD4EF;
        }
        tbody th {
            background: #f7faff;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #E6ECF2;
        }
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #E6ECF2;
        }
        h2::after {
            content: "";
            display: block;
            width: 25%;
            height: 3px; 
            background: #d40000; 
            margin-top: 5px;
            border-radius: 2px;
        }
        .modal-body{
            text-align: center;
        }
        .podnadpis{
            font-weight: bold;
            padding: 1% 0 1% 1%;
            background: #EEEEEE;
        }

        .riziko-container{
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .slider-range {
            width: 85%;
            margin: 10px 15px;
        }

        #rizikoValue {
            min-width: 25px;
            text-align: center;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            margin: 2px 0;
            box-sizing: border-box;
            border: 1px solid #BCD4EF;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
            display: block;
            transition: border-color 0.2s ease;
        }
        input[type="text"]:hover,
        input[type="number"]:hover{
            border-color:rgb(140, 200, 250); 
            box-shadow: 0 2px 6px rgba(0, 51, 102, 0.2);
        }
        input[type="text"]:focus,
        input[type="number"]:focus{
            border-color: #2196F3;
            box-shadow: 0 0 4px rgba(33, 150, 243, 0.5);
        }
        button, .add, #odeslat{
            color: #FFFFFF;
            border: none;
            border-radius: 50px;
            padding: 10px 20px; 
            font-size: 20px; 
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
            transition: all 0.3s ease;
        }
        button:hover, input[type="submit"]:hover{
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15); 
        }
        button:active, input[type="submit"]:active{
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.2);
        }
        .add {
            background: #39B54A;
        }
        .del {
            background: #FF2C55;
        }
        .add:hover {
            background: #34A343;
        }
        .add:active {
            background: #2E8E3B;
        }
        .del:hover {
            background: #E62A4E;
        }
        .del:active {
            background: #CC2444;
        }

        td:has(button), th:has(button){
            text-align: center;
        }
        .panel {
            padding: 18px 18px;
            background: #f9fcff;
            border: 1px solid #e6ecf2;
            border-radius: 8px;
        }
        table.first td:has(label){
            border-right: 1px solid #e6ecf2 
        }
        .container {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 16px;
            cursor: pointer;
        }
        .container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }
        .checkbox {
            position: absolute;
            left: 0;
            top: 0;
            height: 20px;
            width: 20px;
            background: #eeeeee;
            border-radius: 4px;
            border: 1px solid #bcd4ef;
        }
        .container:hover input ~ .checkbox {
            background: #cccccc;
        }
        .container input:checked ~ .checkbox {
            background: #2196F3;
            border-color: #2196F3;
        }
        .checkbox:after {
            content: "";
            position: absolute;
            display: none;
            left: 6px;
            top: 3px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }
        .container input:checked ~ .checkbox:after {
            display: block;
        }

        .submit-container {
            display: flex;
            justify-content: center; 
            margin: 20px 0 100px 0; 
        }

        .footer, .respons{
            display: none;
        }

        <?php if(isset($_POST['subProdl'])){ ?>
            table:not(#thirteenth):not(.ui-datepicker-calendar){
                display: none;
            }
            #thirteenth{
                display: table;
            }
        <?php } else{ ?>
            #thirteenth{
                display: none;
            }
        <?php }?>
                
        @media (max-width: 660px) {
            .header {
                flex-direction: column;
                align-items: center;
            }
            h1 {
                margin: 5% 0;
                font-size: 1.5em;
            }
            .container{
                background: unset;
                box-shadow: unset;
            }
            .footer{
                display: flex;  
            }
            .podnadpis{
                padding: 4% 0 4% 2%;
            }
            #rizikoValue{
                margin: 1.5% 0 0 1.5%
            }
            table:not(.ui-datepicker-calendar){
                width: 90%;
                padding: 0;
            }
            tbody th, .logo, .origo {
                display: none !important;
            }
            table:not(.ui-datepicker-calendar), table:not(.ui-datepicker-calendar) thead, table:not(.ui-datepicker-calendar)  thead th, table:not(.ui-datepicker-calendar) tbody, table:not(.ui-datepicker-calendar) tr, table:not(.ui-datepicker-calendar) td, .respons {
                display: block;
            }
            table:not(.ui-datepicker-calendar) tbody{
                padding: 0;
                width: 90%;
                margin-left: 5%;
            }
            table:not(.ui-datepicker-calendar) tr {
                margin-bottom: 1rem;
            }
            td[data-label]{
                text-align: right;
                padding-left: 50%;
                padding-bottom: 10%;
                position: relative;
            }
            td[data-label]::before{
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 45%;
                padding-left: 8px;
                font-weight: bold;
                text-align: left;   
            }
            td:has(.panel) {
                text-align: left !important;
                padding-left: 8px !important;
            }
            .panel{
                margin-top: 2rem;
            }
            .svareciTR, .rozboryTR, .prodlZarTR, .prodlOhTR{
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
        } 
    </style>
</body>
</html>