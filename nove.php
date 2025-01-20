<?php
    function GenEvCislo($conn) : int {
        $ev_cislo = (int)date("y") * 1000;

        $sql = "SELECT MAX(ev_cislo) AS max_evcislo FROM Povolenka WHERE ev_cislo >= ? AND ev_cislo < ?";
        $params = [$ev_cislo, $ev_cislo + 1000];
        $result = sqlsrv_query($conn, $sql, $params);
        if ($result === FALSE)
            die(print_r(sqlsrv_errors(), true));
        $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);         
        sqlsrv_free_stmt($result);
            
        if ($zaznam['max_evcislo'] !== null) {
            return $ev_cislo = $zaznam['max_evcislo'] + 1;   
        }
        
        return $ev_cislo + 1;
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
        $sql = "";
        $params = [];

        if (isset($_POST['subOdeslat'])) {
            $svareciPocet = $_POST['svareciPocet'];
            $rozboryPocet = $_POST['rozboryPocet'];
            //INTRO
            $ev_cislo = GenEvCislo($conn);
            $riziko = $_POST['riziko'];
            $interni = $_POST['interni'];
            $externi = $_POST['externi'];
            $pocetOs = $_POST['pocetOs'];
            $povolOd = $_POST['povolOd'] . ' ' . $_POST['hodOd'];
            $povolDo = $_POST['povolDo'] . ' ' . $_POST['hodDo'];
            $prace_na_zarizeni = $_POST['prace_na_zarizeni'] ?? 0;
            $svarovani_ohen = $_POST['svarovani_ohen'] ?? 0;
            $vstup_zarizeni_teren = $_POST['vstup_zarizeni_teren'] ?? 0;
            $prostredi_vybuch = $_POST['prostredi_vybuch'] ?? 0;
            $predani_prevzeti_zarizeni = $_POST['predani_prevzeti_zarizeni']?? 0;
            $provoz = $_POST['provoz'];
            $objekt = $_POST['objekt'];
            $NZarizeni = $_POST['NZarizeni'];
            $CZarizeni = $_POST['CZarizeni'];
            $prace = $_POST['prace'];
            $rizikaPrac = $_POST['rizikaPrac'];
            //TAB 1 1-10 Zařízení bylo
            $vycisteni = $_POST['vycisteni'] ?? 0;
            $vycisteni_kom = $_POST['vycisteni_kom'] ?? null;
            $vyparene = $_POST['vyparene'] ?? 0;
            $vyparene_hod = $_POST['vyparene_hod'] ?? null;
            $vyparene_kom = $_POST['vyparene_kom'] ?? null;
            $vyplachnute = $_POST['vyplachnute'] ?? 0;
            $vyplachnute_kom = $_POST['vyplachnute_kom'] ?? null;
            $plyn_vytesnen = $_POST['plyn_vytesnen'] ?? 0;
            $plyn_vytesnen_kom = $_POST['plyn_vytesnen_kom'] ?? null;
            $vyvetrane = $_POST['vyvetrane'] ?? 0;
            $vyvetrane_hod = $_POST['vyvetrane_hod'] ?? null;
            $vyvetrane_kom = $_POST['vyvetrane_kom'] ?? null;
            $profouk_dusik = $_POST['profouk_dusik'] ?? 0;
            $profouk_dusik_hod = $_POST['profouk_dusik_hod'] ?? null;
            $profouk_dusik_kom = $_POST['profouk_dusik_kom'] ?? null;
            $profouk_vzd = $_POST['profouk_vzd'] ?? 0;
            $profouk_vzd_hod = $_POST['profouk_vzd_hod'] ?? null;
            $profouk_vzd_kom = $_POST['profouk_vzd_kom'] ?? null;
            $odpojeno_od_el = $_POST['odpojeno_od_el'] ?? 0;
            $odpojeno_od_el_kym = $_POST['odpojeno_od_el_kym'] ?? null;
            $oddelene_zaslep = $_POST['oddelene_zaslep'] ?? 0;
            $oddelene_zaslep_kym = $_POST['oddelene_zaslep_kym'] ?? null;
            $jinak_zab = $_POST['jinak_zab'] ?? 0;
            $jinak_zab_jak = $_POST['jinak_zab_jak'] ?? null;
            // TAB 1 11-17 Podmínky BP a PO
            $nejiskrive_naradi = $_POST['nejiskrive_naradi'] ?? 0;
            $nejiskrive_naradi_kom = $_POST['nejiskrive_naradi_kom'] ?? null;
            $zkrapet_vetrat = $_POST['zkrapet_vetrat'] ?? 0;
            $zkrapet_vetrat_pocet = $_POST['zkrapet_vetrat_pocet'] ?? null;
            $zkrapet_vetrat_hod = $_POST['zkrapet_vetrat_hod'] ?? null;
            $zkrapet_vetrat_misto = $_POST['zkrapet_vetrat_misto'] ?? null;
            $rozbor_ovzdusi = $_POST['rozbor_ovzdusi'] ?? 0;
            $rozbor_ovzdusi_misto = $_POST['rozbor_ovzdusi_misto'] ?? null;
            $rozbor_ovzdusi_cas = $_POST['rozbor_ovzdusi_cas'] ?? null;
            $rozbor_ovzdusi_vysl = $_POST['rozbor_ovzdusi_vysl'] ?? null;
            $zab_dozor = $_POST['zab_dozor'] ?? 0;
            $zab_dozor_pocet = $_POST['zab_dozor_pocet'] ?? null;
            $pozar_hlidka = $_POST['pozar_hlidka'] ?? 0;
            $pozar_hlidka_pocet = $_POST['pozar_hlidka_pocet'] ?? null;
            $pozar_hlidka_jmeno = $_POST['pozar_hlidka_jmeno'] ?? null;
            $hasici_pristroj = $_POST['hasici_pristroj'] ?? 0;
            $hasici_pristroj_pocet = $_POST['hasici_pristroj_pocet'] ?? null;
            $hasici_pristroj_druh = $_POST['hasici_pristroj_druh'] ?? null;
            $hasici_pristroj_typ = $_POST['hasici_pristroj_typ'] ?? null;
            $jine_zab_pozar = $_POST['jine_zab_pozar'] ?? 0;
            $jine_zab_pozar_kom = $_POST['jine_zab_pozar_kom'] ?? null;
            // TAB 2 1-8 Osobní ochranné pracovní prostředky
            $ochran_nohy = $_POST['ochran_nohy'];
            $ochran_telo = $_POST['ochran_telo'];
            $ochran_hlava = $_POST['ochran_hlava'];
            $ochran_oci = $_POST['ochran_oci'];
            $ochran_dychadel = $_POST['ochran_dychadel'];
            $ochran_pas = $_POST['ochran_pas'];
            $ochran_rukavice = $_POST['ochran_rukavice'];
            $ochran_dozor = $_POST['ochran_dozor'];
            // TAB 2 9-14 Jiné příkazy
            $jine_prikazy = $_POST['jine_prikazy'];
            $U_220 = $_POST['U_220'] ?? 0;
            $U_24 = $_POST['U_24'] ?? 0;
            $kryt = $_POST['kryt'] ?? 0;
            $bez_krytu = $_POST['bez_krytu'] ?? 0;
            $bez_krytu_kom = $_POST['bez_krytu1'];
            $bez_krytu_kom2 = $_POST['bez_krytu2'];
            $za_praci_odpovida = $_POST['za_praci_odpovida'];
            $odpovednost_dat = $_POST['odpovednost_dat'] . ' ' . $_POST['odpovednost_cas'];
            $osvedceny_prac = $_POST['osvedceny_prac'];
            // TAB 3
            $prohl_prac_dat = $_POST['prohl_prac_dat'];
            $prohl_exter_dat = $_POST['prohl_exter_dat'];
            $prohl_obvod = $_POST['prohl_obvod'];
            $prohl_vedouci_dat = $_POST['prohl_vedouci_dat'];
            // TAB 4
            $podminky = $_POST['podminky'];
            $podminky_jm = $_POST['podminky_jm'];
            $ohen_dat = $_POST['ohen_dat'] . ' ' . $_POST['ohen_cas'];
            $zkontroloval_jm = $_POST['zkontroloval_jm'];
            // TAB 6 (5 jsou rozbory)
            $dalsi_jine = $_POST['dalsi_jine'];
            $dalsi_jine_stanovil = $_POST['dalsi_jine_stanovil'];
            $dalsi_jine_jm = $_POST['dalsi_jine_jm'];
            $dalsi_jine_dat = $_POST['dalsi_jine_dat'] . ' ' . $_POST['dalsi_jine_cas'];
            // TAB 7
            $nutna_dat = $_POST['nutna_dat'];
            $nutna_opatreni = $_POST['nutna_opatreni'];
            // TAB 8
            $oprava_protokol = $_POST['oprava_protokol'];
            $oprava_dat = $_POST['oprava_dat'] . ' ' . $_POST['oprava_cas'];
            $oprava_predal = $_POST['oprava_predal'];
            $oprava_prevzal = $_POST['oprava_prevzal'];
            // TAB 9
            $z_opravy_dat = $_POST['z_opravy_dat'] . ' ' . $_POST['z_opravy_cas'];
            $z_opravy_predal = $_POST['z_opravy_predal'];
            $z_opravy_prevzal = $_POST['z_opravy_prevzal'];
            // TAB 10
            $svarec_ukon_dat = $_POST['svarec_ukon_dat'] . ' ' . $_POST['svarec_ukon_cas'];
            $svarec_ukon_predal = $_POST['svarec_predal'];
            $svarec_ukon_prevzal = $_POST['svarec_prevzal'];
            // TAB 11
            $dozor_od = $_POST['dozor_od'];
            $dozor_do = $_POST['dozor_do'];
            $dozor_jm = $_POST['dozor_jm'];
            // TAB 12
            $kontrola_dat = $_POST['kontrola_dat'] . ' ' . $_POST['kontrola_cas'];
            $kontrola_zjisteno = $_POST['kontrola_zjisteno'];
            $kontrola_jm = $_POST['kontrola_jm'];
            // TAB 14 (13 vyjmuto)
            $doplnky = $_POST['doplnky'];
            $odeslano = DATE("Y-m-d H:i:s");
            
            $sql = "INSERT INTO Povolenka (id_zam, ev_cislo, rizikovost, interni, externi, pocet_osob, povol_od, povol_do, prace_na_zarizeni, svarovani_ohen, vstup_zarizeni_teren, prostredi_vybuch, predani_prevzeti_zarizeni, provoz, objekt, c_zarizeni, nazev_zarizeni, popis_prace, c_karty,
                                            vycisteni, vycisteni_kom, vyparene, vyparene_hod, vyparene_kom, vyplachnute, vyplachnute_kom, plyn_vytesnen, plyn_vytesnen_kom, vyvetrane, vyvetrane_hod, vyvetrane_kom, profouk_dusik, profouk_dusik_hod, profouk_dusik_kom, profouk_vzd, profouk_vzd_hod, profouk_vzd_kom, odpojeno_od_el, odpojeno_od_el_kym, oddelene_zaslep, oddelene_zaslep_kym, jinak_zab, jinak_zab_jak,
                                            nejiskrive_naradi, nejiskrive_naradi_kom, zkrapet_vetrat, zkrapet_vetrat_pocet, zkrapet_vetrat_hod, zkrapet_vetrat_misto, rozbor_ovzdusi, rozbor_ovzdusi_misto, rozbor_ovzdusi_cas, rozbor_ovzdusi_vysl, zab_dozor, zab_dozor_pocet, pozar_hlidka, pozar_hlidka_pocet, pozar_hlidka_jmeno, hasici_pristroj, hasici_pristroj_pocet, hasici_pristroj_druh, hasici_pristroj_typ, jine_zab_pozar, jine_zab_pozar_kom,
                                            ochran_nohy, ochran_telo, ochran_hlava, ochran_oci, ochran_dychadel, ochran_pas, ochran_rukavice, dozor,
                                            jine_prikazy, U_220V, U_24V, kryt, bez_krytu, bez_krytu_kom, bez_krytu_kom2, odpovida, dat_odpovedny, osvedceni_ma,
                                            podminky_ohen, ohen_jmeno, dat_ohen, zkontroloval,
                                            dalsi_jine, dalsi_jine_stanovil, dalsi_jine_jm, dalsi_jine_dat,
                                            nutna_dat, nutna_opatreni,
                                            oprava_protokol, oprava_dat, oprava_predal, oprava_prevzal,
                                            z_opravy_dat, z_opravy_predal, z_opravy_prevzal,
                                            svarec_ukon_dat, svarec_ukon_predal, svarec_ukon_prevzal,
                                            dozor_od, dozor_do, dozor_jm,
                                            kontrola_dat, kontrola_zjisteno, kontrola_jm,
                                            doplnky,
                                            odeslano) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                            ?, ?, ?, ?, ?, ?, ?, ?,
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                            ?, ?, ?, ?,
                            ?, ?, ?, ?,
                            ?, ?,
                            ?, ?, ?, ?,
                            ?, ?, ?,
                            ?, ?, ?, 
                            ?, ?, ?,
                            ?, ?, ?,
                            ?,
                            ?);";
            $params = [$uziv, $ev_cislo, $riziko, $interni, $externi, $pocetOs, $povolOd, $povolDo, $prace_na_zarizeni, $svarovani_ohen, $vstup_zarizeni_teren, $prostredi_vybuch, $predani_prevzeti_zarizeni, $provoz, $objekt, $CZarizeni, $NZarizeni, $prace, $rizikaPrac,
                $vycisteni, $vycisteni_kom, $vyparene, $vyparene_hod, $vyparene_kom, $vyplachnute, $vyplachnute_kom, $plyn_vytesnen, $plyn_vytesnen_kom, $vyvetrane, $vyvetrane_hod, $vyvetrane_kom, $profouk_dusik, $profouk_dusik_hod, $profouk_dusik_kom, $profouk_vzd, $profouk_vzd_hod, $profouk_vzd_kom, $odpojeno_od_el, $odpojeno_od_el_kym, $oddelene_zaslep, $oddelene_zaslep_kym, $jinak_zab, $jinak_zab_jak,
                $nejiskrive_naradi, $nejiskrive_naradi_kom, $zkrapet_vetrat, $zkrapet_vetrat_pocet, $zkrapet_vetrat_hod, $zkrapet_vetrat_misto, $rozbor_ovzdusi, $rozbor_ovzdusi_misto, $rozbor_ovzdusi_cas, $rozbor_ovzdusi_vysl, $zab_dozor, $zab_dozor_pocet, $pozar_hlidka, $pozar_hlidka_pocet, $pozar_hlidka_jmeno, $hasici_pristroj, $hasici_pristroj_pocet, $hasici_pristroj_druh, $hasici_pristroj_typ, $jine_zab_pozar, $jine_zab_pozar_kom,
                $ochran_nohy, $ochran_telo, $ochran_hlava, $ochran_oci, $ochran_dychadel, $ochran_pas, $ochran_rukavice, $ochran_dozor,
                $jine_prikazy, $U_220, $U_24, $kryt, $bez_krytu, $bez_krytu_kom, $bez_krytu_kom2, $za_praci_odpovida, $odpovednost_dat, $osvedceny_prac,
                $podminky, $podminky_jm, $ohen_dat, $zkontroloval_jm,
                $dalsi_jine, $dalsi_jine_stanovil, $dalsi_jine_jm, $dalsi_jine_dat,
                $nutna_dat, $nutna_opatreni,
                $oprava_protokol, $oprava_dat, $oprava_predal, $oprava_prevzal,
                $z_opravy_dat, $z_opravy_predal, $z_opravy_prevzal,
                $svarec_ukon_dat, $svarec_ukon_predal, $svarec_ukon_prevzal,
                $dozor_od, $dozor_do, $dozor_jm,
                $kontrola_dat, $kontrola_zjisteno, $kontrola_jm,
                $doplnky,
                $odeslano];

            $result = sqlsrv_query($conn, $sql, $params);
            if ($result === FALSE)
                die(print_r(sqlsrv_errors(), true));

            $sql = "SELECT @@identity AS id_pov";
            $result = sqlsrv_query($conn, $sql);
            if ($result === FALSE)
                die(print_r(sqlsrv_errors(), true));
        
            $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
            sqlsrv_free_stmt($result);
            $povID = $zaznam['id_pov'];
            
            //TAB 2
            if (!empty($_POST['svarec'][0]['prukaz'])) {
                for ($i = 0; $i < $svareciPocet; $i++) { 
                    $svarecJmeno = $_POST['svarec'][$i]['jmeno'];
                    $svarecPrukaz = $_POST['svarec'][$i]['prukaz'];
        
                    $sql = "SELECT * FROM Svareci AS s WHERE s.c_prukazu = ?;";
                    $params = [$svarecPrukaz];
                    $result = sqlsrv_query($conn, $sql, $params);
                    if ($result === FALSE)
                        die(print_r(sqlsrv_errors(), true));
        
                    if (sqlsrv_has_rows($result)) {
                        $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
                        sqlsrv_free_stmt($result);
                        $svarecID = $zaznam['id_svar'];
                    }
                    else {
                        sqlsrv_free_stmt($result);
                        $sql = "INSERT INTO Svareci (jmeno, c_prukazu) VALUES (?, ?);";
                        $params = [$svarecJmeno, $svarecPrukaz];
                        $result = sqlsrv_query($conn, $sql, $params);
                        if ($result === FALSE)
                            die(print_r(sqlsrv_errors(), true));
        
                        $sql = "SELECT @@identity AS id_svar";
                        $result = sqlsrv_query($conn, $sql);
                        if ($result === FALSE)
                            die(print_r(sqlsrv_errors(), true));
        
                        $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
                        sqlsrv_free_stmt($result);
                        $svarecID = $zaznam['id_svar'];
                    }
                    
                    $sql = "INSERT INTO Pov_Svar (id_pov, id_svar) VALUES (?, ?);";
                    $params = [$povID, $svarecID];
                    $result = sqlsrv_query($conn, $sql, $params);
                        if ($result === FALSE)
                            die(print_r(sqlsrv_errors(), true));
                    sqlsrv_free_stmt($result);
                }
            }
            //TAB 5
            if (!empty($_POST['rozbor'][0]['hodn'])) {
                for ($i = 0; $i < $rozboryPocet; $i++) { 
                    $rozborNazev = $_POST['rozbor'][$i]['nazev'];
                    $rozborDat = $_POST['rozbor'][$i]['dat'];
                    $rozborCas = $_POST['rozbor'][$i]['cas'];
                    $rozborMisto = $_POST['rozbor'][$i]['misto'];
                    $rozborHodn = $_POST['rozbor'][$i]['hodn'];
        
                    $sql = "SELECT * FROM Rozbory AS r WHERE r.nazev = ? AND r.dat = ? AND r.cas = ? AND r.misto = ? AND r.hodn = ?;";
                    $params = [$rozborNazev, $rozborDat, $rozborCas, $rozborMisto, $rozborHodn];
                    $result = sqlsrv_query($conn, $sql, $params);
                    if ($result === FALSE)
                        die(print_r(sqlsrv_errors(), true));
                            
                    if (sqlsrv_has_rows($result)) {
                        $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
                        sqlsrv_free_stmt($result);
                        $rozborID = $zaznam['id_roz'];
                    }
                    else {
                        sqlsrv_free_stmt($result);
                        $sql = "INSERT INTO Rozbory (nazev, dat, cas, misto, hodn) VALUES (?, ?, ?, ?, ?);";
                        $params = [$rozborNazev, $rozborDat, $rozborCas, $rozborMisto, $rozborHodn];
                        $result = sqlsrv_query($conn, $sql, $params);
                        if ($result === FALSE)
                            die(print_r(sqlsrv_errors(), true));
                                
                        $sql = "SELECT @@identity AS id_roz";
                        $result = sqlsrv_query($conn, $sql);
                        if ($result === FALSE)
                            die(print_r(sqlsrv_errors(), true));
    
                        $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
                        sqlsrv_free_stmt($result);
                        $rozborID = $zaznam['id_roz'];
                    }
                    $sql = "INSERT INTO Pov_Roz (id_pov, id_roz) VALUES (?, ?);";
                    $params = [$povID, $rozborID];
                    $result = sqlsrv_query($conn, $sql, $params);
                        if ($result === FALSE)
                            die(print_r(sqlsrv_errors(), true));
                    sqlsrv_free_stmt($result);        
                }
            }
            echo '<script>
                    alert("Žádost byla uspěšně odeslána");
                    window.location.href = "uvod.php";
                  </script>';
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
        <h1>NOVÉ POVOLENÍ</h1>
        <div class="headerB">
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
    <form action="" method="post">
        <div class="firstPage">
            <table class="intro">
                <thead>
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
                <tbody>
                    <tr>
                        <td>
                            <input type="range" id="riziko" name="riziko" min="1" max="10" step="1" value="5">
                            <b id="rizikoValue">5</b>
                        </td>
                        <td><input type="text" name="interni"></td>
                        <td><input type="text" name="externi"></td>
                        <td><input type="text" name="pocetOs"></td>
                        <td><input type="date" name="povolOd" min="<?php echo date("Y-m-d") ?>"></td>
                        <td><input type="date" name="povolDo" min="<?php echo date("Y-m-d") ?>"></td>
                        <td rowspan="5">
                            <div class="panel">
                                <label class="container">K práci na zařízení
                                    <input type="checkbox" name="prace_na_zarizeni" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">Ke svařování a práci s otevřeným ohněm
                                    <input type="checkbox" name="svarovani_ohen" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">Ke vstupu do zařízení nebo pod úroveň terénu
                                    <input type="checkbox" name="vstup_zarizeni_teren" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">K práci v prostředí s nebezpečím výbuchu
                                    <input type="checkbox" name="prostredi_vybuch" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">K předání a převzetí zařízení do opravy a do provozu
                                    <input type="checkbox" name="predani_prevzeti_zarizeni" value="1">
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
                        <td><input type="text" name="hodOd" class="time" maxlength="5" placeholder="00:00"></td>
                        <td><input type="text" name="hodDo" class="time" maxlength="5" placeholder="00:00"></td>
                    </tr>
                    <tr>
                        <th>Název zařízení</th>
                        <td colspan="2"><input type="text" name="NZarizeni"></td>
                        <th>Číslo zařízení</th>
                        <td colspan="2"><input type="text" name="CZarizeni"></td>
                    </tr>
                    <tr>
                        <th>Popis, druh a rozsah práce</th>
                        <td colspan="5"><input type="text" name="prace"></td>
                    </tr>
                    <tr>
                        <th>Seznámení s riziky pracoviště dle karty č.</th>
                        <td colspan="5"><input type="text" name="rizikaPrac"></td>
                    </tr>   
                </tbody>
            </table>
            <table class="first">
                <thead>
                    <tr>
                        <th colspan="2">1. Příprava zařízení k opravě</th>
                        <th colspan="5">Bližší určení</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="podnadpis" colspan="7">Zařízení bylo</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.1 Vyčištění od zbytků
                                <input type="checkbox" class="inputCheckbox" name="vycisteni" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="vycisteni_kom" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.2 Vypařené
                                <input type="checkbox" name="vyparene" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="vyparene_hod" disabled></td>
                        <td colspan="4"><input type="text" name="vyparene_kom" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.3 Vypláchnuté vodou
                                    <input type="checkbox" name="vyplachnute" value="1">
                                    <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="vyplachnute_kom" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.4 Plyn vytěsnen vodou
                                <input type="checkbox" name="plyn_vytesnen" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="plyn_vytesnen_kom" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.5 Vyvětrané
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="vyvetrane_hod" disabled></td>
                        <td colspan="4"><input type="text" name="vyvetrane_kom" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.6 Profoukané dusíkem
                                <input type="checkbox" name="profouk_dusik" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="profouk_dusik_hod" disabled></td>
                        <td colspan="4"><input type="text" name="profouk_dusik_kom" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.7 Profoukané vzduchem
                                <input type="checkbox" name="profouk_vzd" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="profouk_vzd_hod" disabled></td>
                        <td colspan="4"><input type="text" name="profouk_vzd_kom" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.8 Odpojeno od elektrického proudu
                                <input type="checkbox" name="odpojeno_od_el" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Kým</th>
                        <td colspan="3"><input type="text" name="odpojeno_od_el_kym" disabled></td>
                        <th>Podpis</th>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.9 Oddělené záslepkami
                                <input type="checkbox" name="oddelene_zaslep" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Kým</th>
                        <td colspan="3"><input type="text" name="oddelene_zaslep_kym" disabled></td>
                        <th>Podpis</th>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.10 Jinak zapezpečené
                                <input type="checkbox" name="jinak_zab" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Jak</th>
                        <td colspan="6"><input type="text" name="jinak_zab_jak" disabled></td>
                    </tr>
                    <tr>
                        <td class="podnadpis"colspan="7">Podmínky BP a PO</td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.11 Použít nejiskřivého nářadí
                                <input type="checkbox" name="nejiskrive_naradi" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="nejiskrive_naradi_kom" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.12 Po dobu oprav - zkrápět, větrat
                                <input type="checkbox" name="zkrapet_vetrat" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <td><input type="text" name="zkrapet_vetrat_pocet" disabled></td>
                        <th>Krát za</th>
                        <td><input type="text" name="zkrapet_vetrat_hod" disabled></td>
                        <th>Hodin</th>
                        <th>V místě</th>
                        <td><input type="text" name="zkrapet_vetrat_misto" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.13 Provést rozbor ovzduší
                                <input type="checkbox" name="rozbor_ovzdusi" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Místo</th>
                        <td><input type="text" name="rozbor_ovzdusi_misto" disabled></td>
                        <th>Čas</th>
                        <td><input type="text" name="rozbor_ovzdusi_cas" disabled></td>
                        <th>Výsledek</th>
                        <td><input type="text" name="rozbor_ovzdusi_vysl" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.14 Zabezpečit dozor dalšími osobami
                                <input type="checkbox" name="zab_dozor" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Počet</th>
                        <td><input type="text" name="zab_dozor_pocet" disabled></td>
                        <th colspan="4">Jména uvést v bodě 7</th>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.15 Požární hlídka provozu
                                <input type="checkbox" name="pozar_hlidka" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Počet</th>
                        <td><input type="text" name="pozar_hlidka_pocet" disabled></td>
                        <th>Jméno</th>
                        <td colspan="3"><input type="text" name="pozar_hlidka_jmeno" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.16 Hasící přístroj
                                <input type="checkbox" name="hasici_pristroj" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Počet</th>
                        <td><input type="text" name="hasici_pristroj_pocet" disabled></td>
                        <th>Druh</th>
                        <td><input type="text" name="hasici_pristroj_druh" disabled></td>
                        <th>Typ</th>
                        <td><input type="text" name="hasici_pristroj_typ" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.17 Jiné zabezpečení požární ochrany
                                <input type="checkbox" name="jine_zab_pozar" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="jine_zab_pozar_kom" disabled></td>
                    </tr>
                </tbody>
            </table>
            <table class="sec">
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
                        <td colspan="5"><input type="text" name="ochran_nohy"></td>
                    </tr>
                    <tr>
                        <th>2.2 Ochrana těla - jaká</th>
                        <td colspan="6"><input type="text" name="ochran_telo"></td>
                    </tr>
                    <tr>
                        <th>2.3 Ochrana hlavy - jaká</th>
                        <td colspan="6"><input type="text" name="ochran_hlava"></td>
                    </tr>
                    <tr>
                        <th>2.4 Ochrana oči - jaká - druh</th>
                        <td colspan="6"><input type="text" name="ochran_oci"></td>
                    </tr>
                    <tr>
                        <th>2.5 Ochrana dýchadel - jaká</th>
                        <td colspan="6"><input type="text" name="ochran_dychadel"></td>
                    </tr>
                    <tr>
                        <th>2.6 Ochranný pás - druh</th>
                        <td colspan="6"><input type="text" name="ochran_pas"></td>
                    </tr>
                    <tr>
                        <th>2.7 Ochranné rukavice - druh</th>
                        <td colspan="6"><input type="text" name="ochran_rukavice"></td>
                    </tr>
                    <tr>
                        <th>2.8 Dozor jmenovitě</th>
                        <td colspan="5"><input type="text" name="ochran_dozor"></td>
                    </tr>
                    <tr>
                        <td class="podnadpis" colspan="6">Jiné příkazy</td>
                    </tr>
                    <tr>
                        <th>2.9 Jiné</th>
                        <td colspan="5"><input type="text" name="jine_prikazy"></td>
                    </tr>
                    <tr>
                        <td rowspan="2">
                            <div class="panel">
                                <label class="container">2.10 Napětí 220 V
                                    <input type="checkbox" name="U_220" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">2.11 Napětí 24 V
                                    <input type="checkbox" name="U_24" value="1">
                                    <span class="checkbox"></span>
                                </label>
                            </div>
                        </td>
                        <td rowspan="2">
                            <div class="panel">
                                <label class="container">S krytem
                                    <input type="checkbox" name="kryt" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">Bez krytu
                                    <input type="checkbox" name="bez_krytu" value="1">
                                    <span class="checkbox"></span>
                                </label>
                            </div>
                        </td>
                        <th>Bez krytu</th>
                        <td colspan="3"><input type="text" name="bez_krytu1"></td>
                    </tr>
                    <tr>
                        <th>Bez krytu</th>
                        <td colspan="3"><input type="text" name="bez_krytu2"></td>
                    </tr>
                    <tr>
                        <td rowspan="3" colspan="2">
                            2.12 Prohlášení: Prohlašuji, že zajistím dodržení výše uvedených <br>
                            podmínek, jakož i bezpečný provoz a postup práce, mně podřízených <br>
                            pracovníků. Při jakékoliv změně podmínek práci přeruším a požádám <br>
                            o okamžité prověření prostředí. Po skončení práce s otevřeným ohněm <br>
                            zabezpečím pracoviště ve smyslu ČSN 05 06 10.
                        </td>
                        <th>Za práci čety odpovídá</th>
                        <td colspan="3"><input type="text" name="za_praci_odpovida"></td>
                    </tr>
                    <tr>
                        <th>Datum</th>
                        <td><input type="date" name="odpovednost_dat"></td>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="odpovednost_cas"></td>
                        <th>Hodin</th>
                    </tr>
                    <tr>
                        <th>Podpis vedoucího čety</th>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <th colspan="6" class="podnadpis">2.13 Sváření provedou</th>
                    </tr>
                    <tr>
                        <th>Jméno</th>
                        <th colspan="2">Č. svář. průkazu</th>
                        <th colspan="3">Podpis</th>
                    </tr>
                    <tr class="svareciTR" data-index="0">
                        <td><input type="text" name="svarec[0][jmeno]" /></td>
                        <td colspan="2"><input type="text" name="svarec[0][prukaz]" /></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr id="svarecAdd">
                        <td colspan="6"><button type="button" id="svarecAddBut" class="add">+</button></td>
                        <input type="hidden" name="svareciPocet" value="1">
                    </tr>
                    <tr>
                        <th colspan="3">2.14 Osvědčení o způsobilosti k práci a sváření na plynové zařízení má pracovník:</th>
                        <td colspan="3"><input type="text" name="osvedceny_prac"></td>
                    </tr>
                </tbody>
            </table>
            <table class="third">
                <thead>
                    <tr>
                        <th colspan="7">3. Prohlašuji, že jsem se přesvědčil, že výše uvededené zajištění je provedeno.</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Datum</th>
                        <td><input type="date" name="prohl_prac_dat"></td>
                        <th>Datum</th>
                        <td><input type="date" name="prohl_exter_dat"></td>
                        <th>Vyjádření přilehlého obvodu </th>
                        <td colspan="2"><input type="text" name="prohl_obvod"></td>
                    </tr>
                    <tr>
                        <th rowspan="2" colspan="2">Podpis odpovědného pracovníka provozu: </th>
                        <th rowspan="2" colspan="2">Podpis odpovědného pracovníka provádějícího útvaru GB nebo externí firmy:</th>
                        <th rowspan="2">Podpis vedoucího přilehlého obvodu:</th>
                        <th>Datum</th>
                        <td><input type="date" name="prohl_vedouci_dat"></td>
                    </tr>
                    <tr>
                        <th colspan="5"></th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="secPage">
            <table class="fourth">
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
                    </tr>
                    <tr>
                        <td colspan="5"><textarea name="podminky" rows="5" style="resize: none; width: 100%;"></textarea></td>
                    </tr>
                    <tr>
                        <th colspan="2">4.2 Výše uvedené podmínky stanovil - jméno:</th>
                        <td><input type="text" name="podminky_jm"></td>
                        <th>Podpis</th>
                        <td></td>
                    </tr>
                    <tr>
                        <th>4.3 Pracoviště připraveno pro práci s otevřeným ohněm:</th>
                        <th>Dne</th>
                        <td><input type="date" name="ohen_dat"></td>
                        <th>Hodin</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="ohen_cas"></td>
                    </tr>
                    <tr>
                        <th colspan="2">4.4 Osobně zkontroloval - jméno</th>
                        <td><input type="text" name="zkontroloval_jm"></td>
                        <th>Podpis</th>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <table class="fifth">
                <thead>
                    <tr>
                        <th>5. Rozbor obzduší</th>
                        <th>Datum</th>
                        <th>Čas</th>
                        <th>Místo odběru vzorku ovzduší</th>
                        <th>Naměřená hodnota</th>
                        <th>Podpis</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="rozboryTR" data-index="0">
                        <td><input type="text" name="rozbor[0][nazev]"></td>
                        <td><input type="date" name="rozbor[0][dat]"></td>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="rozbor[0][cas]"></td>
                        <td><input type="text" name="rozbor[0][misto]"></td>
                        <td><input type="text" name="rozbor[0][hodn]"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr id="rozborAdd">
                        <td colspan="6"><button type="button" id="rozborAddBut" class="add">+</button></td>
                        <input type="hidden" name="rozboryPocet" value="1">
                    </tr>
                </tbody>
            </table>
            <table class="sixth">
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
                        <td rowspan="4"><textarea name="dalsi_jine" rows="10" style="resize: none; width: 95%;"></textarea></td>
                        <th>Stanovil</th>
                        <td colspan="3"><input type="text" name="dalsi_jine_stanovil"></td>
                    </tr>
                    <tr>
                        <th>Jméno</th>
                        <td colspan="3"><input type="text" name="dalsi_jine_jm"></td>
                    </tr>
                    <tr>
                        <th>Dne</th>
                        <td><input type="date" name="dalsi_jine_dat"></td>
                        <th>Hodin</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="dalsi_jine_cas"></td>
                    </tr>
                    <tr>
                        <th>Podpis</th>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>
            <table class="seventh">
                <thead>
                    <tr>
                        <th>7. Další nutná opatření - případně viz protokol ze dne</th>
                        <th><input type="date" name="nutna_dat"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2"><textarea name="nutna_opatreni" rows="4" style="resize: none; width: 100%;"></textarea></td>
                    </tr>
                </tbody>
            </table>
            <table class="eighth">
                <thead>
                    <tr>
                        <th colspan="3">8. Předání do opravy - protokol č.</th>
                        <th><input type="text" name="oprava_protokol"></th>
                        <th colspan="4">10. Práce svářečské ukončeny</th>
                        <th colspan="4">12. Kontrola BT, PO, jiného orgánu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Dne</th>
                        <td><input type="date" name="oprava_dat"></td>
                        <th>Hodina</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="oprava_cas"></td>
                        <th>Dne</th>
                        <td><input type="date" name="svarec_ukon_dat"></td>
                        <th>Hodina</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="svarec_ukon_cas"></td>
                        <th>Kontrola dne</th>
                        <td><input type="date" name="kontrola_dat"></td>
                        <th>Hodina</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="kontrola_cas"></td>
                    </tr>
                    <tr>
                        <th>Předal</th>
                        <td colspan="3"><input type="text" name="oprava_predal"></td>
                        <th>Předal</th>
                        <td colspan="3"><input type="text" name="svarec_predal"></td>
                        <th rowspan="4">Zjištěno</th>
                        <td colspan="3" rowspan="4"><textarea name="kontrola_zjisteno" rows="10" style="resize: none; width: 100%; padding: 5% 0;"></textarea></td>
                    </tr>
                    <tr>
                        <th>Převzal</th>
                        <td colspan="3"><input type="text" name="oprava_prevzal"></td>
                        <th>Převzal</th>
                        <td colspan="3"><input type="text" name="svarec_prevzal"></td>
                    </tr>
                    <tr>
                        <th colspan="4">9. Převzání z opravy</th>
                        <th colspan="4">11. Následný dozor</th>
                    </tr>
                    <tr>
                        <th>Dne</th>
                        <td><input type="date" name="z_opravy_dat"></td>
                        <th>Hodina</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="z_opravy_cas"></td>
                        <th>Od</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="dozor_od"></td>
                        <th>Do</th>
                        <td><input type="text" class="time" maxlength="5" placeholder="00:00" name="dozor_do"></td>
                    </tr>
                    <tr>
                        <th>Předal</th>
                        <td colspan="3"><input type="text" name="z_opravy_predal"></td>
                        <th>Jméno</th>
                        <td colspan="3"><input type="text" name="dozor_jm"></td>
                        <th>Jméno</th>
                        <td colspan="3"><input type="text" name="kontrola_jm"></td>
                    </tr>
                    <tr>
                        <th>Převzal</th>
                        <td colspan="3"><input type="text" name="z_opravy_prevzal"></td>
                        <th>Podpis</th>
                        <td colspan="3"></td>
                        <th>Podpis</th>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <table class="ninth">
                <thead>
                    <tr>
                        <th colspan="7">13. Prodloužených za podmínek stanovených tímto povolením</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th class="podnadpis" colspan="7">Prodlužuje provozovatel</th>
                    </tr>
                    <tr>
                        <th>13.1 Pro práci na zařízení</th>
                        <td colspan="6"><input type="text" name="prodluz_zarizeni"></td>
                    </tr>
                    <tr>
                        <th>Datum</th>
                        <th>Od - Do</th>
                        <th>Přestávka</th>
                        <th>Počet osob</th>
                        <th>Podpis odpovědného prac. provozu</th>
                        <th>Podpis odpovědného prac. prov. útvaru</th>
                        <th></th>
                    </tr>
                    <tr>
                        <td><input type="date" name="prodluz_zar_dat"></td>
                        <td><input type="text" name="prodluz_zar_oddo"></td>
                        <td><input type="text" name="prodluz_zar_prestavka"></td>
                        <td><input type="text" name="prodluz_zar_os"></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <th>13.2 Pro práci s otevřeným ohněm</th>
                        <td colspan="6"><input type="text" name="prodluz_ohen"></td>
                    </tr>
                    <tr>
                        <th>Datum</th>
                        <th>Od - Do</th>
                        <th>Přestávka</th>
                        <th>Počet osob</th>
                        <th colspan="3">Podpis Vystavovatele</th>
                    </tr>
                    <tr>
                        <td><input type="date" name="prodluz_oh_dat"></td>
                        <td><input type="text" name="prodluz_oh_oddo"></td>
                        <td><input type="text" name="prodluz_oh_prestavka"></td>
                        <td><input type="text" name="prodluz_oh_os"></td>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>
            <table class="tenth">
                <thead>
                    <tr>
                        <th>14. Doplňky, poznámky</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="5"><textarea name="doplnky" rows="5" style="resize: none; width: 100%;"></textarea></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="submit-container">
            <input type="submit" class="add" value="Odeslat" name="subOdeslat" style="font-size: 16px;">
        </div>
    </form>
    <div class="footer">
        <img src="Indorama.png" width="200px">
    </div>
    <style>
        body{
            background: unset;
            background-color: #F0F8FF; 
        }
        table{
            background-color: white;
            padding: 20px;
            border: 1px solid #bcd4ef;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 70%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        thead th {
            background-color: #eaf3ff;
            text-align: center;
            font-weight: bold;
            padding: 10px;
            border-bottom: 2px solid #bcd4ef;
        }
        tbody th {
            background-color: #f7faff;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #e6ecf2;
        }
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e6ecf2;
        }
        .podnadpis{
            font-weight: bold;
            padding: 1% 0 1% 1%;
            background-color: #eeeeee;
        }
        input[type="text"],
        input[type="date"],
        input[type="time"],
        input[type="range"] {
            width: 100%;
            padding: 8px;
            margin: 2px 0;
            box-sizing: border-box;
            border: 1px solid #bcd4ef;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
        }
        input[type="range"] {
            width: 85%; 
            height: 5px; 
            background-color:#eeeeee; 
        }
        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="time"]:focus{
            border-color: #2196F3;
            box-shadow: 0 0 4px rgba(33, 150, 243, 0.5);
        }
        button, input[type="submit"]{
            color: white;
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
            background-color: #39B54A;
        }
        .del {
            background-color: #FF2C55;
        }
        .add:hover {
            background-color: #34A343;
        }
        .add:active {
            background-color: #2E8E3B;
        }
        .del:hover {
            background-color: #E62A4E;
        }
        .del:active {
            background-color: #CC2444;
        }

        td:has(button), th:has(button){
            text-align: center;
        }
        .panel {
            padding: 18px 18px;
            background-color: #f9fcff;
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
            background-color: #eeeeee;
            border-radius: 4px;
            border: 1px solid #bcd4ef;
        }
        .container:hover input ~ .checkbox {
            background-color: #cccccc;
        }
        .container input:checked ~ .checkbox {
            background-color: #2196F3;
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
            margin: 20px 0; 
        }

        .footer{
            display: none;
        }

        @media (max-width: 660px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            .headerB{
                gap: 150px;
            }
            .container{
                background: unset;
                box-shadow: unset;
            }
            .footer{
                display: flex;  
            }
            .logo {
                display: none;
            }
            h1 {
                font-size: 1.5em;
            }
        }
    </style>
</body>
</html>