<?php
    function inputVal($el, $typ) : string {
        if (!isset($el)) {
            return "";
        } 
        
        if ($typ === "dat") {
            return $el->format("d. m.") ?? "";
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sql = [];
        $params = [];

        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $rozbory = [];
            $svareci = [];
            
            $sql[0] = "SELECT * FROM Povolenka as p WHERE p.id_pov = ?;";
            $sql[1] = "SELECT * FROM (Povolenka as p left JOIN Pov_Svar as ps ON p.id_pov = ps.id_pov) LEFT JOIN Svareci AS s ON s.id_svar = ps.id_svar WHERE p.id_pov = ?;"; 
            $sql[2] = "SELECT * FROM (Povolenka as p left JOIN Pov_Roz as pr ON p.id_pov = pr.id_pov) left JOIN Rozbory as r ON pr.id_roz = r.id_roz WHERE p.id_pov = ?;";           
            $params = [$id];

            for ($i = 0; $i <= 2; $i++) { 
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
                    else {
                        $rozbory[] = $row;
                    }
                }
                sqlsrv_free_stmt($result);
            }
        }
        else {
            header("Location: uvod.php");
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tisk</title>
</head>
<script>
    window.onload = function() {
        window.print();
    };
</script>
<style>
    body {
        font-size: 9pt;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        border: 2px solid black;
        font-size: inherit;
    }
    td {
        border: 1px solid black;
        line-height: 1;
    }
    img {
        width: 180px;
    }
    input {
        width: 100%;
        border: none;
        text-align: center;
        font-size: inherit;
        padding: 0;
        margin: 0;
    }
    input[type="checkbox"] {
        appearance: none;
        width: 12px;
        height: 12px;
        border: 1px solid black;
        position: relative;
        margin: 0;
        cursor: pointer;
    }

    input[type="checkbox"]:checked:after {
        content: 'X';
        position: absolute;
        color: black;
        font-size: 14px;
        line-height: 10px;
        left: 0.5px;
    }

    .svisly-text {
        writing-mode: vertical-rl;
        text-orientation: mixed;
        transform: rotate(180deg);
        white-space: normal;
        text-align: center;
        vertical-align: middle;
        width: 20px;
    }
    
    @page {
        size: A4;
        margin: 1cm 0.5cm;
    }
    @media print{
        body{
            margin: 1cm 0.5cm;
        }
        table {
            page-break-after: always;
        }
    }
</style>
<body>
    <table id="firstPage">
        <tbody>
            <tr>
                <td rowspan="3" colspan="6" style="text-align: center; vertical-align: middle;"><img src="Indorama.png" alt=""></td>
                <td rowspan="2" colspan="5" style="text-align: center;"><b style="font-size: 20pt;">POVOLENÍ k práci</b></td>
    
                <td colspan="2"><b>Evidenční číslo:</b></td>
                <td colspan="2"><input type="text" name="ev_cislo" value="<?= $zaznam['ev_cislo'] ?>"></td>
            </tr>
            <tr>
                <td colspan="2">Rizikovost:</td>
                <td colspan="2"><input type="text" name="rizikovost" value="<?= $zaznam['rizikovost'] ?>"></td>
            </tr>
            <tr>
                <td style="text-align: right;"><input type="checkbox" name="" <?= inputVal($zaznam['prace_na_zarizeni'], 'check') ?>></td>
                <td colspan="8">k práci na zařízení</td>
            </tr>
            <tr>
                <td rowspan="2" colspan="6">Interní: <input type="text" name="" value="<?= $zaznam['interni'] ?>"></td>
                <td style="text-align: right;"><input type="checkbox" name="" <?= inputVal($zaznam['svarovani_ohen'], 'check') ?>></td>
                <td colspan="8">ke svařování a práci s otevřeným ohněm</td>
            </tr>
            <tr>
                <td style="text-align: right;"><input type="checkbox" name="" <?= inputVal($zaznam['vstup_zarizeni_teren'], 'check') ?>></td>
                <td colspan="8">ke vstupu do zařízení nebo pod úroveň terénu</td>
            </tr>
            <tr>
                <td rowspan="2" colspan="5">Externí: <input type="text" name="" value="<?= $zaznam['externi'] ?>"></td>
                <td rowspan="2" style="width: 1.5cm;">Počet osob <input type="text" name="" value="<?= $zaznam['pocet_osob'] ?>"></td>
                <td style="text-align: right;"><input type="checkbox" name="" <?= inputVal($zaznam['prostredi_vybuch'], 'check') ?>></td>
                <td colspan="8">k práci v prostředí s nebezpečím výbuchu</td>
            </tr>
            <tr>
                <td style="text-align: right;"><input type="checkbox" name="" <?= inputVal($zaznam['predani_prevzeti_zarizeni'], 'check') ?>></td>
                <td colspan="8">k předání a převzetí zařízení do opravy a do provozu</td>
            </tr>
            <tr>
                <td colspan="3">na dny</td>
                <td colspan="4"><input type="text" name="" value="<?= '' . inputVal($zaznam['povol_od'], 'dat') . ' - ' . inputVal($zaznam['povol_do'], 'dat')  ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['povol_do']->format("Y") ?>"></td>
                <td colspan="2" style="text-align: right;">od</td>
                <td><input type="text" name="" value="<?= inputVal($zaznam['povol_od'], 'cas') ?>"></td>
                <td style="text-align: right;">do</td>
                <td colspan="2"><input type="text" name="" value="<?= inputVal($zaznam['povol_do'], 'cas') ?>"></td>
                <td>hodin</td>
            </tr>
            <tr>
                <td colspan="3">provoz:</td>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['provoz'] ?>"></td>
                <td colspan="2">název (číslo) objektu:</td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['objekt'] ?>"></td>
                <td colspan="2">číslo zařízení:</td>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['c_zarizeni'] ?>"></td>
            </tr>
            <tr>
                <td colspan="5">název zařízení</td>
                <td colspan="10"><input type="text" name="" value="<?= $zaznam['nazev_zarizeni'] ?>"></td>
            </tr>
            <tr>
                <td colspan="5">popis, druh a rozsah práce</td>
                <td colspan="10"><input type="text" name="" value="<?= $zaznam['popis_prace'] ?>"></td>
            </tr>
            <tr>
                <td colspan="6">seznámení s riziky pracoviště dle karty č.:</td>
                <td colspan="9"><input type="text" name="" value="<?= $zaznam['c_karty'] ?>"></td>
            </tr>
            <tr>
                <td colspan="9"><b>1. Příprava zařízení k opravě</b></td>
                <td colspan="6" style="text-align: center;"><b>Bližší určení</b></td>
            </tr>
            <tr>
                <td rowspan="17" class="svisly-text">Zajištění provozovatelem</td>
                <td rowspan="10" class="svisly-text">Zařízení bylo</td>
                <td>1.1</td>
                <td style="width: 0.5cm; text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['vycisteni'], 'check') ?>></td>
                <td colspan="5">Vyčištění od zbytků</td>
                <td colspan="6"><input type="text" name="" value="<?= $zaznam['vycisteni_kom'] ?>"></td>
            </tr>
            <tr>
                <td>1.2</td>
                <td style="text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['vyparene'], 'check') ?>></td>
                <td colspan="3">Vypařené</td>
                <td style="text-align: right;">hodin:</td>
                <td><input type="text" name="" value="<?= $zaznam['vyparene_hod'] ?>"></td>
                <td colspan="6"><input type="text" name="" value="<?= $zaznam['vyparene_kom'] ?>"></td>
            </tr>
            <tr>
                <td>1.3</td>
                <td style="text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['vyplachnute'], 'check') ?>></td>
                <td colspan="5">Vypláchnuté vodou</td>
                <td colspan="6"><input type="text" name="" value="<?= $zaznam['vyplachnute_kom'] ?>"></td>
            </tr>
            <tr>
                <td>1.4</td>
                <td style="text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['plyn_vytesnen'], 'check') ?>></td>
                <td colspan="5">Plyn vytěsnen vodou</td>
                <td colspan="6"><input type="text" name="" value="<?= $zaznam['plyn_vytesnen_kom'] ?>"></td>
            </tr>
            <tr>
                <td>1.5</td>
                <td style="text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['vyvetrane'], 'check') ?>></td>
                <td colspan="3">Vyvětrané</td>
                <td style="text-align: right;">hodin:</td>
                <td><input type="text" name="" value="<?= $zaznam['vyvetrane_hod'] ?>"></td>
                <td colspan="6"><input type="text" name="" value="<?= $zaznam['vyvetrane_kom'] ?>"></td>
            </tr>
            <tr>
                <td>1.6</td>
                <td style="text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['profouk_dusik'], 'check') ?>></td>
                <td colspan="3">Profoukané dusíkem</td>
                <td style="text-align: right;">hodin:</td>
                <td><input type="text" name="" value="<?= $zaznam['profouk_dusik_hod'] ?>"></td>
                <td colspan="6"><input type="text" name="" value="<?= $zaznam['profouk_dusik_kom'] ?>"></td>
            </tr>
            <tr>
                <td>1.7</td>
                <td style="text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['profouk_vzd'], 'check') ?>></td>
                <td colspan="3">Profoukané vzduchem</td>
                <td style="text-align: right;">hodin:</td>
                <td><input type="text" name="" value="<?= $zaznam['profouk_vzd_hod'] ?>"></td>
                <td colspan="6"><input type="text" name="" value="<?= $zaznam['profouk_vzd_kom'] ?>"></td>
            </tr>
            <tr>
                <td>1.8</td>
                <td style="text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['odpojeno_od_el'], 'check') ?>></td>
                <td colspan="5">Odpojeno od elektrického proudu</td>
                <td>kým</td>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['odpojeno_od_el_kym'] ?>"></td>
                <td>podpis</td>
                <td></td>
            </tr>
            <tr>
                <td>1.9</td>
                <td style="text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['oddelene_zaslep'], 'check') ?>></td>
                <td colspan="5">Oddělené záslepkami</td>
                <td>kým</td>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['oddelene_zaslep_kym'] ?>"></td>
                <td>podpis</td>
                <td></td>
            </tr>
            <tr>
                <td>1.10</td>
                <td style="text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['jinak_zab'], 'check') ?>></td>
                <td colspan="5">Jinak zapezpečené</td>
                <td>jak</td>
                <td colspan="5"><input type="text" name="" value="<?= $zaznam['jinak_zab_jak'] ?>"></td>
            </tr>
            <tr>
                <td rowspan="7" class="svisly-text">Podmínky BP a PO</td>
                <td>1.11</td>
                <td style="text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['nejiskrive_naradi'], 'check') ?>></td>
                <td colspan="5">Použít nejiskřivého nářadí</td>
                <td></td>
                <td colspan="5"><input type="text" name="" value="<?= $zaznam['nejiskrive_naradi_kom'] ?>"></td>
            </tr>
            <tr>
                <td>1.12</td>
                <td style="text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['zkrapet_vetrat'], 'check') ?>></td>
                <td colspan="5">Po dobu oprav - zkrápět, větrat</td>
                <td><input type="text" name="" value="<?= $zaznam['zkrapet_vetrat_pocet'] ?>"></td>
                <td style="display: flex;">krát za <input type="text" name="" value="<?= $zaznam['zkrapet_vetrat_hod'] ?>" style="width: 0.5cm;"></td>
                <td>hodin</td>
                <td>v místě:</td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['zkrapet_vetrat_misto'] ?>"></td>
            </tr>
            <tr>
                <td>1.13</td>
                <td style="text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['rozbor_ovzdusi'], 'check') ?>></td>
                <td colspan="5">Provést rozbor ovzduší</td>
                <td>místo</td>
                <td><input type="text" name="" value="<?= $zaznam['rozbor_ovzdusi_misto'] ?>"></td>
                <td>čas</td>
                <td><input type="text" name="" value="<?= $zaznam['rozbor_ovzdusi_cas'] ?>"></td>
                <td>Výsledek</td>
                <td><input type="text" name="" value="<?= $zaznam['rozbor_ovzdusi_vysl'] ?>"></td>
            </tr>
            <tr>
                <td>1.14</td>
                <td style="text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['zab_dozor'], 'check') ?>></td>
                <td colspan="5">Zabezpečit dozor dalšími osobami</td>
                <td>počet</td>
                <td><input type="text" name="" value="<?= $zaznam['zab_dozor_pocet'] ?>"></td>
                <td colspan="4">jména uvést v bodě 7</td>
            </tr>
            <tr>
                <td>1.15</td>
                <td style="text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['pozar_hlidka'], 'check') ?>></td>
                <td colspan="5">Požární hlídka provozu</td>
                <td>počet</td>
                <td><input type="text" name="" value="<?= $zaznam['pozar_hlidka_pocet'] ?>"></td>
                <td>jméno</td>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['pozar_hlidka_jmeno'] ?>"></td>
            </tr>
            <tr>
                <td>1.16</td>
                <td style="text-align: center;"><input type="checkbox" name="" <?= inputVal($zaznam['hasici_pristroj'], 'check') ?>></td>
                <td colspan="5">Hasící přístroj</td>
                <td>počet</td>
                <td><input type="text" name="" value="<?= $zaznam['hasici_pristroj_pocet'] ?>"></td>
                <td>druh</td>
                <td><input type="text" name="" value="<?= $zaznam['hasici_pristroj_druh'] ?>"></td>
                <td>typ</td>
                <td><input type="text" name="" value="<?= $zaznam['hasici_pristroj_typ'] ?>"></td>
            </tr>
            <tr>
                <td style="text-align: center;">1.17</td>
                <td><input type="checkbox" name="" <?= inputVal($zaznam['jine_zab_pozar'], 'check') ?>></td>
                <td colspan="5">Jiné zabezpečení požární ochrany</td>
                <td colspan="6"><input type="text" name="" value="<?= $zaznam['jine_zab_pozar'] ?>"></td>
            </tr>
            <tr>
                <td colspan="15"><b>2. Vlastní zabezpečení prováděné práce</b></td>
            </tr>
            <tr>
                <td rowspan="19"></td>
                <td rowspan="7" class="svisly-text">Osobní ochranné <br> pracovní prostředky</td>
                <td>2.1</td>
                <td colspan="3">Ochrana nohou - jaká</td>
                <td colspan="9"><input type="text" name="" value="<?= $zaznam['ochran_nohy'] ?>"></td>
            </tr>
            <tr>
                <td>2.2</td>
                <td colspan="3">Ochrana těla - jaká</td>
                <td colspan="9"><input type="text" name="" value="<?= $zaznam['ochran_telo'] ?>"></td>
            </tr>
            <tr>
                <td>2.3</td>
                <td colspan="3">Ochrana hlavy - jaká</td>
                <td colspan="9"><input type="text" name="" value="<?= $zaznam['ochran_hlava'] ?>"></td>
            </tr>
            <tr>
                <td>2.4</td>
                <td colspan="3">Ochrana očí - jaká - druh</td>
                <td colspan="9"><input type="text" name="" value="<?= $zaznam['ochran_oci'] ?>"></td>
            </tr>
            <tr>
                <td>2.5</td>
                <td colspan="3">Ochrana dýchadel - jaká</td>
                <td colspan="9"><input type="text" name="" value="<?= $zaznam['ochran_dychadel'] ?>"></td>
            </tr>
            <tr>
                <td>2.6</td>
                <td colspan="3">Ochranný pás - druh</td>
                <td colspan="9"><input type="text" name="" value="<?= $zaznam['ochran_pas'] ?>"></td>
            </tr>
            <tr>
                <td>2.7</td>
                <td colspan="3">Ochranné rukavice - druh</td>
                <td colspan="9"><input type="text" name="" value="<?= $zaznam['ochran_rukavice'] ?>"></td>
            </tr>
            <tr>
                <td>2.8</td>
                <td colspan="3">Dozor jmenovitě</td>
                <td colspan="10"><input type="text" name="" value="<?= $zaznam['dozor'] ?>"></td>
            </tr>
            <tr>
                <td rowspan="3" class="svisly-text">Jiné <br> příkazy</td>
                <td>2.9</td>
                <td colspan="2"><b>Jiné</b></td>
                <td colspan="10"><input type="text" name="" value="<?= $zaznam['jine_prikazy'] ?>"></td>
            </tr>
            <tr>
                <td>2.10</td>
                <td colspan="3">napětí 220 V</td>
                <td><input type="checkbox" name="" <?= inputVal($zaznam['U_220V'], 'check') ?>></td>
                <td>s krytem</td>
                <td colspan="2"><input type="checkbox" name="" <?= inputVal($zaznam['kryt'], 'check') ?>></td>
                <td></td>
                <td>bez krytu</td>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['bez_krytu_kom'] ?>"></td>
            </tr>
            <tr>
                <td>2.11</td>
                <td colspan="3">napětí 24 V</td>
                <td><input type="checkbox" name="" <?= inputVal($zaznam['U_24V'], 'check') ?>></td>
                <td>bez krytu</td>
                <td colspan="2"><input type="checkbox" name="" <?= inputVal($zaznam['bez_krytu'], 'check') ?>></td>
                <td></td>
                <td>bez krytu</td>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['bez_krytu_kom2'] ?>"></td>
            </tr>
            <tr>
                <td rowspan="3" colspan="7" style="font-size: 8pt;">
                    2.12 Prohlášení: Prohlašuji, že zajistím dodržení výše uvedených <br>
                    podmínek, jakož i bezpečný provoz a postup práce, mně podřízených <br>
                    pracovníků. Při jakékoliv změně podmínek práci přeruším a požádám <br>
                    o okamžité prověření prostředí. Po skončení práce s otevřeným ohněm <br>
                    zabezpečím pracoviště ve smyslu ČSN 05 06 10.
                </td>
                <td>Za práci čety odpovídá:</td>
                <td colspan="6"><input type="text" name="" value="<?= $zaznam['odpovida'] ?>"></td>
            </tr>
            <tr>
                <td colspan="2">Datum:</td>
                <td><input type="text" name="" value="<?= inputVal($zaznam['dat_odpovedny'], 'dat') ?>"></td>
                <td></td>
                <td><input type="text" name="" value="<?= inputVal($zaznam['dat_odpovedny'], 'cas') ?>"></td>
                <td colspan="2">hodin</td>
            </tr>
            <tr>
                <td colspan="7">Podpis vedoucího čety:</td>
            </tr>
            <tr>
                <td>2.13</td>
                <td colspan="13">Sváření provedou:</td>
            </tr>
            <tr>
                <td colspan="5">Jméno</td>
                <td colspan="4">Č. sváč. průkazu</td>
                <td colspan="5">Podpis</td>
            </tr>
            <tr>
                <td colspan="5"><input type="text" name="" value="<?= $svareci[0]['jmeno'] ?? '' ?>"></td>
                <td colspan="4"><input type="text" name="" value="<?= $svareci[0]['c_prukazu'] ?? '' ?>"></td>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td colspan="5"><input type="text" name="" value="<?= $svareci[1]['jmeno'] ?? '' ?>"></td>
                <td colspan="4"><input type="text" name="" value="<?= $svareci[1]['c_prukazu'] ?? '' ?>"></td>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td colspan="5"><input type="text" name="" value="<?= $svareci[2]['jmeno'] ?? '' ?>"></td>
                <td colspan="4"><input type="text" name="" value="<?= $svareci[2]['c_prukazu'] ?? '' ?>"></td>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td></td>
                <td>2.14</td>
                <td colspan="8">Osvědčení o způsobilosti k práci a sváření na plynové zařízení má pracovník:</td>
                <td colspan="5"><input type="text" name="" value="<?= $zaznam['osvedceni_ma'] ?>"></td>
            </tr>
            <tr>
                <td colspan="15"><b>3. Prohlašuji, že jsem se osobně přesvědčil, že výše uvedené zajištění je provedeno.</b></td>
            </tr>
            <tr>
                <td colspan="3">Datum</td>
                <td colspan="2"><input type="text" name="" value="<?= inputVal($zaznam['dat_odpov_provoz'], 'dat') ?>"></td>
                <td>Datum</td>
                <td colspan="2"><input type="text" name="" value="<?= inputVal($zaznam['dat_odpov_GB_exter'], 'dat') ?>"></td>
                <td colspan="7">Vyjádření přilehlého obvodu: <input type="text" name="" value="<?= $zaznam['prohl_obvod'] ?>" style="width: auto;"></td>
            </tr>
            <tr>
                <td colspan="5">Podpis odpovědného pracovníka provozu:</td>
                <td colspan="3">Podpis odpovědného pracovníka provádějícího útvaru GB nebo externí firmy:</td>
                <td colspan="4">Podpis vedoucího přilehlého obvodu:</td>
                <td colspan="3">Datum<input type="text" name="" value="<?= inputVal($zaznam['dat_vedouci'], 'dat') ?>"></td>
            </tr>
        </tbody>
    </table>

    <table id="secPage">
        <tbody>
            <tr>
                <td colspan="15"><b>4. podmínky pro práci s otevřeným ohněm</b></td>
            </tr>
            <tr>
                <td rowspan="5" class="svisly-text">Vystavovatel</td>
                <td>4.1</td>
                <td colspan="13">Podmínky: (doplňující bod č. 1)</td>
            </tr>
            <tr>
                <td colspan="14"><textarea name="podminky" rows="4" style="resize: none; width: 95%; border: none;"></textarea></td>
            </tr>
            <tr>
                <td>4.2</td>
                <td colspan="5">Výše uvedené podmínky stanovil - jméno:</td>
                <td colspan="5"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td>Podpis:</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td>4.3</td>
                <td colspan="6">Pracoviště připraveno pro práci s otevřeným ohněm:</td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td>Dne:</td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2">Hodin:</td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
            </tr>
            <tr>
                <td>4.4</td>
                <td colspan="3">Osobně zkontroloval - jméno:</td>
                <td colspan="5"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td>Podpis:</td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="4"><b>5. Rozbor ovzduší</b></td>
                <td>Datum:</td>
                <td colspan="2">Čas:</td>
                <td colspan="4">Místo odběru vzorku ovzduší:</td>
                <td colspan="2">Naměřená hodnota:</td>
                <td colspan="2">Podpis:</td>
            </tr>
            <tr>
                <td colspan="4"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="4"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
            </tr>
            <tr>
                <td colspan="4"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="4"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
            </tr>
            <tr>
                <td colspan="4"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="4"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
            </tr>
            <tr>
                <td colspan="4"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="4"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
            </tr>
            <tr>
                <td colspan="4"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="4"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
            </tr>
            <tr>
                <td colspan="11"><b>6. Další jiné podmínky práce na zařízení</b></td>
                <td colspan="4">Stanovil:</td>
            </tr>
            <tr>
                <td rowspan="3" colspan="2" class="svisly-text">Vystavovatel <br> nebo kontrolní <br> orgán</td>
                <td rowspan="3" colspan="9"><textarea name="dalsi_jine" rows="4" style="resize: none; width: 95%; border: none;"></textarea></td>
                <td>Jméno:</td>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
            </tr>
            <tr>
                <td>Dne:</td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td>hodin:</td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
            </tr>
            <tr>
                <td>Podpis:</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="8"><b>7. Další nutná opatření - případně viz protokol ze dne</b></td>
                <td colspan="7"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
            </tr>
            <tr>
                <td colspan="15"><textarea name="dalsi_jine" rows="4" style="resize: none; width: 95%; border: none;"></textarea></td>
            </tr>
            <tr>
                <td colspan="5"><b>8. Předání do opravy - protokol č. <input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>" style="width: 10%;"></b></td>
                <td colspan="6"><b>10. Práce svářečské ukončeny</b></td>
                <td colspan="4"><b>12. Kontrola BT, PO, jiného orgánu</b></td>
            </tr>
            <tr>
                <td>Dne:</td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td>Hodina:</td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td>Dne:</td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td>Hodina:</td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td>Provedena dne:</td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td>Hodina:</td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
            </tr>
            <tr>
                <td colspan="5">Předal: <input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="6">Předal: <input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="4">Zjištěno: <input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
            </tr>
            <tr>
                <td colspan="5">Převzal: <input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="6">Převzal: <input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="5"><b>9. Předání z opravy</b></td>
                <td colspan="6"><b>11. Následný dozor</b></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="2">Dne:</td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td>Hodina:</td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td>Od:</td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td>hodin</td>
                <td>do</td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td>hodin</td>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td colspan="5">Předal: <input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="6">Jméno: <input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="4">Jméno: <input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
            </tr>
            <tr>
                <td colspan="5">Převzal: <input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="6">Podpis:</td>
                <td colspan="4">Podpis:</td>
            </tr>
            <tr>
                <td colspan="15"><b>13. Prodloužených za podmínek stanovených tímto povolením</b></td>
            </tr>
            <tr>
                <td rowspan="16" class="svisly-text">Prodlužuje provozovatel</td>
                <td>13.1</td>
                <td colspan="13">Pro práci na zařízrní</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center;">Datum</td>
                <td colspan="2" style="text-align: center;">od - do</td>
                <td style="text-align: center;">Přestávka</td>
                <td colspan="2" style="text-align: center;">Počet osob</td>
                <td colspan="3" style="text-align: center;">Podpis odpovědného prac. provozu</td>
                <td colspan="4" style="text-align: center;">Podpis odpovědného prac. prov. útvaru</td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="3"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="3"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="3"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="3"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="3"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="3"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>13.2</td>
                <td colspan="13">Pro práci s otevřeným ohněm</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center;">Datum</td>
                <td colspan="2" style="text-align: center;">od - do</td>
                <td style="text-align: center;">Přestávka</td>
                <td colspan="2" style="text-align: center;">Počet osob</td>
                <td colspan="3" style="text-align: center;">Podpis vystavovatele</td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="2"><input type="text" name="" value="<?= $zaznam['ev_cislo'] ?>"></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="15"><b>14. Doplňky, poznámky</b></td>
            </tr>
            <tr>
                <td colspan="15"><textarea name="doplnky" rows="4" style="resize: none; width: 95%; border: none;"></textarea></td>
            </tr>
        </tbody>
    </table>
</body>
</html>