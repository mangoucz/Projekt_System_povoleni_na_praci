<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tisk</title>
</head>
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
        width: auto;
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
                <td colspan="2"><input type="text" name="ev_cislo"></td>
            </tr>
            <tr>
                <td colspan="2">Rizikovost:</td>
                <td colspan="2"><input type="text" name="rizikovost"></td>
            </tr>
            <tr>
                <td style="text-align: right;"><input type="checkbox" name="" id=""></td>
                <td colspan="8">k práci na zařízení</td>
            </tr>
            <tr>
                <td rowspan="2" colspan="6">Interní: <input type="text" name="" id=""></td>
                <td style="text-align: right;"><input type="checkbox" name="" id=""></td>
                <td colspan="8">ke svařování a práci s otevřeným ohněm</td>
            </tr>
            <tr>
                <td style="text-align: right;"><input type="checkbox" name="" id=""></td>
                <td colspan="8">ke vstupu do zařízení nebo pod úroveň terénu</td>
            </tr>
            <tr>
                <td rowspan="2" colspan="5">Externí: <input type="text" name="" id=""></td>
                <td rowspan="2" style="width: 1.5cm;">Počet osob <input type="text" name="" id=""></td>
                <td style="text-align: right;"><input type="checkbox" name="" id=""></td>
                <td colspan="8">k práci v prostředí s nebezpečím výbuchu</td>
            </tr>
            <tr>
                <td style="text-align: right;"><input type="checkbox" name="" id=""></td>
                <td colspan="8">k předání a převzetí zařízení do opravy a do provozu</td>
            </tr>
            <tr>
                <td colspan="3">na dny</td>
                <td colspan="4"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2" style="text-align: right;">od</td>
                <td><input type="text" name="" id=""></td>
                <td style="text-align: right;">do</td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td>hodin</td>
            </tr>
            <tr>
                <td colspan="3">provoz:</td>
                <td colspan="3"><input type="text" name="" id=""></td>
                <td colspan="2">název (číslo) objektu:</td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="2">číslo zařízení:</td>
                <td colspan="3"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="5">název zařízení</td>
                <td colspan="10"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="5">popis, druh a rozsah práce</td>
                <td colspan="10"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="6">seznámení s riziky pracoviště dle karty č.:</td>
                <td colspan="9"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="9"><b>1. Příprava zařízení k opravě</b></td>
                <td colspan="6" style="text-align: center;"><b>Bližší určení</b></td>
            </tr>
            <tr>
                <td rowspan="17" class="svisly-text">Zajištění provozovatelem</td>
                <td rowspan="10" class="svisly-text">Zařízení bylo</td>
                <td>1.1</td>
                <td style="width: 0.5cm; text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="5">Vyčištění od zbytků</td>
                <td colspan="6"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>1.2</td>
                <td style="text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="3">Vypařené</td>
                <td style="text-align: right;">hodin:</td>
                <td><input type="text" name="" id=""></td>
                <td colspan="6"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>1.3</td>
                <td style="text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="5">Vypláchnuté vodou</td>
                <td colspan="6"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>1.4</td>
                <td style="text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="5">Plyn vytěsnen vodou</td>
                <td colspan="6"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>1.5</td>
                <td style="text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="3">Vyvětrané</td>
                <td style="text-align: right;">hodin:</td>
                <td><input type="text" name="" id=""></td>
                <td colspan="6"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>1.6</td>
                <td style="text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="3">Profoukané dusíkem</td>
                <td style="text-align: right;">hodin:</td>
                <td><input type="text" name="" id=""></td>
                <td colspan="6"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>1.7</td>
                <td style="text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="3">Profoukané vzduchem</td>
                <td style="text-align: right;">hodin:</td>
                <td><input type="text" name="" id=""></td>
                <td colspan="6"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>1.8</td>
                <td style="text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="5">Odpojeno od elektrického proudu</td>
                <td>kým</td>
                <td colspan="3"><input type="text" name="" id=""></td>
                <td>podpis</td>
                <td></td>
            </tr>
            <tr>
                <td>1.9</td>
                <td style="text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="5">Oddělené záslepkami</td>
                <td>kým</td>
                <td colspan="3"><input type="text" name="" id=""></td>
                <td>podpis</td>
                <td></td>
            </tr>
            <tr>
                <td>1.10</td>
                <td style="text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="5">Jinak zapezpečené</td>
                <td>jak</td>
                <td colspan="5"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td rowspan="7" class="svisly-text">Podmínky BP a PO</td>
                <td>1.11</td>
                <td style="text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="5">Použít nejiskřivého nářadí</td>
                <td></td>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td>1.12</td>
                <td style="text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="5">Po dobu oprav - zkrápět, větrat</td>
                <td></td>
                <td>krát za</td>
                <td>hodin</td>
                <td>v místě:</td>
                <td colspan="2"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>1.13</td>
                <td style="text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="5">Provést rozbor ovzduší</td>
                <td>místo</td>
                <td><input type="text" name="" id=""></td>
                <td>čas</td>
                <td><input type="text" name="" id=""></td>
                <td>Výsledek</td>
                <td><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>1.14</td>
                <td style="text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="5">Zabezpečit dozor dalšími osobami</td>
                <td>počet</td>
                <td><input type="text" name="" id=""></td>
                <td colspan="4">jména uvést v bodě 7</td>
            </tr>
            <tr>
                <td>1.15</td>
                <td style="text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="5">Požární hlídka provozu</td>
                <td>počet</td>
                <td><input type="text" name="" id=""></td>
                <td>jméno</td>
                <td colspan="3"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>1.16</td>
                <td style="text-align: center;"><input type="checkbox" name="" id=""></td>
                <td colspan="5">Hasící přístroj</td>
                <td>počet</td>
                <td><input type="text" name="" id=""></td>
                <td>druh</td>
                <td><input type="text" name="" id=""></td>
                <td>typ</td>
                <td><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td style="text-align: center;">1.17</td>
                <td><input type="checkbox" name="" id=""></td>
                <td colspan="5">Jiné zabezpečení požární ochrany</td>
                <td colspan="6"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="15"><b>2. Vlastní zabezpečení prováděné práce</b></td>
            </tr>
            <tr>
                <td rowspan="19"></td>
                <td rowspan="7" class="svisly-text">Osobní ochranné <br> pracovní prostředky</td>
                <td>2.1</td>
                <td colspan="3">Ochrana nohou - jaká</td>
                <td colspan="9"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>2.2</td>
                <td colspan="3">Ochrana těla - jaká</td>
                <td colspan="9"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>2.3</td>
                <td colspan="3">Ochrana hlavy - jaká</td>
                <td colspan="9"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>2.4</td>
                <td colspan="3">Ochrana očí - jaká - druh</td>
                <td colspan="9"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>2.5</td>
                <td colspan="3">Ochrana dýchadel - jaká</td>
                <td colspan="9"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>2.6</td>
                <td colspan="3">Ochranný pás - druh</td>
                <td colspan="9"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>2.7</td>
                <td colspan="3">Ochranné rukavice - druh</td>
                <td colspan="9"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>2.8</td>
                <td colspan="3">Dozor jmenovitě</td>
                <td colspan="10"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td rowspan="3" class="svisly-text">Jiné <br> příkazy</td>
                <td>2.9</td>
                <td colspan="2"><b>Jiné</b></td>
                <td colspan="10"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>2.10</td>
                <td colspan="3">napětí 220 V</td>
                <td><input type="checkbox" name="" id=""></td>
                <td>s krytem</td>
                <td colspan="2"><input type="checkbox" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td>bez krytu</td>
                <td colspan="3"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>2.11</td>
                <td colspan="3">napětí 24 V</td>
                <td><input type="checkbox" name="" id=""></td>
                <td>bez krytu</td>
                <td colspan="2"><input type="checkbox" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td>bez krytu</td>
                <td colspan="3"><input type="text" name="" id=""></td>
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
                <td colspan="6"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="2">Datum:</td>
                <td><input type="text" name="" id=""></td>
                <td></td>
                <td><input type="text" name="" id=""></td>
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
                <td colspan="5"><input type="text" name="" id=""></td>
                <td colspan="4"><input type="text" name="" id=""></td>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td colspan="5"><input type="text" name="" id=""></td>
                <td colspan="4"><input type="text" name="" id=""></td>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td colspan="5"><input type="text" name="" id=""></td>
                <td colspan="4"><input type="text" name="" id=""></td>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td></td>
                <td>2.14</td>
                <td colspan="8">Osvědčení o způsobilosti k práci a sváření na plynové zařízení má pracovník:</td>
                <td colspan="5"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="15"><b>3. Prohlašuji, že jsem se osobně přesvědčil, že výše uvedené zajištění je provedeno.</b></td>
            </tr>
            <tr>
                <td colspan="3">Datum</td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td>Datum</td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="7">Vyjádření přilehlého obvodu: <input type="text" name="" id="" style="width: auto;"></td>
            </tr>
            <tr>
                <td colspan="5">Podpis odpovědného pracovníka provozu:</td>
                <td colspan="3">Podpis odpovědného pracovníka provádějícího útvaru GB nebo externí firmy:</td>
                <td colspan="4">Podpis vedoucího přilehlého obvodu:</td>
                <td colspan="3">Datum<input type="text" name="" id=""></td>
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
                <td colspan="5"><input type="text" name="" id=""></td>
                <td>Podpis:</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td>4.3</td>
                <td colspan="6">Pracoviště připraveno pro práci s otevřeným ohněm:</td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td>Dne:</td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2">Hodin:</td>
                <td><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>4.4</td>
                <td colspan="3">Osobně zkontroloval - jméno:</td>
                <td colspan="5"><input type="text" name="" id=""></td>
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
                <td colspan="4"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="4"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="4"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="4"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="4"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="4"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="4"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="4"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="4"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="4"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="11"><b>6. Další jiné podmínky práce na zařízení</b></td>
                <td colspan="4">Stanovil:</td>
            </tr>
            <tr>
                <td rowspan="3" colspan="2" class="svisly-text">Vystavovatel <br> nebo kontrolní <br> orgán</td>
                <td rowspan="3" colspan="9"><textarea name="dalsi_jine" rows="4" style="resize: none; width: 95%; border: none;"></textarea></td>
                <td>Jméno:</td>
                <td colspan="3"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>Dne:</td>
                <td><input type="text" name="" id=""></td>
                <td>hodin:</td>
                <td><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td>Podpis:</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="8"><b>7. Další nutná opatření - případně viz protokol ze dne</b></td>
                <td colspan="7"><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="15"><textarea name="dalsi_jine" rows="4" style="resize: none; width: 95%; border: none;"></textarea></td>
            </tr>
            <tr>
                <td colspan="5"><b>8. Předání do opravy - protokol č. <input type="text" name="" id="" style="width: 10%;"></b></td>
                <td colspan="6"><b>10. Práce svářečské ukončeny</b></td>
                <td colspan="4"><b>12. Kontrola BT, PO, jiného orgánu</b></td>
            </tr>
            <tr>
                <td>Dne:</td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td>Hodina:</td>
                <td><input type="text" name="" id=""></td>
                <td>Dne:</td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td>Hodina:</td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td>Provedena dne:</td>
                <td><input type="text" name="" id=""></td>
                <td>Hodina:</td>
                <td><input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="5">Předal: <input type="text" name="" id=""></td>
                <td colspan="6">Předal: <input type="text" name="" id=""></td>
                <td colspan="4">Zjištěno: <input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="5">Převzal: <input type="text" name="" id=""></td>
                <td colspan="6">Převzal: <input type="text" name="" id=""></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="5"><b>9. Předání z opravy</b></td>
                <td colspan="6"><b>11. Následný dozor</b></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="2">Dne:</td>
                <td><input type="text" name="" id=""></td>
                <td>Hodina:</td>
                <td><input type="text" name="" id=""></td>
                <td>Od:</td>
                <td><input type="text" name="" id=""></td>
                <td>hodin</td>
                <td>do</td>
                <td><input type="text" name="" id=""></td>
                <td>hodin</td>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td colspan="5">Předal: <input type="text" name="" id=""></td>
                <td colspan="6">Jméno: <input type="text" name="" id=""></td>
                <td colspan="4">Jméno: <input type="text" name="" id=""></td>
            </tr>
            <tr>
                <td colspan="5">Převzal: <input type="text" name="" id=""></td>
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
                <td colspan="3"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="3"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="3"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="3"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="3"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="3"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
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
                <td colspan="3"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="3"><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
                <td><input type="text" name="" id=""></td>
                <td colspan="2"><input type="text" name="" id=""></td>
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