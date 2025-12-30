Documento de Alterações e Melhorias no Fluxo do Bot de Agendamento para a IA
Objetivo:

Melhorar a experiência do usuário no bot de agendamento, implementando um fluxo de navegação mais intuitivo, com novas funcionalidades de reagendamento e sugestões de reagendamento ao invés de cancelamento. O objetivo é tornar o bot mais eficiente, amigável e flexível.

1. Análise Inicial do Fluxo Atual e Problemas Identificados

O fluxo atual do bot tem como objetivo permitir ao usuário agendar, consultar e cancelar agendamentos. No entanto, há algumas questões que precisam ser resolvidas para melhorar a navegação e a experiência geral:

Comando Confuso de "0": O comando 0 é interpretado tanto como "voltar ao menu" quanto como "encerrar a conversa", o que causa confusão.

Falta de Opção de Reagendamento: O fluxo atual não sugere a possibilidade de reagendar um agendamento. Se o usuário deseja cancelar, o bot só oferece essa opção, sem alternativas.

Instruções e Comandos Ambíguos: A navegação no bot não está suficientemente clara, especialmente no que diz respeito aos comandos de voltar ao menu ou sair.

2. Sugestões de Melhorias para o Fluxo de Conversas/Mensagens
2.1. Alteração nos Comandos de Navegação

Para evitar que o comando 0 seja confundido, sugiro as seguintes alterações:

Comando para Voltar ao Menu: Usar o comando menu ou voltar para retornar ao menu principal.

Comando para Sair: O comando 0 será exclusivo para "sair da conversa", mas só será funcional quando o usuário estiver no menu ou em um ponto onde o bot já esteja certo de que ele deseja sair.

2.2. Confirmação de Ação de Sair

Quando o usuário digitar 0 para sair, o bot pedirá uma confirmação adicional para evitar saídas acidentais:

"Você tem certeza que deseja sair? Digite 'sim' para sair ou 'não' para continuar a conversa."

2.3. Implementação de Reagendamento

Quando o usuário optar por consultar seus agendamentos (ao digitar 2), o bot irá oferecer a opção de reagendar em vez de apenas cancelar. A sequência será a seguinte:

O usuário digita 2 para consultar seus agendamentos.

O bot lista os agendamentos futuros.

O bot pergunta ao usuário se ele deseja reagendar ou cancelar cada agendamento, com a opção de reagendar sendo a principal.

Se o usuário escolher reagendar, o bot irá solicitar a nova data e hora para o agendamento.

Caso o usuário escolha cancelar, o bot confirmará o cancelamento, mas sempre sugerirá reagendar antes de finalizar.

Mensagem para consulta de agendamentos:

"Você tem agendamentos futuros. Gostaria de reagendar ou cancelar algum?"

O bot então exibirá os agendamentos e oferecerá a opção de reagendar ou cancelar.

Mensagem para reagendamento:

"Por favor, escolha uma nova data e hora para o seu agendamento. Quando estiver pronto, digite a nova data (ex: 15/05/2025) e hora (ex: 10:00 AM)."

Mensagem para confirmação de reagendamento:

"Seu agendamento foi reagendado para {data} às {hora}. Confirma?"

Se o usuário confirmar, o agendamento será atualizado.

2.4. Mensagem de Cancelamento com Recomendação de Reagendamento

Quando o usuário deseja cancelar, o bot sempre irá perguntar se ele deseja reagendar antes de confirmar o cancelamento.

Mensagem para cancelar:

"Tem certeza que deseja cancelar este agendamento? Digite 'sim' para cancelar ou 'não' para reagendar."

Caso o usuário escolha reagendar, o bot seguirá o fluxo do reagendamento.

3. Alterações no Código e Necessidade de Verificação

Antes de implementar as melhorias, a IA deve realizar uma análise detalhada do código atual para garantir que as alterações não causem duplicidades ou conflitos com funcionalidades existentes. O objetivo é evitar redundância nos métodos e assegurar que as novas funcionalidades sejam integradas de forma eficiente.

3.1. Requisitos de Alteração no Código

