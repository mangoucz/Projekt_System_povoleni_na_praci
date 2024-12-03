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
                        <th colspan="3">1. Příprava zařízení k opravě</th>
                        <th colspan="6">Bližší určení</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="podnadpis" colspan="9">Zařízení bylo</td>
                    </tr>
                    <tr>
                        <td rowspan="10">
                            <div class="panel">
                                <label class="container">1.1 Vyčištění od zbytků
                                    <input type="checkbox" name="vycisteni" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">1.2 Vypařené
                                    <input type="checkbox" name="vyparene" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">1.3 Vypláchnuté vodou
                                    <input type="checkbox" name="vyplachnute" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">1.4 Plyn vytěsnen vodou
                                    <input type="checkbox" name="plyn_vytesnen" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">1.5 Vyvětrané
                                    <input type="checkbox" name="vyvetrane" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">1.6 Profoukané dusíkem
                                    <input type="checkbox" name="profouk_dusik" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">1.7 Profoukané vzduchem
                                    <input type="checkbox" name="profouk_vzd" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">1.8 Odpojeno od elektrického proudu
                                    <input type="checkbox" name="odpojeno_od_el" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">1.9 Oddělené záslepkami
                                    <input type="checkbox" name="oddelene_zaslep" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">1.10 Jinak zapezpečené
                                    <input type="checkbox" name="jinak_zab" value="1">
                                    <span class="checkbox"></span>
                                </label>
                            </div>
                        </td>
                        <td colspan="2"></td>
                        <td colspan="6"><input type="text" name="vycisteni_kom" id=""></td>
                    </tr>
                    <tr>
                        <td>hodin:</td>
                        <td><input type="time" name="vyparene_hod" id=""></td>
                        <td colspan="6"><input type="text" name="vyparene_kom" id=""></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="6"><input type="text" name="vyplachnute_kom" id=""></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="6"><input type="text" name="plyn_vytesnen_kom" id=""></td>
                    </tr>
                    <tr>
                        <td>hodin:</td>
                        <td><input type="time" name="vyvetrane_hod" id=""></td>
                        <td colspan="6"><input type="text" name="vyvetrane_kom" id=""></td>
                    </tr>
                    <tr>
                        <td>hodin:</td>
                        <td><input type="time" name="profouk_dusik_hod" id=""></td>
                        <td colspan="6"><input type="text" name="profouk_dusik_kom" id=""></td>
                    </tr>
                    <tr>
                        <td>hodin:</td>
                        <td><input type="time" name="profouk_vzd_hod" id=""></td>
                        <td colspan="6"><input type="text" name="profouk_vzd_kom" id=""></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td>Kým</td>
                        <td colspan="3"><input type="text" name="odpojeno_od_el_kym" id=""></td>
                        <td>Podpis</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td>Kým</td>
                        <td colspan="3"><input type="text" name="oddelene_zaslep_kym" id=""></td>
                        <td>Podpis</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td>Jak</td>
                        <td colspan="5"><input type="text" name="jinak_zab_jak" id=""></td>
                    </tr>
                    <tr>
                        <td class="podnadpis"colspan="9">Podmínky BP a PO</td>
                    </tr>
                    <tr>
                        <td rowspan="7">
                            <div class="panel">
                                <label class="container">1.11 Použít nejiskřivého nářadí
                                    <input type="checkbox" name="nejiskrive_naradi" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">1.12 Po dobu oprav - zkrápět, větrat
                                    <input type="checkbox" name="zkrapet_vetrat" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">1.13 Provést rozbor ovzduší
                                    <input type="checkbox" name="rozbor_ovzdusi" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">1.14 Zabezpečit dozor dalšími osobami
                                    <input type="checkbox" name="zab_dozor" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">1.15 Požární hlídka provozu
                                    <input type="checkbox" name="pozar_hlidka" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">1.16 Hasící přístroj
                                    <input type="checkbox" name="hasici_pristroj" value="1">
                                    <span class="checkbox"></span>
                                </label>
                                <label class="container">1.17 Jiné zabezpečení požární ochrany
                                    <input type="checkbox" name="jine_zab_pozar" value="1">
                                    <span class="checkbox"></span>
                                </label>
                            </div>
                        </td>
                        <td colspan="2"></td>
                        <td colspan="6"><input type="text" name="nejiskrive_naradi_kom" id=""></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td style="display: flex; align-items: center;"><input type="text" name="zkrapet_vetrat_pocet" id="">Krát za</td>
                        <td><input type="text" name="zkrapet_vetrat_hod" id=""></td>
                        <td>Hodin</td>
                        <td>V místě</td>
                        <td colspan="2"><input type="text" name="zkrapet_vetrat_misto" id=""></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td>Místo</td>
                        <td><input type="text" name="rozbor_ovzdusi_misto" id=""></td>
                        <td>Čas</td>
                        <td><input type="text" name="rozbor_ovzdusi_cas" id=""></td>
                        <td>Výsledek</td>
                        <td><input type="text" name="rozbor_ovzdusi_vysl" id=""></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td>Počet</td>
                        <td><input type="text" name="zab_dozor_pocet" id=""></td>
                        <td colspan="4">Jména uvést v bodě 7</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td>Počet</td>
                        <td><input type="text" name="pozar_hlidka_pocet" id=""></td>
                        <td>Jméno</td>
                        <td colspan="3"><input type="text" name="pozar_hlidka_jmeno" id=""></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td>Počet</td>
                        <td><input type="text" name="hasici_pristroj_pocet" id=""></td>
                        <td>Druh</td>
                        <td><input type="text" name="hasici_pristroj_druh" id=""></td>
                        <td>Typ</td>
                        <td><input type="text" name="hasici_pristroj_typ" id=""></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
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
                        <td>2.1 Ochrana nohou - jaká</td>
                        <td colspan="5"><input type="text" name="ochran_nohy" id=""></td>
                    </tr>
                    <tr>
                        <td>2.2 Ochrana těla - jaká</td>
                        <td colspan="6"><input type="text" name="ochran_telo" id=""></td>
                    </tr>
                    <tr>
                        <td>2.3 Ochrana hlavy - jaká</td>
                        <td colspan="6"><input type="text" name="ochran_hlava" id=""></td>
                    </tr>
                    <tr>
                        <td>2.4 Ochrana oči - jaká - druh</td>
                        <td colspan="6"><input type="text" name="ochran_oci" id=""></td>
                    </tr>
                    <tr>
                        <td>2.5 Ochrana dýchadel - jaká</td>
                        <td colspan="6"><input type="text" name="ochran_dychadel" id=""></td>
                    </tr>
                    <tr>
                        <td>2.6 Ochranný pás - druh</td>
                        <td colspan="6"><input type="text" name="ochran_pas" id=""></td>
                    </tr>
                    <tr>
                        <td>2.7 Ochranné rukavice - druh</td>
                        <td colspan="6"><input type="text" name="ochran_rukavice" id=""></td>
                    </tr>
                    <tr>
                        <td style="font-size: 22px;">Dozor jmenovitě</td>
                        <td colspan="5">
                            <input type="text" name="" id="">
                        </td>
                    </tr>
                    <tr>
                        <td class="podnadpis" colspan="6">Jiné příkazy</td>
                    </tr>
                    <tr>
                        <td style="font-size: 22px;">2.9 Jiné</td>
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
                        <td style="font-size: 22px;">Bez krytu</td>
                        <td colspan="3"><input type="text" name="" id=""></td>
                    </tr>
                    <tr>
                        <td style="font-size: 22px;">Bez krytu</td>
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
                        <td>Za práci čety odpovídá:</td>
                        <td colspan="3"><input type="text" name="" id=""></td>
                    </tr>
                    <tr>
                        <td>Datum:</td>
                        <td><input type="date" name="" id=""></td>
                        <td><input type="time" name="" id=""></td>
                        <td>hodin</td>
                    </tr>
                    <tr>
                        <td colspan="2">Podpis vedoucího čety:</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="6">2.13 Sváření provedou:</td>
                    </tr>
                    <tr>
                        <td colspan="2">Jméno</td>
                        <td colspan="2">Č. svář. průkazu</td>
                        <td colspan="2">Podpis</td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="text" name="" id=""></td>
                        <td colspan="2"><input type="text" name="" id=""></td>
                        <td colspan="2"><input type="text" name="" id=""></td>
                    </tr>
                    <tr>
                        <td colspan="4">2.14 Osvědčení o způsobilosti k práci a sváření na plynové zařízení má pracovník:</td>
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
                        <td>Datum</td>
                        <td><input type="date" name="" id=""></td>
                        <td>Datum</td>
                        <td><input type="date" name="" id=""></td>
                        <td>Vyjádření přilehlého obvodu:</td>
                        <td colspan="2"><input type="text" name="" id=""></td>
                    </tr>
                    <tr>
                        <td colspan="2">Podpis odpovědného pracovníka provozu:</td>
                        <td colspan="2">Podpis odpovědného pracovníka provádějícího útvaru GB nebo externí firmy:</td>
                        <td>Podpis vedoucího přilehlého obvodu</td>
                        <td>Datum</td>
                        <td><input type="date" name="" id=""></td>
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
                </thead>
                <tbody>
                    <tr>
                        <td class="podnadpis" colspan="5">Vystavovatel</td>
                    </tr>
                    <tr>
                        <td colspan="5">4.1 Podmínky (doplňující bod č. 1)</td>
                    </tr>
                    <tr>
                        <td colspan="5"><textarea name="" id="" rows="5" style="resize: none; width: 100%;"></textarea></td>
                    </tr>
                    <tr>
                        <td>4.2 Výše uvedené podmínky stanovil - jméno:</td>
                        <td colspan="2"><input type="text" name="" id=""></td>
                        <td>Podpis</td>
                        <td><input type="text" name="" id=""></td>
                    </tr>
                    <tr>
                        <td>4.3 Pracoviště připraveno pro práci s otevřeným ohněm:</td>
                        <td>Dne</td>
                        <td><input type="date" name="" id=""></td>
                        <td>Hodin</td>
                        <td><input type="time" name="" id=""></td>
                    </tr>
                    <tr>
                        <td>4.4 Osobně zkontroloval - jméno</td>
                        <td colspan="2"><input type="text" name="" id=""></td>
                        <td>Podpis</td>
                        <td><input type="text" name="" id=""></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="submit-container">
            <input type="submit" value="Odeslat" name="subOdeslat">
        </div>
    </form>
    <style>
        body {
            align-items: center;
        }
        
        table{
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #bcd4ef;
            margin: 0 auto;
            width: 95%;
        }
        .header{
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-direction: row;
            flex-wrap: wrap;
        }    
        .headerB{
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .uziv{
            display: inline-flex;
            align-items: center;
            padding: 1% 2% 1% 0;
            cursor: pointer;    
        }
        .uziv_inf p{
            white-space: nowrap;
            text-align: left;
            margin: 0;
            padding: 1% 0;
        }
        .logo{
            margin-left: 1%; 
            max-width: 10%;
        }
        td{
            text-align: left;
        }
        th{
            text-align: center;
            padding: 0.5% 0;
        }
        .podnadpis{
            font-weight: bold;
            padding: 1% 0 1% 1% ;
            background-color: #eee;
        }
        td input{
            width: 90%;
            padding: 5px;
            border: 1px solid #cccccc;
            border-radius: 3px;
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

        .panel {
            padding: 0 18px;
            margin: 10px 0;
        }
        .container {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 22px;
        }
        .container input {
            display: none;
        }
        .checkbox {
            position: absolute;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
        }
        .container:hover input ~ .checkbox {
            background-color: #ccc;
        }
        .container input:checked ~ .checkbox {
            background-color: #2196F3;
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
            left: 9px;
            top: 5px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }
    </style>
</body>
</html>