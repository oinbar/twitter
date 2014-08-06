@extends('master')

@section('main-content')


<p>This application represents the final assignment for the Harvard Ext. Comp Sci-15 course, dynamic web apps.
It is also part of a much larger personal project which I plan to keep working on in the coming months.</p>

<p>In its current form the application represents a twitter feed manager, where users can set up their own feeds
and collect ongoing tweets.  In its next iterations, the software will also provide real time alerting and analytics 
services based on the tweet data collected.</p>

<p>Some of the backend features the application uses are: a MySQL database to store basic information about users and feeds,
A MongoDB database to store raw tweet data, integration with the twitter api, an iron.io push queue to handle twitter calls,
basic route filtering and form validation.</p>

@stop