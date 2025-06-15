<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'التطبيق'))</title>

    <!-- Tailwind CSS via CDN (يمكن استبداله بملف معدل محلي) -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen font-sans">

  <!-- شريط التنقل -->
  <nav class="bg-white shadow">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800">
        {{ config('app.name', 'لوحة التحكم') }}
      </a>

      @auth
        <div class="flex items-center space-x-4">
          <span class="text-gray-600">مرحبًا، {{ Auth::user()->username }}</span>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="text-sm text-gray-600 hover:text-gray-900">
              تسجيل خروج
            </button>
          </form>
        </div>
      @endauth
    </div>
  </nav>

  <!-- المحتوى الرئيسي -->
  <main class="container mx-auto px-4 py-6">
    @yield('content')
  </main>

</body>
</html>
