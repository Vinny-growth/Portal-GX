# Meta Conversions API - Documentação

## Configuração

### 1. Configuração no Facebook Business Manager

1. Acesse o **Gerenciador de Eventos** do Facebook
2. Selecione seu Pixel e copie o **Pixel ID**
3. Vá em **Configurações** → **Conversions API**
4. Gere um **Access Token** e copie o **Dataset ID**
5. Para testes, ative o **Modo de Teste** e adicione um **Test Event Code**

### 2. Configuração no Sistema

1. Acesse `/admin/general-settings`
2. Role até a seção **Meta Conversions API (Facebook)**
3. Preencha os campos:
   - **Pixel ID do Facebook**: Seu ID do pixel
   - **Access Token**: Token de acesso gerado
   - **Dataset ID**: ID do conjunto de dados
   - **Test Event Code**: Código de teste (opcional, só para testes)
4. Ative a API e configure os eventos desejados
5. Clique em **Salvar configurações Meta API**

## Uso no Código

### Funções Disponíveis

#### 1. Rastrear Evento Genérico
```php
trackMetaEvent('CustomEvent', $eventData, $userData);
```

#### 2. Rastrear Page View
```php
trackMetaPageView($pageUrl);
```

#### 3. Rastrear Lead
```php
trackMetaLead($email, $phone, $name, $customData);
```

#### 4. Rastrear Cadastro Completo
```php
trackMetaCompleteRegistration($email, $customData);
```

#### 5. Rastrear Contato
```php
trackMetaContact($email, $customData);
```

### Exemplos Práticos

#### Exemplo 1: Rastrear Lead do Simulador (já implementado)
```php
// No SimLeadModel->addSimLead()
$customData = [
    'content_name' => 'Simulador de Risco Cambial',
    'content_category' => 'Lead Generation',
    'value' => 1,
    'currency' => 'BRL'
];

trackMetaLead($email, $phone, $name, $customData);
```

#### Exemplo 2: Rastrear Page View em Controladores
```php
// Em qualquer controller
public function index()
{
    // Rastrear visualização da página
    trackMetaPageView(current_url());
    
    // Resto do código...
}
```

#### Exemplo 3: Rastrear Cadastro de Usuário
```php
// No AuthController após cadastro bem-sucedido
public function registerPost()
{
    // Código de cadastro...
    
    if ($cadastroSucesso) {
        trackMetaCompleteRegistration($email, [
            'content_name' => 'Cadastro de Usuário',
            'value' => 1,
            'currency' => 'BRL'
        ]);
    }
}
```

#### Exemplo 4: Rastrear Formulário de Contato
```php
// No ContactController
public function submitContact()
{
    // Código de envio...
    
    if ($envioSucesso) {
        trackMetaContact($email, [
            'content_name' => 'Formulário de Contato',
            'content_category' => 'Customer Service'
        ]);
    }
}
```

## Eventos Suportados

- **PageView**: Visualização de página
- **Lead**: Geração de lead (formulários)
- **CompleteRegistration**: Cadastro completo
- **Contact**: Contato/Mensagem

## Dados Coletados Automaticamente

### User Data (para matching)
- IP do cliente
- User Agent
- Facebook Click ID (\_fbc)
- Facebook Browser ID (\_fbp)
- Email (hasheado com SHA256)
- Telefone (hasheado com SHA256)
- Nome (hasheado com SHA256)

### Event Data
- Timestamp do evento
- URL da página
- Dados customizados específicos do evento

## Modo de Teste

Quando o **Modo de Teste** está ativado:
- Os eventos são enviados com o `test_event_code`
- Você pode ver os eventos na ferramenta de teste do Facebook
- Os eventos não afetam suas métricas de produção

## Logs de Erro

Erros são automaticamente logados em:
- Logs do sistema do CodeIgniter
- Verifique os logs em caso de problemas

## Verificação de Status

Para verificar se a API está funcionando:

```php
// Verificar se está habilitada
if (metaConversions()->isEnabled()) {
    echo "Meta Conversions API está ativa";
}

// Verificar se um evento específico está habilitado
if (metaConversions()->isEventEnabled('Lead')) {
    echo "Tracking de Lead está ativo";
}
```

## Troubleshooting

### Eventos não aparecem no Facebook
1. Verifique se os tokens estão corretos
2. Confirme se o Pixel ID e Dataset ID estão corretos
3. Verifique os logs de erro do sistema
4. Use o modo de teste primeiro

### Erro de Autenticação
- Regenere o Access Token no Facebook
- Verifique as permissões do token

### Dados não são enviados
- Confirme se a API está habilitada nas configurações
- Verifique se o evento específico está marcado para tracking
- Confirme se não há bloqueios de firewall para requisições HTTPS

## Boas Práticas

1. **Use modo de teste** antes de ir para produção
2. **Hash dados sensíveis** (já feito automaticamente)
3. **Monitore logs** regularmente
4. **Configure apenas eventos relevantes** para seu negócio
5. **Use custom_data** para adicionar contexto aos eventos