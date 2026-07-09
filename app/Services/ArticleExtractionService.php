<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ArticleExtractionService
{
    public function extract(string $url): string
    {
        $url = trim($url);
        $this->assertSafeUrl($url);

        try {
            $request = Http::timeout(config('fni.url_fetch_timeout'))
                ->withHeaders([
                    'User-Agent' => 'FNI/1.0 (+https://fni.local)',
                    'Accept' => 'text/html,application/xhtml+xml',
                ]);

            $caBundle = config('fni.url_ca_bundle');

            if (is_string($caBundle) && $caBundle !== '' && is_file($caBundle)) {
                $request->withOptions(['verify' => $caBundle]);
            }

            $response = $request->get($url);
        } catch (ConnectionException) {
            throw new RuntimeException('Could not reach that URL. Check the link and try again.');
        }

        if ($response->failed()) {
            throw new RuntimeException('Could not fetch the page (HTTP '.$response->status().').');
        }

        $body = $response->body();
        $maxBytes = config('fni.url_max_bytes');

        if (strlen($body) > $maxBytes) {
            throw new RuntimeException('Page is too large to process.');
        }

        $text = $this->parseHtml($body);

        if (strlen($text) < config('fni.min_input_chars')) {
            throw new RuntimeException('Could not extract enough article text from that URL.');
        }

        if (strlen($text) > config('fni.max_input_chars')) {
            $text = substr($text, 0, config('fni.max_input_chars'));
        }

        return $text;
    }

    private function assertSafeUrl(string $url): void
    {
        if (strlen($url) > 2048) {
            throw new RuntimeException('URL is too long.');
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('Please enter a valid URL.');
        }

        $scheme = strtolower(parse_url($url, PHP_URL_SCHEME) ?? '');

        if (! in_array($scheme, ['http', 'https'], true)) {
            throw new RuntimeException('Only http and https URLs are supported.');
        }

        $host = parse_url($url, PHP_URL_HOST);

        if (! $host) {
            throw new RuntimeException('Please enter a valid URL.');
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            if (! $this->isPublicIp($host)) {
                throw new RuntimeException('That URL is not allowed.');
            }

            return;
        }

        $host = strtolower($host);

        if ($host === 'localhost' || str_ends_with($host, '.localhost')) {
            throw new RuntimeException('That URL is not allowed.');
        }

        $ips = $this->resolveHostIps($host);

        if ($ips === []) {
            throw new RuntimeException('Could not resolve that URL.');
        }

        foreach ($ips as $ip) {
            if (! $this->isPublicIp($ip)) {
                throw new RuntimeException('That URL is not allowed.');
            }
        }
    }

    private function resolveHostIps(string $host): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return [$host];
        }

        $ips = [];

        if (\function_exists('dns_get_record')) {
            $records = @\dns_get_record($host, DNS_A + DNS_AAAA);

            if ($records !== false) {
                foreach ($records as $record) {
                    if (isset($record['ip'])) {
                        $ips[] = $record['ip'];
                    }

                    if (isset($record['ipv6'])) {
                        $ips[] = $record['ipv6'];
                    }
                }
            }
        }

        if ($ips === []) {
            $ip = \gethostbyname($host);

            if ($ip !== $host) {
                $ips[] = $ip;
            }
        }

        return array_values(array_unique($ips));
    }

    private function isPublicIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }

    private function parseHtml(string $html): string
    {
        \libxml_use_internal_errors(true);

        $document = new \DOMDocument;
        $document->loadHTML(
            '<?xml encoding="UTF-8">'.$html,
            LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NONET
        );

        \libxml_clear_errors();

        foreach (['script', 'style', 'nav', 'footer', 'header', 'aside', 'noscript'] as $tag) {
            while (($nodes = $document->getElementsByTagName($tag))->length > 0) {
                $nodes->item(0)->parentNode?->removeChild($nodes->item(0));
            }
        }

        $candidates = [];

        foreach (['article', 'main'] as $tag) {
            $nodes = $document->getElementsByTagName($tag);

            for ($i = 0; $i < $nodes->length; $i++) {
                $text = $this->normalizeText($nodes->item($i)?->textContent ?? '');

                if ($text !== '') {
                    $candidates[] = $text;
                }
            }
        }

        if ($candidates !== []) {
            usort($candidates, fn (string $a, string $b) => strlen($b) <=> strlen($a));

            return $candidates[0];
        }

        $paragraphs = [];

        foreach ($document->getElementsByTagName('p') as $node) {
            $text = $this->normalizeText($node->textContent ?? '');

            if (strlen($text) >= 40) {
                $paragraphs[] = $text;
            }
        }

        return $this->normalizeText(implode("\n\n", $paragraphs));
    }

    private function normalizeText(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';

        return trim($text);
    }
}
