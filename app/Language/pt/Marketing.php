<?php

/**
 * Camada de marketing/módulos (white-label — Fase 2 i18n) — PORTUGUÊS.
 *
 * Strings de UI da GX. Para outra marca/idioma, criar
 * app/Language/<locale>/Marketing.php (ex.: es/) e definir brand('locale').
 * As views consomem via lang('Marketing.<chave>') — o locale é resolvido por
 * brandLocale() (BaseController::initController).
 *
 * Fase 2 (espinha): catálogo iniciado. As ~40 strings de prosa do HomeController
 * e as views de marketing/simuladores serão migradas para cá em seguida.
 */
return [
    // Chave de prova da infra (não exibida em UI).
    '__proof'            => 'i18n-ok-pt',

    // CTAs recorrentes (início do catálogo — ainda não consumidos pelas views).
    'falar_especialista' => 'Falar com especialista',
    'simular_agora'      => 'Simular agora',
    'ver_simuladores'    => 'Ver simuladores',

    // Formulário "Fale com um especialista" (_specialist_form.php)
    'form_heading'       => 'Fale com um especialista',
    'form_description'   => 'Conte o contexto da operação e retornamos com a frente mais aderente.',
    'form_button'        => 'Enviar mensagem',
    'form_placeholder'   => 'Descreva brevemente sua necessidade, objetivo ou estrutura atual.',
    'form_origin_prefix' => 'Formulário especialista - ',
    'form_nome'          => 'Nome',
    'form_email'         => 'E-mail',
    'form_contexto'      => 'Contexto',
    'form_terms_pre'     => 'Li e concordo com os',
    'form_terms_link'    => 'termos e condições',
    'form_captcha_hint'  => 'A verificação de segurança será carregada quando você interagir com o formulário.',
    'form_note_mobile'   => 'Formulário otimizado para preenchimento rápido no celular.',
];
