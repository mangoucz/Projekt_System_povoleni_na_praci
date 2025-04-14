$(document).ready(function() {
    function zobrazTab() {
        const visibleTables = {};
    
        $("#intro input[type='checkbox']:checked").each(function() {
            const tableIds = checkboxMap[$(this).attr("id")] || [];
    
            tableIds.forEach(function(id) {
                visibleTables[id] = true;
            });
        });
        $("#first, #sec, #third, #fourth, #fifth, #sixth").each(function() {
            if (visibleTables[$(this).attr("id")]) 
                $(this).stop().fadeIn(250);
            else 
                $(this).stop().fadeOut(250);
        });
        $("#first input[type='text']").each(function() {
            if ($(this).val() == "") {
                $(this).attr("disabled", true);            
            }
        });
    }
    function updateIndex(selector) {
        $(selector).each(function(i) {
            $(this).attr("data-index", i);
            $(this).find("input").each(function() {
                const name = $(this).attr("name");
                $(this).attr("name", name.replace(/\[\d\]/, `[${i}]`));
            });
        });
    }
    function closeModal() {
        $(".modal").fadeOut(200).css("display", "none");
        window.location.href = "uvod.php";
    }
    function initializeDatepicker(selector) {
        $(selector).datepicker({
            dateFormat: 'dd. mm. yy',
            firstDay: 1, // Pondělí jako první den
            dayNames: ['Neděle', 'Pondělí', 'Úterý', 'Středa', 'Čtvrtek', 'Pátek', 'Sobota'],
            dayNamesMin: ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'],
            dayNamesShort: ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'],
            monthNames: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
            monthNamesShort: ['Led', 'Úno', 'Bře', 'Dub', 'Kvě', 'Čer', 'Čec', 'Srp', 'Zář', 'Říj', 'Lis', 'Pro'],
            showWeek: true,
            weekHeader: 'Týden'
        });
    }
    initializeDatepicker('.date');

    $(document).on('focus', '.date', function () {
        if (!$(this).hasClass('hasDatepicker')) {
            initializeDatepicker(this);
        }
    });

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
    zobrazTab();

    $(document).on('change', '#intro input[type="checkbox"]', function() {
        zobrazTab();
    });

    $(document).on('click', '#odeslat', function() {
        $("#form").find(".date").each(function() {
            const dateValue = $(this).val();
            if (dateValue) {
                const dateParts = dateValue.split('.');
                const dateFinal = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
                $(this).val(dateFinal);
            }
        });
        
        const formData = $("#form").serializeArray();
        $.ajax({
            url: "sub_povoleni.php",
            type: "POST", 
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $(".modal h2").text("Povolení č. " + response.data.ev_cislo);
                    $(".modal input[type=hidden]").val(response.data.id);
                    $(".modal").fadeIn(200).css("display", "flex");
                } else {
                    alert("Chyba při odesílání povolení: " + (response.message || "Neznámá chyba") + response.error);
                }
            },
            error: function(xhr, status, error) {
                alert("Chyba komunikace se serverem! (" + status + " " + error + " " + xhr.responseText + ")");
            }
        });
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
                    if (response.data.pocet_prodl === "6x") {
                        $("#subProdl").attr("disabled", true);
                    }
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

    $(document).on('click', '#closeBtn', closeModal);
    
    $(document).on('keydown', function (e) {
        if (e.key === "Escape") { 
            closeModal();
        }
    });
    
    $("#riziko").on('input', function () {
        $("#rizikoValue").text($(this).val());
    });

    /*$(document).on('click', '.date', function () {
        if ($(this).attr('type') !== 'date') {
            if (this.value) { //dd. mm. yyyy
                const dateParts = this.value.split('.');
                const date = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
                const den = String(date.getDate()).padStart(2, '0');
                const mesic = String(date.getMonth() + 1).padStart(2, '0');
                const rok = date.getFullYear();
                const dateFinal = `${rok}-${mesic}-${den}`;
                $(this).attr('type', 'date'); //yyyy-mm-dd
                $(this).val(dateFinal);
            }
            else {
                $(this).attr('type', 'date');
            }
        }
    });
    
    $(document).on('input', '.date', function () {
    const inputs = [
        { datOd: '#povolOd', datDo: '#povolDo' },
        { datOd: '#prodluzZarOd', datDo: '#prodluzZarDo' },
        { datOd: '#prodluzOhOd', datDo: '#prodluzOhDo' }
    ];
    
    // Funkce pro převod českého formátu data na Date objekt
    function parseDate(dateStr) {
        if (!dateStr) return null;
        const parts = dateStr.split('.');
        if (parts.length !== 3) return null;
        // Vytvoření Date objektu (měsíc - 1 protože JavaScript počítá měsíce od 0)
        return new Date(parts[2], parts[1].trim() - 1, parts[0].trim());
    }

    inputs.forEach(({ datOd, datDo }) => {
        const datDoEl = $(datDo);
        const datOdVal = $(datOd).val();
        const currentVal = $(this).val();

        // Převod obou datumů na Date objekty
        const dateOd = parseDate(datOdVal);
        const dateDo = parseDate(datDoEl.val());
        const currentDate = parseDate(currentVal);

        if (dateOd && currentDate) {
            // Pokud je datDo prázdné nebo menší než aktuální datum
            if (!dateDo || currentDate > dateDo) {
                // Formátování data zpět do českého formátu
                const den = String(currentDate.getDate()).padStart(2, '0');
                const mesic = String(currentDate.getMonth() + 1).padStart(2, '0');
                const rok = currentDate.getFullYear();
                datDoEl.val(`${den}. ${mesic}. ${rok}`);
            }
        }
    });
});*/
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
            <td data-label="Datum"><input type="text" class="date" placeholder="Vyberte datum" name="rozbor[${index}][dat]"></td>
            <td data-label="Čas"><input type="text" class="time" maxlength="5" placeholder="00:00" name="rozbor[${index}][cas]"></td>
            <td data-label="Místo odběru vzorku ovzduší"><input type="text" name="rozbor[${index}][misto]"></td>
            <td data-label="Naměřená hodnota"><input type="text" name="rozbor[${index}][hodn]"></td>
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
        const inputs = tr.find('input[type="text"]'); 
                
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
});