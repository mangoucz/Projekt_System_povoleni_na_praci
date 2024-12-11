document.addEventListener('DOMContentLoaded', function() {
    var svarec_pridat = document.getElementById("svarec_pridat");
    var buttSvareci = document.getElementById("buttSvareci");
    var rozbor_pridat = document.getElementById("rozbor_pridat");
    var buttRozbory = document.getElementById("buttRozbory");

    svarec_pridat.onclick = function () {
        var radek = document.createElement("tr");
        radek.innerHTML = `
            <td><input type="text" name="svarec_jmeno"></td>
            <td colspan="2"><input type="text" name="svarec_prukaz"></td>
            <td colspan="2"></td>
            <td><button type="button" class="svarec_odebrat">-</button></td>
        `;
        buttSvareci.parentNode.insertBefore(radek, buttSvareci);
    };
    rozbor_pridat.onclick = function (){
        var radek = document.createElement("tr");
        radek.innerHTML = `
            <td><input type="text" name="rozbor_nazev"></td>
            <td><input type="date" name="rozbor_dat"></td>
            <td><input type="time" name="rozbor_cas"></td>
            <td><input type="text" name="rozbor_misto"></td>
            <td><input type="text" name="rozbor_hodn"></td>
            <td></td>
            <td><button type="button" class="rozbor_odebrat">-</button></td>
        `;
        buttRozbory.parentNode.insertBefore(radek, buttRozbory);
    };

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('svarec_odebrat')) {
            event.target.closest('tr').remove();
        }
        if (event.target.classList.contains('rozbor_odebrat')) {
            event.target.closest('tr').remove();
        }
    });
});