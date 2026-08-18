<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BibleChapter extends Model
{
    protected $fillable = [
        'reference',
        'content',
        'declaration',
        'category',
    ];

    /**
     * Get a BibleChapter by reference (e.g. "Psalms 91").
     */
    public function scopeByReference($query, string $reference)
    {
        return $query->where('reference', $reference);
    }

    /**
     * Get the declaration text, falling back to personalized content
     * if no explicit declaration was stored.
     */
    public function getDeclarationText(): ?string
    {
        if (!empty($this->declaration)) {
            return $this->declaration;
        }

        return $this->personalize();
    }

    /**
     * Heuristic personalization – transforms KJV third-person
     * declarations into a first-person "I" confession.
     *
     * Rules:
     *   "He that" / "he that"  → "I"
     *   "He shall" / "he shall" → "I shall"  (when the subject is the believer)
     *   "He is" / "he is"      → "I am"      (when the subject is the believer)
     */
    public function personalize(): ?string
    {
        if (empty($this->content)) {
            return null;
        }

        $text = $this->content;

        // Apply common transformations for declaration-style chapters
        $text = preg_replace('/\bHe that\b/i', 'I', $text);
        $text = preg_replace('/\bhe that\b/i', 'I', $text);
        $text = preg_replace('/\bHe shall\b/i', 'I shall', $text);
        $text = preg_replace('/\bhe shall\b/i', 'I shall', $text);
        $text = preg_replace('/\bHe is\b/i', 'I am', $text);
        $text = preg_replace('/\bhe is\b/i', 'I am', $text);

        return $text;
    }
}
