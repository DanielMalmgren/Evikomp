<html>
<head></head>
<body>
    <p>Hej.</p>
    <p>Detta är en notifiering om att en arbetsplats har önskat lärarstöd för ett inplanerat lärtillfälle och att {{$project_time->training_coordinator->name}} har valts som utbildningsanordnare. Ni behöver därför tilldela en lärare till detta lärtillfälle. Gör detta genom att klicka på länken nedan.</p>

    <p><a href="{{env('APP_URL')}}/projecttime/{{$project_time->id}}/edit">{{$project_time->workplace->name}}, {{substr($project_time->starttime, 0, 5)}}-{{substr($project_time->endtime, 0, 5)}}</a></p>

    <p>Med vänlig hälsning, Evikomp projektgrupp</p>
    <p>Detta e-postmeddelande är automatgenererat.</p>
</body>
</html>
