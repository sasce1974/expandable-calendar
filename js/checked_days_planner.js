

/*$(".planner_field").click(function () {
        var inputValue = $(this).children('input[type=text]:first').val();
        console.log(inputValue);
    });*/

$(document).on("click", ".click-field", function () {
    var date = $(this).children('input[type=text]:first').val();
    console.log(date);
    openDay(date);
});

function openDay(date) {
    $.getJSON("plannerController1.php", {"page":'byPeriod', "sd":date, "ed":date}, showOutput);
}

$(".plan-nav").click(function () {
    var plan = $(this).val();
    var start_date = $("#start-date").val();
    var end_date = $("#end-date").val();
    $.getJSON("plannerController1.php", {"page":plan, "sd":start_date, "ed":end_date}, showOutput);
});

$(".plan-sel").change(function () {
    var plan = $(this).val();
    var start_date = $("#start-date").val();
    var end_date = $("#end-date").val();
    $.getJSON("plannerController1.php", {"page":plan, "sd":start_date, "ed":end_date}, showOutput);
});

function errorMessage(message){
    $("#message").html("<h3 class='bg-danger p-2 m-5 rounded'>" + message + "</h3>");
}


$(document).on("change", ".get_check", function () {
    var date = $(this).val();
    var user_id = 1;
    if(this.checked){
        $.post("StayController.php", {"new_date":date, "user_id":user_id}, showMessage);
    }else if(!this.checked){
        $.post("StayController.php", {"erase_date":date, "user_id":user_id}, showMessage);
    }
});

function showMessage(data, textStatus) {
    $("#message").show().html(data, textStatus);
    setTimeout(function () { $("#message").html("").hide(); }, 3000);
    //showPlan("last_one");
}

function showPlan(plan) {
    $.getJSON("plannerController1.php", {"page":plan}, showOutput);
}


