# Configuração do GPT-Image-1

## Status Atual
O sistema está configurado para usar **DALL-E 3** por padrão, pois o modelo **gpt-image-1** requer verificação da organização.

## Como ativar o GPT-Image-1

### 1. Verificar a Organização
1. Acesse: https://platform.openai.com/settings/organization/general
2. Clique em "Verify Organization"
3. Complete o processo de verificação
4. Aguarde até 15 minutos para a propagação do acesso

### 2. Atualizar Configurações
Após a verificação, edite o arquivo `.env`:

```env
# OpenAI model for image generation (after organization verification)
OPENAI_DEFAULT_MODEL=gpt-image-1

# Default image size for GPT-Image-1 (portrait for web stories)
OPENAI_DEFAULT_SIZE=1024x1536

# Default image quality for GPT-Image-1
OPENAI_DEFAULT_QUALITY=high
```

## Vantagens do GPT-Image-1 vs DALL-E 3

### GPT-Image-1
- ✅ Qualidade superior até 4K
- ✅ Melhor renderização de texto
- ✅ Mais estilos disponíveis
- ✅ Melhor compreensão de prompts
- ❌ Requer verificação da organização

### DALL-E 3 (Atual)
- ✅ Disponível imediatamente
- ✅ Alta qualidade
- ✅ Funciona sem verificação
- ❌ Qualidade inferior ao GPT-Image-1

## Sistema de Fallback
O sistema tem fallback automático:
1. Tenta usar gpt-image-1
2. Se falhar por falta de verificação, automaticamente usa dall-e-3
3. Converte tamanhos automaticamente (1024x1536 → 1024x1792)

## Teste
Para testar se está funcionando:
1. Crie um Web Story em qualquer artigo
2. Verifique os logs em `/writable/logs/`
3. Confirme que as imagens foram geradas e salvas em `/uploads/web_stories/ai_generated/`