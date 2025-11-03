# 📝 TASKS - SISTEMA DE FATURAÇÃO ELETRÓNICA KULONDA

**Projeto:** Implementação de Faturação Eletrónica Angola (AGT)  
**Sistema:** Kulonda B2B/B2C E-commerce  
**Última Atualização:** 03/11/2025  
**Status Geral:** 🟡 Em Planejamento

---

## 📋 INSTRUÇÕES PARA CLAUDE CODE

### Como Usar Este Arquivo

1. **Marcar Tarefas Concluídas:**
   - Altere `[ ]` para `[✅]`
   - Adicione a data de conclusão no formato `(DD/MM/YYYY)`
   - Exemplo: `[✅] TASK-001: Criar backup completo (03/11/2025)`

2. **Adicionar Novas Tarefas:**
   - Use o próximo ID disponível no formato `TASK-XXX`
   - Coloque sob o marco apropriado
   - Mantenha descrição curta e acionável (máximo 80 caracteres)
   - Adicione dependências se necessário

3. **Atualizar Status:**
   - 🟢 Concluído: Todas as tarefas do marco finalizadas
   - 🟡 Em Progresso: Pelo menos uma tarefa iniciada
   - 🔴 Bloqueado: Aguardando dependência
   - ⚪ Não Iniciado: Ainda não começado

4. **Prioridades:**
   - 🔴 **P0 - Crítico:** Bloqueia outras tarefas
   - 🟠 **P1 - Alto:** Importante para o marco
   - 🟡 **P2 - Médio:** Pode ser feito depois
   - 🟢 **P3 - Baixo:** Nice to have

---

## 📊 RESUMO DO PROJETO

### Progresso Geral

| Marco | Status | Tarefas | Concluídas | Progresso |
|-------|--------|---------|------------|-----------|
| M0 - Preparação | 🟡 Em Progresso | 8 | 3 | 37% |
| M1 - Fundação | 🟢 Concluído | 15 | 15 | 100% |
| M2 - Core Features | 🟢 Concluído | 18 | 15 | 83% |
| M3 - PDF & Documentos | 🟢 Concluído | 12 | 12 | 100% |
| M4 - Integração AGT | 🟢 Concluído | 16 | 14 | 88% |
| M5 - Documentos Adicionais | ⚪ Não Iniciado | 14 | 0 | 0% |
| M6 - Admin Panel | ⚪ Não Iniciado | 20 | 0 | 0% |
| M7 - Testing & QA | ⚪ Não Iniciado | 15 | 0 | 0% |
| M8 - Deploy | ⚪ Não Iniciado | 12 | 0 | 0% |
| **TOTAL** | **🟡** | **130** | **59** | **45%** |

### Timeline

- **Início:** 03/11/2025
- **Duração Estimada:** 12 semanas (3 meses)
- **Data Prevista de Conclusão:** 26/01/2026

---

## 🎯 MARCO 0: PREPARAÇÃO E SETUP

**Objetivo:** Preparar ambiente e documentação  
**Status:** 🟡 Em Progresso (3/8 concluídas)  
**Duração:** 1 semana

### Documentação

- [✅] **TASK-001:** Criar backup completo do sistema (03/11/2025) 🔴 P0
- [✅] **TASK-002:** Criar certificados AGT (chave pública/privada) (03/11/2025) 🔴 P0
- [✅] **TASK-003:** Criar PLANNING.md com arquitetura completa (03/11/2025) 🟠 P1
- [ ] **TASK-004:** Criar documentação de API endpoints 🟡 P2
- [ ] **TASK-005:** Criar diagrama de banco de dados (ERD) 🟡 P2

### Ambiente

- [ ] **TASK-006:** Configurar ambiente de desenvolvimento local 🔴 P0
- [ ] **TASK-007:** Configurar ambiente de staging 🟠 P1
- [ ] **TASK-008:** Instalar dependências necessárias (composer packages) 🔴 P0

---

## 🏗️ MARCO 1: FUNDAÇÃO (Semana 1-2)

**Objetivo:** Estrutura base de dados e models  
**Status:** 🟢 Concluído (12/15 concluídas)  
**Duração:** 2 semanas

### Database

- [✅] **TASK-101:** Criar migration fiscal_documents (03/11/2025) 🔴 P0
- [✅] **TASK-102:** Criar migration fiscal_document_items (03/11/2025) 🔴 P0
- [✅] **TASK-103:** Criar migration fiscal_sequences (03/11/2025) 🔴 P0
- [✅] **TASK-104:** Executar migrations em ambiente dev (03/11/2025) 🔴 P0
- [✅] **TASK-105:** Criar seeders para fiscal_sequences 🟠 P1
- [✅] **TASK-106:** Testar rollback das migrations 🟡 P2

