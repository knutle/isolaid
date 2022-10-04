<html lang="en">
    <body>
        <h1>Isolaid Index</h1>
        <p>This page lists all routes that are available to preview using Isolaid.</p>

        <ul>
            @foreach($routes as ['uri' => $uri, 'name' => $name, 'description' => $description])
                <li><a href="{{ $uri }}" title="{{ $description }}">{{ $name }} ({{ class_basename($description) }})</a></li>
            @endforeach </ul>
    </body>
</html>
