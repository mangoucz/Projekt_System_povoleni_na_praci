document.addEventListener('DOMContentLoaded', function() {
    var svarecAdd = document.getElementById("svarecAdd");
    var svareciTR = document.getElementById("svareciTR");
    var rozborAdd = document.getElementById("rozborAdd");
    var rozboryTR = document.getElementById("rozboryTR");
    var zarizeniAdd = document.getElementById("zarizeniAdd");
    var zarizeniTR = document.getElementById("zarizeniTR");
    var ohenAdd = document.getElementById("ohenAdd");
    var ohenTR = document.getElementById("ohenTR");

    svarecAdd.onclick = function () {
        var radek = document.createElement("tr");
        radek.innerHTML = `
            <td><input type="text" name="svarec_jmeno"></td>
            <td colspan="2"><input type="text" name="svarec_prukaz"></td>
            <td colspan="2"></td>
            <td><button type="button" class="del">-</button></td>
        `;
        svareciTR.parentNode.insertBefore(radek, svareciTR);
    };
    rozborAdd.onclick = function (){
        var radek = document.createElement("tr");
        radek.innerHTML = `
            <td><input type="text" name="rozbor_nazev"></td>
            <td><input type="date" name="rozbor_dat"></td>
            <td><input type="time" name="rozbor_cas"></td>
            <td><input type="text" name="rozbor_misto"></td>
            <td><input type="text" name="rozbor_hodn"></td>
            <td></td>
            <td><button type="button" class="del">-</button></td>
        `;
        rozboryTR.parentNode.insertBefore(radek, rozboryTR);
    };
    zarizeniAdd.onclick = function(){
        var radek = document.createElement("tr");
        radek.innerHTML = `
        <tr>
            <td><input type="date" name="prodluz_zar_dat" id=""></td>
            <td><input type="text" name="prodluz_zar_oddo" id=""></td>
            <td><input type="text" name="prodluz_zar_prestavka" id=""></td>
            <td><input type="text" name="prodluz_zar_os" id=""></td>
            <td colspan="2"></td>
            <td><button type="button" class="del">-</button></td>
        </tr>
        `;
        zarizeniTR.parentNode.insertBefore(radek, zarizeniTR);
    };
    ohenAdd.onclick = function(){
        var radek = document.createElement("tr");
        radek.innerHTML = `
        <tr>
            <td><input type="date" name="prodluz_oh_dat" id=""></td>
            <td><input type="text" name="prodluz_oh_oddo" id=""></td>
            <td><input type="text" name="prodluz_oh_prestavka" id=""></td>
            <td><input type="text" name="prodluz_oh_os" id=""></td>
            <td colspan="2"></td>
            <td><button type="button" class="del">-</button></td>
        </tr>
        `;
        ohenTR.parentNode.insertBefore(radek, ohenTR);
    };

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('del')) {
            event.target.closest('tr').remove();
        }
    });
});