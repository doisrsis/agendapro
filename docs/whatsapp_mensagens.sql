-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 23/01/2026 às 22:56
-- Versão do servidor: 10.11.15-MariaDB-cll-lve
-- Versão do PHP: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `dois8950_agendapro`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `whatsapp_mensagens`
--

CREATE TABLE `whatsapp_mensagens` (
  `id` int(10) UNSIGNED NOT NULL,
  `estabelecimento_id` int(10) UNSIGNED DEFAULT NULL,
  `direcao` enum('entrada','saida') NOT NULL,
  `numero_destino` varchar(20) NOT NULL,
  `tipo_mensagem` enum('texto','imagem','audio','documento','localizacao') DEFAULT 'texto',
  `conteudo` text NOT NULL,
  `message_id` varchar(100) DEFAULT NULL,
  `status` enum('enviado','entregue','lido','erro','recebido') DEFAULT 'enviado',
  `erro_mensagem` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `whatsapp_mensagens`
--

INSERT INTO `whatsapp_mensagens` (`id`, `estabelecimento_id`, `direcao`, `numero_destino`, `tipo_mensagem`, `conteudo`, `message_id`, `status`, `erro_mensagem`, `created_at`, `updated_at`) VALUES
(5101, NULL, 'entrada', '120363424969304388', 'texto', '👀 *2026 começou… Você vai ficar na mesma?*\n\nDev, muito provavelmente você traçou metas, planos e sonhos para esse ano… E eu quero te ajudar a cumprir com tudo isso, *vou te ajudar a começar o ano com o pé direito!* 🚀\n\nConsegui liberar algumas vagas remanescentes do Full Stack Club, com a *condição de Black Friday.* No Full Stack Club, você terá um acompanhamento completo, para dar o próximo passo na sua carreira! 🔥\n\nSe você quer fazer de 2026 o melhor ano da sua carreira, *toca aqui, preenche o formulário,* que a minha equipe vai te chamar para concluir a sua inscrição! ⬇️\nhttps://swiy.co/vagasremanescentes_ \n\n_2026 apenas começou, dev!_ 🫡', 'false_120363424969304388@g.us_3EB0AA48A9A4C2192C9665E80A80A547BCA0D5CC_160503095664858@lid', 'recebido', NULL, '2026-01-23 22:00:59', '2026-01-23 22:00:59'),
(5102, NULL, 'entrada', '120363175746047388', 'texto', 'pessoal, pra programar os post pela planilha qual o formato da data e hora que vcs estão colocando? Pois programei um post para as 12:00 e ele colocou 06:00', 'false_120363175746047388@g.us_3EB0ECD8101AADD7BE87A9_273804232229023@lid', 'recebido', NULL, '2026-01-23 22:03:02', '2026-01-23 22:03:02'),
(5103, NULL, 'entrada', '120363049065419885', 'texto', '', 'false_120363049065419885@g.us_A56C32AC5786D1D67FAF083E4C5E4942_113816734011404@lid', 'recebido', NULL, '2026-01-23 22:06:17', '2026-01-23 22:06:17'),
(5104, NULL, 'entrada', '120363049065419885', 'texto', 'Iguaria ser modesto', 'false_120363049065419885@g.us_AC2F3771562758D093DE6DC960EC10F1_177867598094463@lid', 'recebido', NULL, '2026-01-23 22:16:01', '2026-01-23 22:16:01'),
(5105, NULL, 'entrada', '120363175746047388', 'texto', 'Interessante! Explica isso pra nós!', 'false_120363175746047388@g.us_3EB05FD2A591B124FA05D8_158218173071584@lid', 'recebido', NULL, '2026-01-23 22:41:00', '2026-01-23 22:41:00'),
(5106, NULL, 'entrada', '120363328506387946', 'texto', '➡️ *Bruno Silva destaca desafios e avanços do Conselho Tutelar de Amargosa em 2025*\n\n📲 Veja agora:\nhttps://criativaonline.com.br/bruno-silva-destaca-desafios-e-avancos-do-conselho-tutelar-de-amargosa-em-2025/\n\nCriativa On Line | Portal de Notícias da Bahia', 'false_120363328506387946@g.us_3EB09A00114588B5F18659_195953990619139@lid', 'recebido', NULL, '2026-01-23 23:09:16', '2026-01-23 23:09:16'),
(5107, NULL, 'entrada', '120363328506387946', 'texto', '➡️ *Encontro Territorial de Agroecologia será realizado em Amargosa no dia 2 de março*\n\n📲 Veja agora:\nhttps://criativaonline.com.br/encontro-territorial-de-agroecologia-sera-realizado-em-amargosa-no-dia-2-de-marco/\n\nCriativa On Line | Portal de Notícias da Bahia', 'false_120363328506387946@g.us_3EB0DD388D46B308CCEBBB_195953990619139@lid', 'recebido', NULL, '2026-01-23 23:15:22', '2026-01-23 23:15:22'),
(5108, NULL, 'entrada', '55719178077214530547', 'texto', 'Estou buscando opções nos seguintes bairros:\n\nGraça \nVitória \nBarra\nOndina\nE até o Rio Vermelho, se for algo bastante interessante. \n\nValores na faixa de R$280K a R$420K\n\nNão me interessam metragens abaixo de 40m2, sendo o ideal entre 50m a 200m', 'false_557191780772-1453054742@g.us_AC4E100EECFE419E70B41B1E08574019_248373764468885@lid', 'recebido', NULL, '2026-01-23 23:23:37', '2026-01-23 23:23:37'),
(5109, NULL, 'entrada', '', 'texto', '🙏🏽💜', 'false_status@broadcast_3A2741B47BE06320257C_557592424240@c.us', 'recebido', NULL, '2026-01-23 23:40:16', '2026-01-23 23:40:16'),
(5110, NULL, 'entrada', '120363328506387946', 'texto', '➡️ *Duas vítimas fatais são confirmadas e três feridos na zona rural de Valença*\n\n📲 Veja agora:\nhttps://criativaonline.com.br/duas-vitimas-fatais-sao-confirmadas-e-tres-feridos-na-zona-rural-de-valenca/\n\nCriativa On Line | Portal de Notícias da Bahia', 'false_120363328506387946@g.us_3EB063FB27E43B41C83087_195953990619139@lid', 'recebido', NULL, '2026-01-23 23:45:03', '2026-01-23 23:45:03'),
(5111, NULL, 'entrada', '120363175746047388', 'texto', 'veja se não é o fuso horário \nEm setting do seu workflow', 'false_120363175746047388@g.us_2AEB9A8B47D8E882FE3C_198483541766250@lid', 'recebido', NULL, '2026-01-23 23:45:05', '2026-01-23 23:45:05'),
(5112, NULL, 'entrada', '120363175746047388', 'texto', 'Estou testando algumas feature com vibecoding \nCriando alguns fronts primeiro para as Automações atuais para ver algumas possibilidades depois', 'false_120363175746047388@g.us_2AED0F57BA48236571EF_198483541766250@lid', 'recebido', NULL, '2026-01-23 23:46:56', '2026-01-23 23:46:56'),
(5113, NULL, 'entrada', '55719178077214530547', 'texto', '*🚨 OPORTUNIDADE QUARTO E SALA MOBILIADO E AMPLO NO JARDIM ARMAÇÃO 🚨*\n🏖️ 1/4 *mobiliado*, pronto para morar\n📐 60m² bem distribuídos\n❄️ Ar-condicionado\n🪑 Móveis de 1ª qualidade\n🌅 Varanda\n🏢 Condomínio clube completo:\n🏊 Piscina | 🏋️ Academia climatizada | 🎉 Salão de festas | 🧖 Sauna | 🔒 Portaria 24h\n📍 Localização privilegiada, na orla, em frente ao mar, perto de academia e mercado 🌊\n💰 Valor baixou: *R$ 460.000,00*\n📎 Condomínio: *R$ 947,00*\n\n📲 Alice/Conrado Creci 3.935', 'false_557191780772-1453054742@g.us_ACB58FCF09A83D3D7DC81B82EC9A76CE_6661561389293@lid', 'recebido', NULL, '2026-01-24 00:10:52', '2026-01-24 00:10:52'),
(5114, NULL, 'entrada', '55719178077214530547', 'texto', '*COBERTURA DUPLEX À VENDA NO RIO VERMELHO, OPORTUNIDADE* – 3/4 revertido com 145m² por:  *R$ 495.000,00*\nViva o melhor do Rio Vermelho em um imóvel espaçoso, moderno e cheio de conforto!\nLocalizado no Morro das Vivendas, uma das áreas mais desejadas do bairro, com fácil acesso, segurança e uma vista incrível.\n🛏️ 3/4 revertido – ambientes amplos e bem distribuídos\n🏊 Piscina privativa, terraço e churrasqueira – perfeitos para relaxar ou receber amigos\n🚗 2 vagas de garagem cobertas e soltas\n☀️ Excelente ventilação e posição privilegiada\n*💰 Valor: R$ 495.000,00*\n40/40/20\n📞DQ/Conrado \nCreci 3.935 Tel 71 999881564\nCondomínio R$ 1.302,00\nIPTU R$ 124,95', 'false_557191780772-1453054742@g.us_AC18B38A486F114BEB9C29E7131758A2_6661561389293@lid', 'recebido', NULL, '2026-01-24 00:10:53', '2026-01-24 00:10:53'),
(5115, NULL, 'entrada', '55719178077214530547', 'texto', '*OCEAN HOUSE R$ 660.000,00, ANDAR ALTO, NOVÍSSIMO, VARANDÃO E NUNCA HABITADO, APARTAMENTO COM VISTA MAR EM JAGUARIBE* \n2 quartos um suíte, WC social, área de serviço, área técnica, 2 garagens soltas e cobertas.\n*R$ 660.000,00*\nCondomínio R$ 500,00.\nImóvel com ótima distribuição interna, ambientes amplos, bem iluminados e ventilados, ideal para moradia ou investimento.\nCondomínio moderno, com estrutura completa, segurança e conforto.\nRegião valorizada, com fácil acesso a comércio, serviços e áreas de lazer.\nImóvel com documentação em dias na minha mão ✋️.\nObs. Não reduz e venda a vista.\nConrado \nCreci 3.935 \nTel 99988-1564', 'false_557191780772-1453054742@g.us_AC5628F001CE7D2D0A9B6CB7C760582F_6661561389293@lid', 'recebido', NULL, '2026-01-24 00:10:54', '2026-01-24 00:10:54'),
(5116, NULL, 'entrada', '55719178077214530547', 'texto', '*JAUÁ VENDA UMA CASA COM 4/4, PORTEIRA FECHADA OU SEJA COMPLETAMENTE MOBILIADA.*\nOPORTUNIDADE MESMO *UMA CASA 🏠 COM PISCINA, 4/4, COM DOIS PAVIMENTOS A UMA QUADRA DA PRAIA.*\nEstou com as chaves 🔑 🔑. \n*Valor R$ 600.000,00*\nConrado \nCreci 3.935 \nTel 71 999881564', 'false_557191780772-1453054742@g.us_ACA0094F2BEE607C411AA34CCBCADA60_6661561389293@lid', 'recebido', NULL, '2026-01-24 00:10:54', '2026-01-24 00:10:54'),
(5117, NULL, 'entrada', '55719178077214530547', 'texto', '*PITUBA APARTAMENTO À VENDA 2/4 REVERSÍVEL 3/4* Excelente\noportunidade!\n*R$ Valor: R$ 420.000,00*\nÁrea: 86m²\nCaracterísticas do imóvel:\n2/4, suíte, WC social, com dependência completa (podendo reverter para o 3° quarto)\nVaranda, Nascente ventilado e fresco, 2 vagas de garagens soltas\nInfraestrutura do condomínio:\nSalão de festas\nGás encanado\nCondomínio: R$ 1.350,00\n*Comissionamento 40/40/20*\nConrado \nCreci 3.935 \nTel 99988-1564\n*LOCALIZAÇÃO*\nhttps://maps.app.goo.gl/yk5T6eRzhewFvfW18?g_st=aw', 'false_557191780772-1453054742@g.us_ACFF8A19178858264C2A95BABF9443CC_6661561389293@lid', 'recebido', NULL, '2026-01-24 00:10:55', '2026-01-24 00:10:55'),
(5118, NULL, 'entrada', '55719178077214530547', 'texto', '⭐ *PITUBA: 93m2. Nascente, 3 Quartos Sendo 1 Suíte, 1 WC. Social, Dependências Completas Que Pode Transformar Em 1 Gabinete Abrindo Uma Porta no Corredor, Área de Serviço Com Banheiro de Empregada, Piso Todo Em Porcelanato, Armários Em Todos os Cômodos, 2 Garagens Livres, Condomínio 1.169,48 Água e Gás Incluso, IPTU 206,59, Acessibilidade Total Para PNE Inclusive Para Piscina, Portaria Com Reconhecimento Farcial, Delicatessens Dentro do Condomínio, Prédio Pastilhado Com Total Infraestrutura na Melhor e Mais Bonita Praça da Pituba R$850.000,00*', 'false_557191780772-1453054742@g.us_AC49E958B5393F745229231E826277CE_228909073326224@lid', 'recebido', NULL, '2026-01-24 01:07:20', '2026-01-24 01:07:20'),
(5119, NULL, 'entrada', '55719178077214530547', 'texto', '⭐ *PITUBA:  80m2. 2 Quartos Sendo 1 Suíte, 1 WC. Social, Área de Serviço, 2 Varandas 1 na Sala e Outra na Suite, Piso Todo Em Porcelanato, Armários Em Todos os Cômodos, 2 Garagens, Condomínio 1.198,00 Água Inclusa, IPTU 163,64 Prédio 100% Pastilhado Com Total Infraestrutura - R$680.000,00*', 'false_557191780772-1453054742@g.us_AC454DBD93B3D20D11E8A0ACFFA556DA_228909073326224@lid', 'recebido', NULL, '2026-01-24 01:07:20', '2026-01-24 01:07:20'),
(5120, NULL, 'entrada', '55719178077214530547', 'texto', '⭐ *PITUBA: 85m2. Nascente, 2 Suites, Área de Serviço Com Banheiro de Empregada, Varanda, Piso Todo Em Porcelanato, Armários Em Todos os Cômodos, 2 Garagens  Livres, Condomínio 990,00, IPTU 309,99, Prédio 100% Pastilhado Com Total Infraestrutura Em Rua Fechada Com Guarita de Segurança - R$750.000,00*', 'false_557191780772-1453054742@g.us_ACC07DA2A2CA42FEAB99037BC948B413_228909073326224@lid', 'recebido', NULL, '2026-01-24 01:07:21', '2026-01-24 01:07:21'),
(5121, NULL, 'entrada', '55719178077214530547', 'texto', '⭐ *PITUBA: 84m2. 2 Quartos Nascente, Vista Mar, 1 Suite, 1 WC. Social , Área de Serviço Com Banheiro  e Lavanderia, Piso Todo Em Porcelanato Armarios Em Todos os Cômodos, Aquecedor a Gás, 2 Garagens, Condomínio 1.120,00 Água e Gás Incluso, IPTU 187,09, Gerador Próprio, Prédio 100% Pastilhado Com Playground Coberto e Descoberta, Salão de Festas e Portaria 24hs. R$620.000,00*', 'false_557191780772-1453054742@g.us_AC4EA4964322413D540FA9460775532E_228909073326224@lid', 'recebido', NULL, '2026-01-24 01:07:22', '2026-01-24 01:07:22'),
(5122, NULL, 'entrada', '55719178077214530547', 'texto', '⭐ *RIO VERMELHO: 75m2. Andar Baixo, 3 Quartos Sendo 1 Suíte, 1 WC. Social, Área de Serviço, Varanda, Piso da Sala e Circulacao Em Porcelanato, Armários Em 2 Quartos e Cozinha, 1 Garagem Descoberta, Condomínio 1.043,00 Água Inclusa, IPTU 128,40, Playground, Salão de Festas e Portaria 24hs. R$450.000,00*', 'false_557191780772-1453054742@g.us_ACF1332F88E29D738E372FC7D193059A_228909073326224@lid', 'recebido', NULL, '2026-01-24 01:07:23', '2026-01-24 01:07:23'),
(5123, NULL, 'entrada', '55719178077214530547', 'texto', '⭐ *PITUBA: 90m2. , Andar Alto, 3 Quartos Sendo 1 Suíte, 1 WC. Social, Área de Serviço Com Banheiro de Empregada, Varanda, Piso Tôdo Em Porcelanato, Armários na Suite e Cozinha, 2 Garagens, Condomínio 900,00, Água  Inclusa, IPTU 245,62 Prédio 100% Pastilhado Com  Total Infraestrutura na Melhor Localização da Pituba - R$750.000,00*', 'false_557191780772-1453054742@g.us_ACF5E7A67D3227254605300F21B78B20_228909073326224@lid', 'recebido', NULL, '2026-01-24 01:07:24', '2026-01-24 01:07:24'),
(5124, NULL, 'entrada', '55719178077214530547', 'texto', '⭐ *JARDIM ARMAÇÃO - Lindo Apto: 98m2. Nascente, Vista Mar Maravilhosa, 3 Quartos Sendo 2 Suítes, 1 Lavabo, Área de Serviço Com Banheiro de Empregada, Varanda Gourmet Com Fechamento Em Reiki Piso da Sala, Circulação e Varandas em Porcelanato, Quartos Em Durafloor,  Armários Em Todos os Cômodos, Decorado, 2 Garagens, Condomínio 1.350,00 Água e Gás Incluso, IPTU 236,36 Prédio 100% Pastilhado Com Total Infraestrutura - R$799.000,00*', 'false_557191780772-1453054742@g.us_AC33634C679BA752B6BC3D4AB684CA73_228909073326224@lid', 'recebido', NULL, '2026-01-24 01:07:25', '2026-01-24 01:07:25'),
(5125, NULL, 'entrada', '55719178077214530547', 'texto', '⭐ *CIDADE JARDIM: 105m2. Nascente, Andar Baixo, 3 Quartos Sendo 1 Suíte, 1 WC. Social, 1 Lavabo, Varanda, Dependências Completas, Armários Só na Cozinha, 2 Garagens Livres, Condomínio 1.314,53 Água e Gás Incluso, IPTU 247,55, Prédio Pastilhado Com Salão de Festas, Salão de Jogos, Academia, Playground Coberto e Portaria 24hs. R$680.000,00*', 'false_557191780772-1453054742@g.us_ACFDC98C265BF03D721A0BD4B067BEA9_228909073326224@lid', 'recebido', NULL, '2026-01-24 01:07:26', '2026-01-24 01:07:26'),
(5126, NULL, 'entrada', '55719178077214530547', 'texto', '⭐ *PITUBA: 220m2. Nascente, Vista Mar Maravilhosa, 4 Quartos Sendo 1 Suíte, 1 WC. Social, 1 Lavabo, Dependências Completas, Piso Todo  Em Granito Bege Bahia, Armários Em Todos os Cômodos, 1 Garagem, Condomínio 1.570,00 Água e Gás Incluso, IPTU 232,40, Prédio 100% Pastilhado, Playground, Salão de Festas, Sala de Reuniões e Portaria 24hs. R$830.000,00*', 'false_557191780772-1453054742@g.us_AC8B6CAC74D4C92E25194A5DFF8CBD71_228909073326224@lid', 'recebido', NULL, '2026-01-24 01:07:27', '2026-01-24 01:07:27'),
(5127, NULL, 'entrada', '55719178077214530547', 'texto', '⭐ *PITUBA: 220m2. Nascente, Vista Mar, 4 Quartos Revertido Para 3 Quartos Com Sala Ampliada, 1 Suíte, 1 WC. Social, 1 Lavabo, Dependências Completas, Despensa, Varanda Incorporada a Sala, Piso Todo Em Porcelanato, Armários Em Todos os cômodos, 1 Garagem, Condomínio 1.850,00 Água e Gás Inclusos, IPTU 239,00 Prédio 100% Pastilhado, Playground Coberto e Descoberto, Salão de Festas, Sala de Reuniões, Academia,  Jardins, Portaria 24hs. OPORTUNIDADE ÚNICA R$680.000,00*', 'false_557191780772-1453054742@g.us_AC0E2AAF4EAE526D815E6626016E268E_228909073326224@lid', 'recebido', NULL, '2026-01-24 01:07:28', '2026-01-24 01:07:28'),
(5128, 4, 'saida', '557588890006', 'texto', 'Notificação de link_pagamento - Agendamento #237', NULL, 'enviado', NULL, '2026-01-24 01:23:33', '2026-01-24 01:23:33'),
(5129, 4, 'saida', '557588890006', 'texto', 'Notificação de confirmacao - Agendamento #237', NULL, 'enviado', NULL, '2026-01-24 01:24:45', '2026-01-24 01:24:45'),
(5130, 4, 'entrada', '557588890006', 'texto', 'oi', 'false_557588890006@c.us_3EB05B7A909E3692953A75', 'recebido', NULL, '2026-01-24 01:31:14', '2026-01-24 01:31:14'),
(5131, 4, 'entrada', '557588890006', 'texto', 'menu', 'false_557588890006@c.us_3EB0EF84AAD12B3102517D', 'recebido', NULL, '2026-01-24 01:31:28', '2026-01-24 01:31:28'),
(5132, 4, 'entrada', '557588890006', 'texto', 'oi', 'false_557588890006@c.us_3EB08B7F3B4D327CDC5763', 'recebido', NULL, '2026-01-24 01:31:40', '2026-01-24 01:31:40'),
(5133, 4, 'entrada', '557588890006', 'texto', 'menu', 'false_557588890006@c.us_3EB0890476543C1ADA2127', 'recebido', NULL, '2026-01-24 01:32:52', '2026-01-24 01:32:52'),
(5134, 4, 'entrada', '557588890006', 'texto', 'oi', 'false_557588890006@c.us_3EB094B337EB0F090D6FBC', 'recebido', NULL, '2026-01-24 01:32:54', '2026-01-24 01:32:54'),
(5135, 4, 'entrada', '557588890006', 'texto', 'menu', 'false_557588890006@c.us_3EB0A05315CEB7E78104C6', 'recebido', NULL, '2026-01-24 01:33:03', '2026-01-24 01:33:03'),
(5136, 4, 'entrada', '557588890006', 'texto', 'oi', 'false_557588890006@c.us_3EB018130B884777130437', 'recebido', NULL, '2026-01-24 01:33:54', '2026-01-24 01:33:54'),
(5137, 4, 'entrada', '557588890006', 'texto', 'menu', 'false_557588890006@c.us_3EB07CA6629879FFA3DA53', 'recebido', NULL, '2026-01-24 01:34:18', '2026-01-24 01:34:18'),
(5138, 4, 'entrada', '557588890006', 'texto', 'oi', 'false_557588890006@c.us_3EB0D5812835EB7C8CFCFD', 'recebido', NULL, '2026-01-24 01:39:25', '2026-01-24 01:39:25'),
(5139, 4, 'entrada', '557588890006', 'texto', 'menu', 'false_557588890006@c.us_3EB03B6DA247ADE14F22C8', 'recebido', NULL, '2026-01-24 01:39:42', '2026-01-24 01:39:42'),
(5140, 4, 'entrada', '557588890006', 'texto', 'oi', 'false_557588890006@c.us_3EB08001F3FB48F899B59E', 'recebido', NULL, '2026-01-24 01:48:02', '2026-01-24 01:48:02'),
(5141, 4, 'entrada', '557588890006', 'texto', 'menu', 'false_557588890006@c.us_3EB0D7AF5848286B5304EE', 'recebido', NULL, '2026-01-24 01:48:27', '2026-01-24 01:48:27'),
(5142, 4, 'entrada', '557588890006', 'texto', 'menu', 'false_557588890006@c.us_3EB0B6B9875BB14938B4F9', 'recebido', NULL, '2026-01-24 01:50:41', '2026-01-24 01:50:41'),
(5143, 4, 'entrada', '557588890006', 'texto', 'oi', 'false_557588890006@c.us_3EB002B8B14B38F48667F2', 'recebido', NULL, '2026-01-24 01:50:50', '2026-01-24 01:50:50'),
(5144, 4, 'entrada', '557588890006', 'texto', 'oi', 'false_557588890006@c.us_3EB0BCBF4CF32B40758832', 'recebido', NULL, '2026-01-24 01:51:14', '2026-01-24 01:51:14'),
(5145, 4, 'entrada', '557588890006', 'texto', 'oi', 'false_557588890006@c.us_3EB0C3920CDF8B78682504', 'recebido', NULL, '2026-01-24 01:52:18', '2026-01-24 01:52:18'),
(5146, 4, 'entrada', '557588890006', 'texto', 'menu', 'false_557588890006@c.us_3EB0B4449FD226997C265B', 'recebido', NULL, '2026-01-24 01:52:58', '2026-01-24 01:52:58');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `whatsapp_mensagens`
--
ALTER TABLE `whatsapp_mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_estabelecimento` (`estabelecimento_id`),
  ADD KEY `idx_numero` (`numero_destino`),
  ADD KEY `idx_direcao` (`direcao`),
  ADD KEY `idx_created` (`created_at`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `whatsapp_mensagens`
--
ALTER TABLE `whatsapp_mensagens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5147;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
