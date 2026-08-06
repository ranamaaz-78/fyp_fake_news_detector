"""Run curated claims through the full prediction pipeline and report accuracy.

This exercises the same code path as POST /predict (style model + signals +
fact layer + verdict combination) so the fact-verification layer can be checked
against known-true and known-false claims without a browser.

    python scripts/verify_samples.py
    python scripts/verify_samples.py --offline   # skip network fact sources
"""

from __future__ import annotations

import argparse
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT))

for _stream in (sys.stdout, sys.stderr):
    try:
        _stream.reconfigure(encoding="utf-8", errors="replace")
    except (AttributeError, ValueError):
        pass

from api.app import CONFIG, FACT_CHECK_CONFIG, _combine  # noqa: E402
from src import explain, fact_verification  # noqa: E402

# (claim, expected verdict) — "FAKE" means the fact layer should catch it.
# "ANY" means we only check the pipeline runs and returns a coherent result.
SAMPLES: list[tuple[str, str]] = [
    # False role claims the style model alone cannot catch.
    ("Babar Azam is Pakistan's Prime Minister.", "FAKE"),
    ("Babar Azam is the Prime Minister of Pakistan.", "FAKE"),
    ("The Prime Minister of Pakistan is Babar Azam.", "FAKE"),
    ("Shaheen Afridi is the President of Pakistan.", "FAKE"),
    ("Elon Musk is the President of the United States.", "FAKE"),
    ("Virat Kohli is the Prime Minister of India.", "FAKE"),
    ("Shah Rukh Khan is the President of India.", "FAKE"),
    ("Cristiano Ronaldo is the Prime Minister of the United Kingdom.", "FAKE"),
    ("Lionel Messi is the President of Brazil.", "FAKE"),
    ("Malala Yousafzai is the Prime Minister of Pakistan.", "FAKE"),
    ("Wasim Akram is the President of Pakistan.", "FAKE"),
    ("Atif Aslam is the Prime Minister of Canada.", "FAKE"),
    # True role claims — must not be flagged by the fact layer.
    ("Shehbaz Sharif is the Prime Minister of Pakistan.", "NOT_FACT_FAKE"),
    ("Asif Ali Zardari is the President of Pakistan.", "NOT_FACT_FAKE"),
    ("Narendra Modi is the Prime Minister of India.", "NOT_FACT_FAKE"),
    ("Vladimir Putin is the President of Russia.", "NOT_FACT_FAKE"),
    ("Emmanuel Macron is the President of France.", "NOT_FACT_FAKE"),
    ("Xi Jinping is the President of China.", "NOT_FACT_FAKE"),
    ("Anthony Albanese is the Prime Minister of Australia.", "NOT_FACT_FAKE"),
    ("Cyril Ramaphosa is the President of South Africa.", "NOT_FACT_FAKE"),
    # Sensational text the style model should handle on its own.
    (
        "SHOCKING!!! Doctors are FURIOUS about this secret miracle cure the government "
        "has been hiding from you for decades!!! Share before it is deleted!!!",
        "ANY",
    ),
    (
        "You won't believe the shocking conspiracy the mainstream media covered up! "
        "This leaked bombshell exposes everything they never wanted you to know!!!",
        "ANY",
    ),
    # Ordinary reporting with no checkable role claim.
    (
        "The State Bank of Pakistan said on Tuesday that inflation eased for a third "
        "consecutive month, according to figures released by the statistics bureau.",
        "ANY",
    ),
    (
        "Officials from the finance ministry confirmed that the quarterly revenue target "
        "had been met, according to a statement issued after the cabinet meeting.",
        "ANY",
    ),
    # Unknown entities the fact layer must not guess about.
    ("Jane Doe is the Prime Minister of Atlantis.", "ANY"),
    ("A local council approved the new drainage project after a public consultation.", "ANY"),
]


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--offline", action="store_true", help="use the local knowledge base only")
    args = parser.parse_args()

    config = dict(FACT_CHECK_CONFIG)
    if args.offline:
        config["sources"] = ["local_kb"]

    fact_verification.reset_verifier()
    verifier = fact_verification.FactVerifier(config)

    try:
        from src.predictor import FakeNewsPredictor

        predictor = FakeNewsPredictor()
    except FileNotFoundError:
        print("Model not trained — run scripts/train.py first.")
        return 1

    print(f"uncertain_threshold={CONFIG['uncertain_threshold']} offline={args.offline}\n")

    passed = failed = 0
    caught_by_fact_layer = 0

    for text, expectation in SAMPLES:
        prediction = predictor.predict(text)
        analysis = explain.analyse(text)
        fact = verifier.verify(text)
        result = _combine(prediction, analysis, fact)

        label = result["label"]
        source = result["verdict_source"]
        if source == "fact_check":
            caught_by_fact_layer += 1

        if expectation == "FAKE":
            ok = label == "FAKE" and source == "fact_check"
        elif expectation == "NOT_FACT_FAKE":
            ok = not (label == "FAKE" and source == "fact_check")
        else:
            ok = label in ("REAL", "FAKE", "UNCERTAIN")

        passed += ok
        failed += not ok

        status = "PASS" if ok else "FAIL"
        print(f"[{status}] {label:<9} via {source:<11} style={result['style_label']:<9} {text[:64]}")
        for item in result["fact_check"].get("evidence", [])[:1]:
            print(f"         evidence: {item['statement']}")

    print(f"\n{passed} passed, {failed} failed, {caught_by_fact_layer} decided by the fact layer")
    return 0 if failed == 0 else 1


if __name__ == "__main__":
    raise SystemExit(main())
