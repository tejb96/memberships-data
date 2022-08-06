jQuery(document).ready(($) => {
    $('#q1-hsb').click(() => {
        var year = $("input[name=year_hsb]:checked").val();
        $('#from-date-hsb').val(year + '-01-01');
        $('#to-date-hsb').val(year + '-03-31');
    });
    $('#q2-hsb').click(() => {
        var year = $("input[name=year_hsb]:checked").val();
        $('#from-date-hsb').val(year + '-04-01');
        $('#to-date-hsb').val(year + '-06-30');
    });
    $('#q3-hsb').click(() => {
        var year = $("input[name=year_hsb]:checked").val();
        $('#from-date-hsb').val(year + '-07-01');
        $('#to-date-hsb').val(year + '-09-30');
    });
    $('#q4-hsb').click(() => {
        var year = $("input[name=year_hsb]:checked").val();
        $('#from-date-hsb').val(year + '-10-01');
        $('#to-date-hsb').val(year + '-12-31');
    });
    $('#clear-dates').click(() => {
        $('#from-date-hsb').val('');
        $('#to-date-hsb').val('');
    });
   
});