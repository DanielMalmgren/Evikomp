@extends('layouts.app')

@section('title', __('Lista användare'))

@section('content')

    <H1>Användare</H1>

    @can('Manage all users')
        <div class="card">
            <div class="card-body">
                <label for="edit_user">@lang('Sök efter användare')</label>
                <select class="edit_user" name="edit_user[]"></select>
            </div>
        </div>
    @endcan

    <br>

    @if(count($workplaces) > 1)
        <script type="text/javascript">
            $(function() {
                $('#workplace').change(function(){
                    var selectedValue = $(this).val();
                    $('#userlist li').each(function(){
                        if ($(this).find("small").text() == selectedValue) {
                            $(this).css("cssText", "");
                        } else {
                            $(this).css("cssText", "display: none !important;");
                        }
                    });
                });
                $("#workplace").change();
            });
        </script>

        <select class="custom-select d-block w-100" id="workplace" name="workplace" required="">
            @foreach($workplaces as $workplace)
                <option value="{{$workplace->name}}">{{$workplace->name}}</option>
            @endforeach
        </select>
    @endif

    @if(count($users) > 0)
        <ul class="list-group mb-3" id="userlist">
            @foreach($users as $user)
                <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div>
                    <a href="/settings/{{$user->id}}">
                        <h6 class="my-0">{{$user->name}}</h6>
                    </a>
                    @if($user->workplace)
                        <small class="text-muted">{{$user->workplace->name}}</small>
                    @endif
                    </div>
                    <span class="text-muted">{{$user->email}}</span>
                </li>
            @endforeach
        </ul>

        {{--<a href="/exportusers" class="btn btn-primary">@lang('Hämta som Excel-fil')</a>--}}
    @endif

    <a href="/users/create" class="btn btn-primary">@lang('Skapa användare manuellt')</a>

    <link href="/select2/select2.min.css" rel="stylesheet" />
    <link href="/select2/select2-bootstrap4.min.css" rel="stylesheet" />
    <script src="/select2/select2.min.js"></script>
    <script src="/select2/i18n/sv.js"></script>

    <script type="text/javascript">
        $('.edit_user').select2({
            width: '100%',
            ajax: {
                url: '/select2users',
                dataType: 'json'
            },
            language: "sv",
            minimumInputLength: 3,
            theme: "bootstrap4"
        });

        $('.edit_user').on('select2:select', function (e) {
            var userid = e.params.data.id;
            window.location='/users/' + userid;
        });
    </script>

@endsection
