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
                        <td colspan="5"><input type="text" name="" id=""></td>
                    </tr>
                    <tr>
                        <td class="podnadpis" colspan="6">Jiné příkazy</td>
                    </tr>
                    <tr>
                        <th>2.9 Jiné</th>
                        <td colspan="5"><input type="text" name="" id=""></td>
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
                        <td colspan="3"><input type="text" name="" id=""></td>
                    </tr>
                    <tr>
                        <th>Bez krytu</th>
                        <td colspan="3"><input type="text" name="" id=""></td>
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
                        <td colspan="3"><input type="text" name="" id=""></td>
                    </tr>
                    <tr>
                        <th>Datum</th>
                        <td><input type="date" name="" id=""></td>
                        <td><input type="time" name="" id=""></td>
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
                    <tr>
                        <td><input type="text" name="svarec_jmeno"></td>
                        <td colspan="2"><input type="text" name="svarec_prukaz"></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr id="buttSvareci">
                        <td colspan="6"><button type="button" id="svarec_pridat">+</button></td>
                    </tr>
                    <tr>
                        <th colspan="4">2.14 Osvědčení o způsobilosti k práci a sváření na plynové zařízení má pracovník:</th>
                        <td colspan="2"><input type="text" name="" id=""></td>
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
                        <td><input type="date" name="" id=""></td>
                        <th>Datum</th>
                        <td><input type="date" name="" id=""></td>
                        <th>Vyjádření přilehlého obvodu </th>
                        <td colspan="2"><input type="text" name="" id=""></td>
                    </tr>
                    <tr>
                        <th rowspan="2" colspan="2">Podpis odpovědného pracovníka provozu: </th>
                        <th rowspan="2" colspan="2">Podpis odpovědného pracovníka provádějícího útvaru GB nebo externí firmy:</th>
                        <th rowspan="2">Podpis vedoucího přilehlého obvodu:</th>
                        <th>Datum</th>
                        <td><input type="date" name="" id=""></td>
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
                        <td colspan="5"><textarea name="" id="" rows="5" style="resize: none; width: 100%;"></textarea></td>
                    </tr>
                    <tr>
                        <th>4.2 Výše uvedené podmínky stanovil - jméno:</th>
                        <td colspan="2"><input type="text" name="" id=""></td>
                        <th>Podpis</th>
                        <td></td>
                    </tr>
                    <tr>
                        <th>4.3 Pracoviště připraveno pro práci s otevřeným ohněm:</th>
                        <th>Dne</th>
                        <td><input type="date" name="" id=""></td>
                        <th>Hodin</th>
                        <td><input type="time" name="" id=""></td>
                    </tr>
                    <tr>
                        <th>4.4 Osobně zkontroloval - jméno</th>
                        <td colspan="2"><input type="text" name="" id=""></td>
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
                        <th colspan="2">Podpis</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="rozbor_nazev"></td>
                        <td><input type="date" name="rozbor_dat"></td>
                        <td><input type="time" name="rozbor_cas"></td>
                        <td><input type="text" name="rozbor_misto"></td>
                        <td><input type="text" name="rozbor_hodn"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr id="buttRozbory">
                        <td colspan="6"><button type="button" id="rozbor_pridat">+</button></td>
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
                        <td rowspan="4"><textarea name="" id="" rows="10" style="resize: none; width: 95%;"></textarea></td>
                        <th>Stanovil</th>
                        <td colspan="3"><input type="text" name="" id=""></td>
                    </tr>
                    <tr>
                        <th>Jméno</th>
                        <td colspan="3"><input type="text" name="" id=""></td>
                    </tr>
                    <tr>
                        <th>Dne</th>
                        <td><input type="date" name="" id=""></td>
                        <th>Hodin</th>
                        <td><input type="time" name="" id=""></td>
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
                        <th><input type="date" name="" id=""></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2"><textarea name="" id="" rows="4" style="resize: none; width: 100%;"></textarea></td>
                    </tr>
                </tbody>
            </table>
            <table class="eighth">
                <thead>
                    <tr>
                        <th colspan="3">8. Předání do opravy - protokol č.</th>
                        <th><input type="text" name="" id=""></th>
                        <th colspan="4">10. Práce svářečské ukončeny</th>
                        <th colspan="4">12. Kontrola BT, PO, jiného orgánu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Dne</th>
                        <td><input type="date" name="" id=""></td>
                        <th>Hodina</th>
                        <td><input type="time" name="" id=""></td>
                        <th>Dne</th>
                        <td><input type="date" name="" id=""></td>
                        <th>Hodina</th>
                        <td><input type="time" name="" id=""></td>
                        <th>Kontrola dne</th>
                        <td><input type="date" name="" id=""></td>
                        <th>Hodina</th>
                        <td><input type="time" name="" id=""></td>
                    </tr>
                    <tr>
                        <th>Předal</th>
                        <td colspan="3"><input type="text" name="" id=""></td>
                        <th>Předal</th>
                        <td colspan="3"><input type="text" name="" id=""></td>
                        <th rowspan="4">Zjištěno</th>
                        <td colspan="3" rowspan="4"><textarea name="" id="" rows="10" style="resize: none; width: 100%; padding: 5% 0;"></textarea></td>
                    </tr>
                    <tr>
                        <th>Převzal</th>
                        <td colspan="3"><input type="text" name="" id=""></td>
                        <th>Převzal</th>
                        <td colspan="3"><input type="text" name="" id=""></td>
                    </tr>
                    <tr>
                        <th colspan="4">9. Převzání z opravy</th>
                        <th colspan="4">11. Následný dozor</th>
                    </tr>
                    <tr>
                        <th>Dne</th>
                        <td><input type="date" name="" id=""></td>
                        <th>Hodina</th>
                        <td><input type="time" name="" id=""></td>
                        <th>Od</th>
                        <td><input type="time" name="" id=""></td>
                        <th>Do</th>
                        <td><input type="time" name="" id=""></td>
                    </tr>
                    <tr>
                        <th>Předal</th>
                        <td colspan="3"><input type="text" name="" id=""></td>
                        <th>Jméno</th>
                        <td colspan="3"><input type="text" name="" id=""></td>
                        <th>Jméno</th>
                        <td colspan="3"><input type="text" name="" id=""></td>
                    </tr>
                    <tr>
                        <th>Předal</th>
                        <td colspan="3"><input type="text" name="" id=""></td>
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
                        <th colspan="6">13. Prodloužených za podmínek stanovených tímto povolením</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th class="podnadpis" colspan="6">Prodlužuje provozovatel</th>
                    </tr>
                    <tr>
                        <th>13.1 Pro práci na zařízení</th>
                        <td colspan="5"><input type="text" name="" id=""></td>
                    </tr>
                    <tr>
                        <th>Datum</th>
                        <th>Od - Do</th>
                        <th>Přestávka</th>
                        <th>Počet osob</th>
                        <th>Podpis odpovědného prac. provozu</th>
                        <th>Podpis odpovědného prac. prov. útvaru</th>
                    </tr>
                    <tr>
                        <td><input type="date" name="" id=""></td>
                        <td><input type="text" name="" id=""></td>
                        <td><input type="text" name="" id=""></td>
                        <td><input type="text" name="" id=""></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>13.2 Pro práci s otevřeným ohněm</th>
                        <td colspan="5"><input type="text" name="" id=""></td>
                    </tr>
                    <tr>
                        <th>Datum</th>
                        <th>Od - Do</th>
                        <th>Přestávka</th>
                        <th>Počet osob</th>
                        <th colspan="2">Podpis Vystavovatele</th>
                    </tr>
                    <tr>
                        <td><input type="date" name="" id=""></td>
                        <td><input type="text" name="" id=""></td>
                        <td><input type="text" name="" id=""></td>
                        <td><input type="text" name="" id=""></td>
                        <td colspan="2"></td>
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
                        <td rowspan="5"><textarea name="" id="" rows="5" style="resize: none; width: 100%;"></textarea></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="submit-container">
            <input type="submit" value="Odeslat" name="subOdeslat">
        </div>
    </form>
    <style>
        table{
            background-color: #ffffff;
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
            color: #333;
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
            background-color: #eee;
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
        input[type="time"]:focus,
        input[type="range"]:focus {
            border-color: #2196F3;
            box-shadow: 0 0 4px rgba(33, 150, 243, 0.5);
        }
        button{

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
            color: #333;
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
            background-color: #eee;
            border-radius: 4px;
            border: 1px solid #bcd4ef;
        }
        .container:hover input ~ .checkbox {
            background-color: #ccc;
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
            margin: 10px 50px;
            text-align: right;
        }
        .submit-container input {
            background-color: #003366;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .submit-container input:hover {
            background-color: #d40000;
        }
        </style>
</body>
</html>