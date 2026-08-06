// VeriFact AI - Interactive Verification Engine
//
// Everything shown here comes from the server response. The browser used to
// compute its own "fact-check cross-reference" score from a hardcoded topic
// list, which meant the number on screen was invented. Scoring, pattern
// detection and fact checking now all happen in the ML service so the inline
// panel and the standalone result pages describe the same analysis.

document.addEventListener('DOMContentLoaded', () => {
    const checkBtn = document.getElementById('verifyBtn');
    const clearBtn = document.getElementById('clearBtn');
    const newsInput = document.getElementById('newsText');
    const newsUrl = document.getElementById('newsUrl');
    const newsImage = document.getElementById('newsImage');
    const loader = document.getElementById('analysisLoader');
    const results = document.getElementById('verifierResults');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    // Drag and Drop elements
    const dragBoxContainer = document.getElementById('dragBoxContainer');
    const imageDragBox = document.getElementById('imageDragBox');
    const imagePreviewBox = document.getElementById('imagePreviewBox');
    const imagePreview = document.getElementById('imagePreview');
    const removeImageBtn = document.getElementById('removeImageBtn');

    // Tab buttons & contents
    const tabButtons = document.querySelectorAll('.verifier-tab-btn');
    const tabContents = document.querySelectorAll('.verifier-tab-content');
    let activeTab = 'text';

    const steps = [
        document.getElementById('step1'),
        document.getElementById('step2'),
        document.getElementById('step3'),
        document.getElementById('step4')
    ].filter(Boolean);

    /* ------------------------------------------------------------------ */
    /* Toasts                                                              */
    /* ------------------------------------------------------------------ */

    let toastHost = null;

    function toast(message, type = 'error') {
        if (!toastHost) {
            toastHost = document.createElement('div');
            toastHost.className = 'vf-toast-host';
            document.body.appendChild(toastHost);
        }

        const el = document.createElement('div');
        el.className = `vf-toast vf-toast-${type}`;
        el.setAttribute('role', type === 'error' ? 'alert' : 'status');

        const icon = document.createElement('i');
        icon.className = type === 'error'
            ? 'fa-solid fa-circle-exclamation'
            : 'fa-solid fa-circle-info';
        el.appendChild(icon);

        const text = document.createElement('span');
        text.textContent = message;
        el.appendChild(text);

        toastHost.appendChild(el);
        requestAnimationFrame(() => el.classList.add('vf-toast-in'));

        const remove = () => {
            el.classList.remove('vf-toast-in');
            setTimeout(() => el.remove(), 300);
        };
        el.addEventListener('click', remove);
        setTimeout(remove, 5000);
    }

    /* ------------------------------------------------------------------ */
    /* Helpers                                                             */
    /* ------------------------------------------------------------------ */

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value == null ? '' : String(value);
        return div.innerHTML;
    }

    // Evidence links can come from an external fact-check API, so only plain
    // http(s) URLs are ever rendered as anchors.
    function safeUrl(value) {
        if (!value) return null;
        try {
            const url = new URL(value, window.location.origin);
            return (url.protocol === 'http:' || url.protocol === 'https:') ? url.href : null;
        } catch (e) {
            return null;
        }
    }

    const TONE_ICONS = {
        negative: 'fa-solid fa-triangle-exclamation',
        positive: 'fa-solid fa-circle-check',
        neutral: 'fa-solid fa-circle-info'
    };

    /* ------------------------------------------------------------------ */
    /* Tabs                                                                */
    /* ------------------------------------------------------------------ */

    if (tabButtons.length > 0) {
        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                tabButtons.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => {
                    c.classList.add('d-none');
                    c.classList.remove('active');
                });

                btn.classList.add('active');
                activeTab = btn.getAttribute('data-tab');
                const targetPanel = document.getElementById(`tab-${activeTab}`);
                if (targetPanel) {
                    targetPanel.classList.remove('d-none');
                    targetPanel.classList.add('active');
                }
            });
        });
    }

    /* ------------------------------------------------------------------ */
    /* Image upload                                                        */
    /* ------------------------------------------------------------------ */

    if (dragBoxContainer && newsImage) {
        dragBoxContainer.addEventListener('click', (e) => {
            if (e.target.closest('#removeImageBtn') || e.target.closest('#imagePreview')) {
                return;
            }
            newsImage.click();
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dragBoxContainer.addEventListener(eventName, (e) => {
                e.preventDefault();
                dragBoxContainer.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dragBoxContainer.addEventListener(eventName, (e) => {
                e.preventDefault();
                dragBoxContainer.classList.remove('dragover');
            }, false);
        });

        dragBoxContainer.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                newsImage.files = files;
                handleImageSelect(files[0]);
            }
        });

        newsImage.addEventListener('change', () => {
            if (newsImage.files.length > 0) {
                handleImageSelect(newsImage.files[0]);
            }
        });

        removeImageBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            newsImage.value = '';
            imagePreview.src = '';
            imagePreviewBox.classList.add('d-none');
            imageDragBox.classList.remove('d-none');
        });
    }

    function handleImageSelect(file) {
        if (!file.type.startsWith('image/')) {
            toast('That file is not an image. Please choose a PNG or JPG.');
            return;
        }
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.src = e.target.result;
            imageDragBox.classList.add('d-none');
            imagePreviewBox.classList.remove('d-none');
            imagePreviewBox.classList.add('d-inline-block');
        };
        reader.readAsDataURL(file);
    }

    if (!checkBtn) return;

    /* ------------------------------------------------------------------ */
    /* Submit                                                              */
    /* ------------------------------------------------------------------ */

    checkBtn.addEventListener('click', async (e) => {
        e.preventDefault();

        let body;
        const csrf = document.querySelector('meta[name="csrf-token"]');
        const headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf ? csrf.getAttribute('content') : ''
        };

        if (activeTab === 'text') {
            const val = newsInput.value.trim();
            if (!val) {
                toast('Please paste some news text to check.');
                return;
            }
            body = JSON.stringify({ text: val });
            headers['Content-Type'] = 'application/json';
        } else if (activeTab === 'url') {
            const val = newsUrl.value.trim();
            if (!val) {
                toast('Please paste a news article link to check.');
                return;
            }
            body = JSON.stringify({ url: val });
            headers['Content-Type'] = 'application/json';
        } else if (activeTab === 'image') {
            const file = newsImage.files[0];
            if (!file) {
                toast('Please upload an image that contains text.');
                return;
            }
            body = new FormData();
            body.append('image', file);
        }

        results.style.display = 'none';
        loader.style.display = 'block';
        checkBtn.disabled = true;
        btnSpinner.style.display = 'inline-block';
        btnText.textContent = 'Checking...';

        steps.forEach(step => { step.className = 'analysis-step'; });

        try {
            const apiPromise = fetch('/check', { method: 'POST', headers, body });

            await runAnalysisStep(0, 600);
            await runAnalysisStep(1, 700);
            await runAnalysisStep(2, 700);
            await runAnalysisStep(3, 600);

            const response = await apiPromise;
            if (!response.ok) {
                const errData = await response.json().catch(() => ({}));
                throw new Error(errData.error || errData.message || 'Something went wrong during the check.');
            }

            const resData = await response.json();
            if (!resData.success) {
                throw new Error('The check could not be completed.');
            }

            // The final step never got its completed state because the animation
            // only ever marked the *previous* step.
            completeAllSteps();
            displayResults(resData.result || {}, resData.text || '');
        } catch (err) {
            console.error(err);
            toast(err.message || 'Something went wrong during the check.');
        } finally {
            loader.style.display = 'none';
            checkBtn.disabled = false;
            btnSpinner.style.display = 'none';
            btnText.textContent = 'Verify Now';
        }
    });

    clearBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (activeTab === 'text' && newsInput) {
            newsInput.value = '';
        } else if (activeTab === 'url' && newsUrl) {
            newsUrl.value = '';
        } else if (activeTab === 'image' && newsImage) {
            removeImageBtn.click();
        }
        results.style.display = 'none';
        loader.style.display = 'none';
        const widget = document.getElementById('verifierWidget');
        if (widget) {
            window.scrollTo({ top: widget.offsetTop - 100, behavior: 'smooth' });
        }
    });

    function runAnalysisStep(index, delay) {
        return new Promise((resolve) => {
            if (!steps[index]) { resolve(); return; }
            if (index > 0 && steps[index - 1]) {
                steps[index - 1].classList.remove('active');
                steps[index - 1].classList.add('completed');
            }
            steps[index].classList.add('active');
            setTimeout(resolve, delay);
        });
    }

    function completeAllSteps() {
        steps.forEach(step => {
            step.classList.remove('active');
            step.classList.add('completed');
        });
    }

    /* ------------------------------------------------------------------ */
    /* Rendering                                                           */
    /* ------------------------------------------------------------------ */

    const VERDICTS = {
        FAKE_FACT: {
            badge: 'Contradicts Known Facts',
            statusClass: 'status-suspicious',
            strokeClass: 'stroke-suspicious'
        },
        FAKE: {
            badge: 'Likely Fake News',
            statusClass: 'status-suspicious',
            strokeClass: 'stroke-suspicious'
        },
        REAL: {
            badge: 'Looks Credible',
            statusClass: 'status-credible',
            strokeClass: 'stroke-credible'
        },
        UNCERTAIN: {
            badge: 'Not Enough Evidence',
            statusClass: 'status-mixed',
            strokeClass: 'stroke-mixed'
        }
    };

    function displayResults(result, originalText) {
        const label = result.label || 'UNCERTAIN';
        const confidence = Number(result.confidence) || 0;
        const factCheck = result.fact_check || {};
        const explanation = result.explanation || {};
        const scores = result.scores || {};
        const viaFactCheck = result.verdict_source === 'fact_check';

        let verdict;
        let trustScore;
        if (label === 'FAKE') {
            verdict = viaFactCheck ? VERDICTS.FAKE_FACT : VERDICTS.FAKE;
            trustScore = Math.max(0, 100 - Math.round(confidence));
        } else if (label === 'REAL') {
            verdict = VERDICTS.REAL;
            trustScore = Math.round(confidence);
        } else {
            verdict = VERDICTS.UNCERTAIN;
            trustScore = 50;
        }

        // Gauge
        const fillCircle = document.getElementById('scoreFill');
        const scoreVal = document.getElementById('scoreValue');
        if (scoreVal) scoreVal.textContent = `${trustScore}%`;
        if (fillCircle) {
            const circumference = 2 * Math.PI * 60;
            fillCircle.classList.remove('stroke-credible', 'stroke-mixed', 'stroke-suspicious');
            fillCircle.classList.add(verdict.strokeClass);
            fillCircle.style.strokeDashoffset = circumference - (circumference * trustScore) / 100;
        }

        // Verdict text
        const verdictBadge = document.getElementById('verdictBadge');
        const verdictTitle = document.getElementById('verdictTitle');
        const verdictDesc = document.getElementById('verdictDesc');
        if (verdictBadge) {
            verdictBadge.textContent = verdict.badge;
            verdictBadge.className = `verdict-badge ${verdict.statusClass}`;
        }
        if (verdictTitle) {
            verdictTitle.textContent = explanation.headline || verdict.badge;
        }
        if (verdictDesc) {
            verdictDesc.textContent = explanation.plain
                || 'We could not produce a detailed explanation for this check.';
        }

        renderFactCheck(factCheck, viaFactCheck, result.style_label, label);
        renderPatterns(result.signals || []);
        renderDisclaimer(explanation.disclaimer);

        updateSubScoreBar('barStyle', 'valStyle', scores.style);
        updateSubScoreBar('barSource', 'valSource', scores.source);
        updateFactMetric(factCheck);
        updateSubScoreBar('barML', 'valML', scores.model != null ? scores.model : Math.round(confidence));

        results.style.display = 'block';
        setTimeout(() => {
            window.scrollTo({ top: results.offsetTop - 120, behavior: 'smooth' });
        }, 100);
    }

    function renderFactCheck(factCheck, viaFactCheck, styleLabel, finalLabel) {
        const panel = document.getElementById('factCheckPanel');
        if (!panel) return;

        const evidence = Array.isArray(factCheck.evidence) ? factCheck.evidence : [];
        if (evidence.length === 0) {
            panel.innerHTML = '';
            panel.style.display = 'none';
            return;
        }

        const sourceNames = {
            local_kb: 'VeriFact fact database',
            wikidata: 'Wikidata',
            google_factcheck: 'Google Fact Check'
        };

        const items = evidence.map(item => {
            const isContradiction = item.verdict === 'CONTRADICTED';
            const url = safeUrl(item.url);
            const sourceLabel = sourceNames[item.source] || item.source || 'Source';
            const link = url
                ? `<a class="fact-evidence-link" href="${escapeHtml(url)}" target="_blank" rel="noopener noreferrer">
                       View source <i class="fa-solid fa-arrow-up-right-from-square"></i>
                   </a>`
                : '';

            return `
                <li class="fact-evidence-item ${isContradiction ? 'is-contradiction' : 'is-support'}">
                    <span class="fact-evidence-icon">
                        <i class="fa-solid ${isContradiction ? 'fa-xmark' : 'fa-check'}"></i>
                    </span>
                    <div class="fact-evidence-body">
                        <p class="fact-evidence-text">${escapeHtml(item.statement)}</p>
                        <div class="fact-evidence-meta">
                            <span class="fact-evidence-source">${escapeHtml(sourceLabel)}</span>
                            ${link}
                        </div>
                    </div>
                </li>`;
        }).join('');

        let note = '';
        if (viaFactCheck && finalLabel === 'FAKE' && styleLabel === 'REAL') {
            note = `<p class="fact-check-note">
                        The writing style alone looked genuine, but a fact we could check does not
                        match. A false statement can still be written calmly, so the fact check
                        decides this result.
                    </p>`;
        }
        if (factCheck.degraded) {
            note += `<p class="fact-check-note">
                        Some online fact sources could not be reached, so only our offline
                        database was used.
                     </p>`;
        }

        panel.innerHTML = `
            <div class="fact-check-header">
                <i class="fa-solid fa-scale-balanced"></i>
                <h5>What we checked against real records</h5>
            </div>
            <ul class="fact-evidence-list">${items}</ul>
            ${note}`;
        panel.style.display = 'block';
    }

    function renderPatterns(signals) {
        const container = document.getElementById('patternBreakdown');
        if (!container) return;

        if (!signals.length) {
            container.innerHTML = '';
            container.style.display = 'none';
            return;
        }

        const order = { negative: 0, positive: 1, neutral: 2 };
        const sorted = signals.slice().sort((a, b) => (order[a.tone] ?? 3) - (order[b.tone] ?? 3));

        const pills = sorted.map(signal => `
            <li class="pattern-pill pattern-${escapeHtml(signal.tone)}" title="${escapeHtml(signal.detail)}">
                <i class="${TONE_ICONS[signal.tone] || TONE_ICONS.neutral}"></i>
                <span class="pattern-pill-label">${escapeHtml(signal.label)}</span>
                <span class="pattern-pill-detail">${escapeHtml(signal.detail)}</span>
            </li>`).join('');

        container.innerHTML = `
            <div class="pattern-header">
                <i class="fa-solid fa-magnifying-glass-chart"></i>
                <h5>How we reached this result</h5>
            </div>
            <ul class="pattern-pill-list">${pills}</ul>`;
        container.style.display = 'block';

        // Stagger the reveal so the list reads top to bottom.
        container.querySelectorAll('.pattern-pill').forEach((pill, index) => {
            pill.style.animationDelay = `${index * 70}ms`;
            pill.classList.add('pattern-pill-in');
        });
    }

    function renderDisclaimer(text) {
        const el = document.getElementById('resultDisclaimer');
        if (!el) return;
        el.textContent = text
            || 'This tool checks writing style and a limited database of known facts. It cannot '
             + 'verify every real-world claim. Always confirm important news with a trusted source.';
        el.style.display = 'block';
    }

    function updateFactMetric(factCheck) {
        const bar = document.getElementById('barDatabase');
        const text = document.getElementById('valDatabase');
        if (!bar || !text) return;

        const verdict = factCheck.verdict;
        const hasClaims = Array.isArray(factCheck.claims) && factCheck.claims.length > 0;

        let width = 0;
        let color = '#94a3b8';
        let statusText = 'No claim found';

        if (verdict === 'CONTRADICTED') {
            width = 100;
            color = '#f54329';
            statusText = 'Contradicted';
        } else if (verdict === 'SUPPORTED') {
            width = 100;
            color = '#48bb78';
            statusText = 'Confirmed';
        } else if (hasClaims) {
            width = 20;
            statusText = 'No record found';
        }

        // Deliberately not a percentage: there is no meaningful score when
        // nothing checkable was found, and inventing one is what this replaced.
        text.textContent = statusText;
        bar.style.width = `${width}%`;
        bar.style.backgroundColor = color;
    }

    function updateSubScoreBar(barId, valId, score) {
        const bar = document.getElementById(barId);
        const text = document.getElementById(valId);
        if (!bar || !text || score == null) return;

        const value = Math.max(0, Math.min(100, Math.round(score)));
        text.textContent = `${value}%`;
        bar.style.width = `${value}%`;

        if (value >= 75) {
            bar.style.backgroundColor = '#48bb78';
        } else if (value >= 45) {
            bar.style.backgroundColor = '#ecc94b';
        } else {
            bar.style.backgroundColor = '#f54329';
        }
    }
});
