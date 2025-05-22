# Portal GX Capital

Portal corporativo da GX Capital, incluindo simuladores financeiros e sistema de captura de leads.

## Funcionalidades Principais

- **Simulador de Risco Cambial**: Permite calcular o impacto de variações cambiais em operações financeiras
- **Sistema de Captura de Leads**: Integração com o simulador para capturar dados de potenciais clientes
- **Painel Administrativo**: Interface para gerenciamento de leads e conteúdo do site
- **Interface Responsiva**: Design adaptado para dispositivos móveis e desktop

## Tecnologias Utilizadas

- **Backend**: PHP/CodeIgniter 4
- **Frontend**: HTML, CSS, JavaScript, jQuery, Tailwind CSS
- **Banco de Dados**: MySQL
- **Captura de Leads**: API personalizada para processamento de dados

## Simuladores Disponíveis

1. **Risco Cambial / Perda Marginal**
   - Cálculo de cenários de variação de cotação
   - Análise de impacto em diferentes moedas
   - Visualização de resultados

## Administração de Leads

O sistema inclui uma interface administrativa para gerenciar leads capturados, com:

- Visualização de todos os leads gerados pelos simuladores
- Filtros e busca avançada
- Sistema de status para acompanhamento (Novo, Contatado, Qualificado, etc.)
- Detalhes completos das simulações realizadas pelos usuários

## Instalação

Para instalação em ambiente de desenvolvimento:

1. Clone o repositório
2. Configure o banco de dados em `/app/Config/Database.php`
3. Importe a estrutura do banco de dados
4. Configure o servidor web para apontar para o diretório raiz

## Configuração

As principais configurações estão disponíveis em:

- `/app/Config/App.php` - Configurações gerais do aplicativo
- `/app/Config/Database.php` - Configurações do banco de dados
- `/app/Config/Routes.php` - Definição de rotas

## Contato

Para mais informações, entre em contato com GX Capital.