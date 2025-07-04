<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Impersonation - SurfsUp Admin</title>
    <meta name="description" content="Admin panel for user impersonation">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('/img/favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://kit.fontawesome.com/d251d3e9b0.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-900 text-white antialiased min-h-screen flex flex-col" style="background-image: url('{{ asset('img/surfsup-hero.png') }}'); background-size: cover; background-position: center; background-attachment: fixed;">
    @include('partials.header')

    <main class="container mx-auto px-4 py-8 flex-grow bg-white/60 backdrop-blur-sm rounded-lg shadow-xl mt-20 mb-4">
        @if(session('success'))
            <div class="bg-green-600 text-white px-4 py-3 rounded-md mb-4 mt-16">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-600 text-white px-4 py-3 rounded-md mb-4 mt-16">
                {{ session('error') }}
            </div>
        @endif

        <div class="mt-16 mb-8">
            <h1 class="text-4xl font-bold text-center text-gray-800 mb-4">User Impersonation</h1>
            <p class="text-center text-gray-600 mb-8">Select a user to impersonate from the list below. This allows you to see the site from their perspective.</p>
            
            @if(Session::has('impersonator_id'))
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-user-secret mr-2"></i>
                            <span class="font-medium">You are currently impersonating: {{ auth()->user()->name }}</span>
                        </div>
                        <form method="POST" action="{{ route('admin.impersonate.stop') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                                <i class="fas fa-stop mr-1"></i>
                                Stop Impersonating
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Search and Filter -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <input type="text" id="userSearch" placeholder="Search users..." 
                           class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <select id="roleFilter" class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="text-gray-400 text-sm">
                    Total: {{ $users->total() }} users
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Roles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Last Login</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800 divide-y divide-gray-700" id="usersTableBody">
                        @foreach($users as $user)
                            <tr class="user-row hover:bg-gray-700 transition-colors" 
                                data-name="{{ strtolower($user->name) }}" 
                                data-email="{{ strtolower($user->email ?? '') }}"
                                data-roles="{{ strtolower($user->roles->pluck('name')->implode(' ')) }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($user->avatar)
                                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full mr-3">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-medium mr-3">
                                                {{ $user->initials() }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                                            @if($user->steam_id)
                                                <div class="text-sm text-gray-400">Steam ID: {{ $user->steam_id }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-300">{{ $user->email ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($user->roles as $role)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $role->name === 'admin' ? 'bg-red-100 text-red-800' : 
                                                   ($role->name === 'staff' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @empty
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                User
                                            </span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $user->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if(auth()->id() !== $user->id)
                                        <form method="POST" action="{{ route('admin.impersonate.start', $user) }}" class="inline" 
                                              onsubmit="return confirm('Are you sure you want to impersonate {{ addslashes($user->name) }}?');">
                                            @csrf
                                            <button type="submit" 
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-medium transition-colors duration-200">
                                                <i class="fas fa-user-secret mr-1"></i>
                                                Impersonate
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-500 text-xs">Current User</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        @endif

        <!-- Back to Admin -->
        <div class="mt-8 text-center">
            <a href="/roadmap" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Roadmap
            </a>
        </div>
    </main>

    @include('partials.footer')

    <script>
        // Search and filter functionality
        document.getElementById('userSearch').addEventListener('input', filterUsers);
        document.getElementById('roleFilter').addEventListener('change', filterUsers);

        function filterUsers() {
            const searchTerm = document.getElementById('userSearch').value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter').value.toLowerCase();
            const rows = document.querySelectorAll('.user-row');

            rows.forEach(row => {
                const name = row.dataset.name;
                const email = row.dataset.email;
                const roles = row.dataset.roles;

                const matchesSearch = !searchTerm || 
                    name.includes(searchTerm) || 
                    email.includes(searchTerm);

                const matchesRole = !roleFilter || 
                    roles.includes(roleFilter) || 
                    (roleFilter === 'user' && roles === '');

                if (matchesSearch && matchesRole) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>