<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Associations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Associations</h1>
    <a href="{{ route('associations.create') }}" class="btn btn-primary mb-3">Create New Association</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Logo</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Creation Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($associations as $association)
                <tr>
                    <td>
                        @if($association->logo_url)
                            <img src="{{ asset('storage/' . $association->logo_url) }}" alt="Logo" width="50" height="50" class="rounded-circle">
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $association->name }}</td>
                    <td>{{ $association->email }}</td>
                    <td>{{ $association->phone }}</td>
                    <td>{{ $association->address }}</td>
                    <td>{{ $association->creation_date->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('associations.edit', $association) }}" class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('associations.destroy', $association) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this association?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
