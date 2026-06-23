<x-layouts.admin active="audit" title="Audit Log">
    <div class="mb-8">
        <h1 class="text-headline-xl">Audit Log</h1>
        <p class="text-on-surface-variant mt-1">Track administrative actions across the system.</p>
    </div>

    <div class="fni-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-surface-container-low text-label-bold text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-4 text-left">Time</th>
                        <th class="px-6 py-4 text-left">Admin</th>
                        <th class="px-6 py-4 text-left">Action</th>
                        <th class="px-6 py-4 text-left">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @forelse($logs as $log)
                        <tr class="hover:bg-surface-container-low/40">
                            <td class="px-6 py-4 whitespace-nowrap text-body-sm text-on-surface-variant">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-6 py-4 text-body-sm">{{ $log->user?->email ?? 'System' }}</td>
                            <td class="px-6 py-4 text-body-sm font-mono">{{ $log->action }}</td>
                            <td class="px-6 py-4 text-body-sm text-on-surface-variant max-w-md truncate">{{ json_encode($log->metadata) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-on-surface-variant">No audit entries yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
</x-layouts.admin>