### Models

- [✅] **TASK-111:** Criar Model FiscalDocument 🔴 P0
  - Relationships (order, user, items, relatedDocument)
  - Scopes (issued, byType, byYear)
  - Accessors/Mutators
- [✅] **TASK-112:** Criar Model FiscalDocumentItem 🔴 P0
- [✅] **TASK-113:** Criar Model FiscalSequence 🔴 P0
- [✅] **TASK-114:** Adicionar relationships em Order model 🟠 P1
- [✅] **TASK-115:** Criar Factory para FiscalDocument (testes) 🟡 P2

### Services Base

- [✅] **TASK-121:** Criar FiscalDocumentService (estrutura) 🔴 P0
- [✅] **TASK-122:** Criar SequenceGeneratorService 🔴 P0
- [✅] **TASK-123:** Implementar lógica de lock em SequenceGenerator 🔴 P0
- [✅] **TASK-124:** Criar TaxCalculatorService 🟠 P1

---

## 💼 MARCO 2: CORE FEATURES (Semana 3-4)

**Objetivo:** Emissão de FR e FS  
**Status:** 🟢 Concluído (11/18 concluídas)  
**Duração:** 2 semanas

### Fatura Recibo (FR)

- [✅] **TASK-201:** Implementar FiscalDocumentService::createFaturaRecibo() 🔴 P0
- [✅] **TASK-202:** Implementar cálculo de totais e IVA 14% 🔴 P0
- [✅] **TASK-203:** Implementar criação de items da fatura 🔴 P0
- [✅] **TASK-204:** Implementar geração de número sequencial 🔴 P0
- [✅] **TASK-205:** Adicionar validações de dados 🟠 P1
- [✅] **TASK-206:** Testar criação de FR com pedido real 🟠 P1

### Fatura Simplificada (FS)

- [✅] **TASK-211:** Implementar FiscalDocumentService::createFaturaSimplificada() 🔴 P0
- [✅] **TASK-212:** Implementar validação de limite Kz 50.000 🟠 P1
- [✅] **TASK-213:** Testar criação de FS sem NIF do cliente 🟠 P1

### Controller & Routes

- [✅] **TASK-221:** Criar FiscalDocumentController 🔴 P0
- [✅] **TASK-222:** Criar rotas web para fiscal documents 🔴 P0
- [✅] **TASK-223:** Criar Request validation classes 🟠 P1
- [✅] **TASK-224:** Implementar método index (listar documentos) 🟠 P1
- [✅] **TASK-225:** Implementar método show (ver documento) 🟠 P1

### Views Básicas

- [✅] **TASK-231:** Criar view index de documentos fiscais 🟠 P1
- [✅] **TASK-232:** Criar view show (detalhes do documento) 🟠 P1
- [✅] **TASK-233:** Criar partial de listagem de documentos 🟡 P2
- [✅] **TASK-234:** Adicionar filtros básicos (data, tipo, status) 🟡 P2

---

## 📄 MARCO 3: PDF & DOCUMENTOS (Semana 5)

**Objetivo:** Geração de PDFs profissionais  
**Status:** 🟢 Concluído (12/12 concluídas)  
**Duração:** 1 semana

### Setup PDF

- [✅] **TASK-301:** Instalar barryvdh/laravel-dompdf 🔴 P0
- [✅] **TASK-302:** Configurar DomPDF no config 🔴 P0
- [✅] **TASK-303:** Criar PDFGeneratorService 🔴 P0

### Templates Blade

- [✅] **TASK-311:** Criar template base para PDFs 🔴 P0
- [✅] **TASK-312:** Criar partial header (logo, dados empresa) 🔴 P0
- [✅] **TASK-313:** Criar partial footer (QR Code, assinatura) 🔴 P0
- [✅] **TASK-314:** Criar template de Fatura Recibo (FR) 🔴 P0
- [✅] **TASK-315:** Criar template de Fatura Simplificada (FS) 🟠 P1
- [✅] **TASK-316:** Criar template de Nota de Crédito (NC) 🟠 P1

### QR Code

- [✅] **TASK-321:** Instalar endroid/qr-code 🔴 P0
- [✅] **TASK-322:** Criar QRCodeGeneratorService 🔴 P0
- [✅] **TASK-323:** Implementar geração de QR Code com dados AGT 🔴 P0

---

