<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 10px;
            background-color: #f9f9f9;
        }
        h1, h2 {
            color: #333;
        }
        p {
            line-height: 1.5;
        }
        .child-info {
            margin-left: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #fff;
        }
    </style>
</head>
<body>
    <h1>Event Details</h1>
    <p><strong>Member Name:</strong> {{ $event->member_name }}</p>
    <p><strong>ACC:</strong> {{ $event->acc }}</p>
    <p><strong>Home Address:</strong> {{ $event->home_address }}</p>
    <p><strong>Email:</strong> {{ $event->email }}</p>
    <p><strong>Phone:</strong> {{ $event->phone }}</p>
    <p><strong>Location:</strong> {{ $event->location }}</p>

    <h2>Children:</h2>
    @if ($event->children->isEmpty())
        <p>No children registered for this event.</p>
    @else
        @foreach ($event->children as $child)
            <div class="child-info">
                <p><strong>Child's Name:</strong> {{ $child->child_name }}</p>
                <p><strong>Gender:</strong> {{ $child->gender }}</p>
                <p><strong>Age:</strong> {{ $child->age }}</p>
            </div>
        @endforeach
    @endif
</body>
</html>
