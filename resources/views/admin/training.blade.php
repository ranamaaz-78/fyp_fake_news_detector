<x-layouts.admin active="training" title="Model Training">
    <div
        class="mb-8"
        x-data="trainingPanel({
            pollUrl: @js(route('admin.training.status')),
            uploadUrl: @js(route('admin.training.upload')),
            startUrl: @js(route('admin.training.start')),
            csrf: @js(csrf_token()),
            initialActive: @js((bool) $activeJob),
            initialStatus: @js($activeJob?->status ?? 'idle'),
            initialProgress: @js($activeJob?->progress ?? 0),
            initialStage: @js($activeJob?->stage ?? 'Idle'),
            hasUploadReady: @js((bool) $uploadToken),
        })"
        x-init="init()"
    >
        <div class="mb-8">
            <h1 class="text-headline-xl text-on-surface mb-1">Model Training</h1>
            <p class="text-body-md text-on-surface-variant">Upload Kaggle-style Fake.csv + True.csv and retrain the ML model from the admin panel.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 fni-card p-4 border-l-4 border-status-real bg-status-real-light/30 text-body-sm">
                {{ session('status') }}
            </div>
        @endif

        @error('training')
            <div class="mb-6 fni-card p-4 border-l-4 border-error bg-red-50 text-body-sm text-error">
                {{ $message }}
            </div>
        @enderror

        <div class="grid xl:grid-cols-2 gap-6 mb-8">
            <div class="fni-card p-6">
                <h2 class="text-headline-lg font-semibold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">upload_file</span>
                    Upload Dataset
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-label-caps text-on-surface-variant mb-2">Fake.csv</label>
                        <input type="file" x-ref="fakeCsv" accept=".csv" class="block w-full text-body-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-fixed file:text-primary file:font-label-bold" />
                    </div>
                    <div>
                        <label class="block text-label-caps text-on-surface-variant mb-2">True.csv</label>
                        <input type="file" x-ref="trueCsv" accept=".csv" class="block w-full text-body-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-fixed file:text-primary file:font-label-bold" />
                    </div>
                    <p class="text-body-sm text-on-surface-variant">Files must be named exactly <code class="bg-surface-container-low px-1 rounded">Fake.csv</code> and <code class="bg-surface-container-low px-1 rounded">True.csv</code> with at least 100 rows each.</p>

                    <button
                        type="button"
                        @click="uploadFiles()"
                        class="fni-btn-primary text-sm"
                        :disabled="uploading"
                        x-bind:class="uploading ? 'opacity-50 cursor-not-allowed' : ''"
                    >
                        <span x-text="uploading ? 'Uploading...' : 'Upload Files'"></span>
                    </button>

                    <div x-show="uploading || uploadProgress > 0" x-cloak class="pt-2">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-label-caps text-on-surface-variant">Upload progress</p>
                            <p class="text-label-bold text-primary"><span x-text="uploadProgress"></span>%</p>
                        </div>
                        <div class="w-full h-2 bg-surface-container-low rounded-full overflow-hidden mb-2">
                            <div class="h-full bg-secondary transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
                        </div>
                        <p class="text-body-sm text-on-surface-variant" x-text="uploadStage"></p>
                    </div>

                    <template x-if="uploadError">
                        <p class="text-error text-body-sm" x-text="uploadError"></p>
                    </template>

                    <div x-show="uploadReady" x-cloak class="p-3 rounded-lg bg-trust-blue-light/40 border border-primary-fixed text-body-sm">
                        Files ready for training<span x-show="uploadToken"> (batch <span x-text="uploadToken.substring(0, 8)"></span>)</span>.
                    </div>
                </div>
            </div>

            <div class="fni-card p-6">
                <h2 class="text-headline-lg font-semibold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">play_circle</span>
                    Start Training
                </h2>
                <p class="text-body-sm text-on-surface-variant mb-4">
                    Training runs in the background (typically 5–15 minutes). LR, Naive Bayes, and SVM are compared; the best model is saved automatically.
                </p>
                <button
                    type="button"
                    @click="startTraining()"
                    class="fni-btn-primary text-sm w-full sm:w-auto"
                    :disabled="!uploadReady || starting || (polling && ['queued','running'].includes(status))"
                    x-bind:class="(!uploadReady || starting || (polling && ['queued','running'].includes(status))) ? 'opacity-50 cursor-not-allowed' : ''"
                >
                    <span x-text="starting ? 'Starting...' : 'Start Training'"></span>
                </button>
                <p x-show="!uploadReady" class="text-body-sm text-on-surface-variant mt-3">Upload both CSV files first.</p>
            </div>
        </div>

        <div class="fni-card p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-headline-lg font-semibold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">hourglass_top</span>
                    Training Progress
                </h2>
                <span class="text-label-caps px-3 py-1 rounded-full capitalize"
                      :class="{
                          'bg-trust-blue-light text-trust-blue-dark': ['queued','running'].includes(status),
                          'bg-status-real-light text-status-real': status === 'completed',
                          'bg-red-100 text-error': status === 'failed',
                          'bg-surface-container-low text-on-surface-variant': status === 'idle',
                      }"
                      x-text="status">
                </span>
            </div>

            <div class="w-full h-3 bg-surface-container-low rounded-full overflow-hidden mb-3">
                <div class="h-full bg-primary transition-all duration-500" :style="`width: ${progress}%`"></div>
            </div>
            <p class="text-body-sm text-on-surface-variant mb-1"><span x-text="progress"></span>% — <span x-text="stage"></span></p>
            <template x-if="metrics">
                <div class="mt-4 p-4 rounded-lg bg-surface-container-low text-body-sm space-y-1">
                    <p><strong>Best model:</strong> <span x-text="metrics.best_model"></span></p>
                    <p><strong>Accuracy:</strong> <span x-text="(metrics.accuracy * 100).toFixed(2)"></span>%</p>
                    <p><strong>F1 score:</strong> <span x-text="metrics.f1_weighted?.toFixed(4)"></span></p>
                </div>
            </template>
            <template x-if="error">
                <p class="mt-4 text-error text-body-sm" x-text="error"></p>
            </template>
        </div>

        <div class="fni-card overflow-hidden">
            <div class="px-6 py-4 border-b border-outline-variant">
                <h2 class="text-headline-lg font-semibold">Recent Training Jobs</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-surface-container-low text-label-bold text-on-surface-variant">
                        <tr>
                            <th class="px-6 py-3 text-left">Started by</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Progress</th>
                            <th class="px-6 py-3 text-left">Best model</th>
                            <th class="px-6 py-3 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30">
                        @forelse($history as $job)
                            <tr class="hover:bg-surface-container-low/40">
                                <td class="px-6 py-4 text-body-sm">{{ $job->starter?->email ?? '—' }}</td>
                                <td class="px-6 py-4 capitalize text-body-sm">{{ $job->status }}</td>
                                <td class="px-6 py-4 text-body-sm">{{ $job->progress }}%</td>
                                <td class="px-6 py-4 text-body-sm">{{ $job->metrics['best_model'] ?? '—' }}</td>
                                <td class="px-6 py-4 text-body-sm text-on-surface-variant">{{ $job->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">No training jobs yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function trainingPanel(config) {
            return {
                pollUrl: config.pollUrl,
                uploadUrl: config.uploadUrl,
                startUrl: config.startUrl,
                csrf: config.csrf,
                polling: false,
                timer: null,
                status: config.initialStatus,
                progress: config.initialProgress,
                stage: config.initialStage,
                metrics: null,
                error: null,
                uploading: false,
                uploadProgress: 0,
                uploadStage: '',
                uploadError: null,
                uploadReady: config.hasUploadReady,
                uploadToken: null,
                starting: false,
                init() {
                    if (config.initialActive || ['queued', 'running'].includes(this.status)) {
                        this.startPolling();
                    }
                },
                uploadFiles() {
                    const fakeFile = this.$refs.fakeCsv?.files?.[0];
                    const trueFile = this.$refs.trueCsv?.files?.[0];
                    this.uploadError = null;

                    if (!fakeFile || !trueFile) {
                        this.uploadError = 'Select both Fake.csv and True.csv.';
                        return;
                    }

                    if (fakeFile.name.toLowerCase() !== 'fake.csv') {
                        this.uploadError = 'First file must be named Fake.csv.';
                        return;
                    }

                    if (trueFile.name.toLowerCase() !== 'true.csv') {
                        this.uploadError = 'Second file must be named True.csv.';
                        return;
                    }

                    const formData = new FormData();
                    formData.append('fake_csv', fakeFile);
                    formData.append('true_csv', trueFile);

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', this.uploadUrl, true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', this.csrf);
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                    this.uploading = true;
                    this.uploadProgress = 0;
                    this.uploadStage = 'Uploading files to server...';

                    xhr.upload.addEventListener('progress', (event) => {
                        if (event.lengthComputable) {
                            this.uploadProgress = Math.round((event.loaded / event.total) * 100);
                            this.uploadStage = `Uploading files... ${this.uploadProgress}%`;
                        }
                    });

                    xhr.addEventListener('load', () => {
                        this.uploading = false;

                        if (xhr.status >= 200 && xhr.status < 300) {
                            const data = JSON.parse(xhr.responseText);
                            this.uploadProgress = 100;
                            this.uploadStage = data.message || 'Upload complete.';
                            this.uploadReady = true;
                            this.uploadToken = data.token || null;
                            return;
                        }

                        this.uploadProgress = 0;
                        try {
                            const data = JSON.parse(xhr.responseText);
                            const messages = data.errors ? Object.values(data.errors).flat() : [data.message];
                            this.uploadError = messages.join(' ');
                        } catch (e) {
                            this.uploadError = 'Upload failed. Check file size and format.';
                        }
                        this.uploadStage = '';
                    });

                    xhr.addEventListener('error', () => {
                        this.uploading = false;
                        this.uploadProgress = 0;
                        this.uploadStage = '';
                        this.uploadError = 'Network error during upload.';
                    });

                    xhr.send(formData);
                },
                async startTraining() {
                    this.starting = true;
                    this.error = null;

                    try {
                        const response = await fetch(this.startUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': this.csrf,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            this.error = data.message || 'Could not start training.';
                            return;
                        }

                        this.status = data.status || 'queued';
                        this.progress = 0;
                        this.stage = 'Queued — starting ML worker...';
                        this.uploadReady = false;
                        this.startPolling();
                    } catch (e) {
                        this.error = 'Could not reach server to start training.';
                    } finally {
                        this.starting = false;
                    }
                },
                startPolling() {
                    if (this.timer) return;
                    this.polling = true;
                    this.fetchStatus();
                    this.timer = setInterval(() => this.fetchStatus(), 2000);
                },
                stopPolling() {
                    this.polling = false;
                    if (this.timer) {
                        clearInterval(this.timer);
                        this.timer = null;
                    }
                },
                async fetchStatus() {
                    try {
                        const response = await fetch(this.pollUrl, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        const data = await response.json();
                        this.status = data.status ?? 'idle';
                        this.progress = data.progress ?? 0;
                        this.stage = data.stage ?? '';
                        this.metrics = data.metrics ?? null;
                        this.error = data.error ?? null;

                        if (['completed', 'failed'].includes(this.status)) {
                            this.stopPolling();
                        }
                    } catch (e) {
                        this.error = 'Could not reach training status endpoint.';
                    }
                },
            };
        }
    </script>
    @endpush
</x-layouts.admin>
