<x-layouts.admin active="users" title="User Management">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-headline-xl">User Management</h1>
            <p class="text-on-surface-variant mt-1">Search, review, and manage registered accounts.</p>
        </div>
        <form method="GET" class="flex gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-base">search</span>
                <input type="search" name="search" value="{{ $search }}" placeholder="Search users..." class="fni-input pl-10 py-2 text-body-sm" />
            </div>
            <button type="submit" class="fni-btn-primary !py-2 !px-4 text-sm">Search</button>
        </form>
    </div>

    <div class="fni-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-surface-container-low text-label-bold text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-4 text-left">Username</th>
                        <th class="px-6 py-4 text-left">Email</th>
                        <th class="px-6 py-4 text-left">Checks Performed</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @foreach($users as $user)
                        <tr class="hover:bg-surface-container-low/40">
                            <td class="px-6 py-4 font-label-bold">{{ $user->username }}</td>
                            <td class="px-6 py-4 text-body-sm">{{ $user->email }}</td>
                            <td class="px-6 py-4">{{ $user->predictions_count }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-label-caps font-medium {{ $user->is_active ? 'bg-status-real-light text-status-real' : 'bg-status-fake-light text-status-fake' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-primary text-label-bold hover:underline">
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $users->withQueryString()->links() }}</div>
</x-layouts.admin>
