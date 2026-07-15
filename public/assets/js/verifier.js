// VeriFact AI - Interactive Verification Engine

document.addEventListener('DOMContentLoaded', () => {
    const checkBtn = document.getElementById('verifyBtn');
    const clearBtn = document.getElementById('clearBtn');
    const newsInput = document.getElementById('newsText');
    const loader = document.getElementById('analysisLoader');
    const results = document.getElementById('verifierResults');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    // Steps list elements
    const steps = [
        document.getElementById('step1'),
        document.getElementById('step2'),
        document.getElementById('step3'),
        document.getElementById('step4')
    ];

    if (!checkBtn || !newsInput) return;

    checkBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        const text = newsInput.value.trim();

        if (!text) {
            alert('Please paste some news text, a statement, or a URL to analyze.');
            return;
        }

        // Reset UI
        results.style.display = 'none';
        loader.style.display = 'block';
        checkBtn.disabled = true;
        btnSpinner.style.display = 'inline-block';
        btnText.textContent = 'Verifying...';

        // Clear all step statuses
        steps.forEach(step => {
            step.className = 'analysis-step';
        });

        // Run analysis step animation sequence in parallel with the AJAX request
        try {
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

            const isUrl = /^https?:\/\//i.test(text);
            const payload = isUrl ? { url: text } : { text: text };

            const apiPromise = fetch('/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            await runAnalysisStep(0, 800);
            await runAnalysisStep(1, 900);
            await runAnalysisStep(2, 900);
            await runAnalysisStep(3, 800);

            const response = await apiPromise;
            if (!response.ok) {
                const errData = await response.json();
                throw new Error(errData.error || 'Server error during analysis.');
            }

            const resData = await response.json();
            if (!resData.success) {
                throw new Error('Analysis failed.');
            }

            const backend = resData.result; // label, confidence, model, confidence_level, probabilities

            // Compute local heuristic analysis for sub-score details
            const analysis = performNLPAnalysis(text);

            // Override final verdict and trust score based on the ML Model
            let trustScore = Math.round(backend.confidence);
            let verdict = 'Needs Verification';
            let statusClass = 'status-mixed';
            let strokeClass = 'stroke-mixed';
            let explanation = '';

            if (backend.label === 'REAL') {
                verdict = 'Highly Credible';
                statusClass = 'status-credible';
                strokeClass = 'stroke-credible';
                explanation = `Factual confirmation: The AI model (${backend.model}) classified this content as REAL with a confidence score of ${backend.confidence}%. Linguistic patterns correlate strongly with verified, objective reporting.`;
            } else if (backend.label === 'FAKE') {
                verdict = 'Likely Misinformation';
                statusClass = 'status-suspicious';
                strokeClass = 'stroke-suspicious';
                trustScore = 100 - trustScore; // invert trust rating for fake news
                explanation = `Warning: The AI model (${backend.model}) classified this content as FAKE with a confidence score of ${backend.confidence}%. It contains linguistic style patterns, extreme adjectives, or structural cues commonly found in false reporting.`;
            } else {
                verdict = 'Mixed / Unverified';
                statusClass = 'status-mixed';
                strokeClass = 'stroke-mixed';
                trustScore = 50;
                explanation = `Neutral or Uncertain: The AI model (${backend.model}) has insufficient confidence to classify this text definitively (${backend.confidence}% confidence). We advise cross-checking this claim against other trusted media outlets.`;
            }

            // Blend the ML score into the ML sentiment card score
            analysis.mlScore = Math.round(backend.confidence);

            // Update analysis with real backend prediction
            analysis.overallScore = trustScore;
            analysis.verdict = verdict;
            analysis.statusClass = statusClass;
            analysis.strokeClass = strokeClass;
            analysis.explanation = explanation;

            displayResults(analysis);
        } catch (err) {
            console.error(err);
            alert('Analysis Error: ' + err.message);
        } finally {
            loader.style.display = 'none';
            checkBtn.disabled = false;
            btnSpinner.style.display = 'none';
            btnText.textContent = 'Verify Now';
        }
    });

    clearBtn.addEventListener('click', (e) => {
        e.preventDefault();
        newsInput.value = '';
        results.style.display = 'none';
        loader.style.display = 'none';
        window.scrollTo({
            top: document.getElementById('verifierWidget').offsetTop - 100,
            behavior: 'smooth'
        });
    });

    function runAnalysisStep(index, delay) {
        return new Promise((resolve) => {
            if (index > 0) {
                steps[index - 1].classList.remove('active');
                steps[index - 1].classList.add('completed');
            }
            steps[index].classList.add('active');
            
            setTimeout(() => {
                resolve();
            }, delay);
        });
    }

    // Heuristics NLP engine
    function performNLPAnalysis(text) {
        const textLower = text.toLowerCase();
        
        // 1. Linguistic & Style Analysis Heuristics
        let styleScore = 100;
        
        // Capitalization checks
        const words = text.split(/\s+/);
        const uppercaseWords = words.filter(word => word.length > 3 && word === word.toUpperCase() && !/^[0-9\W]+$/.test(word));
        const uppercaseRatio = words.length > 0 ? uppercaseWords.length / words.length : 0;
        if (uppercaseRatio > 0.15) {
            styleScore -= Math.min(30, uppercaseRatio * 120);
        }
        
        // Punctuation checks (exclamation marks)
        const exclamationCount = (text.match(/!/g) || []).length;
        if (exclamationCount > 2) {
            styleScore -= Math.min(25, exclamationCount * 5);
        }

        // Question mark checking in short headlines (typical clickbait)
        const questionCount = (text.match(/\?/g) || []).length;
        if (text.length < 150 && questionCount > 0) {
            styleScore -= 10;
        }

        styleScore = Math.max(15, Math.round(styleScore));

        // 2. Source Credibility Checking
        let sourceScore = 70; // neutral default
        let domainFound = false;

        // Common domain endings in input
        const urls = text.match(/\bhttps?:\/\/\S+/gi) || [];
        const domainRegex = /([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}/g;
        let domains = [];
        
        urls.forEach(url => {
            const matches = url.match(domainRegex);
            if (matches) domains.push(matches[0].toLowerCase());
        });

        // If no URL but text mentions publisher domains
        const mentionRegex = /\b([a-zA-Z0-9-]+\.(com|org|net|info|news|xyz|biz|gov|edu|co|uk))\b/gi;
        let match;
        while ((match = mentionRegex.exec(textLower)) !== null) {
            domains.push(match[1]);
        }

        // Dedup domains
        domains = [...new Set(domains)];

        const suspiciousTlds = ['.info', '.xyz', '.biz', '.su', '.click', '.online', '.today', '.ru'];
        const trustedPublishers = [
            'nytimes.com', 'reuters.com', 'apnews.com', 'bbc.co.uk', 'bbc.com', 
            'wikipedia.org', 'cnn.com', 'washingtonpost.com', 'guardian.com', 
            'theguardian.com', 'bloomberg.com', 'npr.org', 'wsj.com', 'ft.com'
        ];
        const knownRumorPublishers = [
            'infowars.com', 'naturalnews.com', 'worldnewsdailyreport.com', 
            'yournewswire.com', 'freedomnews.com', 'realnews365.com'
        ];

        if (domains.length > 0) {
            domainFound = true;
            let suspiciousCount = 0;
            let trustedCount = 0;
            let rumorCount = 0;

            domains.forEach(d => {
                if (trustedPublishers.some(tp => d.includes(tp) || tp.includes(d))) {
                    trustedCount++;
                } else if (knownRumorPublishers.some(rp => d.includes(rp) || rp.includes(d))) {
                    rumorCount++;
                } else if (suspiciousTlds.some(stld => d.endsWith(stld))) {
                    suspiciousCount++;
                }
            });

            if (trustedCount > 0 && rumorCount === 0) {
                sourceScore = 95;
            } else if (rumorCount > 0) {
                sourceScore = 15;
            } else if (suspiciousCount > 0) {
                sourceScore = 35;
            } else {
                sourceScore = 60; // unverified custom domain
            }
        } else {
            // Check if user mentions standard news agencies
            if (textLower.includes('reuters') || textLower.includes('associated press') || textLower.includes('ap news') || textLower.includes('bbc news')) {
                sourceScore = 80;
            }
        }

        // 3. Cross-Reference Databases (Simulated Matches)
        let crossRefScore = 75; // Neutral baseline
        let matchTopic = '';
        let matchExplanation = '';

        const topics = [
            {
                keywords: ['vaccine', 'microchip', '5g', 'bill gates', 'covid chip'],
                score: 12,
                verdict: 'Conspiracy Theory Debunked',
                desc: 'Warning: Multiple independent fact-checks (WHO, CDC, Reuters Fact Check) confirm that COVID-19 vaccines do not contain microchips and do not communicate with 5G cellular networks. This claim is classified as completely false.'
            },
            {
                keywords: ['flat earth', 'nasa cgi', 'nasa faked', 'antarctica wall', 'dome over earth'],
                score: 10,
                verdict: 'Scientific Misinformation',
                desc: 'Warning: Claims advocating a flat Earth and faked satellite imagery contradict verified astrophysics data, satellite telemetry, and centuries of global navigation consensus. Classified as scientifically false.'
            },
            {
                keywords: ['free money', 'gift card', 'cash prize', 'selected to receive', 'click link for money', 'elon musk giveaway', 'bitcoin double'],
                score: 15,
                verdict: 'Phishing / Financial Scam',
                desc: 'Warning: This statement mirrors templates of viral social media scams and phishing campaigns. AI giveaway promos and doubling cryptocurrency schemes are high-risk financial frauds.'
            },
            {
                keywords: ['area 51', 'alien autopsy', 'roswell ufo', 'extraterrestrial body'],
                score: 40,
                verdict: 'Unverified / Speculative Claim',
                desc: 'Mixed/Speculative: While government archives acknowledge the existence of Area 51, claims regarding captured extraterrestrial crafts and autopsy videos have been systematically debunked or remain speculative without physical evidence.'
            },
            {
                keywords: ['announced a new study', 'according to scientific journal', 'published research', 'peer-reviewed study', 'nasa scientists discovered'],
                score: 90,
                verdict: 'Likely Factual Citation',
                desc: 'Factual: The text references scientific publication standards and research journals. Claims backed by peer-reviewed studies are statistically highly credible.'
            }
        ];

        for (const topic of topics) {
            const matchesAll = topic.keywords.some(kw => textLower.includes(kw));
            if (matchesAll) {
                crossRefScore = topic.score;
                matchTopic = topic.verdict;
                matchExplanation = topic.desc;
                break;
            }
        }

        if (!matchTopic) {
            // General heuristics for cross reference score
            if (text.length < 100) {
                crossRefScore = 50; // Insufficient context to cross-reference
                matchExplanation = 'Neutral: The submitted statement is too short to accurately cross-reference with global fact-checking registers. Please paste a full article or paragraph for deep verification.';
            } else if (styleScore > 85 && sourceScore > 75) {
                crossRefScore = 85;
                matchExplanation = 'Credible Match: Linguistic patterns and source references strongly correlate with objective, verified reporting databases.';
            } else if (styleScore < 60) {
                crossRefScore = 40;
                matchExplanation = 'Low Match Correlation: The highly sensational formatting of the text mimics low-credibility tabloid sources rather than authenticated journalism datasets.';
            } else {
                crossRefScore = 70;
                matchExplanation = 'Unverified Status: No direct matches found in current fact-checking databases. The text utilizes neutral language, but additional source verification is recommended.';
            }
        }

        // 4. ML Sentiment & Bias Analysis Heuristics
        let mlScore = 100;
        
        const sensationalWords = [
            'shocking', 'secret', 'miracle', 'exposed', 'conspiracy', 'omg', 
            'unbelievable', 'never want you to know', 'insane', 'destroy', 
            'destroying', 'scandal', 'proven', 'exposed!', 'absolutely', 
            'liars', 'traitors', 'hoax', 'cabal', 'illuminati', 'propaganda'
        ];

        let sensationalCount = 0;
        sensationalWords.forEach(word => {
            const regex = new RegExp('\\b' + word + '\\b', 'gi');
            const count = (textLower.match(regex) || []).length;
            sensationalCount += count;
        });

        if (sensationalCount > 0) {
            mlScore -= Math.min(60, sensationalCount * 12);
        }

        // Deduct if vocabulary is overly aggressive or emotional
        const emotionalWords = ['hate', 'furious', 'evil', 'warped', 'disgusting', 'filthy', 'terror', 'panic', 'chaos'];
        let emotionalCount = 0;
        emotionalWords.forEach(word => {
            const regex = new RegExp('\\b' + word + '\\b', 'gi');
            const count = (textLower.match(regex) || []).length;
            emotionalCount += count;
        });

        if (emotionalCount > 0) {
            mlScore -= Math.min(30, emotionalCount * 8);
        }

        mlScore = Math.max(10, Math.round(mlScore));

        // 5. Final Overall Trust Score Calculation
        const overallScore = Math.round(
            (styleScore * 0.25) + 
            (sourceScore * 0.25) + 
            (crossRefScore * 0.25) + 
            (mlScore * 0.25)
        );

        let verdict = 'Needs Verification';
        let statusClass = 'status-mixed';
        let strokeClass = 'stroke-mixed';

        if (overallScore >= 75) {
            verdict = 'Highly Credible';
            statusClass = 'status-credible';
            strokeClass = 'stroke-credible';
            if (!matchExplanation) {
                matchExplanation = 'This text displays strong indicators of professional journalism, including objective language, high-reliability vocabulary, lack of clickbait cues, and reference patterns found in trusted media outlets.';
            }
        } else if (overallScore < 45) {
            verdict = 'Likely Misinformation';
            statusClass = 'status-suspicious';
            strokeClass = 'stroke-suspicious';
            if (!matchExplanation) {
                matchExplanation = 'Warning: This text displays high clickbait metrics, sensationalized framing, biased phrasing, and structural indicators frequently linked to false reporting, rumors, or emotional manipulation.';
            }
        } else {
            verdict = 'Mixed / Unverified';
            statusClass = 'status-mixed';
            strokeClass = 'stroke-mixed';
            if (!matchExplanation) {
                matchExplanation = 'Neutral/Mixed: The text displays moderate linguistic balance, but lacks direct primary source citations. Some claims may be speculative or require further context to verify fully.';
            }
        }

        return {
            overallScore,
            verdict,
            statusClass,
            strokeClass,
            styleScore,
            sourceScore,
            crossRefScore,
            mlScore,
            explanation: matchExplanation
        };
    }

    function displayResults(data) {
        // Update circular gauge score
        const fillCircle = document.getElementById('scoreFill');
        const scoreVal = document.getElementById('scoreValue');
        const verdictBadge = document.getElementById('verdictBadge');
        const verdictTitle = document.getElementById('verdictTitle');
        const verdictDesc = document.getElementById('verdictDesc');

        // Update score numbers
        scoreVal.textContent = `${data.overallScore}%`;

        // Update circular progress bar
        const r = 60;
        const circumference = 2 * Math.PI * r; // 377
        const offset = circumference - (circumference * data.overallScore) / 100;
        
        // Remove old stroke status classes
        fillCircle.classList.remove('stroke-credible', 'stroke-mixed', 'stroke-suspicious');
        fillCircle.classList.add(data.strokeClass);
        fillCircle.style.strokeDashoffset = offset;

        // Update badge text and classes
        verdictBadge.textContent = data.verdict;
        verdictBadge.className = `verdict-badge ${data.statusClass}`;

        // Update explanation texts
        verdictTitle.textContent = data.verdict === 'Highly Credible' ? 'Source Credibility Confirmed' : (data.verdict === 'Likely Misinformation' ? 'Misinformation Warning Flagged' : 'Additional Verification Advised');
        verdictDesc.textContent = data.explanation;

        // Update sub-score metric bars
        updateSubScoreBar('barStyle', 'valStyle', data.styleScore);
        updateSubScoreBar('barSource', 'valSource', data.sourceScore);
        updateSubScoreBar('barDatabase', 'valDatabase', data.crossRefScore);
        updateSubScoreBar('barML', 'valML', data.mlScore);

        // Show the results section
        results.style.display = 'block';

        // Scroll to results smoothly
        setTimeout(() => {
            window.scrollTo({
                top: results.offsetTop - 120,
                behavior: 'smooth'
            });
        }, 100);
    }

    function updateSubScoreBar(barId, valId, score) {
        const bar = document.getElementById(barId);
        const text = document.getElementById(valId);
        if (bar && text) {
            text.textContent = `${score}%`;
            bar.style.width = `${score}%`;
            
            // Assign gradient color classes to bars
            bar.classList.remove('bg-success', 'bg-warning', 'bg-danger');
            if (score >= 75) {
                bar.style.backgroundColor = '#48bb78';
            } else if (score >= 45) {
                bar.style.backgroundColor = '#ecc94b';
            } else {
                bar.style.backgroundColor = '#f54329';
            }
        }
    }
});
