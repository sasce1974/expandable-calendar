
//Function works on click on table cell (td) passing date and 'ch' parametter
//to switch between checked and empty cell and insert, or delete the date in database
//for the authenticated user...
function checkField (date, ch) {
    //console.log(date);

    if(ch === 0){
        $.post("StayController.php", {"new_date":date}, showMessage);
    }else{
        $.post("StayController.php", {"erase_date":date}, showMessage);
    }
    showPlan('month');
}

//Function openDay open the selected day
function openDay(date) {
    $.getJSON("plannerController1.php", {"page":'byPeriod', "sd":date, "ed":date}, showOutput);
}

//Function for all navbar buttons and date fields
$(".plan-nav").click(function () {
    var plan = $(this).val();
    var start_date = $("#start-date").val();
    var end_date = $("#end-date").val();
    $.getJSON("plannerController1.php", {"page":plan, "sd":start_date, "ed":end_date}, showOutput);
});

//Function for select plan in navbar
$(".plan-sel").change(function () {
    var plan = $(this).val();
    var start_date = $("#start-date").val();
    var end_date = $("#end-date").val();
    $.getJSON("plannerController1.php", {"page":plan, "sd":start_date, "ed":end_date}, showOutput);
});

function errorMessage(message){
    $("#message").html("<h3 class='bg-danger p-2 m-5 rounded'>" + message + "</h3>");
}

function showMessage(data, textStatus) {
    console.log(textStatus);
    if(data.trim() != ''){
        $("#message").show().html(data, textStatus);
    }

    setTimeout(function () { $("#message").html("").hide(); }, 3000);
    //showPlan("last_one");
}

function showPlan(plan) {
    $.getJSON("plannerController1.php", {"page":plan}, showOutput);
}

/** FUNCTION @showOutput receive JSON array with 2 sub arrays: 'plan' and 'stayDates'
 * plan is used to create the planner, stayDates contain all the checked dates from
 * the database in Y-m-d string format */

