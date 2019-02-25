@extends('layouts.app')

@section('title', __('Utloggning'))

@section('content')

<script type="text/javascript">
    $(function() {
        $.ajax({
            url: 'https://idp.itsam.se/wa/logout',
            dataType:"text/plain",
            type: 'GET'
        });
    });
</script>

<h1>@lang('Du är nu utloggad')</h1>

@endsection
