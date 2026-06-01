<?php

namespace App\Libraries;

class TrendNormalizer
{
    protected static array $stopwords = [
        // articles + prepositions
        'a','o','as','os','um','uma','uns','umas','de','da','do','das','dos',
        'em','no','na','nos','nas','por','para','pra','pro','com','sem',
        'sob','sobre','ate','desde','entre',
        // pronouns
        'eu','tu','ele','ela','nos','vos','eles','elas','me','te','se','lhe','lhes',
        'meu','minha','seu','sua','nosso','nossa','dele','dela','isso','isto','aquilo',
        // conjunctions
        'e','ou','mas','porem','contudo','todavia','entao','logo','assim','que','se',
        'quando','onde','como','porque','pois','enquanto',
        // verbs (auxiliares e comuns sem valor informacional)
        'ser','sera','foi','era','sao','sou','sera','estar','esta','estao','estava',
        'ter','tem','tinha','tera','havia','vai','vem','foi','ficou',
        // common filler
        'mais','menos','muito','pouco','tudo','nada','algo','outro','outra','mesmo',
        'apenas','ja','ainda','tambem','depois','antes','hoje','ontem','amanha',
        'pode','podem','poderia','deve','devem',
        // common headline words (low signal)
        'apos','sera','sera','noticias','noticia','agora','novo','nova','novos','novas',
        'dia','dias','ano','anos','mes','meses','hoje','semana',
    ];

    /**
     * Strip accents, lowercase, keep alphanumeric only.
     */
    public static function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        if (function_exists('iconv')) {
            $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        }
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    /**
     * Build a semantic fingerprint for cross-source dedup.
     * Two headlines with the same meaningful tokens produce the same hash
     * regardless of word order, accents, casing, stopwords or filler.
     */
    public static function semanticHash(string $title): string
    {
        $normalized = self::normalize($title);
        $tokens = array_filter(explode(' ', $normalized), function ($t) {
            return mb_strlen($t) >= 3 && !in_array($t, self::$stopwords, true);
        });
        // Light stemming: collapse common PT-BR suffix variants
        $stemmed = array_map([self::class, 'stem'], $tokens);
        $stemmed = array_unique($stemmed);
        sort($stemmed);
        return md5(implode(' ', $stemmed));
    }

    /**
     * Very lightweight Portuguese stemmer — collapses plurals and common
     * verbal/gender endings so "dolares" == "dolar", "alta" ~~ "alto".
     */
    public static function stem(string $token): string
    {
        $len = mb_strlen($token);
        if ($len <= 4) {
            return $token;
        }
        $suffixes = ['mente', 'oes', 'aes', 'ais', 'eis', 'ois', 'uis', 'ndo', 'ado', 'ada', 'ido', 'ida', 'ar', 'er', 'ir', 'es', 'as', 'os', 'a', 'o', 's'];
        foreach ($suffixes as $sfx) {
            $sl = strlen($sfx);
            if ($len - $sl >= 4 && substr($token, -$sl) === $sfx) {
                return substr($token, 0, $len - $sl);
            }
        }
        return $token;
    }

    /**
     * Source authority weight (0-10). Higher = more trusted/regulatory.
     */
    public static function sourceAuthority(string $source): int
    {
        $map = [
            'bacen'                => 10,
            'cvm'                  => 10,
            'valor_economico'      =>  9,
            'google_trends'        =>  8, // native demand signal
            'infomoney'            =>  7,
            'investing_br'         =>  6,
            'google_news_finance'  =>  5,
            'google_news_market'   =>  5,
        ];
        return $map[$source] ?? 4;
    }
}
