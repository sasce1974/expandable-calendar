# expandable-calendar

This script can be used to create monthly, weekly, daily or by custom set days calendar type planner. 
It is ment to easily addopt diferent javascripts that will work inside the day (or hour) fields.
Exp: There is alredy implemented script that allows checking of the day and reports the number of checked days
within the last 6 months <i>(needed for no visa regulated stays abroad - many countries allows stay for foreigners
for maximum of 3 months within 6 months period.)</i>

<h4>How to use</h4>
<p>First - Edit the <b>includes/confing.inc.php.empty</b> file by defining the information in constants to set local and
production server/sql data. Then erase <b>".empty"</b> from the file name to rename ir to <b>"config.inc.php"</b>.</p>

<p>Second - Import the planner.sql file into your database (in the next stage there will be migration option that 
can create tables without any need of importing the file).</p>

<p>Third - Tweack the <b>js/checked_days_planner2.js</b> by importing and setting different data to be shown in the 
planner fields.</p>

<p>Forth - No forth so far... :)</p>
