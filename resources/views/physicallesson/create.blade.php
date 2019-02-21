@extends('layouts.app')

@section('content')

<script type="text/javascript">
    $(function() {
        $('#workplace').change(function(){
            var selectedValue = $(this).val();
            $("#users").load("/physicallessonajax/" + selectedValue);
        });
    });
</script>

<div class="col-md-5 mb-3">

    <H1>Lägg till lektionstillfälle</H1>

    <select class="custom-select d-block w-100" id="workplace" name="workplace" required="">
        <option disabled selected>Välj arbetsplats...</option>
        @foreach($workplaces as $workplace)
            <option value="{{$workplace->id}}">{{$workplace->name}}</option>
        @endforeach
    </select>

    <br>

    <div id="users"></div>

</div>

@endsection
