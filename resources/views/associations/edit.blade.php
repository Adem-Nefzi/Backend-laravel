<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Association</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Edit Association</h1>
    <a href="{{ route('associations.index') }}" class="btn btn-secondary mb-3">Back to List</a>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('associations.update', $association) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Name*</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $association->name) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email*</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $association->email) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $association->phone) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="{{ old('address', $association->address) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $association->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Creation Date*</label>
            <input type="date" name="creation_date" class="form-control" value="{{ old('creation_date', $association->creation_date->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Logo (Upload New if Needed)</label>
            <input type="file" name="logo_url" class="form-control">
            @if($association->logo_url)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $association->logo_url) }}" alt="Current Logo" width="70" height="70" class="rounded">
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Update Association</button>
    </form>
</div>
</body>
</html>
