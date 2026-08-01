<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - TaskFlow</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3525cd',
                        background: '#f8f9ff',
                        surface: '#ffffff',
                        secondary: '#565e74',
                        outline: '#c7c4d8',
                        error: '#ba1a1a'
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-background font-sans text-slate-950 flex items-center justify-center px-4">
    <main class="w-full max-w-md">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 w-12 h-12 rounded-xl bg-primary text-white flex items-center justify-center">
                <span class="material-symbols-outlined">rocket_launch</span>
            </div>
            <h1 class="text-3xl font-bold">Welcome back</h1>
            <p class="text-secondary mt-2">Login to manage your tasks and projects.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="bg-surface border border-outline rounded-2xl p-6 shadow-sm space-y-5">
            @csrf

            @if(session('status'))
                <div class="rounded-xl bg-green-50 text-green-700 px-4 py-3 text-sm">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="rounded-xl bg-red-50 text-error px-4 py-3 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label for="email" class="block text-sm font-semibold mb-2">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                       class="w-full rounded-xl border-outline bg-slate-50 focus:border-primary focus:ring-primary">
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold mb-2">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                       class="w-full rounded-xl border-outline bg-slate-50 focus:border-primary focus:ring-primary">
            </div>

            <label class="flex items-center gap-2 text-sm text-secondary">
                <input type="checkbox" name="remember" class="rounded border-outline text-primary focus:ring-primary">
                Remember me
            </label>

            <button type="submit" class="w-full rounded-xl bg-primary text-white font-semibold py-3 hover:bg-[#2418a8] active:scale-[0.99] transition">
                Login
            </button>

            <p class="text-center text-sm text-secondary">
                No account yet?
                <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline">Create one</a>
            </p>
        </form>
    </main>
</body>
</html>
