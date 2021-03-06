<html>
<head></head>
<body>
    <p>Hej.</p>
    <p>Detta är en påminnelse om att lärtillfällen inom Evikomp har genomförts och du som chef eller arbetsplatskoordinator behöver bekräfta vilka deltagare som var närvarande. Gör detta genom att klicka på länkarna nedan.</p>

    @foreach($project_times as $project_time)
        <p><a href="{{env('APP_URL')}}/projecttime/{{$project_time->id}}/edit">{{$project_time->workplace->name}}, {{$project_time->date}} {{substr($project_time->starttime, 0, 5)}}-{{substr($project_time->endtime, 0, 5)}}</a></p>
    @endforeach

    <p>Observera att oavsett om närvaron stämmer eller inte behöver du alltid bekräfta genom att klicka på lärtillfället och spara.</p>
    <p>Påminnelser kommer automatiskt att skickas ut tills detta har gjorts.</p>

    <p>Med vänlig hälsning, Evikomp projektgrupp</p>
    <p>Detta e-postmeddelande är automatgenererat.</p>
</body>
</html>
