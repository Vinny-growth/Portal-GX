<?php

/**
 * Camada de marketing — strings da view simulators/consorcio.php
 * (white-label — Fase 2 i18n, Lote 4) — PORTUGUÊS.
 *
 * Catálogo SEPARADO de Simuladores.php de propósito: o seguro está sob refatoração
 * concorrente (redesign do gate) e mexer no catálogo compartilhado arriscaria varrer
 * WIP alheio para um commit meu. Consumido por lang('Consorcio.<chave>') e, quando a
 * string carrega o nome completo da marca, por brandLang('Consorcio.<chave>') — o
 * token literal {brand} é trocado por brand('display_name'). Menções curtas "GX"
 * seguem literais. Reconciliável com Simuladores.php depois que o seguro estabilizar.
 *
 * Migração incremental por sub-lote (4b-N).
 */
return [
    // ===================================================================
    // 4b-1 — topo de marketing (nav, hero, quick-lead, strip, autoridade)
    // ===================================================================

    // navegação
    'nav_mesa'        => 'A Mesa',
    'nav_estrategias' => 'Estratégias',
    'nav_simulador'   => 'Simulador',
    'nav_tecnologia'  => 'Tecnologia',
    'nav_especialista' => 'Especialista',
    'nav_blog'        => 'Blog',
    'nav_cta'         => 'Receber meu plano',
    'nav_menu'        => 'Menu',

    // prova do hero
    'hp1_title' => '20+ administradoras',
    'hp1_text'  => 'Você não fica preso a uma prateleira. Comparamos as melhores opções do mercado.',
    'hp2_title' => '1.000+ grupos analisados',
    'hp2_text'  => 'IA cruza prazo, taxa e assembleia para achar o grupo certo para o seu perfil.',
    'hp3_title' => 'Resultado em minutos',
    'hp3_text'  => 'Diagnóstico imediato com números reais para decidir com segurança.',

    // hero
    'hero_badge'       => 'Exclusivo {brand} &mdash; simulador com inteligência artificial',
    'hero_title'       => 'Consórcio ou financiamento? Simule e descubra.',
    'hero_copy'        => 'A {brand} cruza 20+ administradoras com IA para encontrar a rota de contemplação mais rápida para o seu caso.',
    'hero_cta'         => 'Simular agora &mdash; grátis',
    'hero_reassurance' => 'Gratuito &bull; Sem compromisso &bull; Resultado em minutos',

    // mini-simulador do hero
    'mini_title'         => 'Simulação rápida',
    'mini_credit_label'  => 'Quanto você quer de carta de crédito?',
    'mini_months_label'  => 'Em quanto tempo quer ser contemplado?',
    'mini_btn'           => 'Ver minha economia estimada &rarr;',
    'mini_result_suffix' => 'em relação ao financiamento tradicional.',
    'mini_result_link'   => 'Quer o plano detalhado? &rarr;',

    // quick-lead
    'quick_h2'      => 'Prefere que um especialista simule para você?',
    'quick_p'       => 'Deixe seu WhatsApp e receba um plano personalizado em até 24h. Sem compromisso.',
    'quick_ph_name' => 'Seu nome',
    'quick_btn'     => 'Quero meu plano gratuito',
    'quick_trust'   => 'Seus dados estão seguros. Sem spam.',

    // strip de passos
    'strip_lead' => '4 passos',
    'strip_1'    => 'Escolha seu objetivo',
    'strip_2'    => 'Ajuste os números',
    'strip_3'    => 'Veja o resultado ao vivo',
    'strip_4'    => 'Receba o plano do especialista',

    // autoridade (quem-somos)
    'auth_label' => 'Quem está por trás do simulador',
    'auth_title' => 'Consórcio com estratégia de contemplação, não com parcela genérica.',
    'auth_desc'  => 'A {brand} é uma boutique financeira fundada por <strong>Vinicius Teixeira</strong>, com mais de 16 anos de atuação em mercado financeiro. A frente de consórcio nasceu da mesma lógica que a mesa de câmbio e crédito: comparar dezenas de opções, filtrar com tecnologia e recomendar a estrutura mais eficiente para cada perfil.',
    'auth_card1_label' => 'Comparativo estruturado',
    'auth_card1_title' => 'Consórcio x financiamento com números reais, não com propaganda.',
    'auth_card1_desc'  => 'A decisão entre consórcio e financiamento depende de entrada disponível, custo total, prazo e capacidade de lance. O simulador coloca os dois lado a lado para que a conta fale por si.',
    'auth_card2_label' => 'Planejamento de contemplação',
    'auth_card2_title' => 'Rota para contemplar no prazo certo, com o grupo certo.',
    'auth_card2_desc'  => 'A IA cruza mais de 20 administradoras e 1.000+ grupos para encontrar a combinação de taxa, assembleia e estratégia de lance mais aderente ao seu fluxo de caixa e objetivo.',
    'auth_card3_label' => 'Tese de investimento',
    'auth_card3_title' => 'Consórcio como veículo de retorno, não só de compra.',
    'auth_card3_desc'  => 'Para quem quer contemplar e revender com margem, o simulador projeta ROI líquido, custo de carregamento e break-even. O especialista valida a tese antes de você entrar no grupo.',
    'boutique_note'    => '<strong>Modelo independente:</strong> a {brand} não é administradora de consórcio e não tem cota própria para vender. Isso permite recomendar o grupo, a administradora e a estratégia de lance mais eficiente para o seu caso, sem viés comercial. Os simuladores abaixo são o primeiro passo para dimensionar a operação antes de falar com o especialista.',

    // ===================================================================
    // 4b-2 — depoimentos (fallbacks) + estratégias + cards de cenário + header do simulador
    // ===================================================================

    // depoimentos (fallbacks do config + aria)
    'testi_label' => 'Quem já simulou',
    'testi_title' => 'Quem já simulou, aprovou',
    'testi_stars' => '5 estrelas',

    // seção estratégias
    'estr_label' => 'Escolha sua jornada',
    'estr_title' => 'Qual é o seu objetivo com o consórcio? Comece pelo cenário certo.',
    'estr_desc'  => "Cada objetivo pede uma conta diferente. Escolha o cenário que mais se parece com o seu momento\n                    e veja os números que realmente importam para a sua decisão.",

    // cards de cenário ($scenarioCards) — pílula + compare/planned/investor
    'sc_gratis'           => 'Simulação gratuita',
    'sc_compare_eyebrow'  => 'Mais popular',
    'sc_compare_title'    => 'Consórcio x financiamento',
    'sc_compare_desc'     => 'Já tem entrada? Veja em números se o consórcio reduz a parcela mensal e o custo total comparado ao financiamento.',
    'sc_compare_b1'       => 'Descubra a diferença real de parcela entre consórcio e financiamento.',
    'sc_compare_b2'       => 'Veja quanto sua entrada acelera a contemplação como lance.',
    'sc_compare_b3'       => 'Saiba exatamente quanto você economiza no desembolso total.',
    'sc_compare_btn'      => 'Comparar agora',
    'sc_planned_eyebrow'  => 'Compra inteligente',
    'sc_planned_title'    => 'Comprar imóvel de forma planejada',
    'sc_planned_desc'     => 'Quer comprar sem apertar o caixa? Veja se seu ritmo mensal sustenta a contemplação no prazo que você precisa.',
    'sc_planned_b1'       => 'Saiba quanto de reserva você terá para dar lance no mês certo.',
    'sc_planned_b2'       => 'Veja se parcela + reserva cabem no seu orçamento confortável.',
    'sc_planned_b3'       => 'Descubra se sobra ou aperta antes de se comprometer.',
    'sc_planned_btn'      => 'Planejar minha compra',
    'sc_investor_eyebrow' => 'Alta rentabilidade',
    'sc_investor_title'   => 'Investir para revender após contemplação',
    'sc_investor_desc'    => 'Use o consórcio como alavanca patrimonial. Veja se a margem de revenda compensa o capital investido.',
    'sc_investor_b1'      => 'Calcule o capital total que você precisa mobilizar até a venda.',
    'sc_investor_b2'      => 'Veja a margem líquida real depois de todos os custos de saída.',
    'sc_investor_b3'      => 'Descubra o ROI da operação sobre o seu dinheiro travado.',
    'sc_investor_btn'     => 'Calcular retorno',

    // seção simulador ao vivo
    'sim_label'          => 'Simulador ao vivo',
    'sim_title'          => 'Ajuste os números do seu caso e veja o resultado mudar em tempo real.',
    'sim_desc'           => "Mexa nos campos abaixo e acompanhe o impacto na parcela, no lance e no custo total. Quando estiver satisfeito,\n                    envie a simulação e receba o plano de contemplação do especialista.",
    'sim_switcher_aria'  => 'Escolha a jornada do simulador',
    'sim_infobar_strong' => 'Os resultados atualizam ao vivo.',
    'sim_infobar_span'   => 'Mude qualquer campo e veja o impacto na parcela, no custo total e na viabilidade da contemplação.',

    // ===================================================================
    // 4b-3 — formulário do simulador (4 fieldsets). Sufixo '%' fica literal.
    // ===================================================================
    'f_meses' => 'meses',
    'f_mes'   => 'mês',
    'f_aa'    => '% a.a.',

    // fieldset: dados da cota
    'f_cota_head'  => 'Dados da cota',
    'f_cota_desc'  => 'Informe o valor do bem e as condições do grupo. Esses campos valem para qualquer cenário.',
    'f_credit'     => 'Valor da carta de crédito',
    'f_credit_h'   => 'Use o valor do imóvel ou do ativo que pretende adquirir.',
    'f_term'       => 'Prazo do grupo',
    'f_term_h'     => 'Prazo total de pagamento da cota.',
    'f_admin'      => 'Taxa administrativa',
    'f_admin_h'    => 'Percentual total de taxa administrativa considerado na estrutura.',
    'f_reserve'    => 'Fundo de reserva',
    'f_reserve_h'  => 'Percentual adicional para reserva e composição do custo.',
    'f_target'     => 'Meta de contemplação',
    'f_target_h'   => 'Janela que você deseja mirar para a contemplação.',

    // fieldset: compare com o financiamento
    'f_cmp_head'   => 'Compare com o financiamento',
    'f_cmp_desc'   => 'Informe quanto você tem de entrada e as condições do financiamento para ver a diferença real.',
    'f_entry'      => 'Recurso disponível para entrada/lance',
    'f_entry_h'    => 'Valor que você já teria para usar no início da operação.',
    'f_frate'      => 'Taxa anual do financiamento',
    'f_frate_h'    => 'Taxa nominal usada para o comparativo da linha de crédito tradicional.',
    'f_fterm'      => 'Prazo do financiamento',
    'f_fterm_h'    => 'Prazo total do contrato de financiamento usado como referência.',
    'f_fcost'      => 'Custos iniciais do financiamento',
    'f_fcost_h'    => 'Inclua tarifas, seguros embutidos ou custos acessórios de contratação.',

    // fieldset: planeje sua compra
    'f_pln_head'    => 'Planeje sua compra',
    'f_pln_desc'    => 'Informe quanto consegue guardar por mês e veja se dá para contemplar no prazo que você precisa.',
    'f_avail'       => 'Reserva disponível hoje',
    'f_avail_h'     => 'Valor já disponível para compor a estratégia de lance.',
    'f_mreserve'    => 'Reserva mensal para lance',
    'f_mreserve_h'  => 'Quanto você consegue acumular por mês além da parcela.',
    'f_mbudget'     => 'Orçamento mensal confortável',
    'f_mbudget_h'   => 'Teto que você deseja respeitar somando parcela e formação de reserva.',
    'f_correction'  => 'Correção anual esperada do ativo',
    'f_correction_h' => 'Serve para ajustar o valor de referência do imóvel até a contemplação.',

    // fieldset: calcule o retorno da revenda
    'f_inv_head'     => 'Calcule o retorno da revenda',
    'f_inv_desc'     => 'Informe o capital disponível e a margem esperada para ver se a operação vale o investimento.',
    'f_bidcash'      => 'Caixa para lance próprio',
    'f_bidcash_h'    => 'Capital que você pretende usar para acelerar a contemplação.',
    'f_margin'       => 'Margem esperada na revenda',
    'f_margin_h'     => 'Margem bruta estimada entre aquisição e revenda.',
    'f_salecost'     => 'Custos de saída e transação',
    'f_salecost_h'   => 'Inclua corretagem, impostos, documentação e atritos da operação.',
    'f_holdmonths'   => 'Meses até a revenda após contemplação',
    'f_holdmonths_h' => 'Horizonte entre contemplação, aquisição e venda do ativo.',
    'f_holdcost'     => 'Custo mensal de carregamento',
    'f_holdcost_h'   => 'Condomínio, manutenção, carência de aluguel ou outro custo de carregamento.',

    // ===================================================================
    // 4b-4 — painel de resultados + card do especialista + agendamento
    // ===================================================================

    // painel de resultados (defaults SSR; o JS reescreve por cenário)
    'res_kicker'         => 'Comparativo estratégico',
    'res_headline'       => 'Use a sua entrada como lance e compare o consórcio com o financiamento.',
    'res_body'           => 'Ajuste os campos do seu caso para acompanhar parcela indicativa, custo total e nível de esforço até a contemplação.',
    'res_live'           => 'Ao vivo',
    'res_indicador'      => 'Indicador',
    'res_band_a'         => 'Estratégia',
    'res_band_b'         => 'Leitura GX',
    'res_insights_title' => 'Leitura executiva inicial',
    'res_disclaimer'     => 'Simulação indicativa. A recomendação final depende de grupo, administradora, histórico de assembleia e estratégia de lance disponível no momento da contratação.',

    // card do especialista (fale-especialista)
    'spec_eyebrow'   => 'Último passo',
    'spec_title'     => 'Transforme essa simulação em um plano real de contemplação.',
    'spec_copy'      => 'Envie seus dados e um especialista vai analisar seu cenário, selecionar os melhores grupos e montar a estratégia de lance para o seu caso.',
    'spec_ch_eyebrow' => 'Resposta rápida',
    'spec_ch_strong' => 'Quer falar agora com o especialista?',
    'spec_ch_p'      => 'A conversa já começa com os dados do seu cenário. Sem precisar repetir informações.',
    'spec_wa_btn'    => 'Falar pelo WhatsApp',
    'spec_send_btn'  => 'Enviar simulação',
    'spec_ch_note'   => 'Atendimento em horário comercial com especialista dedicado.',
    'spec_ctx_label' => 'Cenário atual',
    'spec_ctx_hook'  => 'Vamos comparar sua entrada, o custo mensal e o desembolso total antes da decisão.',
    'spec_promise_1' => 'Você recebe a seleção dos melhores grupos para o seu objetivo e perfil.',
    'spec_promise_2' => 'Saiba exatamente quanto dar de lance e quanto pagar por mês sem apertar.',
    'spec_promise_3' => 'Plano de contemplação completo, não apenas uma parcela estimada.',
    'spec_link_wa'   => 'WhatsApp',
    'spec_link_sims' => 'Ver outros simuladores',
    'spec_lbl_nome'  => 'Nome',
    'spec_lbl_email' => 'E-mail',
    'spec_phone_hint' => 'Selecione o país e informe o telefone principal para retorno.',
    'spec_lbl_notes' => 'Observações adicionais',
    'spec_notes_ph'  => 'Se quiser, detalhe o prazo desejado, o tipo de imóvel ou a tese de revenda.',
    'spec_terms_pre' => 'Li e concordo com os',
    'spec_terms_link' => 'termos e condições',
    'spec_submit'    => 'Receber meu plano de contemplação',
    'spec_note'      => 'Retorno consultivo e personalizado. Nada de proposta genérica.',

    // etapa de agendamento (pós-envio)
    'sched_badge' => 'Próximo passo',
    'sched_title' => 'Simulação recebida. Agende com o especialista.',
    'sched_desc'  => 'Seus dados já foram enviados. Escolha o melhor horário para conversar com o especialista em consórcio e transformar a simulação em plano de contemplação.',
    'sched_cta'   => 'Agendar conversa com especialista',
    'sched_alt'   => 'Prefere outro canal?',
    'sched_note'  => 'Seus dados já estarão preenchidos na agenda. Basta escolher data e horário.',

    // ===================================================================
    // 4b-5 — tecnologia (aiSteps) + callout final + FAB + exit popup
    // ===================================================================

    // seção tecnologia-ia
    'tec_label' => 'Como funciona',
    'tec_title' => 'Da simulação ao plano de contemplação em 3 passos.',
    'tec_desc'  => 'A IA faz o trabalho pesado de análise e comparação. O especialista garante que o plano final faz sentido para o seu momento, seu caixa e seu objetivo.',

    // passos da IA ($aiSteps) — números 01/02/03 ficam literais
    'ai1_title' => 'IA filtra os melhores grupos para você',
    'ai1_desc'  => 'Em vez de pesquisar dezenas de opções, a inteligência artificial elimina os grupos incompatíveis e destaca os que mais se encaixam no seu objetivo.',
    'ai1_i1'    => 'Prazos e taxas compatíveis com o valor que você precisa.',
    'ai1_i2'    => 'Grupos com histórico favorável de contemplação.',
    'ai1_i3'    => 'Alinhamento com seu objetivo: compra, revenda ou comparativo.',
    'ai2_title' => 'Monta o plano de contemplação sob medida',
    'ai2_desc'  => 'A partir do seu caixa, ritmo mensal e prazo desejado, a IA calcula a melhor estratégia de lance para contemplar com o menor custo possível.',
    'ai2_i1'    => 'Quanto dar de lance para contemplar no prazo certo.',
    'ai2_i2'    => 'Impacto real da parcela no seu fluxo mensal.',
    'ai2_i3'    => 'Ajustes para acelerar a contemplação ou proteger caixa.',
    'ai3_title' => 'Especialista valida e você decide com segurança',
    'ai3_desc'  => 'O plano gerado pela IA passa pela análise do especialista, que confere administradora, grupo e timing antes de você dar o próximo passo.',
    'ai3_i1'    => 'Plano de contemplação claro, sem jargão técnico.',
    'ai3_i2'    => 'Riscos mapeados e alternativas para cada cenário.',
    'ai3_i3'    => 'Você decide com todas as informações na mesa.',

    // callout final
    'cta_label'       => 'Pronto para avançar?',
    'cta_title'       => 'Você já tem os números. Agora deixe o especialista montar o plano para contemplar.',
    'cta_copy'        => "Envie sua simulação e receba a análise completa com os melhores grupos, a estratégia de lance ideal\n                        e um cronograma realista de contemplação para o seu caso.",
    'cta_btn_wa'      => 'Chamar no WhatsApp',
    'cta_btn_contact' => 'Ir para contato',

    // exit popup
    'exit_close'   => 'Fechar',
    'exit_title'   => 'Espera! Quer receber uma simulação personalizada?',
    'exit_p'       => 'Nosso especialista faz a simulação para você em até 24h.',
    'exit_submit'  => 'Receber minha simulação',
    'exit_dismiss' => 'Não, obrigado',
];
