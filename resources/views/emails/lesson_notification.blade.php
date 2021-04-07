<html>
<head></head>
<body>
    <p>Hej.</p>
    <p>Detta är en notifiering om att {{$user_name}} har gjort färdigt modulen {{$lesson_name}} i Evikomps lärplattform.</p>

    <p><a href="{{env('APP_URL')}}/users/{{$user_id}}">Klicka här för att se vilka övriga moduler {{$user_firstname}} har gjort i lärplattformen.</a></p>

    <p>Med vänlig hälsning, Evikomp projektgrupp</p>
    <p>Detta e-postmeddelande är automatgenererat.</p>
</body>
</html>
