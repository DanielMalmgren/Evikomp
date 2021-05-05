<html>
<head></head>
<body>
    <p>Hej.</p>
    <p>Detta är en notifiering om att ett (eller flera) inplanerat lärtillfälle inom Evikomp har genomförts. Du som chef eller arbetsplatskoordinator behöver nu gå in i lärplattformen och bekräfta vilka deltagare som var närvarande. Gör detta genom att klicka på länken nedan.</p>

    @foreach($project_times as $project_time)
        <p><a href="{{env('APP_URL')}}/projecttime/{{$project_time->id}}/edit">{{$project_time->workplace->name}}, {{substr($project_time->starttime, 0, 5)}}-{{substr($project_time->endtime, 0, 5)}}</a></p>
    @endforeach

    <p>Observera att oavsett om närvaron stämmer eller inte behöver du alltid bekräfta genom att klicka på lärtillfället och spara.</p>
    <p>Påminnelser kommer automatiskt att skickas ut tills detta har gjorts.</p>

    <p>Med vänlig hälsning, Evikomp projektgrupp</p>
    <p>Detta e-postmeddelande är automatgenererat.</p>
</body>
</html>
