<?php

namespace App\Traits;

use Cviebrock\EloquentSluggable\Sluggable;

trait GeneralClasses
{
    use Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'onUpdate' => true,
                'unique' => true,
                'method' => function ($string, $separator) {
                    return $this->slugify($string, $separator);
                }
            ]
        ];
    }

    /**
     * Convert the given string to a slug, preserving Persian characters.
     *
     * @param string $string
     * @param string $separator
     * @return string
     */
    protected function slugify(string $string, string $separator = '-'): string
    {
        // Remove any characters that are not letters or numbers (unicode safe)
        $string = preg_replace('/[^\pL\pN\s]+/u', '', $string);

        // Replace spaces with the separator
        $string = preg_replace('/[\s]+/u', $separator, $string);

        // Trim any separator characters from the beginning or end
        return trim($string, $separator);
    }
}
