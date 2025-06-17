<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Welcome to Laravel</h1>
    <p>This is a simple Laravel application.</p>
    {{-- <p>Current time: {{ now() }}</p> --}}
    <p>Current date: {{ now()->toDateString() }}</p>
</body>
</html>