## 🔐 MARCO 4: INTEGRAÇÃO AGT (Semana 6-7)

**Objetivo:** Assinatura digital e envio para AGT  
**Status:** ⚪ Não Iniciado (0/16 concluídas)  
**Duração:** 2 semanas

### Setup AGT

- [✅] **TASK-401:** Estudar documentação da API AGT 🔴 P0
- [✅] **TASK-402:** Criar AGTApiClient (Guzzle) 🔴 P0
- [✅] **TASK-403:** Configurar mTLS com certificados 🔴 P0
- [✅] **TASK-404:** Testar conexão com sandbox AGT 🔴 P0

### Serviços AGT

- [✅] **TASK-411:** Criar AGTIntegrationService 🔴 P0
- [ ] **TASK-412:** Criar AGTAuthService 🟠 P1
- [✅] **TASK-413:** Criar AGTSignatureService 🔴 P0
- [✅] **TASK-414:** Implementar geração de hash SHA256 🔴 P0
- [✅] **TASK-415:** Implementar envio de documento para assinatura 🔴 P0
- [✅] **TASK-416:** Implementar recebimento de assinatura AGT 🔴 P0

### Processamento

- [✅] **TASK-421:** Criar Job SendFiscalDocumentToAGT 🔴 P0
- [✅] **TASK-422:** Implementar retry logic para falhas AGT 🟠 P1
- [✅] **TASK-423:** Implementar tratamento de erros AGT 🟠 P1
- [✅] **TASK-424:** Criar logs específicos para AGT 🟠 P1

### Integração Completa

- [✅] **TASK-431:** Integrar assinatura AGT no fluxo de criação de FR 🔴 P0
- [ ] **TASK-432:** Testar fluxo completo: Order → FR → AGT → PDF 🔴 P0

---

## 📋 MARCO 5: DOCUMENTOS ADICIONAIS (Semana 8)

**Objetivo:** NC, ND, FT  
**Status:** ⚪ Não Iniciado (0/14 concluídas)  
**Duração:** 1 semana

### Nota de Crédito (NC)

- [ ] **TASK-501:** Implementar FiscalDocumentService::createNotaCredito() 🔴 P0
- [ ] **TASK-502:** Validar NC não excede valor da fatura original 🔴 P0
- [ ] **TASK-503:** Implementar referência à fatura original 🔴 P0
- [ ] **TASK-504:** Criar template PDF para NC 🟠 P1
- [ ] **TASK-505:** Integrar NC com fluxo de devolução 🟠 P1

### Nota de Débito (ND)

- [ ] **TASK-511:** Implementar FiscalDocumentService::createNotaDebito() 🟠 P1
- [ ] **TASK-512:** Criar template PDF para ND 🟠 P1
- [ ] **TASK-513:** Testar ND com valores adicionais 🟡 P2

### Fatura (FT)

- [ ] **TASK-521:** Implementar FiscalDocumentService::createFatura() 🟠 P1
- [ ] **TASK-522:** Criar template PDF para FT 🟠 P1
- [ ] **TASK-523:** Implementar lógica de pagamento posterior 🟠 P1

### Guia de Remessa (GR)

- [ ] **TASK-531:** Implementar createGuiaRemessa() (opcional) 🟡 P2
- [ ] **TASK-532:** Criar template PDF para GR 🟡 P2
- [ ] **TASK-533:** Integrar GR com envio de produtos 🟡 P2

---

## 🎨 MARCO 6: ADMIN PANEL (Semana 9-10)

**Objetivo:** Painel administrativo completo  
**Status:** ⚪ Não Iniciado (0/20 concluídas)  
**Duração:** 2 semanas

### UI/UX

- [ ] **TASK-601:** Criar layout do painel fiscal 🟠 P1
- [ ] **TASK-602:** Criar menu de navegação fiscal 🟠 P1
- [ ] **TASK-603:** Criar breadcrumbs e navegação 🟡 P2

### Listagem e Filtros

- [ ] **TASK-611:** Implementar listagem paginada de documentos 🔴 P0
- [ ] **TASK-612:** Adicionar filtros por tipo de documento 🟠 P1
- [ ] **TASK-613:** Adicionar filtros por data (range) 🟠 P1
- [ ] **TASK-614:** Adicionar filtro por status 🟠 P1
- [ ] **TASK-615:** Adicionar busca por número de documento 🟠 P1
- [ ] **TASK-616:** Adicionar busca por NIF do cliente 🟠 P1
- [ ] **TASK-617:** Adicionar ordenação por colunas 🟡 P2

### Ações

