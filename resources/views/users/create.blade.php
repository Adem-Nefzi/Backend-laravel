<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New User</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9fafb;
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 600px;
            padding: 30px 40px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 15px;
        }

        textarea {
            resize: vertical;
        }

        .error {
            background: #ffecec;
            border: 1px solid #ff5a5f;
            color: #d8000c;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .error ul {
            margin: 0;
            padding-left: 20px;
        }

        button {
            background-color: #4f46e5;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New User</h1>

        @if($errors->any())
            <div class="error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="form-group">
                <label>First Name:</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" required>
            </div>

            <div class="form-group">
                <label>Last Name:</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" required>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password_hash" required minlength="8">
            </div>

            <div class="form-group">
                <label>Phone:</label>
                <input type="text" name="phone" value="{{ old('phone') }}">
            </div>

            <div class="form-group">
                <label>Address:</label>
                <textarea name="address">{{ old('address') }}</textarea>
            </div>

            <div class="form-group">
                <label>User Type:</label>
                <select name="user_type" required>
                    <option value="">-- Select --</option>
                    <option value="donor" {{ old('user_type') == 'donor' ? 'selected' : '' }}>Donor</option>
                    <option value="recipient" {{ old('user_type') == 'recipient' ? 'selected' : '' }}>Recipient</option>
                    <option value="admin" {{ old('user_type') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <button type="submit">Add User</button>
        </form>
    </div>
</body>
</html>
