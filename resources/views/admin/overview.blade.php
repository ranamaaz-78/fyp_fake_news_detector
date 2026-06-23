<x-layouts.admin active="overview" title="Overview">
    <div class="mb-8">
        <h1 class="text-headline-xl text-on-surface mb-1">System Overview</h1>
        <p class="text-body-md text-on-surface-variant">Monitor platform activity and prediction trends.</p>
    </div>

    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
        <x-fni.stat-card title="Total Checks" :value="number_format($stats['total_predictions'])" icon="analytics" />
        <x-fni.stat-card title="Registered Users" :value="number_format($stats['total_users'])" icon="group" />
        <x-fni.stat-card title="Real" :value="number_format($stats['real_count'])" accent="border-t-status-real" valueClass="text-status-real" />
        <x-fni.stat-card title="Fake" :value="number_format($stats['fake_count'])" accent="border-t-status-fake" valueClass="text-status-fake" />
        <x-fni.stat-card title="Uncertain" :value="number_format($stats['uncertain_count'])" accent="border-t-status-uncertain" valueClass="text-status-uncertain" />
    </div>

    <div class="fni-card overflow-hidden">
        <div class="px-6 py-4 border-b border-outline-variant flex items-center justify-between">
            <h2 class="text-headline-lg font-semibold">Recent Predictions</h2>
            <a href="{{ route('admin.predictions') }}" class="text-label-bold text-primary hover:underline">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-surface-container-low text-label-bold text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-3 text-left">User</th>
                        <th class="px-6 py-3 text-left">Result</th>
                        <th class="px-6 py-3 text-left">Confidence</th>
                        <th class="px-6 py-3 text-left">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @forelse($recent as $item)
                        <tr class="hover:bg-surface-container-low/40">
                            <td class="px-6 py-4 text-body-sm">{{ $item->user?->email ?? 'Guest' }}</td>
                            <td class="px-6 py-4"><x-fni.status-badge :label="$item->result" /></td>
                            <td class="px-6 py-4 text-label-bold">{{ number_format($item->confidence, 1) }}%</td>
                            <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $item->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-on-surface-variant">No predictions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
