PRD - Sistema de Agendamento para Salões de Beleza e Barbearias
Visão Geral do Projeto

Este é um sistema SaaS para salões de beleza, barbearias, manicures e estabelecimentos semelhantes. O objetivo é fornecer uma plataforma simples e eficiente para o gerenciamento de agendamentos, pagamentos e interação com os clientes via WhatsApp. O sistema deve permitir a criação de perfis para os estabelecimentos, profissionais, e clientes, e facilitar a comunicação entre os clientes e os prestadores de serviço. O sistema também deve integrar-se com o Mercado Pago para pagamentos e com a Evolution API para integração com o WhatsApp.

Funcionalidades Principais

Cadastro de Estabelecimentos:

Informações básicas do estabelecimento (nome, CNPJ/CPF, endereço, logo, WhatsApp).

Definição de serviços oferecidos.

Escolha do plano de pagamento (trimestral, semestral ou anual).

Cadastro de Profissionais:

Nome, foto, WhatsApp e telefone.

Definir serviços que o profissional pode oferecer.

Configurar disponibilidade (dias da semana, horários, bloqueios de horários, etc.).

Cadastro de Clientes:

Nome, CPF, WhatsApp e foto opcional.

Histórico de agendamentos (visível apenas para administradores).

Gestão de Agendamentos:

Agendamento via WhatsApp, com interação automática para escolher o serviço, profissional, dia e horário.

Configuração do tempo máximo para o agendamento (ex: agendamento até 1h, 2h antes do serviço).

Preço do serviço configurado pelo administrador do estabelecimento.

Pagamento de pré-agendamento ou pagamento integral do serviço via Mercado Pago (com links de Pix e pagamento recorrente via cartão de crédito).

Integração com o WhatsApp via Evolution API:

Cadastro e autenticação do número do WhatsApp via QR Code.

Sistema de notificação para problemas de conexão com o WhatsApp (notificação para o admin e link de suporte).

Controle de Sessões no WhatsApp:

Gerenciamento da sessão de cada cliente, com controle de tempo de interação.

Opções de reiniciar conversa ou retomar onde parou (caso a conversa tenha sido abandonada).

Notificações Personalizáveis:

Notificações de agendamento, confirmação, cancelamento e reagendamento configuráveis pelo administrador.

Notificações de pagamento confirmadas via Mercado Pago, enviadas diretamente para o WhatsApp do cliente.

Fila de Agendamentos para Profissionais:

Exibição dos agendamentos do dia com status (confirmado, cancelado, reagendado).

Notificação ao profissional sobre mudanças de status (cancelamento, reagendamento, etc.).

Opção de marcar o atendimento como finalizado. O sistema também marca automaticamente como finalizado se o profissional esquecer.

Promoções e Descontos:

Sistema de promoções configuráveis pelo admin, com base no perfil do cliente (recorrente, VIP, etc.).

Envio de cupons de desconto para clientes fiéis e recorrentes.

Feedback do Cliente:

Sistema de feedback pós-serviço, onde o cliente pode avaliar o serviço e o profissional.

Avaliação com estrelas ou notas.

Requisitos Técnicos

Dashboard:

O sistema utilizará o Tabler como framework de layout. O dashboard já está desenvolvido, com todas as funcionalidades de gerenciamento de usuários, incluindo cadastro, recuperação de senha e notificações por e-mail.

A interação com o sistema será 100% baseada no layout do Tabler, sem alterações significativas no design.

Tecnologias:

Backend: PHP, CodeIgniter (usando padrão MVC).

Banco de Dados: MySQL, administrado via PHPMyAdmin.

API: Criar uma API para consulta de agendamentos, profissionais e clientes.

Integração com Mercado Pago: Utilização do SDK do Mercado Pago para pagamentos via Pix e recorrência via cartão de crédito.

Integração com Evolution API: Para gerenciar a comunicação via WhatsApp.

Funcionalidades de Integração:

Mercado Pago: Para realizar o pagamento de agendamentos (Pix e recorrência).

Evolution API: Para a integração com WhatsApp, incluindo o envio de notificações, confirmação de agendamentos e gerenciamento de sessões.

Segurança:

O sistema deve garantir a segurança dos dados do cliente e do profissional, com criptografia de senhas e comunicação segura.

Cronograma de Desenvolvimento

Semana 1-2:

Finalização do painel admin com funcionalidades de cadastro de estabelecimentos e profissionais.

Desenvolvimento dos CRUDs para cadastro de clientes, serviços e agendamentos.

Semana 3-4:

Implementação da integração com o Mercado Pago para pagamento de pré-agendamento e agendamento total.

Integração com Evolution API para autenticação via QR Code e comunicação com WhatsApp.

Semana 5-6:

Desenvolvimento das notificações personalizáveis e integração com o WhatsApp (notificações de pagamento, confirmação de agendamento, etc.).

Implementação do sistema de feedback pós-serviço e promoções/descontos.

Semana 7-8:

Testes de integração entre os sistemas de pagamento, agendamento e WhatsApp.

Testes de usabilidade, focando em feedback dos clientes e profissionais.

Ajustes finais e implementação de funcionalidades de automação (ex: marcação de finalização de atendimentos automaticamente).

Pontos Finais:

O sistema deve ser escalável para novos estabelecimentos e profissionais.

Será possível adicionar novos recursos futuramente, como integração com redes sociais ou novos métodos de pagamento.

A experiência do cliente e do profissional deve ser fluida e intuitiva, com ênfase na simplicidade de uso.
