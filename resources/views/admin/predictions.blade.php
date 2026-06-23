<x-layouts.admin active="predictions" title="Prediction Logs">
    <div class="mb-8">
        <h1 class="text-headline-xl">Prediction Logs</h1>
        <p class="text-on-surface-variant mt-1">Filter and review all system predictions.</p>
    </div>

    <form method="GET" class="fni-card p-4 mb-6 flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-label-caps text-on-surface-variant mb-1">Result</label>
            <select name="result" class="fni-input py-2 text-body-sm w-40">
                <option value="">All</option>
                @foreach(['REAL', 'FAKE', 'UNCERTAIN'] as $r)
                    <option value="{{ $r }}" @selected(request('result') === $r)>{{ $r }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-label-caps text-on-surface-variant mb-1">From</label>
            <input type="date" name="from" value="{{ request('from') }}" class="fni-input py-2 text-body-sm" />
        </div>
        <div>
            <label class="block text-label-caps text-on-surface-variant mb-1">To</label>
            <input type="date" name="to" value="{{ request('to') }}" class="fni-input py-2 text-body-sm" />
        </div>
        <button type="submit" class="fni-btn-primary !py-2 !px-4 text-sm">Filter</button>
    </form>

    <div class="fni-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-surface-container-low text-label-bold text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-4 text-left">User</th>
                        <th class="px-6 py-4 text-left">Preview</th>
                        <th class="px-6 py-4 text-left">Result</th>
                        <th class="px-6 py-4 text-left">Model</th>
                        <th class="px-6 py-4 text-left">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @foreach($predictions as $prediction)
                        <tr class="hover:bg-surface-container-low/40">
                            <td class="px-6 py-4 text-body-sm">{{ $prediction->user?->email ?? 'Guest' }}</td>
                            <td class="px-6 py-4 max-w-xs truncate text-body-sm">{{ $prediction->preview(80) }}</td>
                            <td class="px-6 py-4"><x-fni.status-badge :label="$prediction->result" /></td>
                            <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $prediction->model_used }}</td>
                            <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $prediction->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $predictions->withQueryString()->links() }}</div>
</x-layouts.admin>
