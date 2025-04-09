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
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sql = "";
        $params = [];

        if (empty($_POST['prodluzZarDo']) && empty($_POST['prodluzOhDo'])) {
            $id_pov = $_POST['id_pov'] ?? null;
            $svareciPocet = $_POST['svareciPocet'];
            $rozboryPocet = $_POST['rozboryPocet'];
            //INTRO 
            $ev_cislo = GenEvCislo($conn);
            $riziko = $_POST['riziko'];
            $interni = inputCheck($_POST['interni']);
            $externi = inputCheck($_POST['externi']);
            $pocetOs = $_POST['pocetOs'] === "" ? 1 : $_POST['pocetOs'];
            $hodOd = $_POST['hodOd'] ?? '';
            $hodDo = $_POST['hodDo'] ?? '';
            $povolOd = ($_POST['povolOd'] ?? '') . ' ' . $hodOd;
            $povolDo = ($_POST['povolDo'] ?? '') . ' ' . $hodDo;
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
            $ochran_nohy = inputCheck($_POST['ochran_nohy']);
            $ochran_telo = inputCheck($_POST['ochran_telo']);
            $ochran_hlava = inputCheck($_POST['ochran_hlava']);
            $ochran_oci = inputCheck($_POST['ochran_oci']);
            $ochran_dychadel = inputCheck($_POST['ochran_dychadel']);
            $ochran_pas = inputCheck($_POST['ochran_pas']);
            $ochran_rukavice = inputCheck($_POST['ochran_rukavice']);
            $ochran_dozor =inputCheck($_POST['ochran_dozor']);
            // TAB 2 9-14 Jiné příkazy
            $jine_prikazy = inputCheck($_POST['jine_prikazy']);
            $U_220 = $_POST['U_220'] ?? 0;
            $U_24 = $_POST['U_24'] ?? 0;
            $kryt = $_POST['kryt'] ?? 0;
            $bez_krytu = $_POST['bez_krytu'] ?? 0;
            $bez_krytu_kom = inputCheck($_POST['bez_krytu1']);
            $bez_krytu_kom2 = inputCheck($_POST['bez_krytu2']);
            $za_praci_odpovida = inputCheck($_POST['za_praci_odpovida']);
            $odpovednost_dat = $_POST['odpovednost_dat'] . ' ' . $_POST['odpovednost_cas'];
            $osvedceny_prac = inputCheck($_POST['osvedceny_prac']);
            // TAB 3
            $prohl_prac_dat = $_POST['prohl_prac_dat'];
            $prohl_exter_dat = $_POST['prohl_exter_dat'];
            $prohl_obvod = inputCheck($_POST['prohl_obvod']);
            $prohl_vedouci_dat = $_POST['prohl_vedouci_dat'];
            // TAB 4
            $podminky = inputCheck($_POST['podminky']);
            $podminky_jm = inputCheck($_POST['podminky_jm']);
            $ohen_dat = $_POST['ohen_dat'] . ' ' . $_POST['ohen_cas'];
            $zkontroloval_jm = inputCheck($_POST['zkontroloval_jm']);
            // TAB 6 (5 jsou rozbory)
            $dalsi_jine = inputCheck($_POST['dalsi_jine']);
            $dalsi_jine_stanovil = inputCheck($_POST['dalsi_jine_stanovil']);
            $dalsi_jine_jm = inputCheck($_POST['dalsi_jine_jm']);
            $dalsi_jine_dat = $_POST['dalsi_jine_dat'] . ' ' . $_POST['dalsi_jine_cas'];
            // TAB 7
            $nutna_dat = $_POST['nutna_dat'];
            $nutna_opatreni = inputCheck($_POST['nutna_opatreni']);
            // TAB 8
            $oprava_protokol = $_POST['oprava_protokol'];
            $oprava_dat = $_POST['oprava_dat'] . ' ' . $_POST['oprava_cas'];
            $oprava_predal = inputCheck($_POST['oprava_predal']);
            $oprava_prevzal =inputCheck($_POST['oprava_prevzal']);
            // TAB 9
            $z_opravy_dat = $_POST['z_opravy_dat'] . ' ' . $_POST['z_opravy_cas'];
            $z_opravy_predal = inputCheck($_POST['z_opravy_predal']);
            $z_opravy_prevzal = inputCheck($_POST['z_opravy_prevzal']);
            // TAB 10
            $svarec_ukon_dat = $_POST['svarec_ukon_dat'] . ' ' . $_POST['svarec_ukon_cas'];
            $svarec_ukon_predal = inputCheck($_POST['svarec_predal']);
            $svarec_ukon_prevzal = inputCheck($_POST['svarec_prevzal']);
            // TAB 11
            $dozor_od = $_POST['dozor_od'];
            $dozor_do = $_POST['dozor_do'];
            $dozor_jm = inputCheck($_POST['dozor_jm']);
            // TAB 12
            $kontrola_dat = $_POST['kontrola_dat'] . ' ' . $_POST['kontrola_cas'];
            $kontrola_zjisteno = inputCheck($_POST['kontrola_zjisteno']);
            $kontrola_jm = inputCheck($_POST['kontrola_jm']);
            // TAB 14
            $doplnky = inputCheck($_POST['doplnky']);
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
                if ($result === FALSE) {
                    echo json_encode([
                        "success" => false,
                        "message" => "Chyba SQL dotazu pro editaci!",
                        "error" => sqlsrv_errors()
                    ]);
                    exit;
                }        
                sqlsrv_free_stmt($result);  
            }//INSERT
            else{
                $sql = "INSERT INTO Povolenka (id_zam, ev_cislo, rizikovost, interni, externi, pocet_osob, povol_od, povol_do, prace_na_zarizeni, svarovani_ohen, vstup_zarizeni_teren, prostredi_vybuch, predani_prevzeti_zarizeni, provoz, objekt, c_zarizeni, nazev_zarizeni, popis_prace, c_karty,
                                            vycisteni, vycisteni_kom, vyparene, vyparene_hod, vyparene_kom, vyplachnute, vyplachnute_kom, plyn_vytesnen, plyn_vytesnen_kom, vyvetrane, vyvetrane_hod, vyvetrane_kom, profouk_dusik, profouk_dusik_hod, profouk_dusik_kom, profouk_vzd, profouk_vzd_hod, profouk_vzd_kom, odpojeno_od_el, odpojeno_od_el_kym, oddelene_zaslep, oddelene_zaslep_kym, jinak_zab, jinak_zab_jak,
                                            nejiskrive_naradi, nejiskrive_naradi_kom, zkrapet_vetrat, zkrapet_vetrat_pocet, zkrapet_vetrat_hod, zkrapet_vetrat_misto, rozbor_ovzdusi, rozbor_ovzdusi_misto, rozbor_ovzdusi_cas, rozbor_ovzdusi_vysl, zab_dozor, zab_dozor_pocet, pozar_hlidka, pozar_hlidka_pocet, pozar_hlidka_jmeno, hasici_pristroj, hasici_pristroj_pocet, hasici_pristroj_druh, hasici_pristroj_typ, jine_zab_pozar, jine_zab_pozar_kom,
                                            ochran_nohy, ochran_telo, ochran_hlava, ochran_oci, ochran_dychadel, ochran_pas, ochran_rukavice, dozor,
                                            jine_prikazy, U_220V, U_24V, kryt, bez_krytu, bez_krytu_kom, bez_krytu_kom2, odpovida, dat_odpovedny, osvedceni_ma,
                                            dat_odpov_provoz, dat_odpov_GB_exter, prohl_obvod, dat_vedouci,
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
                        $prohl_prac_dat, $prohl_exter_dat, $prohl_obvod, $prohl_vedouci_dat,
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
                if ($result === FALSE) {
                    echo json_encode([
                        "success" => false,
                        "message" => "Chyba SQL dotazu!",
                        "error" => sqlsrv_errors()
                    ]);
                    exit;
                }

                $sql = "SELECT @@identity AS id_pov";
                $result = sqlsrv_query($conn, $sql);
                if ($result === FALSE) {
                    echo json_encode([
                        "success" => false,
                        "message" => "Chyba SQL dotazu pro získání ID!",
                        "error" => sqlsrv_errors()
                    ]);
                    exit;
                }

                $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
                sqlsrv_free_stmt($result);
                $id_pov = $zaznam['id_pov'];


                //TAB 2
                if (!empty($_POST['svarec'][0]['prukaz'])) {
                    for ($i = 0; $i < $svareciPocet; $i++) {
                        $svarecJmeno = $_POST['svarec'][$i]['jmeno'];
                        $svarecPrukaz = $_POST['svarec'][$i]['prukaz'];

                        $sql = "SELECT * FROM Svareci AS s WHERE s.c_prukazu = ?;";
                        $params = [$svarecPrukaz];
                        $result = sqlsrv_query($conn, $sql, $params);
                        if ($result === FALSE) {
                            echo json_encode([
                                "success" => false,
                                "message" => "Chyba SQL dotazu!",
                                "error" => sqlsrv_errors()
                            ]);
                            exit;
                        }

                        if (sqlsrv_has_rows($result)) {
                            $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
                            sqlsrv_free_stmt($result);
                            $svarecID = $zaznam['id_svar'];
                        } else {
                            sqlsrv_free_stmt($result);
                            $sql = "INSERT INTO Svareci (jmeno, c_prukazu) VALUES (?, ?);";
                            $params = [$svarecJmeno, $svarecPrukaz];
                            $result = sqlsrv_query($conn, $sql, $params);
                            if ($result === FALSE) {
                                echo json_encode([
                                    "success" => false,
                                    "message" => "Chyba SQL dotazu!",
                                    "error" => sqlsrv_errors()
                                ]);
                                exit;
                            }

                            $sql = "SELECT @@identity AS id_svar";
                            $result = sqlsrv_query($conn, $sql);
                            if ($result === FALSE) {
                                echo json_encode([
                                    "success" => false,
                                    "message" => "Chyba SQL dotazu!",
                                    "error" => sqlsrv_errors()
                                ]);
                                exit;
                            }

                            $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
                            sqlsrv_free_stmt($result);
                            $svarecID = $zaznam['id_svar'];
                        }

                        $sql = "INSERT INTO Pov_Svar (id_pov, id_svar) VALUES (?, ?);";
                        $params = [$id_pov, $svarecID];
                        $result = sqlsrv_query($conn, $sql, $params);
                        if ($result === FALSE) {
                            echo json_encode([
                                "success" => false,
                                "message" => "Chyba SQL dotazu!",
                                "error" => sqlsrv_errors()
                            ]);
                            exit;
                        }
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
                        } else {
                            sqlsrv_free_stmt($result);
                            $sql = "INSERT INTO Rozbory (nazev, dat, cas, misto, hodn) VALUES (?, ?, ?, ?, ?);";
                            $params = [$rozborNazev, $rozborDat, $rozborCas, $rozborMisto, $rozborHodn];
                            $result = sqlsrv_query($conn, $sql, $params);
                            if ($result === FALSE) {
                                echo json_encode([
                                    "success" => false,
                                    "message" => "Chyba SQL dotazu!",
                                    "error" => sqlsrv_errors()
                                ]);
                                exit;
                            }

                            $sql = "SELECT @@identity AS id_roz";
                            $result = sqlsrv_query($conn, $sql);
                            if ($result === FALSE) {
                                echo json_encode([
                                    "success" => false,
                                    "message" => "Chyba SQL dotazu!",
                                    "error" => sqlsrv_errors()
                                ]);
                                exit;
                            }

                            $zaznam = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
                            sqlsrv_free_stmt($result);
                            $rozborID = $zaznam['id_roz'];
                        }
                        $sql = "INSERT INTO Pov_Roz (id_pov, id_roz) VALUES (?, ?);";
                        $params = [$id_pov, $rozborID];
                        $result = sqlsrv_query($conn, $sql, $params);
                        if ($result === FALSE) {
                            echo json_encode([
                                "success" => false,
                                "message" => "Chyba SQL dotazu!",
                                "error" => sqlsrv_errors()
                            ]);
                            exit;
                        }
                        sqlsrv_free_stmt($result);
                    }
                }
            }
        }
        //TAB 13
        else if(!empty($_POST['prodluzZarDo'])){
            $id_pov = $_POST['id_pov'];
            $typ = "zařízení";
            $pro_praci = $_POST['prodluz_zarizeni'];
            $od = $_POST['prodluzZarOd'] . ' ' . $_POST['prodluzZarhodOd'];
            $do = $_POST['prodluzZarDo'] . ' ' . $_POST['prodluzZarhodDo'];
            $prestavka = $_POST['prodluz_zar_prestavka'];
            $pocet_os = $_POST['prodluz_zar_os'];
            $dat_zadosti = DATE("Y-m-d");
    
            $sql = "INSERT INTO Prodlouzeni (id_pov, typ, pro_praci, od, do, prestavka, pocet_os, dat_zadosti)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
            $params = [$id_pov, $typ, $pro_praci, $od, $do, $prestavka, $pocet_os, $dat_zadosti];
            $result = sqlsrv_query($conn, $sql, $params);
            if ($result === FALSE){
                echo json_encode([
                    "success" => false,
                    "message" => "Chyba SQL dotazu!",
                    "error" => sqlsrv_errors()
                ]);     
                exit;
            }
        }
        else if(!empty($_POST['prodluzOhDo'])) {
            $id_pov = $_POST['id_pov'];
            $typ = "oheň";
            $pro_praci = $_POST['prodluz_ohen'];
            $od = $_POST['prodluzOhOd'] . ' ' . $_POST['prodluzOhHodOd'];
            $do = $_POST['prodluzOhDo'] . ' ' . $_POST['prodluzOhHodDo'];
            $prestavka = $_POST['prodluz_oh_prestavka'];
            $pocet_os = $_POST['prodluz_oh_os'];
            $dat_zadosti = DATE("Y-m-d");
    
            $sql = "INSERT INTO Prodlouzeni (id_pov, typ, pro_praci, od, do, prestavka, pocet_os, dat_zadosti)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
            $params = [$id_pov, $typ, $pro_praci, $od, $do, $prestavka, $pocet_os, $dat_zadosti];
            $result = sqlsrv_query($conn, $sql, $params);
            if ($result === FALSE){
                echo json_encode([
                    "success" => false,
                    "message" => "Chyba SQL dotazu!",
                    "error" => sqlsrv_errors()
                ]);     
                exit;
            }
        }

        echo json_encode([
            "success" => true,
            "data" => [
                "ev_cislo" => $ev_cislo,
                "id" => $id_pov
            ]
        ]);
    }
?>