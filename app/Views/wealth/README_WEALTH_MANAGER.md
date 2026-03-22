Wealth Manager (GX Capital)

Rotas públicas:
- `/wealth` (landing)
- `/wealth/conversa` (chat com agente, requer login)
- `/wealth/resultado` (resultado com gráficos)
- `/wealth/agendar` (formulário de agendamento)
- `/wealth/resultado/pdf` (download de resumo básico)

Rotas admin:
- `/admin/wealth` (visão geral)
- `/admin/wealth/settings` (configurações)
- `/admin/wealth/tokens` (gestão de tokens)
- `/admin/wealth/appointments` (agendamentos)
- `/admin/wealth/cms` (conteúdo da landing)
- `/admin/wealth/export` (exportação de dossiê CSV)

Tabelas:
- wm_user_profile, wm_income_expense, wm_assets_financial, wm_assets_realestate, wm_business_holdings, wm_liabilities, wm_goals, wm_sessions, wm_messages, wm_tokens, wm_settings, wm_appointments, wm_audit_logs

Configurações (wm_settings):
- wm_model, wm_inflacao, wm_crescimento_renda, wm_limit_senior, wm_credit_after_confirm, wm_credit_amount, wm_landing_content, wm_copy_json

Como habilitar:
1. Executar as migrations
2. Acessar Admin > Wealth Manager > Configurações e ajustar parâmetros
3. (Opcional) Editar landing no CMS

Observações:
- Primeira sessão grátis: criada automaticamente ao abrir `/wealth/conversa` se o usuário não tiver registro em wm_tokens
- PDF: exporta HTML simples para download (sem lib externa)
- Analytics: contadores gravados em wm_settings (wm_analytics_*)