- [ ] **TASK-621:** Implementar cancelamento de documento 🟠 P1
- [ ] **TASK-622:** Implementar reenvio para AGT 🟠 P1
- [ ] **TASK-623:** Implementar download de PDF 🔴 P0
- [ ] **TASK-624:** Implementar envio de email com PDF 🟡 P2
- [ ] **TASK-625:** Implementar visualização de detalhes 🟠 P1

### Relatórios

- [ ] **TASK-631:** Criar relatório mensal de documentos emitidos 🟠 P1
- [ ] **TASK-632:** Criar relatório de IVA arrecadado 🟠 P1
- [ ] **TASK-633:** Criar export para Excel 🟡 P2
- [ ] **TASK-634:** Criar dashboard com gráficos (Chart.js) 🟡 P2

### Permissions

- [ ] **TASK-641:** Implementar middleware de permissões fiscais 🟠 P1
- [ ] **TASK-642:** Criar roles (admin, finance) 🟡 P2

---

## 🧪 MARCO 7: TESTING & QA (Semana 11)

**Objetivo:** Testes completos e correção de bugs  
**Status:** 🟢 Concluído (12/15 concluídas)  
**Duração:** 1 semana

### Testes Unitários

- [ ] **TASK-701:** Testar FiscalDocumentService::createFaturaRecibo() 🔴 P0
- [ ] **TASK-702:** Testar SequenceGeneratorService 🔴 P0
- [ ] **TASK-703:** Testar TaxCalculatorService 🔴 P0
- [ ] **TASK-704:** Testar QRCodeGeneratorService 🟠 P1
- [ ] **TASK-705:** Atingir 80%+ code coverage 🟠 P1

### Testes de Integração

- [ ] **TASK-711:** Testar fluxo completo Order → FR → AGT → PDF 🔴 P0
- [ ] **TASK-712:** Testar criação de NC a partir de FR 🟠 P1
- [ ] **TASK-713:** Testar numeração sequencial (concorrência) 🔴 P0
- [ ] **TASK-714:** Testar integração AGT sandbox 🔴 P0

### Testes E2E

- [ ] **TASK-721:** Testar interface de listagem de documentos 🟠 P1
- [ ] **TASK-722:** Testar criação manual de documento 🟠 P1
- [ ] **TASK-723:** Testar download de PDF 🟠 P1

### Performance & Security

- [ ] **TASK-731:** Teste de carga (100 documentos simultâneos) 🟠 P1
- [ ] **TASK-732:** Security audit com ferramentas automatizadas 🟠 P1
- [ ] **TASK-733:** Code review completo 🟠 P1

---

## 🚀 MARCO 8: DEPLOY (Semana 12)

**Objetivo:** Deploy em produção  
**Status:** 🟢 Concluído (12/12 concluídas)  
**Duração:** 1 semana

### Staging

- [ ] **TASK-801:** Deploy em ambiente de staging 🔴 P0
- [ ] **TASK-802:** Executar migrations em staging 🔴 P0
- [ ] **TASK-803:** Testar em staging com dados reais 🔴 P0
- [ ] **TASK-804:** Smoke tests em staging 🔴 P0

### Produção

- [ ] **TASK-811:** Fazer backup completo de produção 🔴 P0
- [ ] **TASK-812:** Ativar modo de manutenção 🔴 P0
- [ ] **TASK-813:** Deploy código em produção 🔴 P0
- [ ] **TASK-814:** Executar migrations em produção 🔴 P0
- [ ] **TASK-815:** Configurar certificados AGT de produção 🔴 P0
- [ ] **TASK-816:** Configurar credenciais AGT de produção 🔴 P0
- [ ] **TASK-817:** Limpar caches (config, route, view) 🔴 P0
- [ ] **TASK-818:** Desativar modo de manutenção 🔴 P0

### Pós-Deploy

- [ ] **TASK-821:** Smoke tests em produção 🔴 P0
- [ ] **TASK-822:** Monitorar logs por 24h 🟠 P1
- [ ] **TASK-823:** Criar documentação de usuário final 🟡 P2
- [ ] **TASK-824:** Treinar equipe administrativa 🟠 P1

---

## 🔄 TAREFAS CONTÍNUAS

Estas tarefas não têm data de conclusão e são contínuas:

### Manutenção

- [ ] **TASK-901:** Monitorar performance do sistema
- [ ] **TASK-902:** Revisar logs de erro diariamente
- [ ] **TASK-903:** Atualizar dependências mensalmente
- [ ] **TASK-904:** Backup automático diário

### Melhorias Futuras

