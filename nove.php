<?php
    session_start();

    if (isset($_SESSION['uziv']))
        $uziv = $_SESSION['uziv'];
    else{
        header("Location: login.html");
        exit();    
    }
    require_once 'server.php';

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
        <h1>NOVÉ POVOLENÍ</h1>
    </div><br>
    <form action="" method="post">
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
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">1.7 Profoukané vzduchem
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">1.8 Odpojeno od elektrického proudu
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">1.9 Oddělené záslepkami
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">1.10 Jinak zapezpečené
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </div>
                    </td>
                    <td colspan="2"></td>
                    <td colspan="6"><input type="text" name="" id=""></td>
                </tr>
                <tr>
                    <td>hodin:</td>
                    <td><input type="time" name="" id=""></td>
                    <td colspan="6"><input type="text" name="" id=""></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="6"><input type="text" name="" id=""></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="6"><input type="text" name="" id=""></td>
                </tr>
                <tr>
                    <td>hodin:</td>
                    <td><input type="time" name="" id=""></td>
                    <td colspan="6"><input type="text" name="" id=""></td>
                </tr>
                <tr>
                    <td>hodin:</td>
                    <td><input type="time" name="" id=""></td>
                    <td colspan="6"><input type="text" name="" id=""></td>
                </tr>
                <tr>
                    <td>hodin:</td>
                    <td><input type="time" name="" id=""></td>
                    <td colspan="6"><input type="text" name="" id=""></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Kým</td>
                    <td colspan="3"><input type="text" name="" id=""></td>
                    <td>Podpis</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Kým</td>
                    <td colspan="3"><input type="text" name="" id=""></td>
                    <td>Podpis</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Jak</td>
                    <td colspan="5"><input type="text" name="" id=""></td>
                </tr>
                <tr>
                    <td class="podnadpis"colspan="9">Podmínky BP a PO</td>
                </tr>
                <tr>
                    <td rowspan="7">
                        <div class="panel">
                            <label class="container">1.11 Použít nejiskřivého nářadí
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">1.12 Po dobu oprav - zkrápět, větrat
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">1.13 Provést rozbor ovzduší
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">1.14 Zabezpečit dozor dalšími osobami
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">1.15 Požární hlídka provozu
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">1.16 Hasící přístroj
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">1.17 Jiné zabezpečení požární ochrany
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </div>
                    </td>
                    <td colspan="2"></td>
                    <td colspan="6"><input type="text" name="" id=""></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td style="display: flex; align-items: center;"><input type="text" name="" id="">Krát za</td>
                    <td><input type="text" name="" id=""></td>
                    <td>Hodin</td>
                    <td>V místě</td>
                    <td colspan="2"><input type="text" name="" id=""></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Místo</td>
                    <td><input type="text" name="" id=""></td>
                    <td>Čas</td>
                    <td><input type="text" name="" id=""></td>
                    <td>Výsledek</td>
                    <td><input type="text" name="" id=""></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Počet</td>
                    <td><input type="text" name="" id=""></td>
                    <td colspan="4">Jména uvést v bodě 7</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Počet</td>
                    <td><input type="text" name="" id=""></td>
                    <td>Jméno</td>
                    <td colspan="3"><input type="text" name="" id=""></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Počet</td>
                    <td><input type="text" name="" id=""></td>
                    <td>Druh</td>
                    <td><input type="text" name="" id=""></td>
                    <td>Typ</td>
                    <td><input type="text" name="" id=""></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="6"><input type="text" name="" id=""></td>
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
                    <td rowspan="7">
                        <div class="panel">
                            <label class="container">2.1 Ochrana nohou - jaká
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">2.2 Ochrana těla - jaká
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">2.3 Ochrana hlavy - jaká
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">2.4 Ochrana oči - jaká - druh
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">2.5 Ochrana dýchadel - jaká
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">2.6 Ochranný pás - druh
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                            <label class="container">2.7 Ochranné rukavice - druh
                                <input type="checkbox" name="vyvetrane" value="1">
                                <span class="checkbox"></span>
                            </label>
                        </div>
                    </td>
                    <td colspan="5">
                        <input type="text" name="" id="">
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <input type="text" name="" id="">
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <input type="text" name="" id="">
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <input type="text" name="" id="">
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <input type="text" name="" id="">
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <input type="text" name="" id="">
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <input type="text" name="" id="">
                    </td>
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
                    <th colspan="6">3. Prohlašuji, že jsem se přesvědčil, že výše uvededené zajištění je provedeno.</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Datum</td>
                    <td><input type="date" name="" id=""></td>
                    <td>Datum</td>
                    <td><input type="date" name="" id=""></td>
                    <td>Vyjádření přilehlého obvodu:</td>
                    <td><input type="text" name="" id=""></td>
                </tr>
                <tr>
                    <td colspan="2">Podpis odpovědného pracovníka provozu:</td>
                    <td colspan="2">Podpis odpovědného pracovníka provádějícího útvaru GB nebo externí firmy:</td>
                    <td>Podpis vedoucího přilehlého obvodu</td>
                    <td>Datum</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2"></td>
                    <td></td>
                    <td><input type="date" name="" id=""></td>
                </tr>
            </tbody>
        </table>
        <div class="submit-container">
            <input type="submit" value="Odeslat" name="subOdeslat">
        </div>
    </form><br><br><br><br><br>
    <div class="footer">
        <p style="margin-left: 1%;">Přihlášený uživatel: <?php echo $uziv ?> </p>
        <img src="Indorama.png" style="margin-right: 5.7%;">
        <a href="login.php">
            <img src="logout_icon.png" width="78%" style="cursor: pointer;">
        </a>
    </div>
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