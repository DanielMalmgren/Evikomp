@extends('errors::layout')

@section('title', 'HTTP 403')

@section('message', __($exception->getMessage() ?: __('Du har inte behörighet till denna sida!')))
