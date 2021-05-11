<html>
<head></head>
<body>
    <p>Hej.</p>
    <p>Detta är en notifiering om att {{$user->name}} har gjort färdigt modulen {{$lesson->translateOrDefault(\App::getLocale())->name}} i Evikomps lärplattform.</p>

    <p>{{$user->firstname}} har även sedan innan genomfört följande moduler inom spåret {{$lesson->track->translateOrDefault(\App::getLocale())->name}}:</p>

    @foreach($lesson->track->lessons as $other_lesson)
        <p>{{$other_lesson->translateOrDefault(\App::getLocale())->name}}</p>
    @endforeach

    <p>Med vänlig hälsning, Evikomp projektgrupp</p>
    <p>Detta e-postmeddelande är automatgenererat.</p>
</body>
</html>