Alterar Comandos de Navegação: Alterar as funções que processam os comandos de 0 para que o comportamento de "sair" seja distinto do comportamento de "voltar ao menu". Garantir que o comando menu ou voltar esteja implementado corretamente.

Implementar a Lógica de Reagendamento:

Criar uma nova função para oferecer a opção de reagendar quando o usuário consultar seus agendamentos.

Incluir validações para garantir que a nova data e hora do reagendamento sejam viáveis, sem conflito com outros agendamentos.

Alterar as mensagens e fluxos de estado do bot para que ele ofereça o reagendamento em vez de apenas cancelar.

Confirmar Cancelamento: Adicionar um novo fluxo de confirmação para o cancelamento, onde o usuário sempre será perguntado se deseja reagendar antes de confirmar o cancelamento.

3.2. Funções e Modelos Relevantes

A IA deve revisar as funções e modelos seguintes:

Função de Processamento de Comandos: Verificar onde o 0 é utilizado e alterá-lo para lidar corretamente com "voltar ao menu" e "sair".

Bot_conversa_model: Garantir que o estado da conversa seja atualizado corretamente para refletir as novas opções de reagendamento e cancelamento.

Estabelecimento_model, Cliente_model, Agendamento_model: Verificar se as alterações para reagendamento e consulta de agendamentos impactam os modelos de dados e se é necessário ajustar a lógica de criação, consulta e atualização de agendamentos.

4. Cronograma de Implementação

Semana 1: Análise e Planejamento

Reunião com a IA para análise detalhada do código atual e das necessidades de alteração.

Identificação de funcionalidades já existentes que podem ser reutilizadas ou modificadas para suportar o reagendamento.

Planejamento das alterações necessárias no banco de dados, se houver necessidade de novos campos ou modificações.

Semana 2: Desenvolvimento das Alterações

Implementação do fluxo de reagendamento.

Alterações nos comandos de navegação para evitar confusão com o comando 0.

Desenvolvimento do fluxo de confirmação para cancelamento e sugestão de reagendamento.

Semana 3: Testes e Ajustes

Testar o novo fluxo de navegação com casos reais de uso, incluindo o reagendamento e cancelamento.

Garantir que os novos comandos estejam funcionando corretamente.

Testar a lógica de validação de novas datas e horários para o reagendamento.

Semana 4: Documentação e Entrega

Documentação das alterações no código.

Revisão de desempenho e correção de bugs encontrados durante os testes.

Entrega do código finalizado e atualizado para a produção.

5. PRD (Product Requirements Document) para Implementação do Fluxo de Reagendamento
Objetivo do Produto:

Adicionar a funcionalidade de reagendamento ao bot de agendamento, permitindo que os usuários possam reagendar agendamentos diretamente via WhatsApp. Além disso, melhorar a navegação e os comandos de interação para evitar confusão, especialmente com o comando 0.

Funcionalidades Principais:

Consulta de Agendamentos:

Mostrar agendamentos futuros.

Permitir ao usuário reagendar ou cancelar agendamentos.

Reagendamento:

Permitir que o usuário escolha uma nova data e hora.

Confirmar o reagendamento e atualizar o banco de dados.

Cancelamento com Recomendação de Reagendamento:

Sempre sugerir reagendar antes de permitir o cancelamento definitivo.

Alterações nos Comandos de Navegação:

0 para sair apenas no menu.

menu ou voltar para retornar ao menu.

Feedback Claro e Confirmatório:

Mensagens de confirmação para cada ação realizada, seja reagendamento ou cancelamento.

Requisitos Técnicos:

Integração com o banco de dados: Garantir que o modelo de agendamento permita alterações (reagendamento) e que a lógica de conflito de horários seja bem gerida.

UI e UX: Melhorar as mensagens e fluxos para uma navegação clara e sem confusão.

Conclusão

Este documento descreve as melhorias necessárias no fluxo de conversa do bot de agendamento, focando em melhorar a navegação, implementar o reagendamento e resolver o problema de confusão com os comandos. A IA deve analisar a implementação atual, fazer as alterações necessárias e seguir o cronograma proposto para garantir que as funcionalidades sejam implementadas corretamente e de forma eficiente.
