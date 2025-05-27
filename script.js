$(document).ready(function() {
    function zobrazTab() {
        const visibleTables = {};
    
        $("#intro input[type='checkbox']:checked").each(function() {
            const tableIds = checkboxMap[$(this).attr("id")] || [];
    
            tableIds.forEach(function(id) {
                visibleTables[id] = true;
            });
        });
        $("#first, #third, #fourth, #fifth, #sixth").each(function() {
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
        if (!window.location.href.includes("uvod.php")) {
            window.location.href = "uvod.php";            
        }
    }
    function initializeDatepicker(selector) {
        $('.date').attr('autocomplete', 'off');
        
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
    function initializeRange(selector, output, input) {
        $(selector).slider({
            min: 1,
            max: 10,
            step: 1,
            value: $(selector).val() || 5,
            slide: function(event, ui) {
                $(output).text(ui.value);
                $(input).val(ui.value);
                $(this).val(ui.value);
            }
        });
        $(selector).on("touchstart touchmove", function(e) {
            e.preventDefault();
            
            const touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
            const slider = $(this);
            const offset = slider.offset();
            const width = slider.width();
            const x = touch.pageX - offset.left;
            
            const min = slider.slider("option", "min");
            const max = slider.slider("option", "max");
            let value = Math.round((x / width) * (max - min)) + min;
            value = Math.min(Math.max(value, 1), 10);
            
            slider.slider("value", value);
            $(output).text(value);
            $(input).val(value);
        });
    }
    function initializeTitle(selector) {
        $(selector).tooltip({
            position: {
                my: "center bottom-10",
                at: "center top"
            },
            content: $(this).attr("title"), 
            show: {
                effect: "fadeIn",
                duration: 200
            },
            hide: {
                effect: "fadeOut",
                duration: 200
            }
        });
    }
    function parseDate(dateStr) {
        dateStr = dateStr.trim();
        const parts = dateStr.split('.');
        return new Date(parts[2], parts[1].trim() - 1, parts[0].trim());
    }
    function dateCheck(inputOd, inputDo) {
        const dateOd = parseDate(inputOd.val());
        const min = typeof $(inputOd).attr("data-min") !== "undefined" ? new Date($(inputOd).attr("data-min")) : new Date();
        if (dateOd < min) {
            inputOd.val(min.toLocaleDateString('cs-CZ'));
        }
        
        if (inputDo.val()) {
            const dateDo = parseDate(inputDo.val());
            if (dateDo < min) {
                inputDo.val(min.toLocaleDateString('cs-CZ'));
            }
            if (dateOd > dateDo) {
                inputDo.val(inputOd.val());
            }
        }
        else {
            inputDo.val(inputOd.val());
        }
    }
    function timeCheck(inputOd, inputDo, hodOd, hodDo) {
        const min = typeof $(inputOd).attr("data-min") !== "undefined" ? new Date($(inputOd).attr("data-min")) : new Date();
        
        if (inputOd.val() === inputDo.val() && hodOd.val() && hodDo.val()) {
            const dateOd = parseDate(inputOd.val());
            const dateDo = parseDate(inputDo.val());
            const timeOd = hodOd.val().split(":").map(Number);
            const timeDo = hodDo.val().split(":").map(Number);
            
            const dateTimeOd = new Date(dateOd.getFullYear(), dateOd.getMonth(), dateOd.getDate(), timeOd[0], timeOd[1]);
            const dateTimeDo = new Date(dateDo.getFullYear(), dateDo.getMonth(), dateDo.getDate(), timeDo[0], timeDo[1]);
            
            if (dateTimeOd > dateTimeDo) {
                $(hodDo).val(hodOd.val());
            }
        }
    }
    function loadHlaseniPage(page, velStranky = 5) {
        $.ajax({
            url: "get_povoleni.php",
            type: "POST",
            data: { page: page, pageSize: velStranky, hlaseni: true },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $("#hlaseniTable tbody").empty();
                    response.data.forEach(function(hl) {
                        $("#hlaseniTable tbody").append(`
                            <tr>
                                <td class="evc">${hl.Zam ?? ''}</td>
                                <td class="evc">${hl.Kdy ?? ''}</td>
                                <td class="evc">${hl.EvidCislo ?? ''}</td>
                                <td class="cpovol">${hl.CisPovolenky ?? ''}</td>
                                <td class="nazev">${hl.Nazev ?? ''}</td>
                                <td class="nakls">${hl.NaklStredisko ?? ''}</td>
                                <td><button type="submit" name="id_hlas" class="defButt" value="${hl.id_hlas}">Vybrat</button></td>
                            </tr>
                        `);
                    });
                    let strHTML = '';
                    for (let i = 1; i <= response.pocetStran; i++) {
                        strHTML += `<a href="#" class="page-link" data-page="${i}" style="margin:0 5px;${i === page ? 'font-weight:bold;' : ''}">${i}</a>`;
                    }
                    $('#strankovani').html(strHTML);
                    strHTML = `
                        <div style="margin-top: 10px;">
                            <label for="maxZobrazeni">Zobrazit na stránce:</label>
                            <select name="maxZobrazeni" id="maxZobrazeni">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="0">Vše</option>
                            </select>
                        </div>`;    
                    $('#strankovani').append(strHTML);
                    $('#maxZobrazeni').val(response.velStranky);                
                }else {
                    alert("Chyba při načítání dat! " + (response.message || "Neznámá chyba"));
                }
            },
            error: function() {
                alert("Chyba komunikace se serverem!");
            }
        });     
    }
    initializeDatepicker('.date');
    initializeRange('#riziko', '#rizikoValue', '#rizikoInput');

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

    $("#first, #third, #fourth, #fifth, #sixth").hide();
    const checkboxMap = {
        "prace_na_zarizeni": ["first", "third", "sixth"],
        "svarovani_ohen": ["third", "fourth", "fifth"],
        "vstup_zarizeni_teren": ["first", "third"],
        "prostredi_vybuch": ["third", "fifth"],
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

    initializeTitle("input");
    initializeTitle("textarea");

    if (window.location.href.includes("nove.php?nove=true")) {
        $("#modalHlaseni").fadeIn(200).css("display", "flex");
        loadHlaseniPage(1);   
    }

    $(document).on('change', '#intro input[type="checkbox"]', zobrazTab);
    
    $(document).on('click', '#subNove', function(){
        window.location.href = "nove.php?nove=true";
    });

    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const newPage = $(this).data("page");
        const velStranky = $('#maxZobrazeni').val();
        loadHlaseniPage(newPage, velStranky);
    });

    $(document).on('change', '#maxZobrazeni', function() {
        const velStranky = $(this).val();
        loadHlaseniPage(1, velStranky);
    });
    
    $(document).on('change', '#archiv', function() {
        $("#my select").each(function() {
            $(this).attr("disabled", !$(this).is(":disabled"));
        });
    });
    $(document).on('change', '#archivT', function() {
        $("#team select").each(function() {
            $(this).attr("disabled", !$(this).is(":disabled"));
        });
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

    $(document).on('change', '.date', function() {
        const povolOd = $('#povolOd');
        const povolDo = $('#povolDo');
        const prodluzZarOd = $('#prodluzZarOd');
        const prodluzZarDo = $('#prodluzZarDo');
        const prodluzOhOd = $('#prodluzOhOd');
        const prodluzOhDo = $('#prodluzOhDo');

        if (povolOd.val()){
            dateCheck(povolOd, povolDo);
        }
        else if(povolDo.val()) {
            dateCheck(povolDo, povolOd);
        }
        else if ($(this).attr("id") == "prodluzZarOd" || $(this).attr("id") == "prodluzZarDo") {
            if (prodluzZarOd.val()) {
                dateCheck(prodluzZarOd, prodluzZarDo);
            } else if (prodluzZarDo.val()) {
                dateCheck(prodluzZarDo, prodluzZarOd);
            }
        }
        else if ($(this).attr("id") == "prodluzOhOd" || $(this).attr("id") == "prodluzOhDo") {
            if (prodluzOhOd.val()) {
                dateCheck(prodluzOhOd, prodluzOhDo);
            } else if (prodluzOhDo.val()) {
                dateCheck(prodluzOhDo, prodluzOhOd);
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
        const povolOd = $('#povolOd');
        const povolDo = $('#povolDo');
        const hodOd = $("#hodOd");
        const hodDo = $("#hodDo");

        const prodluzZarOd = $('#prodluzZarOd');
        const prodluzZarDo = $('#prodluzZarDo');
        const prodluzZarHodOd = $("#prodluzZarhodOd");
        const prodluzZarHodDo = $("#prodluzZarhodDo");

        const prodluzOhOd = $('#prodluzOhOd');
        const prodluzOhDo = $('#prodluzOhDo');
        const prodluzOhHodOd = $("#prodluzOhHodOd");
        const prodluzOhHodDo = $("#prodluzOhHodDo");

        if (povolOd.val() && povolDo.val() && hodOd.val() && hodDo.val()) {
            timeCheck(povolOd, povolDo, hodOd, hodDo);
        }
        else if ($(this).attr("id") == "prodluzZarhodOd" || $(this).attr("id") == "prodluzZarhodDo") {
            if (prodluzZarOd.val() && prodluzZarDo.val() && prodluzZarHodOd.val() && prodluzZarHodDo.val()) {
                timeCheck(prodluzZarOd, prodluzZarDo, prodluzZarHodOd, prodluzZarHodDo);
            }
        }
        else if ($(this).attr("id") == "prodluzOhHodOd" || $(this).attr("id") == "prodluzOhHodDo") {
            if (prodluzOhOd.val() && prodluzOhDo.val() && prodluzOhHodOd.val() && prodluzOhHodDo.val()) {
                timeCheck(prodluzOhOd, prodluzOhDo, prodluzOhHodOd, prodluzOhHodDo);
            }
        }
    });

    $(document).on('click', '#svarecAddBut', function() {
        const index = $("tr.svareciTR[data-index]").length;
        
        const radek = vytvorRadek('svareciTR', index, `
            <td data-label="Jméno"><input type="text" name="svarec[${index}][jmeno]"></td>
            <td data-label="Příjmení"><input type="text" name="svarec[${index}][prijmeni]"></td>
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