<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use NlpTools\Tokenizers\WhitespaceTokenizer;
use NlpTools\Stemmers\PorterStemmer;
use LanguageDetector\LanguageDetector;

class TextController extends Controller
{
    public function preprocess(Request $request)
    {
        $text = $request->input('text');

        // Tokenize and preprocess text
        $tokenizer = new WhitespaceTokenizer();
        $stemmer = new PorterStemmer();

        // Tokenize the text
        $tokens = $tokenizer->tokenize($text);

        // Lowercase and stem each token
        $stemmedTokens = [];
        foreach ($tokens as $token) {
            $lowercasedToken = strtolower($token);
            $stemmedToken = $stemmer->stem($lowercasedToken);
            $stemmedTokens[] = $stemmedToken;
        }

        // Join the preprocessed tokens back into a string
        $preprocessedText = implode(' ', $stemmedTokens);

        $detector = new LanguageDetector();
        $languageResult = $detector->evaluate($preprocessedText);
        $languageCode = $languageResult->getLanguage();

        $confidenceScore = $detector->getScores();

        return response()->json([
            'text' => $text,
            'preprocessed_text' => $preprocessedText,
            'language_code' => (string) $languageCode,
            'confidence_score' => $confidenceScore,
        ]);
    }
}