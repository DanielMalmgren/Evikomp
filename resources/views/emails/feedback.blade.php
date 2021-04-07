<html>
<head></head>
<body>
@if($lesson != 'null')
    <p>Avser modul: {{$lesson}}</p>
@endif
<p>{{$content}}</p>
<hr>
<p>Detta meddelande har skickats via feedback-funktionen i Evikomps webbplattform. Nedan följer information om den som skickat<p>
<p>Namn: {{$name}}</p>
<p>E-post: {{$email}}</p>
<p>Mobilnummer: {{$mobile}}</p>
<p>Arbetsplats: {{$workplace}}</p>
@if($contacted)
    Personen önskar bli kontaktad gällande detta!
@endif
</body>
</html>
