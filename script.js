$(document).ready(function() {
    $('#riziko').on('input', function () {
        $('#rizikoValue').text($(this).val());
    });

    $("#svarecAddBut").on('click', function() {
        let index = $("tr.svareciTR[data-index]").length;

        const $radek = $("<tr>")
            .addClass('svareciTR')
            .attr("data-index", index)
            .html(`
                <td><input type="text" name="svarec[${index}][jmeno]"></td>
                <td colspan="2"><input type="text" name="svarec[${index}][prukaz]"></td>
                <td colspan="2"></td>
                <td><button type="button" class="svarecDel del">-</button></td>
            `);

        $("#svarecAdd").before($radek);
        $("#svarecAdd input[type=hidden]").attr("value", index + 1);
    });

    $("#rozborAdd").on('click', function() {
        let index = $("tr.rozboryTR[data-index]").length;

        const $radek = $("<tr>")
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
        $("#rozborAdd").before($radek);
        $("#rozborAdd input[type=hidden]").attr("value", index + 1);
    });

    $(document).on('click', 'input[type="checkbox"]', function() {
        const tr = $(this).closest('tr'); 
        const inputs = tr.find('input[type="text"], input[type="time"]'); 

        if ($(this).is(':checked')) 
            inputs.removeAttr("disabled"); 
        else 
            inputs.attr("disabled", true);
    });
    $(document).on('click', '.svarecDel', function() {
        $(this).closest('tr').remove();
        updateIndex("tr.svareciTR[data-index]");
        updateValue("#svarecAdd input[type=hidden]");
    });
    $(document).on('click', '.rozborDel', function() {
        $(this).closest('tr').remove();
        updateIndex("tr.rozboryTR[data-index]");
        updateValue("#rozborAdd input[type=hidden]");
    });

    
    function updateValue(selector) {
        $(selector).each(function(i) {
            $(this).attr("value", i);
        });
    }
    function updateIndex(selector) {
        $(selector).each(function(i) {
            $(this).attr("data-index", i);
            $(this).find("input").each(function() {
                let name = $(this).attr("name");
                $(this).attr("name", name.replace(/\[\d+\]/, `[${i}]`));
            });
        });
    }
});