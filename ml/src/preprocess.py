"""Text preprocessing for fake news classification."""

import re
import string

import nltk
from nltk.corpus import stopwords
from nltk.stem import PorterStemmer

_stemmer = PorterStemmer()
_stop_words: set[str] | None = None


def ensure_nltk_data() -> None:
    """Download NLTK resources if missing."""
    for resource in ("stopwords", "punkt", "punkt_tab"):
        try:
            nltk.data.find(f"corpora/{resource}" if resource == "stopwords" else f"tokenizers/{resource}")
        except LookupError:
            nltk.download(resource, quiet=True)


def _get_stop_words() -> set[str]:
    global _stop_words
    if _stop_words is None:
        ensure_nltk_data()
        _stop_words = set(stopwords.words("english"))
    return _stop_words


def clean_text(text: str) -> str:
    """Remove HTML, URLs, punctuation; normalize whitespace and case."""
    if not text:
        return ""

    text = re.sub(r"<[^>]+>", " ", text)
    text = re.sub(r"https?://\S+|www\.\S+", " ", text)
    text = text.lower()
    text = re.sub(r"[^a-z\s]", " ", text)
    text = re.sub(r"\s+", " ", text).strip()
    return text


def tokenize_and_stem(text: str) -> str:
    """Remove stop words and apply Porter stemming."""
    cleaned = clean_text(text)
    if not cleaned:
        return ""

    words = cleaned.split()
    stop = _get_stop_words()
    stemmed = [_stemmer.stem(w) for w in words if w not in stop and len(w) > 1]
    return " ".join(stemmed)
