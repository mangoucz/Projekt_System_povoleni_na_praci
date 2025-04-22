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
    function vytvorRadek(rowClass, index, html) {
        return $("<tr>")
            .addClass(rowClass)
            .attr("data-index", index)
            .html(html);
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
    function parseDate(dateStr) {
        dateStr = dateStr.trim();
        const parts = dateStr.split('.');
        return new Date(parts[2], parts[1].trim() - 1, parts[0].trim());
    }
    initializeDatepicker('.date');

    $(document).on('focus', '.date', function () {
        if (!$(this).hasClass('hasDatepicker')) {
            initializeDatepicker(this);
        }
    });

    if($(".respons").css("display") == "none"){
        $(".respons input, .respons textarea").each(function() {
            $(this).attr("disabled", true);
        });
    }
    else{
        $(".origo input, .origo textarea").each(function() {
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

    let index = $("tr.svareciTR[data-index]").length;
    if (index >= 3) {
         $("#svarecAddBut").hide();
    }
    $("tr.svareciTR[data-index]").each(function() {
        $(this).append('<td><button type="button" class="svarecDel del">-</button></td>');
    });
    $("#svarecAdd input[type=hidden]").attr("value", index);
    updateIndex("tr.svareciTR[data-index]");
    
    index = $("tr.rozboryTR[data-index]").length;
    if (index >= 5) {
        $("#rozborAddBut").hide();
    }
    $("tr.rozboryTR[data-index]").each(function() {
        $(this).append('<td><button type="button" class="rozborDel del">-</button></td>');
    });
    $("#rozborAdd input[type=hidden]").attr("value", index);
    updateIndex("tr.rozboryTR[data-index]");


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
    
    $(document).on('input', '#riziko', function () {
        $("#rizikoValue").text($(this).val());
    });

    function dateCheck(inputOd, inputDo, toto) {
        const dateOd = parseDate(inputOd.val());
        if (dateOd < new Date()) {
            inputOd.val(new Date().toLocaleDateString('cs-CZ'));
        }
        
        if (inputDo.val()) {
            const dateDo = parseDate(inputDo.val());
            if (dateOd > dateDo) {
                if ($(toto).attr("id") == "povolDo") {
                    inputOd.val(inputDo.val());
                }
                else {
                    inputDo.val(inputOd.val());
                }
            }
        }
        else {
            inputDo.val(inputOd.val());
        }
    }

    $(document).on('change', '.date', function() {
        const povolOd = $('#povolOd');
        const povolDo = $('#povolDo');
        const prodluzZarOd = $('#prodluzZarOd');
        const prodluzZarDo = $('#prodluzZarDo');
        const prodluzOhOd = $('#prodluzOhOd');
        const prodluzOhDo = $('#prodluzOhDo');

        if (povolOd.val()){
            dateCheck(povolOd, povolDo, this);
        }
        else if(povolDo.val()) {
            dateCheck(povolDo, povolOd, this);
        }
        else if ($(this).attr("id") == "prodluzZarOd" || $(this).attr("id") == "prodluzZarDo") {
            if (prodluzZarOd.val()) {
                dateCheck(prodluzZarOd, prodluzZarDo, this);
            } else if (prodluzZarDo.val()) {
                dateCheck(prodluzZarDo, prodluzZarOd, this);
            }
        }
        else if ($(this).attr("id") == "prodluzOhOd" || $(this).attr("id") == "prodluzOhDo") {
            if (prodluzOhOd.val()) {
                dateCheck(prodluzOhOd, prodluzOhDo, this);
            } else if (prodluzOhDo.val()) {
                dateCheck(prodluzOhDo, prodluzOhOd, this);
            }
        }
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
    $(document).on('change', '.time', function() {
        const value = $(this).val();
        
        if (value.length == 2)
            $(this).val(value + ":00");
        else if(value.length == 1)
            $(this).val("0" + value + ":00");
    });
    $(document).on('blur', '.time', function() {
        const povolOd = $('#povolOd').val();
        const povolDo = $('#povolDo').val();
        const hodOd = $("#hodOd").val();
        const hodDo = $("#hodDo").val();

        console.log('od: ' + povolOd + ' ' + hodOd + ' do: ' + povolDo + ' ' + hodDo);
        if (povolOd === povolDo && hodOd && hodDo) {
            const dateOd = parseDate(povolOd);
            const dateDo = parseDate(povolDo);
            const timeOd = hodOd.split(":").map(Number);
            const timeDo = hodDo.split(":").map(Number);
            
            const dateTimeOd = new Date(dateOd.getFullYear(), dateOd.getMonth(), dateOd.getDate(), timeOd[0], timeOd[1]);
            const dateTimeDo = new Date(dateDo.getFullYear(), dateDo.getMonth(), dateDo.getDate(), timeDo[0], timeDo[1]);
            console.log('od: ' + dateTimeOd + ' do: ' + dateTimeDo);
            
            if (dateTimeOd > dateTimeDo) {
                $("#hodDo").val(hodOd);
            }
            
        }
    });

    $(document).on('click', '#svarecAddBut', function() {
        const index = $("tr.svareciTR[data-index]").length;
        
        const radek = vytvorRadek('svareciTR', index, `
            <td colspan="2" data-label="Jméno"><input type="text" name="svarec[${index}][jmeno]"></td>
            <td colspan="3" data-label="Č. svář. průkazu"><input type="text" name="svarec[${index}][prukaz]"></td>
            <td><button type="button" class="svarecDel del">-</button></td>
        `);
            
            $("#svarecAdd").before(radek);
            $("#svarecAdd input[type=hidden]").attr("value", index + 1);
            
            if (index >= 2) {
                $(this).hide();
            }
    });
    $(document).on('click', '#rozborAddBut', function() {
        const index = $("tr.rozboryTR[data-index]").length;
            
        const radek = vytvorRadek('rozboryTR', index, `
            <td data-label="Rozbor ovzduší"><input type="text" name="rozbor[${index}][nazev]"></td>
            <td data-label="Datum"><input type="text" class="date" placeholder="Vyberte datum" name="rozbor[${index}][dat]"></td>
            <td data-label="Čas"><input type="text" class="time" maxlength="5" placeholder="00:00" name="rozbor[${index}][cas]"></td>
            <td data-label="Místo odběru vzorku ovzduší"><input type="text" name="rozbor[${index}][misto]"></td>
            <td data-label="Naměřená hodnota"><input type="text" name="rozbor[${index}][hodn]"></td>
            <td><button type="button" class="rozborDel del">-</button></td>
        `);
        $("#rozborAdd").before(radek);
        $("#rozborAdd input[type=hidden]").attr("value", index + 1);
                
        if (index >= 4) {
            $(this).hide();
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
            $("#svarecAddBut").show();
        }
    });
    $(document).on('click', '.rozborDel', function() {
        $(this).closest('tr').remove();
        updateIndex("tr.rozboryTR[data-index]");
                        
        const val = $("#rozborAdd input[type=hidden]").attr("value");
        $("#rozborAdd input[type=hidden]").attr("value", val - 1);
                
        const index = $("tr.rozboryTR[data-index]").length;
        if (index == 4) {
            $("#rozborAddBut").show();
        }
    });  
});