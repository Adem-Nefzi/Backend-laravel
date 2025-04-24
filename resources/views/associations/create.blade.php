<!-- resources/views/associations/create.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Create Association</title>
</head>
<body>
    <h1>Create Association</h1>

    <form method="POST" action="{{ route('associations.store') }}">
        @csrf

        <label>User:</label>
        <select name="user_id" required>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->full_name }}</option>
            @endforeach
        </select><br>

        <label>Description:</label>
        <textarea name="description" required></textarea><br>

        <label>Foundation Date:</label>
        <input type="date" name="foundation_date" required><br>

        <label>Documents (JSON string):</label>
        <input type="text" name="documents"><br>

        <label>Category:</label>
        <input type="text" name="category" required maxlength="100"><br>

        <button type="submit">Create</button>
    </form>

</body>
</html>
