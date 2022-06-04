<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>

    </head>
    <body class="antialiased">
        <div>Create new post</div>
        <form action="/post" method="post">
            @csrf
            <input type="text" name="title" placeholder="Type your name"><br>
            <input type="text" name="description" placeholder="Type description"><br>
            <input type="submit">
        </form>
    </body>
</html>