function showOutput(data, textStatus) {
    var output = "";

    //CHECKED DATES VARIABLES
    var dates = data.stayDates; //All checked dates from StayDays class from current user
    var today = new Date(); //Todays date
    var someDay = new Date(); //Todays date used to filter the checked dates by given period
    var firstDay = new Date(someDay.setDate(someDay.getDate() - 180));
    function checkLast180Days(date) {
        return (new Date(date) <= today && new Date(date) >= firstDay);
    }
    var stayD = dates.filter(checkLast180Days); // Filter only dates within period from today to 180 days behind
    var numberOfDaysStayed = stayD.length; //Count filtered dates

    //PLAN VARIABLES
    var plan = data.plan.plan;
    var startDate = data.plan.startDate;
    var endDate = data.plan.endDate;
    var rows = data.plan.rows;

    console.log(plan + " " + startDate + " " + endDate);

    // Get number of days between startDate and EndDate...
    var numberOfDays = ((new Date(endDate)).getTime() - (new Date(startDate)).getTime())/86400000;

    //Setting the number of days in plan to be 42(0-41) if it is a month plan (to fit 6rows x 7columns)
    if($.inArray(plan,['month', 'next_month', 'previous_month']) !== -1) numberOfDays = 41;


    /** Next there are two bars, showing overall checked days in the last 180 days and precentage
     * with a progress bar */

    var bar_color = '#afa';
    if(numberOfDaysStayed > 80){
        bar_color = '#f77';
    }else if(numberOfDaysStayed >50){
        bar_color = '#f70'
    }

    output += "<div style='background-color:" + bar_color + ";text-align:center'>" +
        "You have overall " + numberOfDaysStayed + " days checked in during the " +
        "period of the last 180 days!</div>";

    var periodInProcent = Math.round(numberOfDaysStayed/90 * 100);

    output += "<div class='bg-secondary border my-1 rounded'><div style='text-align:center;background-color:" +
        bar_color +"; width:" + (periodInProcent <= 100 ? periodInProcent : 100) + "%'>" +
        periodInProcent + "%</div></div>";


    /** The Name of the current/actual month is calculated from the end date minus 15?? days
     * using constant @month as array of months names... */

    var daysToMiddle = 0;
    if($.inArray(plan, ['month', 'next_month', 'previous_month']) !== -1){
        daysToMiddle = 15;
    }else if($.inArray(plan, ['current', 'next', 'back']) !== -1){
        daysToMiddle = 7;
    }


    var dateInMiddleOfMonth = new Date(new Date(endDate).setDate(new Date(endDate).getDate() - daysToMiddle));


    var month = ['January', 'February', 'March', 'April', 'May', 'Jun', 'July', 'August', 'September',
        'October', 'November', 'December'];
    output += "<h4 class='text-uppercase text-center'>" + month[dateInMiddleOfMonth.getMonth()] + " " +
        dateInMiddleOfMonth.getFullYear() + "</h4>";

    output += "<div class=\"px-1\" style=\"overflow-x:auto\">" +
        "<table class=\"table table-striped text-center\">";

    if($.inArray(plan, ['month', 'next_month', 'previous_month']) !== -1){
        output += "<tr class='small'><th>Mon</th><th>Tue</th><th>Wen</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th></tr>";
    }


    output += "<tr>";

    var weekday = 0;

    for(d = 0; d <= numberOfDays; d++) {
        var next_day = new Date(new Date(startDate).setDate(new Date(startDate).getDate() + d));
        var formated_next_day = formatDate(next_day);//formated as Y-m-d
        var daysNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        var daysNamesShort = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        var dayName = daysNames[next_day.getDay()]; //Current Day name
        var dayNameShort = daysNamesShort[next_day.getDay()];

        /** Here starts MONTH PLANNER (CALENDAR) */

        if ($.inArray(plan, ['month', 'next_month', 'previous_month']) !== -1) {
            var current_month = next_day.getMonth(); //Number of the current month for each field/day
            if (weekday === 0) output += "<tr>"; //Start of the week for the calendar/planner
            weekday++;

            var field_color = '';
            formated_next_day === formatDate(today) ? field_color = '#afa' : field_color = '#9af';

            if (current_month !== dateInMiddleOfMonth.getMonth()) field_color = '#ddd';
            var checked = '';
            if ($.inArray(formated_next_day, dates) !== -1) checked = 'checked';
            if ($.inArray(formated_next_day, stayD) !== -1) field_color = '#faa';

            //function to select only dates between next_day and date -180 days
            function checkPeriod180Days(date) {
                var dayBack = new Date(new Date(next_day).setDate(new Date(next_day).getDate() - 180));
                return (new Date(date) <= next_day && new Date(date) >= dayBack);
            }

            var td = dates.filter(checkPeriod180Days).length; //count selected dates in last 180 days

            // print field as table cell
            output += "<td class='planner_field p-1 border text-left' style='background-color:" + field_color + "'>" +
                "<input class='val' type='text' value='" + formated_next_day + "' disabled hidden>" +
                "<span class='rounded bg-white text-dark px-1 open'>" + next_day.getDate() + "</span>" +
                // "<div class='text-center d-inline ml-1 open'>" + dayNameShort + "</div>" +
                "<div class='text-center d-inline ml-1 open'></div>" +
                "<span class='float-right'><input class='form-check m-1 get_check' style='transform: scale(2)'" +
                " type='checkbox' value='" + formated_next_day + "'" + checked + "></span>" +
                "<div class='click-field h-50'><input class='val' type='text' value='" + formated_next_day + "' disabled hidden></div>" +
                "<div class='bottom-right-corner'>" + td + "</div>" +
                "</td>";

            if (weekday === 7) {
                weekday = 0;
                output += "</tr>";
            }

        } else {
            /** HERE STARTS WEEK or DAY PLANNER/CALENDAR */

            output += "<th style='min-width:80px;width:auto !important;'><b>" + next_day.getDate() + " " +
                dayName + "</b></th>";

        }
    }
    output +="</tr>";

    if($.inArray(plan, ['month', 'next_month', 'previous_month']) === -1){
        for(r = 1; r <= rows; r++){
            output += "<tr>";
            for (d = 0; d <= numberOfDays; d++) {
                next_day = new Date(new Date(startDate).setDate(new Date(startDate).getDate() + d));
                formated_next_day = formatDate(next_day);
                output += "<td class='border-right' style='height: 7.5vh'>" +
                    //HERE WOULD BE CONTENT OF THE FIELD
                    "</td>";
            }
            output += "</tr>";
        }
    }
    output += "</table>";
    output += "</div>";


    $("#output").html(output);

} //END OF FUNCTION showOutput


/*function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [year, month, day].join('-');
}*/

function formatDate(date){
    return date.toISOString().split('T')[0]
}
