
//DEFINE FIELD VARIABLES
/** var @dates is used as global to be available to other functions after
 * been set by function @topInfoByField() */
var dates, stayD, numberOfDaysStayed;


/** Next function creates two bars, showing overall checked days in the last 180 days
 * and percentage with a progress bar */

function topInfoByField(data) {

    //Variables needed for the function @checkLast180Days()
    var today = new Date();
    var someDay = new Date(); //Todays date used to filter the checked dates by given period
    var firstDay = new Date(someDay.setDate(someDay.getDate() - 180));
    function checkLast180Days(date) {
        return (new Date(date) <= today && new Date(date) >= firstDay);
    }

    //CHECKED DATES VARIABLES
    dates = data.stayDates; //All checked dates from StayDays class from current user
    stayD = dates.filter(checkLast180Days); // Filter only dates within period from today to 180 days behind
    numberOfDaysStayed = stayD.length; //Count filtered dates
    var periodInProcent = Math.round(numberOfDaysStayed / 90 * 100);
    var outp = '';
    var bar_color = 'lightgreen';
    var topFirstLineInfo = "You have overall " + numberOfDaysStayed + " days checked in during the " +
        "period of the last 180 days!";

    if (numberOfDaysStayed > 80) {
        bar_color = '#f77';
    }else if (numberOfDaysStayed > 50) {
        bar_color = '#f70'
    }else if(numberOfDaysStayed === 0){
        topFirstLineInfo = "There are no checked days.";
    }

    outp += "<div style='background-color:" + bar_color + ";text-align:center'>" +
            topFirstLineInfo + "</div>";

    //Second (progress) bar with the percentage of checked dates in last 180 days
    outp += "<div class='bg-light border m-1 rounded'><div style='transition: all ease 0.5s;text-align:center;background-color:" +
        bar_color + "; width:" + (periodInProcent <= 100 ? periodInProcent : 100) + "%'>" +
        periodInProcent + "%</div></div>";

    return outp;

}

/** FUNCTION currentField returns checkmark for provided
 * date (field) and number of days (dates) that are checked
 * between provided date and 180 days before that date */



function currentField(date) {

    var checked = '', checkmark = '', ch = 0, next, dayBack, td, output;

    if ($.inArray(date, dates) !== -1){
        checked = 'checked';
        ch = 1;
        checkmark = "<i class='fa fa-check-circle fa-2x text-light'></i>";
    }

    //function to select only dates between date and date -180 days
    function checkPeriod180Days(some_date) {
        next = date;
        dayBack = new Date(new Date(next).setDate(new Date(next).getDate() - 180));
        return (new Date(some_date) <= new Date(next) && new Date(some_date) >= dayBack);
    }

    td = dates.filter(checkPeriod180Days).length; //count selected dates in last 180 days


    output = "<div class='text-center d-inline ml-1 open'></div>" +
        "<div class='click-field h-50' onclick='checkField(\"" + date + "\"," + ch + ")'>" +
        "<input class='val' type='text' value='" + date + "' disabled hidden>" +
        checkmark + "</div>" +
        "<div class='bottom-right-corner'>" + td + "</div>";

    return output;
}
