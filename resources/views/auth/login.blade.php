@extends("template.partials.login")

@section("content")
    <form method="POST" action="{{ route("login") }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Username</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old("name") }}" required
                autofocus>
            @error("name")
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
            @error("password")
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
            <label class="form-check-label" for="remember_me">Remember me</label>
        </div>

        <div>
            <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0">Login</button>
            <a href="{{ route("password.request") }}" class="d-block mt-3 text-muted">Forgot your password?</a>
        </div>
    </form>
@endsection
