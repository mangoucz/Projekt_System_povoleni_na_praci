<?php
    session_start();

    if (isset($_SESSION['uziv']))
        $uziv = $_SESSION['uziv'];
    else{
        header("Location: login.html");
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
        $sql = "";

        if (isset($_POST['subOdeslat'])) {
            $svareciPocet = $_POST['svareciPocet'];
            
            $sql = "INSERT INTO Svareci (jmeno, c_prukazu) VALUES (?, ?)";
            $stmt = sqlsrv_prepare($conn, $sql);
            if ($stmt === FALSE) 
                die(print_r(sqlsrv_errors(), true));

            for ($i = 0; $i < $svareciPocet; $i++) { 
                $svarecJmeno = $_POST['svarec[' . $i . ']jmeno'];
                $svarecPrukaz = $_POST['svarec[' . $i . ']prukaz'];
                
                $params = [$svarecJmeno, $svarecPrukaz];
                $result = sqlsrv_execute($stmt, $params);
                if ($result === FALSE)
                    die(print_r(sqlsrv_errors(), true));
            }
            sqlsrv_free_stmt($stmt);


            echo '<script>
                        alert("Žádost byla uspěšně odeslána");
                        window.location.href = "' . $_SERVER['PHP_SELF'] . '";
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
                    <p style="font-size: 12px; margin-left: 2px;"><?php echo $funkce; ?></p>
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
                        <td><input type="range" name="riziko"></td>
                        <td><input type="text" name="interni"></td>
                        <td><input type="text" name="externi"></td>
                        <td><input type="text" name="pocetOs"></td>
                        <td><input type="date" name="povolOd"></td>
                        <td><input type="date" name="povolDo"></td>
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
                        <td><input type="time" name="hodOd"></td>
                        <td><input type="time" name="hodDo"></td>
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
                                <input type="checkbox" name="vycisteni" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="vycisteni_kom" id=""></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.2 Vypařené
                                <input type="checkbox" name="vyparene" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td><input type="time" name="vyparene_hod" id=""></td>
                        <td colspan="4"><input type="text" name="vyparene_kom" id=""></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.3 Vypláchnuté vodou
                                    <input type="checkbox" name="vyplachnute" value="1">
                                    <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="vyplachnute_kom" id=""></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.4 Plyn vytěsnen vodou
                                <input type="checkbox" name="plyn_vytesnen" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="plyn_vytesnen_kom" id=""></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.5 Vyvětrané
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td><input type="time" name="vyvetrane_hod" id=""></td>
                        <td colspan="4"><input type="text" name="vyvetrane_kom" id=""></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.6 Profoukané dusíkem
                                <input type="checkbox" name="profouk_dusik" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td><input type="time" name="profouk_dusik_hod" id=""></td>
                        <td colspan="4"><input type="text" name="profouk_dusik_kom" id=""></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.7 Profoukané vzduchem
                                <input type="checkbox" name="profouk_vzd" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Hodin</th>
                        <td><input type="time" name="profouk_vzd_hod" id=""></td>
                        <td colspan="4"><input type="text" name="profouk_vzd_kom" id=""></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.8 Odpojeno od elektrického proudu
                                <input type="checkbox" name="odpojeno_od_el" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Kým</th>
                        <td colspan="3"><input type="text" name="odpojeno_od_el_kym" id=""></td>
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
                        <td colspan="3"><input type="text" name="oddelene_zaslep_kym" id=""></td>
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
                        <td colspan="6"><input type="text" name="jinak_zab_jak" id=""></td>
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
                        <td colspan="6"><input type="text" name="nejiskrive_naradi_kom" id=""></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.12 Po dobu oprav - zkrápět, větrat
                                <input type="checkbox" name="zkrapet_vetrat" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <td><input type="text" name="zkrapet_vetrat_pocet" id=""></td>
                        <th>Krát za</th>
                        <td><input type="text" name="zkrapet_vetrat_hod" id=""></td>
                        <th>Hodin</th>
                        <th>V místě</th>
                        <td><input type="text" name="zkrapet_vetrat_misto" id=""></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.13 Provést rozbor ovzduší
                                <input type="checkbox" name="rozbor_ovzdusi" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Místo</th>
                        <td><input type="text" name="rozbor_ovzdusi_misto" id=""></td>
                        <th>Čas</th>
                        <td><input type="text" name="rozbor_ovzdusi_cas" id=""></td>
                        <th>Výsledek</th>
                        <td><input type="text" name="rozbor_ovzdusi_vysl" id=""></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.14 Zabezpečit dozor dalšími osobami
                                <input type="checkbox" name="zab_dozor" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Počet</th>
                        <td><input type="text" name="zab_dozor_pocet" id=""></td>
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
                        <td><input type="text" name="pozar_hlidka_pocet" id=""></td>
                        <th>Jméno</th>
                        <td colspan="3"><input type="text" name="pozar_hlidka_jmeno" id=""></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.16 Hasící přístroj
                                <input type="checkbox" name="hasici_pristroj" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <th>Počet</th>
                        <td><input type="text" name="hasici_pristroj_pocet" id=""></td>
                        <th>Druh</th>
                        <td><input type="text" name="hasici_pristroj_druh" id=""></td>
                        <th>Typ</th>
                        <td><input type="text" name="hasici_pristroj_typ" id=""></td>
                    </tr>
                    <tr>
                        <td>
                            <label class="container">1.17 Jiné zabezpečení požární ochrany
                                <input type="checkbox" name="jine_zab_pozar" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </td>
                        <td colspan="6"><input type="text" name="jine_zab_pozar_kom" id=""></td>
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
                        <td colspan="5"><input type="text" name="ochran_nohy" id=""></td>
                    </tr>
                    <tr>
                        <th>2.2 Ochrana těla - jaká</th>
                        <td colspan="6"><input type="text" name="ochran_telo" id=""></td>
                    </tr>
                    <tr>
                        <th>2.3 Ochrana hlavy - jaká</th>
                        <td colspan="6"><input type="text" name="ochran_hlava" id=""></td>
                    </tr>
                    <tr>
                        <th>2.4 Ochrana oči - jaká - druh</th>
                        <td colspan="6"><input type="text" name="ochran_oci" id=""></td>
                    </tr>
                    <tr>
                        <th>2.5 Ochrana dýchadel - jaká</th>
                        <td colspan="6"><input type="text" name="ochran_dychadel" id=""></td>
                    </tr>
                    <tr>
                        <th>2.6 Ochranný pás - druh</th>
                        <td colspan="6"><input type="text" name="ochran_pas" id=""></td>
                    </tr>
                    <tr>
                        <th>2.7 Ochranné rukavice - druh</th>
                        <td colspan="6"><input type="text" name="ochran_rukavice" id=""></td>
                    </tr>
                    <tr>
                        <th>2.8 Dozor jmenovitě</th>
                        <td colspan="5"><input type="text" name="ochran_dozor" id=""></td>
                    </tr>
                    <tr>
                        <td class="podnadpis" colspan="6">Jiné příkazy</td>
                    </tr>
                    <tr>
                        <th>2.9 Jiné</th>
                        <td colspan="5"><input type="text" name="jine_prikazy" id=""></td>
                    </tr>
                    <tr>
                        <td rowspan="2">
                            <div class="panel">
                                <label class="container">2.10 Napětí 220 V
                                    <input type="checkbox" name="vyvetrane" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">2.11 Napětí 24 V
                                    <input type="checkbox" name="vyvetrane" value="1">
                                    <span class="checkbox"></span>
                                </label>
                            </div>
                        </td>
                        <td rowspan="2">
                            <div class="panel">
                                <label class="container">S krytem
                                    <input type="checkbox" name="vyvetrane" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">Bez krytu
                                    <input type="checkbox" name="vyvetrane" value="1">
                                    <span class="checkbox"></span>
                                </label>
                            </div>
                        </td>
                        <th>Bez krytu</th>
                        <td colspan="3"><input type="text" name="bez_krytu1" id=""></td>
                    </tr>
                    <tr>
                        <th>Bez krytu</th>
                        <td colspan="3"><input type="text" name="bez_krytu2" id=""></td>
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
                        <td colspan="3"><input type="text" name="za_praci_odpovida" id=""></td>
                    </tr>
                    <tr>
                        <th>Datum</th>
                        <td><input type="date" name="odpovednost_dat" id=""></td>
                        <td><input type="time" name="odpovednost_cas" id=""></td>
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
                        <td><input type="text" name="svarec[0]jmeno"></td>
                        <td colspan="2"><input type="text" name="svarec[0]prukaz"></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr id="svarecAdd">
                        <td colspan="6"><button type="button" id="svarecAdd" class="add">+</button></td>
                        <input type="hidden" name="svareciPocet" value="1">
                    </tr>
                    <tr>
                        <th colspan="3">2.14 Osvědčení o způsobilosti k práci a sváření na plynové zařízení má pracovník:</th>
                        <td colspan="3"><input type="text" name="osvedceny_prac" id=""></td>
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
                        <td><input type="date" name="prohlaseni_dat" id=""></td>
                        <th>Datum</th>
                        <td><input type="date" name="prohlaseni_cas" id=""></td>
                        <th>Vyjádření přilehlého obvodu </th>
                        <td colspan="2"><input type="text" name="prohlaseni_obvodu" id=""></td>
                    </tr>
                    <tr>
                        <th rowspan="2" colspan="2">Podpis odpovědného pracovníka provozu: </th>
                        <th rowspan="2" colspan="2">Podpis odpovědného pracovníka provádějícího útvaru GB nebo externí firmy:</th>
                        <th rowspan="2">Podpis vedoucího přilehlého obvodu:</th>
                        <th>Datum</th>
                        <td><input type="date" name="prohl_popis_dat" id=""></td>
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
                        <td colspan="5"><textarea name="podminky" id="" rows="5" style="resize: none; width: 100%;"></textarea></td>
                    </tr>
                    <tr>
                        <th>4.2 Výše uvedené podmínky stanovil - jméno:</th>
                        <td colspan="2"><input type="text" name="podminky_jm" id=""></td>
                        <th>Podpis</th>
                        <td></td>
                    </tr>
                    <tr>
                        <th>4.3 Pracoviště připraveno pro práci s otevřeným ohněm:</th>
                        <th>Dne</th>
                        <td><input type="date" name="ohen_dat" id=""></td>
                        <th>Hodin</th>
                        <td><input type="time" name="ohen_cas" id=""></td>
                    </tr>
                    <tr>
                        <th>4.4 Osobně zkontroloval - jméno</th>
                        <td colspan="2"><input type="text" name="zkontroloval_jm" id=""></td>
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
                        <td><input type="time" name="rozbor[0][cas]"></td>
                        <td><input type="text" name="rozbor[0][misto]"></td>
                        <td><input type="text" name="rozbor[0][hodn]"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr id="rozborAdd">
                        <td colspan="6"><button type="button" id="rozborAdd" class="add">+</button></td>
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
                        <td rowspan="4"><textarea name="dalsi_jine" id="" rows="10" style="resize: none; width: 95%;"></textarea></td>
                        <th>Stanovil</th>
                        <td colspan="3"><input type="text" name="dalsi_jine_stanovil" id=""></td>
                    </tr>
                    <tr>
                        <th>Jméno</th>
                        <td colspan="3"><input type="text" name="dalsi_jine_jm" id=""></td>
                    </tr>
                    <tr>
                        <th>Dne</th>
                        <td><input type="date" name="dalsi_jine_dat" id=""></td>
                        <th>Hodin</th>
                        <td><input type="time" name="dalsi_jine_cas" id=""></td>
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
                        <th><input type="date" name="nutna_dat" id=""></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2"><textarea name="nutna_opatreni" id="" rows="4" style="resize: none; width: 100%;"></textarea></td>
                    </tr>
                </tbody>
            </table>
            <table class="eighth">
                <thead>
                    <tr>
                        <th colspan="3">8. Předání do opravy - protokol č.</th>
                        <th><input type="text" name="oprava_protokol" id=""></th>
                        <th colspan="4">10. Práce svářečské ukončeny</th>
                        <th colspan="4">12. Kontrola BT, PO, jiného orgánu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Dne</th>
                        <td><input type="date" name="oprava_dat" id=""></td>
                        <th>Hodina</th>
                        <td><input type="time" name="oprava_cas" id=""></td>
                        <th>Dne</th>
                        <td><input type="date" name="svarec_ukon_dat" id=""></td>
                        <th>Hodina</th>
                        <td><input type="time" name="scarec_ukon_cas" id=""></td>
                        <th>Kontrola dne</th>
                        <td><input type="date" name="kontrola_dat" id=""></td>
                        <th>Hodina</th>
                        <td><input type="time" name="kontrola_cas" id=""></td>
                    </tr>
                    <tr>
                        <th>Předal</th>
                        <td colspan="3"><input type="text" name="oprava_predal" id=""></td>
                        <th>Předal</th>
                        <td colspan="3"><input type="text" name="svarec_predal" id=""></td>
                        <th rowspan="4">Zjištěno</th>
                        <td colspan="3" rowspan="4"><textarea name="kontrola_zjisteno" id="" rows="10" style="resize: none; width: 100%; padding: 5% 0;"></textarea></td>
                    </tr>
                    <tr>
                        <th>Převzal</th>
                        <td colspan="3"><input type="text" name="oprava_prevzal" id=""></td>
                        <th>Převzal</th>
                        <td colspan="3"><input type="text" name="svarec_prevzal" id=""></td>
                    </tr>
                    <tr>
                        <th colspan="4">9. Převzání z opravy</th>
                        <th colspan="4">11. Následný dozor</th>
                    </tr>
                    <tr>
                        <th>Dne</th>
                        <td><input type="date" name="z_opravy_dat" id=""></td>
                        <th>Hodina</th>
                        <td><input type="time" name="z_opravy_cas" id=""></td>
                        <th>Od</th>
                        <td><input type="time" name="dozor_od" id=""></td>
                        <th>Do</th>
                        <td><input type="time" name="dozor_do" id=""></td>
                    </tr>
                    <tr>
                        <th>Předal</th>
                        <td colspan="3"><input type="text" name="z_opravy_predal" id=""></td>
                        <th>Jméno</th>
                        <td colspan="3"><input type="text" name="dozor_jm" id=""></td>
                        <th>Jméno</th>
                        <td colspan="3"><input type="text" name="kontrola_jm" id=""></td>
                    </tr>
                    <tr>
                        <th>Převzal</th>
                        <td colspan="3"><input type="text" name="z_opravy_prevzal" id=""></td>
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
                        <td colspan="6"><input type="text" name="prodluz_zarizeni" id=""></td>
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
                        <td><input type="date" name="prodluz_zar_dat" id=""></td>
                        <td><input type="text" name="prodluz_zar_oddo" id=""></td>
                        <td><input type="text" name="prodluz_zar_prestavka" id=""></td>
                        <td><input type="text" name="prodluz_zar_os" id=""></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <th>13.2 Pro práci s otevřeným ohněm</th>
                        <td colspan="6"><input type="text" name="prodluz_ohen" id=""></td>
                    </tr>
                    <tr>
                        <th>Datum</th>
                        <th>Od - Do</th>
                        <th>Přestávka</th>
                        <th>Počet osob</th>
                        <th colspan="3">Podpis Vystavovatele</th>
                    </tr>
                    <tr>
                        <td><input type="date" name="prodluz_oh_dat" id=""></td>
                        <td><input type="text" name="prodluz_oh_oddo" id=""></td>
                        <td><input type="text" name="prodluz_oh_prestavka" id=""></td>
                        <td><input type="text" name="prodluz_oh_os" id=""></td>
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
                        <td rowspan="5"><textarea name="doplnky" id="" rows="5" style="resize: none; width: 100%;"></textarea></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="submit-container">
            <input type="submit" class="add" value="Odeslat" name="subOdeslat" style="font-size: 16px;">
        </div>
    </form>
    <style>
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
            font-size: 16px;
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
        }
        .container input:checked ~ .checkbox:after {
            display: block;
        }
        .container .checkbox:after {
            left: 6px;
            top: 3px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }

        .submit-container {
            display: flex;
            justify-content: center; 
            margin: 20px 0; 
        }
        </style>
</body>
</html>