ERROR - 2026-01-30 06:15:47 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 06:15:47 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 06:59:40 --> 404 Page Not Found: Robotstxt/index
ERROR - 2026-01-30 06:59:40 --> 404 Page Not Found: Robotstxt/index
ERROR - 2026-01-30 08:00:04 --> Notificacao WhatsApp: Estabelecimento 2 sem sessão WAHA configurada
ERROR - 2026-01-30 08:00:04 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 08:22:43 --> 404 Page Not Found: Apple-touch-icon-precomposedpng/index
ERROR - 2026-01-30 08:22:43 --> 404 Page Not Found: Apple-touch-iconpng/index
ERROR - 2026-01-30 08:25:02 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 08:25:02 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 09:17:44 --> 404 Page Not Found: Robotstxt/index
ERROR - 2026-01-30 09:17:54 --> 404 Page Not Found: Robotstxt/index
ERROR - 2026-01-30 09:28:23 --> Mercadopago_lib::processar_webhook - Erro ao buscar pagamento: {"status":404,"response":{"message":"Payment not found","error":"not_found","status":404,"cause":[{"code":2000,"description":"Payment not found","data":"30-01-2026T12:28:24UTC;849024d3-2ec0-4fb2-b5d2-a8a5fd36e644"}]}}
ERROR - 2026-01-30 09:28:23 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 09:31:19 --> Agendamento_model::create - Horário não disponível: Já existe um agendamento neste horário.
ERROR - 2026-01-30 10:00:03 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 10:06:03 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 10:06:03 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 10:06:03 --> WAHA API Error Response: {"statusCode":500,"timestamp":"2026-01-30T13:06:04.055Z","exception":{"message":"2 UNKNOWN: no LID found for 230047206142047@s.whatsapp.net from server","code":2,"details":"no LID found for 230047206142047@s.whatsapp.net from server","metadata":{},"name":"Error","stack":"Error: 2 UNKNOWN: no LID found for 230047206142047@s.whatsapp.net from server\n    at callErrorFromStatus (/app/node_modules/@grpc/grpc-js/build/src/call.js:32:19)\n    at Object.onReceiveStatus (/app/node_modules/@grpc/grpc-js/build/src/client.js:193:76)\n    at Object.onReceiveStatus (/app/node_modules/@grpc/grpc-js/build/src/client-interceptors.js:367:141)\n    at Object.onReceiveStatus (/app/node_modules/@grpc/grpc-js/build/src/client-interceptors.js:327:181)\n    at /app/node_modules/@grpc/grpc-js/build/src/resolving-call.js:135:78\n    at process.processTicksAndRejections (node:internal/process/task_queues:84:11)\nfor call at\n    at MessageServiceClient.makeUnaryRequest (/app/node_modules/@grpc/grpc-js/build/src/client.js:161:32)\n    at MessageServiceClient.SendMessage (/app/node_modules/@grpc/grpc-js/build/src/make-client.js:105:19)\n    at MessageServiceClient.SendMessage (/app/dist/core/engines/gows/grpc/gows.js:10097:30)\n    at node:internal/util:495:21\n    at new Promise (<anonymous>)\n    at node:internal/util:481:12\n    at WhatsappSessionGoWSPlus.sendText (/app/dist/core/engines/gows/session.gows.core.js:490:78)\n    at descriptor.value (/app/dist/core/abc/activity.js:9:35)\n    at process.processTicksAndRejections (node:internal/process/task_queues:103:5)"},"request":{"path":"/api/sendText","method":"POST","body":{"session":"est_11_barbearia_perfil","chatId":"230047206142047@c.us","text":"❌ *Agendamento Cancelado*\n\nOlá Drr,\n\nSeu agendamento foi cancelado:\n\n📅 *Data:* 30/01/2026\n⏰ *Horário:* 10:30\n💇 *Serviço:* Cabelo Degradê\n\n📝 *Motivo:* Pagamento não realizado dentro do prazo\n\nPara reagendar, entre em contato conosco.\n\n_Mensagem automática - não responda._"},"query":{}},"version":{"version":"2026.1.2","engine":"GOWS","tier":"PLUS","browser":null,"platform":"linux/x64"}}
ERROR - 2026-01-30 10:06:03 --> WAHA API Request Data: {"session":"est_11_barbearia_perfil","chatId":"230047206142047@c.us","text":"\u274c *Agendamento Cancelado*\n\nOl\u00e1 Drr,\n\nSeu agendamento foi cancelado:\n\n\ud83d\udcc5 *Data:* 30\/01\/2026\n\u23f0 *Hor\u00e1rio:* 10:30\n\ud83d\udc87 *Servi\u00e7o:* Cabelo Degrad\u00ea\n\n\ud83d\udcdd *Motivo:* Pagamento n\u00e3o realizado dentro do prazo\n\nPara reagendar, entre em contato conosco.\n\n_Mensagem autom\u00e1tica - n\u00e3o responda._"}
ERROR - 2026-01-30 10:20:46 --> 404 Page Not Found: Robotstxt/index
ERROR - 2026-01-30 10:20:46 --> 404 Page Not Found: Robotstxt/index
ERROR - 2026-01-30 12:09:50 --> 404 Page Not Found: Robotstxt/index
ERROR - 2026-01-30 12:09:50 --> 404 Page Not Found: Robotstxt/index
ERROR - 2026-01-30 12:30:41 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 12:30:41 --> WAHA API Request Data: null
ERROR - 2026-01-30 12:30:41 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 12:30:41 --> WAHA API Request Data: null
ERROR - 2026-01-30 12:30:41 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 12:30:41 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 12:30:41 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 12:55:43 --> 404 Page Not Found: Apple-touch-icon-precomposedpng/index
ERROR - 2026-01-30 12:55:43 --> 404 Page Not Found: Apple-touch-iconpng/index
ERROR - 2026-01-30 12:56:22 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 12:56:22 --> WAHA API Request Data: null
ERROR - 2026-01-30 12:56:22 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 12:56:22 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 12:56:23 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 12:56:23 --> WAHA API Request Data: {"session":"est_11_barbearia_perfil","chatId":"269681013346509@c.us","text":"\u2b50 *Obrigado pela visita!*\n\nOl\u00e1 Wendell,\n\nEsperamos que tenha gostado do atendimento!\n\n\ud83d\udc87 *Servi\u00e7o:* Cabelo Degrad\u00ea\n\ud83d\udc64 *Profissional:* Mago\n\nSua opini\u00e3o \u00e9 muito importante para n\u00f3s.\nVolte sempre! \ud83d\ude0a\n\n\ud83d\udccd Barbearia Perfil\n\n_Mensagem autom\u00e1tica - n\u00e3o responda._"}
ERROR - 2026-01-30 12:56:42 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 12:56:42 --> WAHA API Request Data: null
ERROR - 2026-01-30 12:56:42 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 12:56:42 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 12:56:42 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 12:56:42 --> WAHA API Request Data: {"session":"est_11_barbearia_perfil","chatId":"276123464237103@c.us","text":"\u2b50 *Obrigado pela visita!*\n\nOl\u00e1 Andr\u00e9,\n\nEsperamos que tenha gostado do atendimento!\n\n\ud83d\udc87 *Servi\u00e7o:* Cabelo Degrad\u00ea\n\ud83d\udc64 *Profissional:* Mago\n\nSua opini\u00e3o \u00e9 muito importante para n\u00f3s.\nVolte sempre! \ud83d\ude0a\n\n\ud83d\udccd Barbearia Perfil\n\n_Mensagem autom\u00e1tica - n\u00e3o responda._"}
ERROR - 2026-01-30 12:57:52 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 12:57:52 --> WAHA API Request Data: null
ERROR - 2026-01-30 12:57:52 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 12:57:52 --> WAHA API Request Data: null
ERROR - 2026-01-30 12:57:52 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 12:57:52 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 12:57:53 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 12:57:53 --> WAHA API Request Data: {"session":"est_11_barbearia_perfil","chatId":"557599436070@c.us","text":"\u2b50 *Obrigado pela visita!*\n\nOl\u00e1 Felipe,\n\nEsperamos que tenha gostado do atendimento!\n\n\ud83d\udc87 *Servi\u00e7o:* Cabelo Degrad\u00ea\n\ud83d\udc64 *Profissional:* Mago\n\nSua opini\u00e3o \u00e9 muito importante para n\u00f3s.\nVolte sempre! \ud83d\ude0a\n\n\ud83d\udccd Barbearia Perfil\n\n_Mensagem autom\u00e1tica - n\u00e3o responda._"}
ERROR - 2026-01-30 13:00:03 --> Notificacao WhatsApp: Estabelecimento 2 sem sessão WAHA configurada
ERROR - 2026-01-30 13:00:03 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 13:14:31 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 13:14:31 --> WAHA API Request Data: null
ERROR - 2026-01-30 13:14:31 --> Configuracoes: Falha ao obter status da sessão WAHA
ERROR - 2026-01-30 13:17:01 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 13:17:01 --> WAHA API Request Data: null
ERROR - 2026-01-30 13:17:02 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 13:17:02 --> WAHA API Request Data: {"name":"est_11_barbearia_perfil","start":true,"config":{"webhooks":[{"url":"https:\/\/iafila.doisr.com.br\/webhook_waha\/estabelecimento\/11","events":["message","message.any","message.reaction","message.ack","message.waiting","message.revoked","session.status"],"retries":{"delaySeconds":2,"attempts":15,"policy":"constant"}}],"metadata":{"tipo":"estabelecimento","estabelecimento_id":"11","nome":"Barbearia Perfil"}}}
ERROR - 2026-01-30 13:17:02 --> WAHA criar_sessao erro: {"success":false,"http_code":502,"response":"error code: 502","error":"error code: 502"}
ERROR - 2026-01-30 13:17:02 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 13:17:02 --> WAHA API Request Data: null
ERROR - 2026-01-30 13:17:02 --> Configuracoes: Falha ao obter status da sessão WAHA
ERROR - 2026-01-30 13:17:09 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 13:17:09 --> WAHA API Request Data: null
ERROR - 2026-01-30 13:17:09 --> Configuracoes: Falha ao obter status da sessão WAHA
ERROR - 2026-01-30 13:17:11 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 13:17:11 --> WAHA API Request Data: null
ERROR - 2026-01-30 13:17:12 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 13:17:12 --> WAHA API Request Data: {"name":"est_11_barbearia_perfil","start":true,"config":{"webhooks":[{"url":"https:\/\/iafila.doisr.com.br\/webhook_waha\/estabelecimento\/11","events":["message","message.any","message.reaction","message.ack","message.waiting","message.revoked","session.status"],"retries":{"delaySeconds":2,"attempts":15,"policy":"constant"}}],"metadata":{"tipo":"estabelecimento","estabelecimento_id":"11","nome":"Barbearia Perfil"}}}
ERROR - 2026-01-30 13:17:12 --> WAHA criar_sessao erro: {"success":false,"http_code":502,"response":"error code: 502","error":"error code: 502"}
ERROR - 2026-01-30 13:17:12 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 13:17:12 --> WAHA API Request Data: null
ERROR - 2026-01-30 13:17:12 --> Configuracoes: Falha ao obter status da sessão WAHA
ERROR - 2026-01-30 13:17:59 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 13:17:59 --> WAHA API Request Data: null
ERROR - 2026-01-30 13:18:00 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 13:18:00 --> WAHA API Request Data: {"name":"est_11_barbearia_perfil","start":true,"config":{"webhooks":[{"url":"https:\/\/iafila.doisr.com.br\/webhook_waha\/estabelecimento\/11","events":["message","message.any","message.reaction","message.ack","message.waiting","message.revoked","session.status"],"retries":{"delaySeconds":2,"attempts":15,"policy":"constant"}}],"metadata":{"tipo":"estabelecimento","estabelecimento_id":"11","nome":"Barbearia Perfil"}}}
ERROR - 2026-01-30 13:18:00 --> WAHA criar_sessao erro: {"success":false,"http_code":502,"response":"error code: 502","error":"error code: 502"}
ERROR - 2026-01-30 13:18:00 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 13:18:00 --> WAHA API Request Data: null
ERROR - 2026-01-30 13:18:00 --> Configuracoes: Falha ao obter status da sessão WAHA
ERROR - 2026-01-30 13:18:38 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 13:18:38 --> WAHA API Request Data: null
ERROR - 2026-01-30 13:18:38 --> Configuracoes: Falha ao obter status da sessão WAHA
ERROR - 2026-01-30 13:18:40 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 13:18:40 --> WAHA API Request Data: null
ERROR - 2026-01-30 13:18:40 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 13:18:40 --> WAHA API Request Data: {"name":"est_4_modelo_barber","start":true,"config":{"webhooks":[{"url":"https:\/\/iafila.doisr.com.br\/webhook_waha\/estabelecimento\/4","events":["message","message.any","message.reaction","message.ack","message.waiting","message.revoked","session.status"],"retries":{"delaySeconds":2,"attempts":15,"policy":"constant"}}],"metadata":{"tipo":"estabelecimento","estabelecimento_id":"4","nome":"modelo barber"}}}
ERROR - 2026-01-30 13:18:40 --> WAHA criar_sessao erro: {"success":false,"http_code":502,"response":"error code: 502","error":"error code: 502"}
ERROR - 2026-01-30 13:18:41 --> WAHA API Error Response: error code: 502
ERROR - 2026-01-30 13:18:41 --> WAHA API Request Data: null
ERROR - 2026-01-30 13:18:41 --> Configuracoes: Falha ao obter status da sessão WAHA
ERROR - 2026-01-30 13:46:44 --> Mercadopago_lib::processar_webhook - Erro ao buscar pagamento: {"status":404,"response":{"message":"Payment not found","error":"not_found","status":404,"cause":[{"code":2000,"description":"Payment not found","data":"30-01-2026T16:46:44UTC;020a304e-24af-4a58-8b8f-1b1ea61049fe"}]}}
ERROR - 2026-01-30 13:46:44 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 13:57:12 --> WAHA API Error: Operation timed out after 30001 milliseconds with 0 bytes received
ERROR - 2026-01-30 14:41:48 --> Mercadopago_lib::processar_webhook - Erro ao buscar pagamento: {"status":404,"response":{"message":"Payment not found","error":"not_found","status":404,"cause":[{"code":2000,"description":"Payment not found","data":"30-01-2026T17:41:49UTC;5e56ebec-b3a4-4d4a-8b90-eb490fb5405b"}]}}
ERROR - 2026-01-30 14:41:48 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 14:53:25 --> 404 Page Not Found: Adm/index
ERROR - 2026-01-30 14:53:30 --> 404 Page Not Found: Adm/usuarios
ERROR - 2026-01-30 14:53:52 --> 404 Page Not Found: admin/Usuariosphp/index
ERROR - 2026-01-30 15:12:35 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 15:48:02 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 15:50:03 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 15:50:03 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 17:50:24 --> 404 Page Not Found: Assets/img
ERROR - 2026-01-30 17:50:38 --> 404 Page Not Found: Assets/img
ERROR - 2026-01-30 17:50:45 --> 404 Page Not Found: Assets/img
ERROR - 2026-01-30 17:52:34 --> 404 Page Not Found: Assets/img
ERROR - 2026-01-30 17:53:19 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/profissionais/index.php 63
ERROR - 2026-01-30 17:53:19 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/profissionais/index.php 63
ERROR - 2026-01-30 17:53:56 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/profissionais/index.php 63
ERROR - 2026-01-30 17:53:56 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/profissionais/index.php 63
ERROR - 2026-01-30 17:54:02 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 17:54:02 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 17:56:55 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 17:56:55 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 17:58:11 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 17:58:11 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 17:59:31 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 17:59:31 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:00:49 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:00:49 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:01:14 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:01:14 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:02:53 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:02:53 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:04:03 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:04:03 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:05:47 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:05:47 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:06:58 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:06:58 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:07:40 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:07:40 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:08:00 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:08:00 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:09:09 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:09:09 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:10:05 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:10:05 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:12:04 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:12:04 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:12:15 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:12:15 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:13:55 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/profissionais/index.php 63
ERROR - 2026-01-30 18:13:55 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/profissionais/index.php 63
ERROR - 2026-01-30 18:14:19 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/profissionais/index.php 63
ERROR - 2026-01-30 18:14:19 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/profissionais/index.php 63
ERROR - 2026-01-30 18:14:30 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/profissionais/index.php 63
ERROR - 2026-01-30 18:14:30 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/profissionais/index.php 63
ERROR - 2026-01-30 18:19:50 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:19:50 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:20:05 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:20:05 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 18:20:50 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/profissionais/index.php 63
ERROR - 2026-01-30 18:20:50 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/profissionais/index.php 63
ERROR - 2026-01-30 18:24:28 --> WAHA API Error Response: {"message":"Session not found","error":"Not Found","statusCode":404}
ERROR - 2026-01-30 18:24:28 --> WAHA API Request Data: null
ERROR - 2026-01-30 20:16:22 --> Mercadopago_lib::processar_webhook - Erro ao buscar pagamento: {"status":404,"response":{"message":"Payment not found","error":"not_found","status":404,"cause":[{"code":2000,"description":"Payment not found","data":"30-01-2026T23:16:23UTC;80a4da77-d854-44e8-a81a-adec86b17088"}]}}
ERROR - 2026-01-30 20:16:22 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 20:19:07 --> Mercadopago_lib::processar_webhook - Erro ao buscar pagamento: {"status":404,"response":{"message":"Payment not found","error":"not_found","status":404,"cause":[{"code":2000,"description":"Payment not found","data":"30-01-2026T23:19:08UTC;0c333da8-fdce-44ad-a4aa-7cc391d6bb7f"}]}}
ERROR - 2026-01-30 20:19:07 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 20:19:08 --> Notificacao WhatsApp: Falha ao configurar WAHA para estabelecimento 12
ERROR - 2026-01-30 20:19:14 --> Mercadopago_lib::processar_webhook - Erro ao buscar pagamento: {"status":404,"response":{"message":"Payment not found","error":"not_found","status":404,"cause":[{"code":2000,"description":"Payment not found","data":"30-01-2026T23:19:14UTC;dda7a86c-2603-4620-9ee8-4504888c4362"}]}}
ERROR - 2026-01-30 20:19:14 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 20:23:47 --> Severity: Warning --> Undefined variable $estabelecimentos /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 20:23:47 --> Severity: Warning --> foreach() argument must be of type array|object, null given /home/dois8950/iafila.doisr.com.br/application/views/admin/servicos/index.php 63
ERROR - 2026-01-30 20:25:04 --> Mercadopago_lib::processar_webhook - Erro ao buscar pagamento: {"status":404,"response":{"message":"Payment not found","error":"not_found","status":404,"cause":[{"code":2000,"description":"Payment not found","data":"30-01-2026T23:25:05UTC;97e255e9-4a16-4737-b51e-87dd956ee177"}]}}
ERROR - 2026-01-30 20:25:04 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 20:45:55 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 20:48:11 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 21:18:45 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 21:24:48 --> Mercadopago_lib::processar_webhook - Erro ao buscar pagamento: {"status":404,"response":{"message":"Payment not found","error":"not_found","status":404,"cause":[{"code":2000,"description":"Payment not found","data":"31-01-2026T00:24:48UTC;66881823-7d35-4ad1-966e-99754615df81"}]}}
ERROR - 2026-01-30 21:24:48 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 21:26:45 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 21:26:45 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 21:26:46 --> Mercadopago_lib::processar_webhook - Erro ao buscar pagamento: {"status":404,"response":{"message":"Payment not found","error":"not_found","status":404,"cause":[{"code":2000,"description":"Payment not found","data":"31-01-2026T00:26:46UTC;244454ef-28fe-42d5-9f23-5b9ddfc88926"}]}}
ERROR - 2026-01-30 21:26:46 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 21:26:46 --> WAHA API Error Response: {"statusCode":500,"timestamp":"2026-01-31T00:26:46.950Z","exception":{"message":"2 UNKNOWN: no LID found for 200991920156673@s.whatsapp.net from server","code":2,"details":"no LID found for 200991920156673@s.whatsapp.net from server","metadata":{},"name":"Error","stack":"Error: 2 UNKNOWN: no LID found for 200991920156673@s.whatsapp.net from server\n    at callErrorFromStatus (/app/node_modules/@grpc/grpc-js/build/src/call.js:32:19)\n    at Object.onReceiveStatus (/app/node_modules/@grpc/grpc-js/build/src/client.js:193:76)\n    at Object.onReceiveStatus (/app/node_modules/@grpc/grpc-js/build/src/client-interceptors.js:367:141)\n    at Object.onReceiveStatus (/app/node_modules/@grpc/grpc-js/build/src/client-interceptors.js:327:181)\n    at /app/node_modules/@grpc/grpc-js/build/src/resolving-call.js:135:78\n    at process.processTicksAndRejections (node:internal/process/task_queues:84:11)\nfor call at\n    at MessageServiceClient.makeUnaryRequest (/app/node_modules/@grpc/grpc-js/build/src/client.js:161:32)\n    at MessageServiceClient.SendMessage (/app/node_modules/@grpc/grpc-js/build/src/make-client.js:105:19)\n    at MessageServiceClient.SendMessage (/app/dist/core/engines/gows/grpc/gows.js:10097:30)\n    at node:internal/util:495:21\n    at new Promise (<anonymous>)\n    at node:internal/util:481:12\n    at WhatsappSessionGoWSPlus.sendText (/app/dist/core/engines/gows/session.gows.core.js:490:78)\n    at descriptor.value (/app/dist/core/abc/activity.js:9:35)\n    at process.processTicksAndRejections (node:internal/process/task_queues:103:5)"},"request":{"path":"/api/sendText","method":"POST","body":{"session":"est_11_barbearia_perfil","chatId":"200991920156673@c.us","text":"✅ *Agendamento Confirmado!*\n\n📅 *Data:* 31/01/2026\n⏰ *Horário:* 10:30\n💇 *Serviço:* Cabelo Degradê\n👤 *Profissional:* Mago\n💰 *Valor:* R$ 30,00\n\n📍 *Local:* Barbearia Perfil\n\nCaso precise *cancelar* ou *reagendar*, digite *menu*.\n"},"query":{}},"version":{"version":"2026.1.2","engine":"GOWS","tier":"PLUS","browser":null,"platform":"linux/x64"}}
ERROR - 2026-01-30 21:26:46 --> WAHA API Request Data: {"session":"est_11_barbearia_perfil","chatId":"200991920156673@c.us","text":"\u2705 *Agendamento Confirmado!*\n\n\ud83d\udcc5 *Data:* 31\/01\/2026\n\u23f0 *Hor\u00e1rio:* 10:30\n\ud83d\udc87 *Servi\u00e7o:* Cabelo Degrad\u00ea\n\ud83d\udc64 *Profissional:* Mago\n\ud83d\udcb0 *Valor:* R$ 30,00\n\n\ud83d\udccd *Local:* Barbearia Perfil\n\nCaso precise *cancelar* ou *reagendar*, digite *menu*.\n"}
ERROR - 2026-01-30 21:31:40 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 21:40:10 --> Mercadopago_lib::processar_webhook - Erro ao buscar pagamento: {"status":404,"response":{"message":"Payment not found","error":"not_found","status":404,"cause":[{"code":2000,"description":"Payment not found","data":"31-01-2026T00:40:10UTC;5c52c463-9448-42a8-9d29-0435b6b34aa5"}]}}
ERROR - 2026-01-30 21:40:10 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 22:30:52 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 23:00:59 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
ERROR - 2026-01-30 23:02:58 --> Severity: Warning --> Undefined array key "WARNING" /home/dois8950/iafila.doisr.com.br/system/core/Log.php 181
