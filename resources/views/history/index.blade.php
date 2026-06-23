@extends('layouts.fni')

@section('title', 'Your History | FNI')

@section('content')
<div class="max-w-container-max mx-auto px-4 md:px-gutter">
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-stack-lg">
        <div>
            <h1 class="text-headline-xl text-on-surface">Your History</h1>
            <p class="text-on-surface-variant mt-1">Review and manage your past AI verification results.</p>
        </div>
        <a href="{{ route('home') }}" class="fni-btn-container inline-flex">
            <span class="material-symbols-outlined">add_circle</span>
            Check new news
        </a>
    </header>

    <section class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-stack-lg">
        <x-fni.stat-card title="Total checks" :value="$stats['total']" />
        <x-fni.stat-card title="Marked Real" :value="$stats['real']" accent="border-t-status-real" valueClass="text-status-real" />
        <x-fni.stat-card title="Marked Fake" :value="$stats['fake']" accent="border-t-status-fake" valueClass="text-status-fake" />
        <x-fni.stat-card title="Uncertain" :value="$stats['uncertain']" accent="border-t-status-uncertain" valueClass="text-status-uncertain" />
    </section>

    @if($predictions->isEmpty())
        <div class="fni-card p-12 text-center animate-reveal">
            <span class="material-symbols-outlined text-5xl text-outline mb-4">history</span>
            <p class="text-body-lg text-on-surface-variant mb-4">No predictions yet.</p>
            <a href="{{ route('home') }}" class="text-primary font-label-bold hover:underline">Check your first article</a>
        </div>
    @else
        <div class="fni-card overflow-hidden animate-reveal">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-surface-container-low border-b border-outline-variant">
                        <tr>
                            <th class="px-4 py-4 text-left text-label-bold text-on-surface-variant">Date</th>
                            <th class="px-4 py-4 text-left text-label-bold text-on-surface-variant">Preview</th>
                            <th class="px-4 py-4 text-left text-label-bold text-on-surface-variant">Result</th>
                            <th class="px-4 py-4 text-left text-label-bold text-on-surface-variant">Confidence</th>
                            <th class="px-4 py-4 text-right text-label-bold text-on-surface-variant">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30">
                        @foreach($predictions as $prediction)
                            <tr class="hover:bg-surface-container-low/50 transition-colors">
                                <td class="px-4 py-4 whitespace-nowrap text-body-sm text-on-surface-variant">{{ $prediction->created_at->format('M j, Y H:i') }}</td>
                                <td class="px-4 py-4 max-w-xs truncate text-body-md">{{ $prediction->preview() }}</td>
                                <td class="px-4 py-4">
                                    <x-fni.status-badge :label="$prediction->result" />
                                </td>
                                <td class="px-4 py-4 text-label-bold">{{ number_format($prediction->confidence, 1) }}%</td>
                                <td class="px-4 py-4 text-right whitespace-nowrap space-x-3">
                                    <a href="{{ route('history.recheck', $prediction) }}" class="text-primary text-label-bold hover:underline">Recheck</a>
                                    <form method="POST" action="{{ route('history.destroy', $prediction) }}" class="inline" onsubmit="return confirm('Delete this entry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-error text-label-bold hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $predictions->links() }}</div>
    @endif
</div>
@endsection