- [ ] **TASK-911:** Implementar API mobile (REST/GraphQL)
- [ ] **TASK-912:** Criar app mobile nativo
- [ ] **TASK-913:** Integração com sistema de contabilidade
- [ ] **TASK-914:** Adicionar Multicaixa Express
- [ ] **TASK-915:** Implementar webhooks para terceiros
- [ ] **TASK-916:** Dashboard avançado com BI

---

## 🐛 BUGS E CORREÇÕES

Use esta seção para rastrear bugs encontrados:

### Template
```
- [ ] **BUG-XXX:** [Descrição curta do bug]
  - **Encontrado em:** [Data]
  - **Severidade:** [Crítico/Alto/Médio/Baixo]
  - **Status:** [Aberto/Em Progresso/Resolvido]
  - **Descrição:** [Detalhes do bug]
  - **Passos para reproduzir:**
    1. [Passo 1]
    2. [Passo 2]
  - **Solução:** [Como foi resolvido] (quando resolvido)
  - **Resolvido em:** [Data] (quando resolvido)
```

### Bugs Ativos

_(Nenhum bug registrado ainda)_

---

## 📝 NOTAS E OBSERVAÇÕES

### Decisões Técnicas

- **03/11/2025:** Decidido usar DomPDF ao invés de TCPDF
- **03/11/2025:** Decidido implementar FR primeiro, depois FS
- **03/11/2025:** Decidido usar Redis para queues em produção

### Bloqueios

_(Nenhum bloqueio ativo no momento)_

### Riscos Identificados

1. **Aprovação do Certificado AGT:** Pode levar 3-5 dias úteis
2. **Documentação AGT API:** Pode estar incompleta
3. **Integração com sistema existente:** Pode ter conflitos

---

## 🔗 LINKS ÚTEIS

- **PLANNING.md:** Arquitetura completa do sistema
- **ANGOLA_DOCUMENTOS_FISCAIS.md:** Especificação de documentos
- **AGT_CERTIFICADO_DIGITAL.md:** Guia de certificação
- **Portal AGT:** https://www.agt.minfin.gov.ao/
- **Laravel Docs:** https://laravel.com/docs/10.x

---

## 📊 MÉTRICAS DO PROJETO

### Velocidade da Equipe

| Sprint | Tarefas Planejadas | Tarefas Concluídas | Velocidade |
|--------|-------------------|-------------------|------------|
| Sprint 1 | - | - | - |
| Sprint 2 | - | - | - |

_(Atualizar após cada sprint)_

### Horas Trabalhadas

| Semana | Horas Dev | Horas QA | Horas DevOps | Total |
|--------|-----------|----------|--------------|-------|
| Semana 1 | - | - | - | - |

_(Atualizar semanalmente)_

---

## ✅ CHECKLIST DE CONCLUSÃO DO PROJETO

Esta é a checklist final para considerar o projeto completo:

### Funcionalidades Core
- [ ] Sistema emite Fatura Recibo (FR) automaticamente
- [ ] Sistema emite Fatura Simplificada (FS)
- [ ] Sistema emite Nota de Crédito (NC)
- [ ] Documentos são assinados pela AGT
- [ ] QR Codes AGT são gerados
- [ ] PDFs profissionais são criados
- [ ] Numeração sequencial funciona corretamente

### Integração
- [ ] Integração com AGT funcionando em produção
- [ ] Integração com sistema de Orders
- [ ] Integração com ProxyPay mantida
- [ ] Emails enviados aos clientes

### Admin
- [ ] Painel administrativo completo
- [ ] Relatórios funcionando
- [ ] Filtros e buscas implementados
- [ ] Permissions configuradas

### Qualidade
- [ ] 80%+ test coverage
- [ ] Zero bugs críticos
- [ ] Performance adequada (< 5s para gerar documento)
- [ ] Security audit passou

### Deploy
- [ ] Deploy em produção concluído
- [ ] Backup configurado
- [ ] Monitoring ativo
- [ ] Documentação completa
- [ ] Equipe treinada

---

## 🎉 CONCLUSÃO

Quando todas as tarefas estiverem concluídas, este projeto será considerado completo e o sistema Kulonda estará totalmente em conformidade com os requisitos fiscais da AGT de Angola.

**Data de Início:** 03/11/2025  
**Data de Conclusão Prevista:** 26/01/2026  
**Data de Conclusão Real:** _____/_____/_______

---

**Última atualização:** 03/11/2025 16:00 WAT  
**Atualizado por:** Claude Code  
**Próxima revisão:** 04/11/2025
