$(document).ready(function() {
    if($(".respons").css("display") == "none"){
        $(".respons input").each(function() {
            $(this).attr("disabled", true);
        });
    }
    else{
        $(".origo input").each(function() {
            $(this).attr("disabled", true);
        });
    }

    $("#first, #sec, #third, #fourth, #fifth, #sixth").hide();
    const checkboxMap = {
        "prace_na_zarizeni": ["first", "third", "sixth"],
        "svarovani_ohen": ["sec", "third", "fourth", "fifth"],
        "vstup_zarizeni_teren": ["first", "sec", "third"],
        "prostredi_vybuch": ["sec", "third", "fifth"],
        "predani_prevzeti_zarizeni": [] 
    };

    $(document).on('change', '#intro input[type="checkbox"]', function() {
        const checkboxName = $(this).attr("id");
        const isChecked = $(this).is(":checked");        
        const tableIds = checkboxMap[checkboxName];
        
        if (tableIds.length > 0) {
            tableIds.forEach(function(i) {
                $("#" + i).toggle(isChecked);
            });
        } 
    });

    $(document).on('click', '.link', function () {
        const id = $(this).attr("id");
        $.ajax({
            url: "get_povoleni.php",
            type: "POST",
            data: { id_pov: id },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $(".modal h2").text("Povolení č. " + response.data.ev_cislo);
                    $(".modal .zadal").html("<strong>" + response.data.zadal + "</strong>");
                    $(".modal .povoleni_na").html(response.data.povoleni_na.join("<br>"));
                    $(".modal .od").text(response.data.od);
                    $(".modal .do").text(response.data.do);
                    $(".modal .prodlDo").text(response.data.prodl_do);
                    $(".modal .popis_prace").html("<em>" + response.data.popis_prace + "</em>");
                    $(".modal .odeslano").text(response.data.odeslano);
                    $(".modal .upraveno").text(response.data.upraveno);
                    $(".modal .prodl").text(response.data.pocet_prodl);
                    $(".modal input[type='hidden']").val(id);
        
                    $(".modal").fadeIn(200).css("display", "flex");
                } else {
                    alert("Chyba při načítání dat!");
                    alert(response.message);
                }
            },
            error: function() {
                alert("Chyba komunikace se serverem!");
            }
        });        
    });

    $(document).on('click', '.close', function () {
        $(".modal").fadeOut(200).css("display", "none");
    });

    $("#riziko").on('input', function () {
        $("#rizikoValue").text($(this).val());
    });

    $(document).on('input', '.date', function () {
        const inputs = [
            { datOd: '#povolOd', datDo: '#povolDo' },
            { datOd: '#prodluzZarOd', datDo: '#prodluzZarDo' },
            { datOd: '#prodluzOhOd', datDo: '#prodluzOhDo' }
        ];
    
        inputs.forEach(({ datOd, datDo }) => {
            const datDoEl = $(datDo);
            const datOdVal = $(datOd).val();
            
            if (datOdVal) {
                datDoEl.attr('min', datOdVal);
                if (datOdVal > datDoEl.val()){
                    datDoEl.attr('type', 'date')
                    datDoEl.val(datOdVal);
                }
            }
        });
    });
    

    $(document).on('input', '.time', function () {
        let value = $(this).val().replace(/[^0-9]/g, "");
    
        if (value.length >= 1) {
            const hod1 = parseInt(value.substring(0, 1)); 
            const hod2 = parseInt(value.substring(1, 2)) || "";
    
            if (hod1 >= 3)
                value = "0" + hod1;
            else if (hod1 == 2 && hod2 > 3) 
                value = value.substring(0, 1);
    
            if (value.length > 2) {
                const min1 = value.length >= 3 ? parseInt(value.substring(2, 3)) : ""; 
                if (min1 > 5) 
                    value = value.substring(0, 2) + ":";
                else {
                    const min2 = value.substring(3, 4) || "";
                    value = value.substring(0, 2) + ":" + min1 + min2;
                }
            }
        }
        $(this).val(value);
    });
    
    $(document).on('blur', '.time', function() {
        const value = $(this).val();
        const povolOd = $('#povolOd').val();
        const povolDo = $('#povolDo').val();
        const hodOd = $("#hodOd").val();
        const hodDo = $("#hodDo").val();
        const hodOdNum = hodOd ? parseInt(hodOd) : null;
        const hodDoNum = hodDo ? parseInt(hodDo) : null;
    
        if (value.length == 2)
            $(this).val(value + ":00");
        else if(value.length == 1)
            $(this).val("0" + value + ":00");
        
        if (povolOd === povolDo && hodOdNum !== null && hodDoNum !== null && hodOdNum > hodDoNum) {
            $("#hodDo").val("");
        }
    });

    $(document).on('click', '#svarecAddBut', function() {
        const index = $("tr.svareciTR[data-index]").length;

        const radek = $("<tr>")
            .addClass('svareciTR')
            .attr("data-index", index)
            .html(`
                <td data-label="Jméno"><input type="text" name="svarec[${index}][jmeno]"></td>
                <td data-label="Č. svář. průkazu" colspan="2"><input type="text" name="svarec[${index}][prukaz]"></td>
                <td class="origo" colspan="2"></td>
                <td><button type="button" class="svarecDel del">-</button></td>
            `);

        $("#svarecAdd").before(radek);
        $("#svarecAdd input[type=hidden]").attr("value", index + 1);

        if (index >= 2) {
            $("#svarecAddBut").remove();
        }
    });

    $(document).on('click', '#rozborAddBut', function() {
        const index = $("tr.rozboryTR[data-index]").length;

        const radek = $("<tr>")
            .addClass('rozboryTR')
            .attr("data-index", index)
            .html(`
                <td data-label="Rozbor ovzduší"><input type="text" name="rozbor[${index}][nazev]"></td>
                <td data-label="Datum"><input type="date" name="rozbor[${index}][dat]"></td>
                <td data-label="Čas"><input type="text" class="time" maxlength="5" placeholder="00:00" name="rozbor[${index}][cas]"></td>
                <td data-label="Místo odběru vzorku ovzduší"><input type="text" name="rozbor[${index}][misto]"></td>
                <td data-label="Naměřená hodnota"><input type="text" name="rozbor[${index}][hodn]"></td>
                <td class="origo"></td>
                <td><button type="button" class="rozborDel del">-</button></td>
            `);
        $("#rozborAdd").before(radek);
        $("#rozborAdd input[type=hidden]").attr("value", index + 1);

        if (index == 4) {
            $("#rozborAddBut").remove();
        }
    });

    $(document).on('click', '#first input[type="checkbox"]', function() {
        const tr = $(this).closest('tr'); 
        const inputs = tr.find('input[type="text"], input[type="time"]'); 

        if ($(this).is(':checked')){
            inputs.removeAttr("disabled"); 
            inputs.attr("required", true);
        }
        else{
            inputs.attr("disabled", true).val("");
            inputs.removeAttr("required"); 
        }
    });

    $(document).on('click', '.svarecDel', function() {
        $(this).closest('tr').remove();
        updateIndex("tr.svareciTR[data-index]");

        const val = $("#svarecAdd input[type=hidden]").attr("value");
        $("#svarecAdd input[type=hidden]").attr("value", val - 1);

        const index = $("tr.svareciTR[data-index]").length;
        if (index == 2) {
            $("#svarecAdd td:first").html(`<button type="button" id="svarecAddBut" class="add">+</button>`);
        }
    });
    $(document).on('click', '.rozborDel', function() {
        $(this).closest('tr').remove();
        updateIndex("tr.rozboryTR[data-index]");

        
        const val = $("#rozborAdd input[type=hidden]").attr("value");
        $("#rozborAdd input[type=hidden]").attr("value", val - 1);

        const index = $("tr.rozboryTR[data-index]").length;
        if (index == 4) {
            $("#rozborAdd td:first").html(`<button type="button" id="rozborAddBut" class="add">+</button>`);
        }
    });

    
    function updateIndex(selector) {
        $(selector).each(function(i) {
            $(this).attr("data-index", i);
            $(this).find("input").each(function() {
                const name = $(this).attr("name");
                $(this).attr("name", name.replace(/\[\d\]/, `[${i}]`));
            });
        });
    }
});