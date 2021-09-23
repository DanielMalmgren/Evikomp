<html>
<head></head>
<body>
    <p>Hej.</p>
    <p>Detta är en notifiering om att {{$project_time->workplace->name}} har avbokat ett inplanerat lärtillfälle i Evikomp.</p>

    <p>Datum: {{$project_time->date}}</p>
    <p>Tidpunkt: {{$project_time->startstr()}}</p>

    <p>Med vänlig hälsning, Evikomp projektgrupp</p>
    <p>Detta e-postmeddelande är automatgenererat.</p>
</body>
</html>
