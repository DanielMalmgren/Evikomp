@extends('errors::layout')

@section('title', 'HTTP 404')

@section('message', __($exception->getMessage() ?: __('Sidan du försöker nå existerar inte. Vänligen försök med en annan sida!')))
