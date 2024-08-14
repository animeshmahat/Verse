<?php

namespace App\Services;

class TextRankService
{
    public function summarizeText($title, $text)
    {
        // Remove HTML tags and decode entities
        $text = htmlspecialchars_decode(strip_tags($text));

        // Preprocessing: split text into sentences
        $sentences = $this->splitIntoSentences($text);

        // Calculate word frequencies across all sentences
        $wordFrequency = $this->calculateWordFrequency($sentences);

        // Calculate TF-IDF scores for words
        $tfidfScores = $this->calculateTFIDF($sentences, $wordFrequency);

        // Calculate sentence scores based on various factors
        $sentenceScores = $this->scoreSentences($sentences, $wordFrequency, $tfidfScores);

        // Sort sentences by score
        arsort($sentenceScores);

        // Calculate the number of sentences for the summary (approximately 25% of the original text)
        $numSentences = count($sentences);
        $summaryLength = max(1, intval($numSentences * 0.25)); // Ensure at least one sentence is selected

        // Select the top sentences for the summary
        $summarySentences = array_slice(array_keys($sentenceScores), 0, $summaryLength, true);

        // Ensure the first sentence explains the topic
        if (!in_array($sentences[0], $summarySentences)) {
            array_unshift($summarySentences, $sentences[0]);
        }

        // Include the title as the first sentence of the summary
        array_unshift($summarySentences, $title);

        // Create the paragraph summary
        $paragraphSummary = implode('. ', $summarySentences) . '.';

        // Create the bullet-point summary
        $bulletSummary = array_map('trim', $summarySentences);

        return [
            'paragraph' => $paragraphSummary,
            'bullet_points' => $bulletSummary
        ];
    }
    private function splitIntoSentences($text)
    {
        // Split the text into sentences based on common delimiters
        $sentences = preg_split('/(?<=[.?!])\s+(?=[A-Z])/i', $text);

        // Rejoin sentences that are inside quotation marks
        $quotedSentences = [];
        $insideQuote = false;
        foreach ($sentences as $sentence) {
            if (preg_match('/"[^"]+"/', $sentence)) {
                if ($insideQuote) {
                    $quotedSentences[count($quotedSentences) - 1] .= ' ' . $sentence;
                } else {
                    $quotedSentences[] = $sentence;
                }
                $insideQuote = !$insideQuote;
            } elseif ($insideQuote) {
                $quotedSentences[count($quotedSentences) - 1] .= ' ' . $sentence;
            } else {
                $quotedSentences[] = $sentence;
            }
        }

        return $quotedSentences;
    }

    private function calculateWordFrequency($sentences)
    {
        $stopWords = [
            'the', 'is', 'in', 'and', 'to', 'of', 'that', 'a', 'with', 'for',
            'as', 'on', 'it', 'this', 'by', 'an', 'be', 'are', 'at', 'from',
            'or', 'was', 'but', 'not', 'he', 'she', 'they', 'we', 'you', 'his',
            'her', 'their', 'my', 'your', 'its', 'what', 'which', 'who', 'whom',
            'this', 'these', 'those', 'all', 'any', 'each', 'few', 'more', 'most',
            'some', 'such', 'no', 'nor', 'too', 'very', 's', 't', 'can', 'will',
            'just', 'than', 'if', 'then', 'while', 'when', 'where', 'why', 'how',
            'after', 'before', 'during', 'between', 'above', 'below', 'under',
            'over', 'about', 'against', 'through', 'across', 'during', 'until',
            'since', 'despite', 'whether', 'because', 'like', 'unlike', 'except',
            'also', 'but', 'or', 'as', 'either', 'neither', 'once', 'every',
            'anyone', 'anything', 'nobody', 'nothing', 'somebody', 'somewhere'
        ]; // Add more stop words as needed
        $wordFrequency = [];
        foreach ($sentences as $sentence) {
            $words = explode(' ', $sentence);
            foreach ($words as $word) {
                $word = strtolower(trim($word));
                if (!empty($word) && !in_array($word, $stopWords)) {
                    if (!isset($wordFrequency[$word])) {
                        $wordFrequency[$word] = 0;
                    }
                    $wordFrequency[$word]++;
                }
            }
        }
        return $wordFrequency;
    }

    private function calculateTFIDF($sentences, $wordFrequency)
    {
        $tfidfScores = [];
        $totalSentences = count($sentences);
        foreach ($sentences as $sentence) {
            $words = explode(' ', $sentence);
            foreach ($words as $word) {
                $word = strtolower(trim($word));
                if (!empty($word) && isset($wordFrequency[$word])) {
                    $tf = $wordFrequency[$word] / count($words); // Term frequency
                    $idf = log($totalSentences / $wordFrequency[$word]); // Inverse document frequency
                    if (!isset($tfidfScores[$word])) {
                        $tfidfScores[$word] = 0;
                    }
                    $tfidfScores[$word] += $tf * $idf; // TF-IDF score
                }
            }
        }
        return $tfidfScores;
    }
    private function scoreSentences($sentences, $wordFrequency, $tfidfScores)
    {
        $sentenceScores = [];
        $totalSentences = count($sentences);
        foreach ($sentences as $index => $sentence) {
            $sentenceScores[$sentence] = 0;
            $words = explode(' ', $sentence);
            $sentenceLength = count($words);
            foreach ($words as $word) {
                $word = strtolower(trim($word));
                if (isset($wordFrequency[$word]) && isset($tfidfScores[$word])) {
                    // Weighted sum of word frequency and TF-IDF score
                    $sentenceScores[$sentence] += $wordFrequency[$word] + $tfidfScores[$word];
                }
            }
            // Penalize sentence length
            $sentenceScores[$sentence] -= abs(20 - $sentenceLength);
            // Additional scoring based on position and keywords
            $positionScore = 1 - abs($index - $totalSentences / 2) / ($totalSentences / 2);
            $sentenceScores[$sentence] += $positionScore;
            // Example keyword boost
            if (stripos($sentence, 'important') !== false) {
                $sentenceScores[$sentence] += 2;
            }
        }

        // Sort sentences by score, keeping keys
        arsort($sentenceScores);

        // If the last sentence of the original text is included, ensure it is the last sentence of the summary
        if (isset($sentenceScores[end($sentences)])) {
            $lastSentence = end($sentences);
            unset($sentenceScores[$lastSentence]);
            $sentenceScores[$lastSentence] = 0; // Reset score to ensure it is placed at the end
        }

        return $sentenceScores;
    }
}
