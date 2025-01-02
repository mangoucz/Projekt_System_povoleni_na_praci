$(document).ready(function() {
    $("#svarecAdd").on('click', function() {
        var index = $("tr.svareciTR[data-index]").length;

        var $radek = $("<tr>")
            .addClass('svareciTR')
            .attr("data-index", index)
            .html(`
                <td><input type="text" name="svarec[${index}]jmeno"></td>
                <td colspan="2"><input type="text" name="svarec[${index}]prukaz"></td>
                <td colspan="2"></td>
                <td><button type="button" class="svarecDel del">-</button></td>
            `);

        $("#svarecAdd").before($radek);
        $("#svarecAdd input[type=hidden]").attr("value", index + 1);
    });

    $("#rozborAdd").on('click', function() {
        var index = $("tr.rozboryTR[data-index]").length;

        var $radek = $("<tr>")
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
    });

    $(document).on('click', '.svarecDel', function() {
        $(this).closest('tr').remove();
        updater("tr.svareciTR[data-index]");
    });
    $(document).on('click', '.rozborDel', function() {
        $(this).closest('tr').remove();
        updater("tr.rozboryTR[data-index]");
        
    });

    function updater(selector) {
        $(selector).each(function(i) {
            $(this).attr("data-index", i);
            $(this).find("input").each(function() {
                var name = $(this).attr("name");
                $(this).attr("name", name.replace(/\[\d+\]/, `[${i}]`));
            });
        });
    }
});
