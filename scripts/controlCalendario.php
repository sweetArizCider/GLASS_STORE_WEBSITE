<!DOCTYPE html>
<html>
<head>
<title>Calendar</title>
</head>
<body>
<div id="calendarDays"></div>
<div id="calendarMonth"></div>
<button id="prevMonth">Previous Month</button>
<button id="nextMonth">Next Month</button>
<input type="text" id="selectedDateInput">
<form id="dateForm"></form>

<?php
    $calendarDays = document.getElementById('calendarDays');
    $calendarMonth = document.getElementById('calendarMonth');
    $prevMonth = document.getElementById('prevMonth');
    $nextMonth = document.getElementById('nextMonth');
    $selectedDateInput = document.getElementById('selectedDateInput');
    $dateForm = document.getElementById('dateForm');

    $currentDate = new DateTime();
    $selectedDate = null;

    function renderCalendar($date) {
    $calendarDays->innerHTML = '';
    $year = $date->format('Y');
    $month = $date->format('m');

    $firstDayOfMonth = $date->format('w');
    $daysInMonth = $date->format('t');

    $calendarMonth->textContent = $date->format('F Y');

    for ($i = 0; $i < $firstDayOfMonth; $i++) {
        $calendarDays->innerHTML .= '<div></div>';
    }

    for ($i = 1; $i <= $daysInMonth; $i++) {
        $day = document.createElement('div');
        $day->classList.add('calendar-day');
        $day->textContent = $i;
        $day->addEventListener('click', function() {
        selectDate(new DateTime("$year-$month-$i"));
        });
        $calendarDays->appendChild($day);
    }
    }

    function selectDate($date) {
    global $selectedDate, $calendarDays, $selectedDateInput;

    $selectedDate = $date;
    foreach (document.getElementsByClassName('calendar-day') as $day) {
        $day->classList.remove('selected');
    }
    $dayElement = $calendarDays->children[$date->format('d') + $firstDayOfMonth - 1];
    $dayElement->classList.add('selected');
    $selectedDateInput->value = $date->format('Y-m-d');
    }

    $prevMonth->addEventListener('click', function() {
    global $currentDate;

    $currentDate->modify('-1 month');
    renderCalendar($currentDate);
    });

    $nextMonth->addEventListener('click', function() {
    global $currentDate;

    $currentDate->modify('+1 month');
    renderCalendar($currentDate);
    });

    renderCalendar($currentDate);
?>
</body>
</html>