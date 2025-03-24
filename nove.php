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
    function inputCheck($input){
        return $input === "" ? $input = null : $input;
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
            if (empty($_POST['prodluz_zarizeni']) && empty($_POST['prodluz_ohen'])) {
                $id_pov = $_POST['id_pov'] ?? null;
                $svareciPocet = $_POST['svareciPocet'];
                $rozboryPocet = $_POST['rozboryPocet'];
                //INTRO
                $ev_cislo = GenEvCislo($conn);
                $riziko = $_POST['riziko'];
                $interni = inputCheck($_POST['interni']);
                $externi = inputCheck($_POST['externi']);
                $pocetOs = $_POST['pocetOs'] === "" ? 1 : $_POST['pocetOs'];
                $_POST['hodOd'] = $_POST['hodOd'] ?? null;
                $_POST['hodDo'] = $_POST['hodDo'] ?? null;
                $povolOd = $_POST['povolOd'] ?? null . ' ' . $_POST['hodOd'];
                $povolDo = $_POST['povolDo'] ?? null . ' ' . $_POST['hodDo'];
                $prace_na_zarizeni = $_POST['prace_na_zarizeni'] ?? 0;
                $svarovani_ohen = $_POST['svarovani_ohen'] ?? 0;
                $vstup_zarizeni_teren = $_POST['vstup_zarizeni_teren'] ?? 0;
                $prostredi_vybuch = $_POST['prostredi_vybuch'] ?? 0;
                $predani_prevzeti_zarizeni = $_POST['predani_prevzeti_zarizeni'] ?? 0;
                $provoz = inputCheck($_POST['provoz']);
                $objekt = inputCheck($_POST['objekt']);
                $NZarizeni = inputCheck($_POST['NZarizeni']);
                $CZarizeni = inputCheck($_POST['CZarizeni']);
                $prace = inputCheck($_POST['prace']);
                $rizikaPrac = inputCheck($_POST['rizikaPrac']);
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
                // TAB 14
                $doplnky = $_POST['doplnky'];
                $odeslano = DATE("Y-m-d H:i:s");
                
                //EDIT
                if ($id_pov != null) {
                    $sql = "UPDATE Povolenka SET 
                                upraveno = GETDATE(),
                                rizikovost = ?, interni = ?, externi = ?, pocet_osob = ?, prace_na_zarizeni = ?, svarovani_ohen = ?, vstup_zarizeni_teren = ?, prostredi_vybuch = ?, predani_prevzeti_zarizeni = ?, provoz = ?, objekt = ?, c_zarizeni = ?, nazev_zarizeni = ?, popis_prace = ?, c_karty = ?
                            WHERE id_pov = ?;";
                    $params = [$riziko, $interni, $externi, $pocetOs, $prace_na_zarizeni, $svarovani_ohen, $vstup_zarizeni_teren, $prostredi_vybuch, $predani_prevzeti_zarizeni, $provoz, $objekt, $CZarizeni, $NZarizeni, $prace, $rizikaPrac,
                                $id_pov];

                    $result = sqlsrv_query($conn, $sql, $params);
                    if ($result === FALSE)
                        die(print_r(sqlsrv_errors(), true));
                    sqlsrv_free_stmt($result);

                    echo '<script>
                            alert("Povolení bylo upraveno!");
                            window.location.href = "uvod.php";
                        </script>';
                }//INSERT
                else{
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
                                    GETDATE());";
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
                        $doplnky];
        
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
            if(!empty($_POST['prodluz_zarizeni'])){
                $id_pov = $_POST['id_pov'];
                $typ = "zařízení";
                $pro_praci = $_POST['prodluz_zarizeni'];
                $od = $_POST['prodluzZarOd'] . ' ' . $_POST['prodluzZarhodOd'];
                $do = $_POST['prodluzZarDo'] . ' ' . $_POST['prodluzZarhodDo'];
                $prestavka = $_POST['prodluz_zar_prestavka'];
                $pocet_os = $_POST['prodluz_zar_os'];

                $sql = "INSERT INTO Prodlouzeni (id_pov, typ, pro_praci, od, do, prestavka, pocet_os)
                        VALUES (?, ?, ?, ?, ?, ?, ?);";
                $params = [$id_pov, $typ, $pro_praci, $od, $do, $prestavka, $pocet_os];
                $result = sqlsrv_query($conn, $sql, $params);
                if ($result === FALSE)
                    die(print_r(sqlsrv_errors(), true));

                echo '<script>
                        alert("Povolení bylo prodlouženo!");
                        window.location.href = "uvod.php";
                    </script>';
            }
            if(!empty($_POST['prodluz_ohen'])) {
                $id_pov = $_POST['id_pov'];
                $typ = "oheň";
                $pro_praci = $_POST['prodluz_ohen'];
                $od = $_POST['prodluzOhOd'] . ' ' . $_POST['prodluzOhHodOd'];
                $do = $_POST['prodluzOhDo'] . ' ' . $_POST['prodluzOhHodDo'];
                $prestavka = $_POST['prodluz_oh_prestavka'];
                $pocet_os = $_POST['prodluz_oh_os'];

                $sql = "INSERT INTO Prodlouzeni (id_pov, typ, pro_praci, od, do, prestavka, pocet_os)
                        VALUES (?, ?, ?, ?, ?, ?, ?);";
                $params = [$id_pov, $typ, $pro_praci, $od, $do, $prestavka, $pocet_os];
                $result = sqlsrv_query($conn, $sql, $params);
                if ($result === FALSE)
                    die(print_r(sqlsrv_errors(), true));

                echo '<script>
                        alert("Povolení bylo prodlouženo!");
                        window.location.href = "uvod.php";
                    </script>';
            }  
        }
        else if(isset($_POST['subEdit']) || isset($_POST['subProdl'])){
            $id = $_POST['id'];

            if (isset($_POST['subProdl'])) {
                $sql = "SELECT
                            p.id_pov as id,
                            p.prace_na_zarizeni as zar,
                            p.svarovani_ohen as oh,
                            p.povol_do as povolDo,
                            (select MAX(prd.do) from Prodlouzeni as prd where prd.id_pov = p.id_pov AND prd.typ = 'zařízení') as prodlZarDo,
                            (select MAX(prd.do) from Prodlouzeni as prd where prd.id_pov = p.id_pov AND prd.typ = 'oheň') as prodlOhDo
                        FROM Povolenka as p
                        WHERE id_pov = ?;";
            }
            else{
                $sql = "SELECT 
                            Povolenka.*, 
                            (SELECT MAX(prd.do) FROM Prodlouzeni AS prd WHERE prd.id_pov = Povolenka.id_pov AND prd.typ = 'zařízení') AS prodlZarDo,
                            (SELECT MAX(prd.do) FROM Prodlouzeni AS prd WHERE prd.id_pov = Povolenka.id_pov AND prd.typ = 'oheň') AS prodlOhDo    
                        FROM Povolenka 
                        WHERE Povolenka.id_pov = ?;";
            }
            $params = [$id];
            $result = sqlsrv_query($conn, $sql, $params);
            if ($result === false) 
                die(print_r(sqlsrv_errors(), true));

            $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);  
            sqlsrv_free_stmt($result);

            if (isset($_POST['subEdit'])) {
                $poleDat = [$zaznam['povol_do'], $zaznam['prodlZarDo'], $zaznam['prodlOhDo']];
                $nejDo = max(array_filter($poleDat));
            }
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
    <form action="" method="POST">
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
                                <input type="range" id="riziko" name="riziko" min="1" max="10" step="1" value="<?= $zaznam['rizikovost'] ?? 5 ?>">
                                <b id="rizikoValue"><?= isset($zaznam['rizikovost']) ? $zaznam['rizikovost'] : 5 ?></b>
                            </div>
                        </td>
                        <td data-label="Interní"><input type="text" name="interni" value="<?= $zaznam['interni'] ?? null ?>"></td>
                        <td data-label="Externí"><input type="text" name="externi" value="<?= $zaznam['externi'] ?? null ?>"></td>
                        <td data-label="Počet osob"><input type="text" name="pocetOs" value="<?= $zaznam['pocet_osob'] ?? null ?>"></td>
                        <td class="origo"><input type="text" name="povolOd" id="povolOd" class="date" min="<?= date("Y-m-d") ?>" value="<?= inputVal($zaznam['povol_od'] ?? null, 'dat'); ?>" <?= isset($zaznam['povol_od']) ? 'disabled' : '' ?> onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'" placeholder="Vyberte datum"></td>
                        <td data-label="Od" class="respons" rowspan="2">
                            <input type="text" name="povolOd" id="povolOd" class="date" min="<?= date("Y-m-d") ?>" value="<?= inputVal($zaznam['povol_od'] ?? null, 'dat') ?>" <?= isset($zaznam['povol_od']) ? 'disabled' : '' ?> onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'" placeholder="Vyberte datum" style="margin-bottom: 10%;">
                            <input type="text" name="hodOd" class="time" id="hodOd" value="<?= inputVal($zaznam['povol_od'] ?? null, "cas") ?>" maxlength="5" placeholder="00:00" <?= isset($zaznam['povol_od']) ? 'disabled' : '' ?>>
                        </td>
                        <td class="origo"><input type="text" name="povolDo" id="povolDo" class="date" min="<?= date("Y-m-d") ?>" value="<?= inputVal($nejDo ?? null, "dat") ?>" <?= isset($zaznam['povol_do']) ? 'disabled' : '' ?> onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'" placeholder="Vyberte datum"></td>
                        <td data-label="Do" class="respons" rowspan="2">
                            <input type="text" name="povolDo" id="povolDo" class="date" min="<?= date("Y-m-d") ?>" value="<?= inputVal($nejDo ?? null, "dat") ?>" <?= isset($zaznam['povol_do']) ? 'disabled' : '' ?> onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'" placeholder="Vyberte datum" style="margin-bottom: 10%;">
                            <input type="text" name="hodDo" class="time" id="hodDo" maxlength="5" placeholder="00:00" value="<?= inputVal($nejDo ?? null, "cas") ?>" <?= isset($zaznam['povol_do']) ? 'disabled' : '' ?>   >
                        </td>
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
                        <td data-label="Provoz"><input type="text" name="provoz" value="<?= $zaznam['provoz'] ?? null ?>"></td>
                        <th>Název(číslo) objektu</th>
                        <td data-label="Název(číslo) objektu"><input type="text" name="objekt" value="<?= $zaznam['objekt'] ?? null ?>"></td>
                        <td class="origo"><input type="text" name="hodOd" class="time" id="hodOd" maxlength="5" placeholder="00:00" value="<?= inputVal($zaznam['povol_od'] ?? null, "cas") ?>" <?= isset($zaznam['povol_od']) ? 'disabled' : '' ?>></td>
                        <td class="origo"><input type="text" name="hodDo" class="time" id="hodDo" maxlength="5" placeholder="00:00" value="<?= inputVal($nejDo ?? null, "cas") ?>" <?= isset($zaznam['povol_do']) ? 'disabled' : '' ?>></td>
                    </tr>
                    <tr>
                        <th>Název zařízení</th>
                        <td data-label="Název zařízení" colspan="2"><input type="text" name="NZarizeni" value="<?= $zaznam['nazev_zarizeni'] ?? null ?>"></td>
                        <th>Číslo zařízení</th>
                        <td data-label="Číslo zařízení" colspan="2"><input type="text" name="CZarizeni" value="<?= $zaznam['c_zarizeni'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <th>Popis, druh a rozsah práce</th>
                        <td data-label="Popis, druh a rozsah práce" colspan="5"><input type="text" name="prace" value="<?= $zaznam['popis_prace'] ?? null ?>"></td>
                    </tr>
                    <tr>
                        <th>Seznámení s riziky pracoviště dle karty č.</th>
                        <td data-label="Seznámení s riziky pracov. dle karty č." colspan="5"><input type="text" name="rizikaPrac" value="<?= $zaznam['c_karty'] ?? null ?>"></td>
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
                        <td colspan="6"><input type="text" name="vycisteni_kom" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.2 Vypařené
                                <input type="checkbox" name="vyparene" value="1" <?= inputVal($zaznam['vyparene'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="vyparene_hod" disabled></td>
                        <td colspan="4"><input type="text" name="vyparene_kom" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.3 Vypláchnuté vodou
                                    <input type="checkbox" name="vyplachnute" value="1" <?= inputVal($zaznam['vyplachnute'] ?? null, "check") ?>>
                                    <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="vyplachnute_kom" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.4 Plyn vytěsnen vodou
                                <input type="checkbox" name="plyn_vytesnen" value="1" <?= inputVal($zaznam['plyn_vytesnen'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="plyn_vytesnen_kom" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.5 Vyvětrané
                                <input type="checkbox" name="vyvetrane" value="1" <?= inputVal($zaznam['vyvetrane'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="vyvetrane_hod" disabled></td>
                        <td colspan="4"><input type="text" name="vyvetrane_kom" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.6 Profoukané dusíkem
                                <input type="checkbox" name="profouk_dusik" value="1" <?= inputVal($zaznam['profouk_dusik'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="profouk_dusik_hod" disabled></td>
                        <td colspan="4"><input type="text" name="profouk_dusik_kom" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.7 Profoukané vzduchem
                                <input type="checkbox" name="profouk_vzd" value="1" <?= inputVal($zaznam['profouk_vzd'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="profouk_vzd_hod" disabled></td>
                        <td colspan="4"><input type="text" name="profouk_vzd_kom" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.8 Odpojeno od elektrického proudu
                                <input type="checkbox" name="odpojeno_od_el" value="1" <?= inputVal($zaznam['odpojeno_od_el'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Kým</th>
                        <td data-label="Kým" colspan="3"><input type="text" name="odpojeno_od_el_kym" disabled></td>
                        <th>Podpis</th>
                        <td class="origo"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.9 Oddělené záslepkami
                                <input type="checkbox" name="oddelene_zaslep" value="1" <?= inputVal($zaznam['oddelene_zaslep'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Kým</th>
                        <td data-label="Kým" colspan="3"><input type="text" name="oddelene_zaslep_kym" disabled></td>
                        <th>Podpis</th>
                        <td class="origo"></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.10 Jinak zapezpečené
                                <input type="checkbox" name="jinak_zab" value="1" <?= inputVal($zaznam['jinak_zab'] ?? null, "check") ?>>
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Jak</th>
                        <td data-label="Jak" colspan="6"><input type="text" name="jinak_zab_jak" disabled></td>
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
                        <td data-label="Krát za"><input type="text" name="zkrapet_vetrat_pocet" disabled></td>
                        <th>Krát za</th>
                        <td data-label="Hodin"><input type="text" name="zkrapet_vetrat_hod" disabled></td>
                        <th>Hodin</th>
                        <th>V místě</th>
                        <td data-label="V místě"><input type="text" name="zkrapet_vetrat_misto" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.13 Provést rozbor ovzduší
                                <input type="checkbox" name="rozbor_ovzdusi" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Místo</th>
                        <td data-label="Místo"><input type="text" name="rozbor_ovzdusi_misto" disabled></td>
                        <th>Čas</th>
                        <td data-label="Čas"><input type="text" name="rozbor_ovzdusi_cas" disabled></td>
                        <th>Výsledek</th>
                        <td data-label="Výsledek"><input type="text" name="rozbor_ovzdusi_vysl" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.14 Zabezpečit dozor dalšími osobami
                                <input type="checkbox" name="zab_dozor" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Počet</th>
                        <td data-label="Počet"><input type="text" name="zab_dozor_pocet" disabled></td>
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
                        <td data-label="Počet"><input type="text" name="pozar_hlidka_pocet" disabled></td>
                        <th>Jméno</th>
                        <td data-label="Jméno" colspan="3"><input type="text" name="pozar_hlidka_jmeno" disabled></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.16 Hasící přístroj
                                <input type="checkbox" name="hasici_pristroj" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Počet</th>
                        <td data-label="Počet"><input type="text" name="hasici_pristroj_pocet" disabled></td>
                        <th>Druh</th>
                        <td data-label="Druh"><input type="text" name="hasici_pristroj_druh" disabled></td>
                        <th>Typ</th>
                        <td data-label="Typ"><input type="text" name="hasici_pristroj_typ" disabled></td>
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
                        <td data-label="2.1 Ochrana nohou - jaká" colspan="5"><input type="text" name="ochran_nohy"></td>
                    </tr>
                    <tr>
                        <th>2.2 Ochrana těla - jaká</th>
                        <td data-label="2.2 Ochrana těla - jaká" colspan="6"><input type="text" name="ochran_telo"></td>
                    </tr>
                    <tr>
                        <th>2.3 Ochrana hlavy - jaká</th>
                        <td data-label="2.3 Ochrana hlavy - jaká" colspan="6"><input type="text" name="ochran_hlava"></td>
                    </tr>
                    <tr>
                        <th>2.4 Ochrana oči - jaká - druh</th>
                        <td data-label="2.4 Ochrana oči - jaká - druh" colspan="6"><input type="text" name="ochran_oci"></td>
                    </tr>
                    <tr>
                        <th>2.5 Ochrana dýchadel - jaká</th>
                        <td data-label="2.5 Ochrana dýchadel - jaká" colspan="6"><input type="text" name="ochran_dychadel"></td>
                    </tr>
                    <tr>
                        <th>2.6 Ochranný pás - druh</th>
                        <td data-label="2.6 Ochranný pás - druh" colspan="6"><input type="text" name="ochran_pas"></td>
                    </tr>
                    <tr>
                        <th>2.7 Ochranné rukavice - druh</th>
                        <td data-label="2.7 Ochranné rukavice - druh" colspan="6"><input type="text" name="ochran_rukavice"></td>
                    </tr>
                    <tr>
                        <th>2.8 Dozor jmenovitě</th>
                        <td data-label="2.8 Dozor jmenovitě" colspan="5"><input type="text" name="ochran_dozor"></td>
                    </tr>
                    <tr>
                        <td class="podnadpis" colspan="6">Jiné příkazy</td>
                    </tr>
                    <tr>
                        <th>2.9 Jiné</th>
                        <td data-label="2.9 Jiné" colspan="5"><input type="text" name="jine_prikazy"></td>
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
                        <td data-label="Bez krytu" colspan="3"><input type="text" name="bez_krytu1"></td>
                    </tr>
                    <tr>
                        <th>Bez krytu</th>
                        <td data-label="Bez krytu" colspan="3"><input type="text" name="bez_krytu2"></td>
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
                        <td data-label="Za práci čety odpovídá" colspan="3"><input type="text" name="za_praci_odpovida"></td>
                    </tr>
                    <tr>
                        <th>Datum</th>
                        <td data-label="Datum"><input type="date" name="odpovednost_dat"></td>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="odpovednost_cas"></td>
                        <th>Hodin</th>
                    </tr>
                    <tr>
                        <th>Podpis vedoucího čety</th>
                        <td class="origo" colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="podnadpis">2.13 Sváření provedou</td>
                    </tr>
                    <tr>
                        <th>Jméno</th>
                        <th colspan="2">Č. svář. průkazu</th>
                        <th colspan="3">Podpis</th>
                    </tr>
                    <tr class="svareciTR" data-index="0">
                        <td data-label="Jméno"><input type="text" name="svarec[0][jmeno]" /></td>
                        <td data-label="Č. svář. průkazu" colspan="2"><input type="text" name="svarec[0][prukaz]" /></td>
                        <td class="origo" colspan="3"></td>
                    </tr>
                    <tr id="svarecAdd">
                        <td colspan="6"><button type="button" id="svarecAddBut" class="add">+</button></td>
                        <input type="hidden" name="svareciPocet" value="1">
                    </tr>
                    <tr>
                        <th colspan="3">2.14 Osvědčení o způsobilosti k práci a sváření na plynové zařízení má pracovník:</th>
                        <td data-label="2.14 Osvědčení o způsobilosti k práci a sváření na plyn. zař. má pracovník:" colspan="3"><input type="text" name="osvedceny_prac"></td>
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
                        <td data-label="Datum"><input type="date" name="prohl_prac_dat"></td>
                        <th>Datum</th>
                        <td data-label="Datum"><input type="date" name="prohl_exter_dat"></td>
                        <th>Vyjádření přilehlého obvodu</th>
                        <td data-label="Vyjádření přilehlého obvodu" colspan="2"><input type="text" name="prohl_obvod"></td>
                    </tr>
                    <tr>
                        <th rowspan="2" colspan="2">Podpis odpovědného pracovníka provozu: </th>
                        <th rowspan="2" colspan="2">Podpis odpovědného pracovníka provádějícího útvaru GB nebo externí firmy:</th>
                        <th rowspan="2">Podpis vedoucího přilehlého obvodu:</th>
                        <th>Datum</th>
                        <td data-label="Datum"><input type="date" name="prohl_vedouci_dat"></td>
                    </tr>
                    <tr>
                        <th colspan="5"></th>
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
                        <td colspan="5"><textarea name="podminky" rows="5" style="resize: none; width: 100%;"></textarea></td>
                    </tr>
                    <tr>
                        <th colspan="2">4.2 Výše uvedené podmínky stanovil - jméno:</th>
                        <td data-label="4.2 Výše uvedené podmínky stanovil - jméno:"><input type="text" name="podminky_jm"></td>
                        <th>Podpis</th>
                        <td class="origo"></td>
                    </tr>
                    <tr>
                        <th>4.3 Pracoviště připraveno pro práci s otevřeným ohněm:</th>
                        <th>Dne</th>
                        <td data-label="4.3 Pracoviště připraveno pro práci s otevřeným ohněm dne"><input type="date" name="ohen_dat"></td>
                        <th>Hodin</th>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="ohen_cas"></td>
                    </tr>
                    <tr>
                        <th colspan="2">4.4 Osobně zkontroloval - jméno</th>
                        <td data-label="4.4 Osobně zkontroloval - jméno"><input type="text" name="zkontroloval_jm"></td>
                        <th>Podpis</th>
                        <td class="origo"></td>
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
                        <th>Podpis</th>
                        <th></th>
                    </tr>
                </thead>
                <thead class="respons">
                    <th>5. Rozbor ovzduší</th>
                </thead>
                <tbody>
                    <tr class="rozboryTR" data-index="0">
                        <td data-label="Rozbor ovzduší"><input type="text" name="rozbor[0][nazev]"></td>
                        <td data-label="Datum"><input type="date" name="rozbor[0][dat]"></td>
                        <td data-label="Čas"><input type="text" class="time" maxlength="5" placeholder="00:00" name="rozbor[0][cas]"></td>
                        <td data-label="Místo odběru vzorku ovzduší"><input type="text" name="rozbor[0][misto]"></td>
                        <td data-label="Naměřená hodnota"><input type="text" name="rozbor[0][hodn]"></td>
                        <td></td>
                        <td class="origo"></td>
                    </tr>
                    <tr id="rozborAdd">
                        <td colspan="6"><button type="button" id="rozborAddBut" class="add">+</button></td>
                        <input type="hidden" name="rozboryPocet" value="1">
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
                        <td rowspan="4"><textarea name="dalsi_jine" rows="10" style="resize: none; width: 95%;"></textarea></td>
                        <th>Stanovil</th>
                        <td data-label="Stanovil" colspan="3"><input type="text" name="dalsi_jine_stanovil"></td>
                    </tr>
                    <tr>
                        <th>Jméno</th>
                        <td data-label="Jméno" colspan="3"><input type="text" name="dalsi_jine_jm"></td>
                    </tr>
                    <tr>
                        <th>Dne</th>
                        <td data-label="Dne"><input type="date" name="dalsi_jine_dat"></td>
                        <th>Hodin</th>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="dalsi_jine_cas"></td>
                    </tr>
                    <tr class="origo">
                        <th>Podpis</th>
                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>
            <table id="seventh">
                <thead>
                    <tr>
                        <th>7. Další nutná opatření - případně viz protokol ze dne</th>
                        <th class="origo"><input type="date" name="nutna_dat"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="respons"><input type="date" name="nutna_dat"></td>
                        <td colspan="2"><textarea name="nutna_opatreni" rows="4" style="resize: none; width: 100%;"></textarea></td>
                    </tr>
                </tbody>
            </table>
            <table id="eighth">
                <thead class="origo">
                    <tr>
                        <th colspan="3">8. Předání do opravy - protokol č.</th>
                        <th><input type="text" name="oprava_protokol"></th>
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
                        <td class="origo" colspan="3"></td>
                        <th>Podpis</th>
                        <td class="origo"></td>
                    </tr>
                </tbody>
                <tbody class="respons">
                    <tr>
                        <td><input type="text" name="oprava_protokol"></td>
                    </tr>
                    <tr>
                        <td data-label="Dne"><input type="date" name="oprava_dat"></td>
                        <td data-label="Hodina"><input type="text" class="time" maxlength="5" placeholder="00:00" name="oprava_cas"></td>
                    </tr>
                    <tr>
                        <td data-label="Předal" colspan="3"><input type="text" name="oprava_predal"></td>
                    </tr>
                    <tr>
                        <td data-label="Převzal" colspan="3"><input type="text" name="oprava_prevzal"></td>
                    </tr>
                </tbody>
            </table>
            <table id="ninth" class="respons">
                <thead>
                    <th>9. Předání z opravy</th>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Dne"><input type="date" name="oprava_dat"></td>
                        <td data-label="Hodina"><input type="text" class="time" maxlength="5" placeholder="00:00" name="z_opravy_cas"></td>
                    </tr>
                    <tr>
                        <td data-label="Předal"><input type="text" name="z_opravy_predal"></td>
                    </tr>
                    <tr>
                        <td data-label="Převzal"><input type="text" name="z_opravy_prevzal"></td>
                    </tr>
                </tbody>
            </table>
            <table id="tenth" class="respons">
                <thead>
                    <th>10. Práce svářečské ukončeny</th>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Dne"><input type="date" name="svarec_ukon_dat"></td>
                        <td data-label="Hodina"><input type="text" class="time" maxlength="5" placeholder="00:00" name="svarec_ukon_cas"></td>
                    </tr>
                    <tr>
                        <td data-label="Předal"><input type="text" name="svarec_predal"></td>
                    </tr>
                    <tr>
                        <td data-label="Převzal"><input type="text" name="svarec_prevzal"></td>
                    </tr>
                </tbody>
            </table>
            <table id="eleventh" class="respons">
                <thead>
                    <th>11. Následný dozor</th>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Od"><input type="text" class="time" maxlength="5" placeholder="00:00" name="dozor_od"></td>
                        <td data-label="Do"><input type="text" class="time" maxlength="5" placeholder="00:00" name="dozor_do"></td>
                    </tr>
                    <tr>
                        <td data-label="Jméno"><input type="text" name="dozor_jm"></td>
                    </tr>
                </tbody>
            </table>
            <table id="twelfth" class="respons">
                <thead>
                    <th>12. Kontrola BT, PO, jiného orgánu</th>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Dne"><input type="date" name="kontrola_dat"></td>
                        <td data-label="Hodin"><input type="text" class="time" maxlength="5" placeholder="00:00" name="kontrola_cas"></td>
                    </tr>
                    <tr>
                        <td data-label="Zjištěno"></td>
                    </tr>
                    <tr>
                        <td><textarea name="kontrola_zjisteno" rows="10" style="resize: none; width: 100%; padding: 5% 0;"></textarea></td>
                    </tr>
                    <tr>
                        <td data-label="Jméno"><input type="text" name="kontrola_jm"></td>
                    </tr>
                </tbody>
            </table>
            <table id="thirteenth">
                <?php 
                    $prodlZarDo = isset($zaznam['prodlZarDo']) && $zaznam['prodlZarDo']->format("Y-m-d") > date("Y-m-d") ? $zaznam['prodlZarDo']->format("Y-m-d") : date("Y-m-d");
                    $prodlOhDo = isset($zaznam['prodlOhDo']) && $zaznam['prodlOhDo']->format("Y-m-d") > date("Y-m-d") ? $zaznam['prodlOhDo']->format("Y-m-d") : date("Y-m-d"); 
                    $povolDo = isset($zaznam['povolDo']) && $zaznam['povolDo']->format("Y-m-d") > date("Y-m-d") ? $zaznam['povolDo']->format("Y-m-d") : date("Y-m-d");

                    if ($prodlZarDo >= $povolDo) 
                        $zarMin = $prodlZarDo;
                    else
                        $zarMin = $povolDo;

                    if ($prodlOhDo >= $povolDo) 
                        $ohMin = $prodlOhDo;
                    else
                        $ohMin = $povolDo;
                ?>
                <thead>
                    <tr>
                        <th colspan="6">13. Prodloužených za podmínek stanovených tímto povolením</th>
                    </tr>
                </thead>
                <tbody class="origo">
                    <tr>
                        <td class="podnadpis" colspan="6">Prodlužuje provozovatel</td>
                    </tr>
                    <tr>
                        <th>13.1 Pro práci na zařízení</th>
                        <td data-label="13.1 Pro práci na zařízení" colspan="5"><input type="text" name="prodluz_zarizeni" id="prodluzZar" <?= ($zaznam['zar'] == 0) ? 'disabled' : 'required'; ?>></td>
                    </tr>
                    <tr>
                        <th>Od</th>
                        <th>Do</th>
                        <th colspan="4"></th>
                    </tr>
                    <tr class="prodlZarTR">
                        <td data-label="Od" rowspan="2">
                            <input type="text" name="prodluzZarOd" id="prodluzZarOd" class="date" onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'" placeholder="Vyberte datum" min="<?= $zarMin; ?>" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzZarhodOd" class="time" id="prodluzZarhodOd" maxlength="5" placeholder="00:00" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>>
                        </td>
                        <td data-label="Do" rowspan="2">
                            <input type="text" name="prodluzZarDo" id="prodluzZarDo" class="date" onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'" placeholder="Vyberte datum" min="<?= date("Y-m-d") ?>" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzZarhodDo" class="time" id="prodluzZarhodDo" maxlength="5" placeholder="00:00" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>>
                        </td>
                        <th>Přestávka</th>
                        <td data-label="Přestávka"><input type="text" name="prodluz_zar_prestavka" id="prodluzZarPrestavka" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>></td>
                        <th>Podpis odpovědného prac. provozu</th>
                        <td width="15%"></td>
                    </tr>
                    <tr>
                        <th>Počet osob</th>
                        <td data-label="Počet osob"><input type="text" name="prodluz_zar_os" id="prodluzZarOs" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>></td>
                        <th>Podpis odpovědného prac. prov. útvaru</th>
                        <td></td>
                    </tr>
                    <tr>
                        <th>13.2 Pro práci s otevřeným ohněm</th>
                        <td data-label="13.2 Pro práci s otevřeným ohněm" colspan="5"><input type="text" name="prodluz_ohen" id="prodluz_ohen" <?= ($zaznam['oh'] == 0) ? 'disabled' : 'required'; ?>></td>
                    </tr>
                    <tr>
                        <th>Od</th>
                        <th colspan="3">Do</th>
                        <th colspan="2">Podpis Vystavovatele</th>
                    </tr>
                    <tr class="prodlOhTR">
                        <td data-label="Od" rowspan="2">
                            <input type="text" name="prodluzOhOd" id="prodluzOhOd" class="date" onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'" placeholder="Vyberte datum" min="<?= $ohMin; ?>" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzOhHodOd" class="time" id="hodOd" maxlength="5" placeholder="00:00" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?>>
                        </td>
                        <td data-label="Do" rowspan="2">
                            <input type="text" name="prodluzOhDo" id="prodluzOhDo" class="date" onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'" placeholder="Vyberte datum" min="<?= date("Y-m-d") ?>" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzOhHodDo" class="time" id="hodDo" maxlength="5" placeholder="00:00" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?>>
                        </td>
                        <th>Přestávka</th>
                        <td data-label="Přestávka"><input type="text" name="prodluz_oh_prestavka" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?>></td>
                        <td rowspan="2"></td>
                    </tr>
                    <tr>
                        <th>Počet osob</th>
                        <td data-label="Počet osob"><input type="text" name="prodluz_oh_os" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?>></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
                <tbody class="respons">
                    <tr>
                        <td class="podnadpis">Prodlužuje provozovatel</td>
                    </tr>
                    <tr class="prodlZarTR">
                        <td data-label="13.1 Pro práci na zařízení"><input type="text" name="prodluz_zarizeni" <?= ($zaznam['zar'] == 0) ? 'disabled' : 'required'; ?>></td>
                        <td data-label="Od" rowspan="2">
                            <input type="text" name="prodluzZarOd" id="prodluzZarOd" class="date" onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'" placeholder="Vyberte datum" min="<?= (isset($zaznam['povolDo']) && date("Y-m-d") < $zaznam['povolDo']->format("Y-m-d")) ? $zaznam['povolDo']->format("Y-m-d") : date("Y-m-d") ?>" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzZarhodOd" class="prodluzZarhodOd" id="hodOd" maxlength="5" placeholder="00:00" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>>
                        </td>
                        <td data-label="Do" rowspan="2">
                            <input type="text" name="prodluzZarDo" id="prodluzZarDo" class="date" onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'" placeholder="Vyberte datum" min="<?= date("Y-m-d") ?>" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzZarhodDo" class="time" id="prodluzZarhodDo" maxlength="5" placeholder="00:00" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>>
                        </td>
                        <td data-label="Přestávka"><input type="text" name="prodluz_zar_prestavka" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>></td>
                        <td data-label="Počet osob"><input type="text" name="prodluz_zar_os" <?= ($zaznam['zar'] == 0) ? 'disabled' : ''; ?>></td>
                    </tr>
                    <tr class="prodlOhTR">
                        <td data-label="13.2 Pro práci s otevřeným ohněm"><input type="text" name="prodluz_ohen" <?= ($zaznam['oh'] == 0) ? 'disabled' : 'required'; ?>></td>
                        <td data-label="Od" rowspan="2">
                            <input type="text" name="prodluzOhOd" id="prodluzOhOd" class="date" onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'" placeholder="Vyberte datum" min="<?= (isset($zaznam['povolDo']) && date("Y-m-d") < $zaznam['povolDo']->format("Y-m-d")) ? $zaznam['povolDo']->format("Y-m-d") : date("Y-m-d") ?>" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzOhhodOd" class="time" id="prodluzOhhodOd" maxlength="5" placeholder="00:00" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?>>
                        </td>
                        <td data-label="Do" rowspan="2">
                            <input type="text" name="prodluzOhDo" id="prodluzOhDo" class="date" onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'" placeholder="Vyberte datum" min="<?= date("Y-m-d") ?>" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?> style="margin-bottom: 10%;">
                            <input type="text" name="prodluzOhhodDo" class="time" id="prodluzOhhodDo" maxlength="5" placeholder="00:00" <?= ($zaznam['oh'] == 0) ? 'disabled' : ''; ?>>
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
                        <td rowspan="5"><textarea name="doplnky" rows="5" style="resize: none; width: 100%;"></textarea></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="submit-container">
            <input type="hidden" name="id_pov" value="<?= isset($_POST['id']) ? $_POST['id'] : ''?>">
            <input type="submit" class="add" id="odeslat" value="Odeslat" name="subOdeslat" style="font-size: 16px;">
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
            border: 1px solid #BCD4EF;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        thead th {
            background-color: #EAF3FF;
            text-align: center;
            font-weight: bold;
            padding: 10px;
            border-bottom: 2px solid #BCD4EF;
        }
        tbody th {
            background-color: #f7faff;
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
        .podnadpis{
            font-weight: bold;
            padding: 1% 0 1% 1%;
            background-color: #EEEEEE;
        }
        .riziko-container{
            display: flex;
            align-items: center;
            gap: 10px;
        }
        input[type="text"],
        input[type="date"],
        input[type="time"],
        input[type="range"] {
            width: 100%;
            padding: 8px;
            margin: 2px 0;
            box-sizing: border-box;
            border: 1px solid #BCD4EF;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
            display: block;
        }
        input[type="range"] {
            width: 85%; 
            height: 5px; 
            background-color: #EEEEEE; 
        }
        input[type="text"]:hover,
        input[type="date"]:hover,
        input[type="time"]:hover {
            border-color:rgb(140, 200, 250); 
            box-shadow: 0 2px 6px rgba(0, 51, 102, 0.2);
        }
        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="time"]:focus{
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
            margin: 20px 0 100px 0; 
        }

        .footer, .respons{
            display: none;
        }

        <?php if(isset($_POST['subProdl'])){ ?>
            table:not(#thirteenth){
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
            table{
                width: 90%;
                display: block;
                padding: 0;
            }
            tbody th, .logo, .origo {
                display: none;
            }
            table, thead, thead th, tbody, tr, td, .respons {
                display: block;
            }
            tbody{
                display: block;
                padding: 0;
                width: 90%;
                margin-left: 5%;
            }
            tr {
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
                width: 50%;
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