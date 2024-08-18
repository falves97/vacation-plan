<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h1>Holiday Plan of {{ $holidayPlan->owner->name }}</h1>
<h2>{{ $holidayPlan->title }}</h2>
<p>{{ $holidayPlan->description }}</p>
<p>{{ $holidayPlan->date->format('Y-m-d') }}</p>
<p>{{ $holidayPlan->location }}</p>

<h2>Participants</h2>
@foreach($holidayPlan->participants as $participant)
    <p>{{ $participant->name }}</p>
    <p>{{ $participant->email }}</p>
@endforeach
</body>
</html>
