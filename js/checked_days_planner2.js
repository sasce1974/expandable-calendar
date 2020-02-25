
/** Function @checkField works on click on table cell (td) of the MONTH plan, passing the
/* date and 'ch' parameter to switch between checked and empty cell and insert, or to
/* delete the date in the database for the authenticated user... */
function checkField (date, ch) {
    //console.log(date);

    if(ch === 0){
        $.post("StayController.php", {"new_date":date}, showMessage);
    }else{
        $.post("StayController.php", {"erase_date":date}, showMessage);
    }
    showPlan('month');
}

//Function openDay open the selected day when clicking on month plan field
//...can be implemented for week plan also...
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

//Function for select plan in navbar select field
$(".plan-sel").change(function () {
    var plan = $(this).val();
    var start_date = $("#start-date").val();
    var end_date = $("#end-date").val();
    $.getJSON("plannerController1.php", {"page":plan, "sd":start_date, "ed":end_date}, showOutput);
});

function errorMessage(message){
    showMessage("<h3 class='bg-danger p-2 m-5 rounded'>" + message + "</h3>");
}

function showMessage(data, textStatus) {
    if(data.trim() != ''){
        $("#message").show().html(data, textStatus);
    }

    setTimeout(function () { $("#message").html("").hide(); }, 5000);
}

function showPlan(plan) {
    $.getJSON("plannerController1.php", {"page":plan}, showOutput);
}

/** FUNCTION @showOutput receive JSON array with 2 sub arrays: 'plan' and 'stayDates'
 * plan is used to create the planner, stayDates contain all the checked dates from
 * the database in Y-m-d string format */

function showOutput(data, textStatus) {
    var output = "";

    //PLAN VARIABLES
    var plan = data.plan.plan;
    var startDate = data.plan.startDate;
    var endDate = data.plan.endDate;
    var rows = data.plan.rows;


    var today = new Date(); //Todays date - is used in plan and in the field

    //USER NAME used for log in field
    var user_name = data.user_name;

    //SHOW CANCELABLE INFO MESSAGES ON TOP OF THE PLANNER
    var message = "";
    if(data.message !== undefined){
        message = data.message;
        var message_output = '';
        if(message.length > 0){
            message_output += "<div class=\"alert alert-success alert-dismissible fade show mb-0 notification\" role=\"alert\">\n" +
                "            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n" +
                "                <span aria-hidden=\"true\">×</span>\n" +
                "            </button>\n";

            for(i=0; i < message.length; i++){
                message_output +="<i class=\"fa fa-smile-o mr-2\"></i><span class='text-success'>" + message[i] + "</span><br>";
            }
            message_output += "</div>";
            $("#info").html(message_output);
        }
    }

    //SHOW CANCELABLE ERROR MESSAGES ON TOP OF THE PLANNER
    var error = "";
    if(data.error_message !== undefined) {

        error = data.error_message;
        var error_output = '';
        if(error.length > 0){

            error_output += "<div class=\"alert alert-danger alert-dismissible fade show mb-0 notification\" role=\"alert\">\n" +
                "            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n" +
                "                <span aria-hidden=\"true\">×</span>\n" +
                "            </button>\n";

            for(i=0; i<error.length; i++){
                error_output +="<i class=\"fa fa-frown-o mr-2\"></i><span class='text-danger'>" + error[i] + "</span><br>";
            }
            error_output += "</div>";

            $("#error_message").html(error_output);
        }

    }


    // Get number of days between startDate and EndDate...
    var numberOfDays = ((new Date(endDate)).getTime() - (new Date(startDate)).getTime())/86400000;

    //Setting the number of days in plan to be 42(0-41) if it is a month plan (to fit 6rows x 7columns)
    if($.inArray(plan,['month', 'next_month', 'previous_month']) !== -1) numberOfDays = 41;


    /** var @topInfo displays various information on the top of the calendar
     * from the connected fields */
    var topInfo = topInfoByField(data);
    $("#topInfo").html(topInfo);


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

    var weekday = 0, field;

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

            var field_color = 'lightgreen';
            var field_border = (formated_next_day === formatDate(today) ? 'border:2px solid red;' : '');

            if (current_month !== dateInMiddleOfMonth.getMonth()) field_color = '#fff';

            field = currentField(formated_next_day);

            output += "<td class='planner_field p-1 text-left' style='background-color:" + field_color + ";" + field_border + "'>" +
                "<input class='val' type='text' value='" + formated_next_day + "' disabled hidden>" +
                "<span class='rounded d-inline-block bg-white text-center px-1' style='border: 1px solid lightgreen; min-width: 1.5em; color:lightgreen'>" +
                next_day.getDate() + "</span>" + field + "</td>";

            if (weekday === 7) {
                weekday = 0;
                output += "</tr>";
            }

        } else {
            /** HERE STARTS WEEK or DAY PLANNER/CALENDAR */

            output += "<th style='min-width:80px;width:auto !important;'><b>" + next_day.getDate() + " " +
                dayNameShort + "</b></th>";
        }
    }
    output +="</tr>";

    if($.inArray(plan, ['month', 'next_month', 'previous_month']) === -1){
        for(r = 1; r <= rows; r++){
            output += "<tr>";
            for (d = 0; d <= numberOfDays; d++) {
                next_day = new Date(new Date(startDate).setDate(new Date(startDate).getDate() + d));
                formated_next_day = formatDate(next_day);
                output += "<td class='border-right' style='height: 7.5vh'>" + //formated_next_day +
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
        $("#user-login").html("<span onclick='showUserOptions()'>Welcome " + user_name + "</span>");
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

    //return [year, month, day].join('-');
    return [day, month, year].join('/');
}*/

function formatDate(date){
    return date.toISOString().split('T')[0]
    //return date.toLocaleDateString('mk-MK', {weekday:'long', day:'numeric'});
}

function showUserOptions() {

    var message = "<div><a class='btn btn-dark text-light m-2' href='login/logout.php'>Log out</a>" +
        "<a class='btn btn-secondary m-2' onclick=\"hideElement('#message')\">Cancel</a></div>";
    $("#message").show().html(message);
}

function hideElement(element) {
    $(element).hide();
}