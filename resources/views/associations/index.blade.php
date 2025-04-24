<!-- resources/views/associations/index.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Associations</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; }
    </style>
</head>
<body>
    <h1>Associations</h1>
    <a href="{{ route('associations.create') }}">Create New</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Description</th>
                <th>Foundation Date</th>
                <th>Status</th>
                <th>Category</th>
            </tr>
        </thead>
        <tbody>
            @foreach($associations as $association)
    <tr>
        <td>{{ $association->id }}</td>
        <td>{{ optional($association->user)->first_name ?? 'N/A' }}</td>
        <td>{{ Str::limit($association->description, 50) }}</td>
        <td>{{ $association->foundation_date->format('Y-m-d') }}</td>
        <td>{{ ucfirst($association->verification_status) }}</td>
        <td>{{ $association->category }}</td>
        <td>
            <a href="{{ route('associations.edit', $association->id) }}">Edit</a>
            <form action="{{ route('associations.destroy', $association->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit">Delete</button>
            </form>
        </td>
    </tr>
@endforeach
        </tbody>
    </table>
</body>
</html>
