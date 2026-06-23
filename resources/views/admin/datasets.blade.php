<x-layouts.admin active="datasets" title="Dataset Management">
    <div class="mb-8" x-data="datasetUploader({
        uploadUrl: @js(route('admin.datasets.store')),
        csrf: @js(csrf_token()),
    })">
        <h1 class="text-headline-xl">Dataset Management</h1>
        <p class="text-on-surface-variant mt-1">Upload training data for manual model retraining.</p>

        <div class="fni-card p-6 mb-8 max-w-2xl mt-6">
            <h2 class="text-headline-lg font-semibold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">upload_file</span>
                Upload training dataset (CSV)
            </h2>

            <div class="space-y-4">
                <input type="file" x-ref="datasetFile" accept=".csv,.txt" class="block w-full text-body-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-fixed file:text-primary file:font-label-bold" />
                <textarea x-ref="notes" rows="2" placeholder="Optional notes..." class="fni-input text-body-sm"></textarea>

                <button
                    type="button"
                    @click="upload()"
                    class="fni-btn-primary text-sm"
                    :disabled="uploading"
                    x-bind:class="uploading ? 'opacity-50 cursor-not-allowed' : ''"
                >
                    <span x-text="uploading ? 'Uploading...' : 'Upload dataset'"></span>
                </button>

                <div x-show="uploading || progress > 0" x-cloak>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-label-caps text-on-surface-variant">Upload progress</p>
                        <p class="text-label-bold text-primary"><span x-text="progress"></span>%</p>
                    </div>
                    <div class="w-full h-2 bg-surface-container-low rounded-full overflow-hidden mb-2">
                        <div class="h-full bg-secondary transition-all duration-300" :style="`width: ${progress}%`"></div>
                    </div>
                    <p class="text-body-sm text-on-surface-variant" x-text="stage"></p>
                </div>

                <template x-if="error">
                    <p class="text-error text-body-sm" x-text="error"></p>
                </template>

                <template x-if="success">
                    <p class="text-status-real text-body-sm" x-text="success"></p>
                </template>

                <p class="text-body-sm text-on-surface-variant">After upload, retrain from <a href="{{ route('admin.training') }}" class="text-primary hover:underline">Model Training</a> or run <code class="bg-surface-container-low px-1 rounded">python ml/scripts/train.py</code></p>
            </div>
        </div>

        <div class="fni-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-surface-container-low text-label-bold text-on-surface-variant">
                        <tr>
                            <th class="px-6 py-4 text-left">File</th>
                            <th class="px-6 py-4 text-left">Rows</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-left">Uploaded by</th>
                            <th class="px-6 py-4 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30">
                        @forelse($datasets as $dataset)
                            <tr class="hover:bg-surface-container-low/40">
                                <td class="px-6 py-4 font-label-bold">{{ $dataset->original_name }}</td>
                                <td class="px-6 py-4">{{ number_format($dataset->row_count) }}</td>
                                <td class="px-6 py-4 capitalize">
                                    <span class="px-2 py-0.5 rounded-full text-label-caps {{ $dataset->status === 'processed' ? 'bg-status-real-light text-status-real' : 'bg-trust-blue-light text-trust-blue-dark' }}">
                                        {{ $dataset->status === 'processed' ? 'ready' : $dataset->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-body-sm">{{ $dataset->uploader?->email ?? '—' }}</td>
                                <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $dataset->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">No datasets uploaded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $datasets->links() }}</div>
    </div>

    @push('scripts')
    <script>
        function datasetUploader(config) {
            return {
                uploadUrl: config.uploadUrl,
                csrf: config.csrf,
                uploading: false,
                progress: 0,
                stage: '',
                error: null,
                success: null,
                upload() {
                    const file = this.$refs.datasetFile?.files?.[0];
                    this.error = null;
                    this.success = null;

                    if (!file) {
                        this.error = 'Select a CSV file first.';
                        return;
                    }

                    const formData = new FormData();
                    formData.append('dataset', file);
                    formData.append('notes', this.$refs.notes?.value || '');

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', this.uploadUrl, true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', this.csrf);
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                    this.uploading = true;
                    this.progress = 0;
                    this.stage = 'Uploading file...';

                    xhr.upload.addEventListener('progress', (event) => {
                        if (event.lengthComputable) {
                            this.progress = Math.round((event.loaded / event.total) * 100);
                            this.stage = `Uploading... ${this.progress}%`;
                        }
                    });

                    xhr.addEventListener('load', () => {
                        this.uploading = false;

                        if (xhr.status >= 200 && xhr.status < 300) {
                            const data = JSON.parse(xhr.responseText);
                            this.progress = 100;
                            this.stage = 'Processing complete.';
                            this.success = data.message || 'Upload complete.';
                            setTimeout(() => window.location.reload(), 800);
                            return;
                        }

                        this.progress = 0;
                        this.stage = '';
                        try {
                            const data = JSON.parse(xhr.responseText);
                            const messages = data.errors ? Object.values(data.errors).flat() : [data.message];
                            this.error = messages.join(' ');
                        } catch (e) {
                            this.error = 'Upload failed.';
                        }
                    });

                    xhr.addEventListener('error', () => {
                        this.uploading = false;
                        this.progress = 0;
                        this.stage = '';
                        this.error = 'Network error during upload.';
                    });

                    xhr.send(formData);
                },
            };
        }
    </script>
    @endpush
</x-layouts.admin>