function showOutput(data, textStatus) {
    var output = "";

    console.log(textStatus);
    console.log(data);

    //CHECKED DATES VARIABLES
    var dates = data.stayDates; //All checked dates from StayDays class from current user

    //console.log("Dates are: " + dates);
    var today = new Date(); //Todays date
    var someDay = new Date(); //Todays date used to filter the checked dates by given period
    var firstDay = new Date(someDay.setDate(someDay.getDate() - 180));
    function checkLast180Days(date) {
        return (new Date(date) <= today && new Date(date) >= firstDay);
    }
    var stayD;
    if(dates !== undefined){
        stayD = dates.filter(checkLast180Days); // Filter only dates within period from today to 180 days behind
    }else{
        stayD = "";
    }

    var numberOfDaysStayed = stayD.length; //Count filtered dates

    //PLAN VARIABLES

    var plan = data.plan.plan;
    var startDate = data.plan.startDate;
    var endDate = data.plan.endDate;
    var rows = data.plan.rows;

    var message = "";
    if(data.message !== undefined){
        message = data.message;
    }

    var error = "";
    if(data.error_message !== undefined) {
        error = data.error_message;
    }

    //USER NAME used for log in field
    var user_name = data.user_name;


    //console.log(plan + " " + startDate + " " + endDate);

    // Get number of days between startDate and EndDate...
    var numberOfDays = ((new Date(endDate)).getTime() - (new Date(startDate)).getTime())/86400000;

    //Setting the number of days in plan to be 42(0-41) if it is a month plan (to fit 6rows x 7columns)
    if($.inArray(plan,['month', 'next_month', 'previous_month']) !== -1) numberOfDays = 41;



    output += "<div>" + message + "</div>";
    output += "<div>" + error + "</div>";

    /** Next there are two bars, showing overall checked days in the last 180 days and precentage
     * with a progress bar */

    var bar_color = 'lightgreen';
    if(numberOfDaysStayed > 80){
        bar_color = '#f77';
    }else if(numberOfDaysStayed >50){
        bar_color = '#f70'
    }

    output += "<div style='background-color:" + bar_color + ";text-align:center'>" +
        "You have overall " + numberOfDaysStayed + " days checked in during the " +
        "period of the last 180 days!</div>";

    var periodInProcent = Math.round(numberOfDaysStayed/90 * 100);

    output += "<div class='bg-light border m-1 rounded'><div style='transition: all ease 0.5s;text-align:center;background-color:" +
        bar_color +"; width:" + (periodInProcent <= 100 ? periodInProcent : 100) + "%'>" +
        periodInProcent + "%</div></div>";


    /** The Name of the current/actual month is calculated from the end date minus 15 days
     * using constant @month as array of months names...
     * If plan is a week then the month name is calculated endDay - 7 days (will be monday)
     * If plan is a day, then the month name is get from endDate (- 0)*/

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

    output += "<div style=\"overflow-x:auto\">" +
        "<table class=\"table table-striped text-center\">";

    if($.inArray(plan, ['month', 'next_month', 'previous_month']) !== -1){
        output += "<tr class='small'><th>Mon</th><th>Tue</th><th>Wen</th><th>Thu</th><th>Fri</th>" +
            "<th>Sat</th><th>Sun</th></tr>";
    }


    output += "<tr>";

    var weekday = 0;

    for(d = 0; d <= numberOfDays; d++) {

        // var @nex_day contains date object of each consequent day of var @startDate
        var next_day = new Date(new Date(startDate).setDate(new Date(startDate).getDate() + d));

        var formated_next_day = formatDate(next_day);//formated as Y-m-d

        //Creating days names in a array to use for date formatting
        var daysNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var daysNamesShort = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        var dayName = daysNames[next_day.getDay()]; //Current Day name
        var dayNameShort = daysNamesShort[next_day.getDay()]; //Current day name - abbreviated

        /** Here starts MONTH PLANNER (CALENDAR) */

        if ($.inArray(plan, ['month', 'next_month', 'previous_month']) !== -1) {
            var current_month = next_day.getMonth(); //Number of the current month for each field/day
            if (weekday === 0) output += "<tr>"; //Start of the week for the calendar/planner
            weekday++;

            //var field_color = (formated_next_day === formatDate(today) ? '#afa' : '#9af');
            var field_color = 'lightgreen';
            var field_border = (formated_next_day === formatDate(today) ? 'border:2px solid red;' : '');

            if (current_month !== dateInMiddleOfMonth.getMonth()) field_color = '#fff';
            var checked = ''; var checkmark = ''; var ch = 0;
            if ($.inArray(formated_next_day, dates) !== -1){
                checked = 'checked';
                ch = 1;
                checkmark = "<i class='fa fa-check-circle fa-2x text-light'></i>";
            }
            //if ($.inArray(formated_next_day, stayD) !== -1) field_color = '#faa';

            //function to select only dates between next_day and date -180 days
            function checkPeriod180Days(date) {
                var dayBack = new Date(new Date(next_day).setDate(new Date(next_day).getDate() - 180));
                return (new Date(date) <= next_day && new Date(date) >= dayBack);
            }

            var td = dates.filter(checkPeriod180Days).length; //count selected dates in last 180 days

            // print field as table cell
            output += "<td class='planner_field p-1 text-left' onclick='checkField(\"" + formated_next_day + "\"," + ch + ")' style='background-color:" + field_color + ";" + field_border + "'>" +
                "<input class='val' type='text' value='" + formated_next_day + "' disabled hidden>" +
                "<span class='rounded d-inline-block bg-white text-center px-1' style='border: 1px solid lightgreen; min-width: 1.5em; color:lightgreen'>" +
                next_day.getDate() + "</span>" +
                // "<div class='text-center d-inline ml-1 open'>" + dayNameShort + "</div>" +
                "<div class='text-center d-inline ml-1 open'></div>" +
                "<div class='click-field h-50 text-left text-lg-center'><input class='val' type='text' value='" + formated_next_day + "' disabled hidden>" + checkmark + "</div>" +
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

    if(user_name !== null){
        $("#user-login").html("Welcome " + user_name);
    }

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
