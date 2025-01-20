$(document).ready(function() {
    $("#riziko").on('input', function () {
        $("#rizikoValue").text($(this).val());
    });

    $(".time").on('input', function () {
        let value = $(this).val().replace(/[^0-9]/g, "");
    
        if (value.length >= 1) {
            let hod1 = parseInt(value.substring(0, 1)); 
            let hod2 = parseInt(value.substring(1, 2)) || "";
    
            if (hod1 >= 3)
                value = "0" + hod1;
            else if (hod1 == 2 && hod2 > 3) 
                value = value.substring(0, 1);
    
            if (value.length > 2) {
                let min1 = value.length >= 3 ? parseInt(value.substring(2, 3)) : ""; 
                if (min1 > 5) 
                    value = value.substring(0, 2) + ":";
                else {
                    let min2 = value.substring(3, 4) || "";
                    value = value.substring(0, 2) + ":" + min1 + min2;
                }
            }
        }
        $(this).val(value);
    });
    
    
    $(".time").on('blur', function() {
        let value = $(this).val();
        if (value.length == 2)
            $(this).val(value + ":00");
        else if(value.length == 1)
            $(this).val(value + "0:00");
    });

    $(document).on('click', '#svarecAddBut', function() {
        let index = $("tr.svareciTR[data-index]").length;

        const radek = $("<tr>")
            .addClass('svareciTR')
            .attr("data-index", index)
            .html(`
                <td><input type="text" name="svarec[${index}][jmeno]"></td>
                <td colspan="2"><input type="text" name="svarec[${index}][prukaz]"></td>
                <td colspan="2"></td>
                <td><button type="button" class="svarecDel del">-</button></td>
            `);

        $("#svarecAdd").before(radek);
        $("#svarecAdd input[type=hidden]").attr("value", index + 1);

        if (index >= 2) {
            $("#svarecAddBut").remove();
        }
    });

    $(document).on('click', '#rozborAddBut', function() {
        let index = $("tr.rozboryTR[data-index]").length;

        const radek = $("<tr>")
            .addClass('rozboryTR')
            .attr("data-index", index)
            .html(`
                <td><input type="text" name="rozbor[${index}][nazev]"></td>
                <td><input type="date" name="rozbor[${index}][dat]"></td>
                <td><input type="time" name="rozbor[${index}][cas]"></td>
                <td><input type="text" name="rozbor[${index}][misto]"></td>
                <td><input type="text" name="rozbor[${index}][hodn]"></td>
                <td></td>
                <td><button type="button" class="rozborDel del">-</button></td>
            `);
        $("#rozborAdd").before(radek);
        $("#rozborAdd input[type=hidden]").attr("value", index + 1);

        if (index == 4) {
            $("#rozborAddBut").remove();
        }
    });

    $(document).on('click', '.first input[type="checkbox"]', function() {
        const tr = $(this).closest('tr'); 
        const inputs = tr.find('input[type="text"], input[type="time"]'); 

        if ($(this).is(':checked')) 
            inputs.removeAttr("disabled"); 
        else 
            inputs.attr("disabled", true).val("");
    });

    $(document).on('click', '.svarecDel', function() {
        $(this).closest('tr').remove();
        updateIndex("tr.svareciTR[data-index]");

        let val = $("#svarecAdd input[type=hidden]").attr("value");
        $("#svarecAdd input[type=hidden]").attr("value", val - 1);

        let index = $("tr.svareciTR[data-index]").length;
        if (index == 2) {
            $("#svarecAdd td:first").html(`<button type="button" id="svarecAddBut" class="add">+</button>`);
        }
    });
    $(document).on('click', '.rozborDel', function() {
        $(this).closest('tr').remove();
        updateIndex("tr.rozboryTR[data-index]");

        
        let val = $("#rozborAdd input[type=hidden]").attr("value");
        $("#rozborAdd input[type=hidden]").attr("value", val - 1);

        let index = $("tr.rozboryTR[data-index]").length;
        if (index == 4) {
            $("#rozborAdd td:first").html(`<button type="button" id="rozborAddBut" class="add">+</button>`);
        }
    });

    
    function updateIndex(selector) {
        $(selector).each(function(i) {
            $(this).attr("data-index", i);
            $(this).find("input").each(function() {
                let name = $(this).attr("name");
                $(this).attr("name", name.replace(/\[\d\]/, `[${i}]`));
            });
        });
    }
});