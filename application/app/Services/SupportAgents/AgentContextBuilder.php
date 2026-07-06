<?php

namespace App\Services\SupportAgents;

use App\Models\Knowledgebase;
use App\Models\SupportAgent;

class AgentContextBuilder
{
    /**
     * Build context and sources from selected KB categories.
     */
    public function build(SupportAgent $agent, string $audience = 'team', int $maxArticles = 6, ?string $question = null): array
    {
        $categoryIds = $agent->kbCategories()->pluck('kb_categories.kbcategory_id')->all();

        if (empty($categoryIds)) {
            return [
                'context' => '',
                'sources' => [],
            ];
        }

        $articles = Knowledgebase::query()
            ->select([
                'knowledgebase.knowledgebase_id',
                'knowledgebase.knowledgebase_title',
                'knowledgebase.knowledgebase_text',
                'knowledgebase.knowledgebase_slug',
                'kb_categories.kbcategory_id',
                'kb_categories.kbcategory_title',
                'kb_categories.kbcategory_visibility',
            ])
            ->join('kb_categories', 'kb_categories.kbcategory_id', '=', 'knowledgebase.knowledgebase_categoryid')
            ->whereIn('knowledgebase.knowledgebase_categoryid', $categoryIds)
            ->when($audience === 'client', function ($query) {
                $query->whereIn('kb_categories.kbcategory_visibility', ['everyone', 'client']);
            })
            ->when($audience !== 'client', function ($query) {
                $query->whereIn('kb_categories.kbcategory_visibility', ['everyone', 'team']);
            })
            ->orderBy('knowledgebase.knowledgebase_id', 'desc')
            ->limit(max(12, $maxArticles * 8))
            ->get();

        $questionTerms = $this->extractTerms((string) $question);

        $ranked = $articles->map(function ($article) use ($questionTerms) {
            $plainText = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $article->knowledgebase_text)));
            $title = mb_strtolower((string) $article->knowledgebase_title);
            $body = mb_strtolower($plainText);

            $score = 0;
            foreach ($questionTerms as $term) {
                if ($term === '') {
                    continue;
                }

                if (mb_stripos($title, $term) !== false) {
                    $score += 6;
                }

                if (mb_stripos($body, $term) !== false) {
                    $score += 2;
                }
            }

            if (!empty($questionTerms)) {
                $phrase = implode(' ', $questionTerms);
                if ($phrase !== '' && mb_stripos($body, $phrase) !== false) {
                    $score += 8;
                }
            }

            $article->kb_plain_text = $plainText;
            $article->kb_relevance_score = $score;

            return $article;
        });

        $articles = $ranked
            ->sort(function ($a, $b) {
                if ($a->kb_relevance_score === $b->kb_relevance_score) {
                    return $b->knowledgebase_id <=> $a->knowledgebase_id;
                }

                return $b->kb_relevance_score <=> $a->kb_relevance_score;
            })
            ->take(max(1, $maxArticles))
            ->values();

        $chunks = [];
        $sources = [];

        foreach ($articles as $index => $article) {
            $sourceNumber = $index + 1;
            $plainText = (string) ($article->kb_plain_text ?? trim(preg_replace('/\s+/u', ' ', strip_tags((string) $article->knowledgebase_text))));
            $snippet = $this->buildSnippet($plainText, $questionTerms, 1600);

            $chunks[] = "[S{$sourceNumber}] " . $article->knowledgebase_title . "\n" . $snippet;

            $sources[] = [
                'code' => 'S' . $sourceNumber,
                'knowledgebase_id' => $article->knowledgebase_id,
                'title' => $article->knowledgebase_title,
                'category' => $article->kbcategory_title,
                'visibility' => $article->kbcategory_visibility,
            ];
        }

        return [
            'context' => implode("\n\n", $chunks),
            'sources' => $sources,
        ];
    }

    private function extractTerms(string $question): array
    {
        $normalized = mb_strtolower(trim($question));
        if ($normalized === '') {
            return [];
        }

        $normalized = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $normalized);
        $parts = preg_split('/\s+/u', (string) $normalized, -1, PREG_SPLIT_NO_EMPTY);

        $stopWords = [
            'que', 'como', 'para', 'por', 'con', 'una', 'uno', 'las', 'los', 'del', 'al',
            'se', 'en', 'de', 'la', 'el', 'un', 'y', 'o', 'es', 'son', 'mi', 'tu', 'sus',
            'sobre', 'desde', 'hasta', 'donde', 'cuando', 'porque', 'tengo', 'necesito',
        ];

        $terms = [];
        foreach ($parts as $part) {
            if (mb_strlen($part) < 3) {
                continue;
            }
            if (in_array($part, $stopWords, true)) {
                continue;
            }
            $terms[] = $part;
        }

        return array_values(array_unique($terms));
    }

    private function buildSnippet(string $plainText, array $questionTerms, int $maxLength = 1600): string
    {
        if ($plainText === '') {
            return '';
        }

        if (empty($questionTerms)) {
            return mb_substr($plainText, 0, $maxLength);
        }

        $lower = mb_strtolower($plainText);
        $position = null;

        foreach ($questionTerms as $term) {
            $found = mb_stripos($lower, $term);
            if ($found !== false) {
                $position = $found;
                break;
            }
        }

        if ($position === null) {
            return mb_substr($plainText, 0, $maxLength);
        }

        $start = max(0, $position - 400);
        $snippet = mb_substr($plainText, $start, $maxLength);

        return $snippet;
    }
}
