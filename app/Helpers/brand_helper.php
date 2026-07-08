<?php

use Config\Globals;

/**
 * Brand helper (white-label — Fase 0).
 * Acessa a configuração de marca carregada em Globals::$brand (tabela brand_settings).
 * Tudo aqui é tolerante a ausência: se a marca não estiver carregada (install pré-Fase 0
 * sem a tabela) ou a chave for vazia, retorna o $default. Nada consome isto ainda na Fase 0.
 */

if (!function_exists('brand')) {
    /**
     * Valor de marca por chave. Ex.: brand('display_name'), brand('color_primary', '#000').
     */
    function brand(string $key, $default = null)
    {
        $b = Globals::$brand ?? null;
        if (empty($b) || !isset($b->$key) || $b->$key === null || $b->$key === '') {
            return $default;
        }
        return $b->$key;
    }
}

if (!function_exists('brandObj')) {
    /** Objeto de marca completo (ou null). */
    function brandObj()
    {
        return Globals::$brand ?? null;
    }
}

if (!function_exists('brandPressMentions')) {
    /** press_mentions_json decodificado como array (ou []). */
    function brandPressMentions(): array
    {
        $json = brand('press_mentions_json');
        if (empty($json)) {
            return [];
        }
        $arr = json_decode($json, true);
        return is_array($arr) ? $arr : [];
    }
}

if (!function_exists('brandLang')) {
    /**
     * lang() + interpolação do nome da marca. A string de idioma usa o token
     * literal {brand}; aqui ele é trocado por brand('display_name') via strtr —
     * SEM passar pelo MessageFormatter do CI4 (não usamos $args), então caracteres
     * como R$, %, chaves e apóstrofos ficam intactos (byte-safe).
     *
     * Uso: brandLang('Home.wa_home')  // '... da {brand} ...' -> '... da GX Capital ...'
     */
    function brandLang(string $key): string
    {
        $s = lang($key);
        if (!is_string($s)) {
            return (string) $s;
        }
        return strtr($s, ['{brand}' => (string) brand('display_name', 'GX Capital')]);
    }
}

if (!function_exists('brandLocale')) {
    /**
     * Locale curto do install (subtag primária de brand('locale')).
     * Ex.: 'pt-BR' -> 'pt', 'es-MX' -> 'es'. Default 'pt' (GX).
     * Usado para resolver os arquivos de idioma da camada de marketing via lang().
     */
    function brandLocale(): string
    {
        $primary = explode('-', (string) brand('locale', 'pt-BR'))[0];
        return $primary !== '' ? strtolower($primary) : 'pt';
    }
}

if (!function_exists('brandCssVars')) {
    /**
     * Bloco <style> com CSS custom properties da marca, para injetar no <head>.
     * PLUMBING: não é usado ainda na Fase 0 — pronto para a Fase 1 tokenizar as cores.
     */
    function brandCssVars(): string
    {
        $primary   = brand('color_primary', '#0c3163');
        $gold      = brand('color_gold', '#c9a96a');
        $secondary = brand('color_secondary', '#dbc7a2');

        return '<style id="brand-tokens">:root{'
            . '--brand-primary:' . $primary . ';'
            . '--brand-gold:' . $gold . ';'
            . '--brand-secondary:' . $secondary . ';'
            . '}</style>';
    }
}
