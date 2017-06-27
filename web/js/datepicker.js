$(document).ready(function() {
    $.fn.datepicker.dates['fr'] = {
        days: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"],
        daysShort: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"],
        daysMin: ["D", "L", "Ma", "Me", "J", "V", "S", "D"],
        months: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
        monthsShort: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jui", "Juil", "Aoû", "Sep", "Oct", "Nov", "Déc"]
    };

    $('.js-datepicker-visit').datepicker({
        format: 'dd/mm/yyyy',
        language: 'fr',
        weekStart: 1,
        startDate: 'd',
        todayHighlight: true,
        orientation: 'bottom left',
        daysOfWeekDisabled: [0,2],
        datesDisabled: ['01/11/2017', '25/12/2017', '01/05/2018', '01/11/2018', '25/12/2018', '01/05/2019', '01/11/2019', '25/12/2019',
            '01/05/2020', '01/11/2020', '25/12/2020', '01/05/2021', '01/11/2021', '25/12/2021', '01/05/2022', '01/11/2022', '25/12/2022'],
        autoclose:true
    });
});