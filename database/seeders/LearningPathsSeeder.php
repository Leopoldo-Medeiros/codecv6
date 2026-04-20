<?php

namespace Database\Seeders;

use App\Models\Path;
use App\Models\PathStep;
use App\Models\User;
use Illuminate\Database\Seeder;

class LearningPathsSeeder extends Seeder
{
    public function run(): void
    {
        $consultant = User::where('email', 'consultant@consultant.com')->firstOrFail();

        foreach ($this->paths() as $pathData) {
            $steps = $pathData['steps'];
            unset($pathData['steps']);

            $path = Path::create([
                'name'          => $pathData['name'],
                'description'   => $pathData['description'],
                'consultant_id' => $consultant->id,
            ]);

            foreach ($steps as $order => $step) {
                PathStep::create(array_merge($step, [
                    'path_id' => $path->id,
                    'order'   => $order + 1,
                ]));
            }

            $this->command->info("Created path: {$path->name} with " . count($steps) . ' steps');
        }
    }

    private function paths(): array
    {
        return [
            // ─────────────────────────────────────────────────────────────
            // PATH 1 — PHP para o Mundo Real
            // ─────────────────────────────────────────────────────────────
            [
                'name'        => 'PHP para o Mundo Real',
                'description' => 'Fundamentos sólidos de PHP moderno e Laravel com foco em código legível, testável e pronto para produção. Você vai aprender não só a escrever código que funciona, mas código que outros desenvolvedores conseguem manter.',
                'steps'       => [
                    [
                        'title'       => 'PHP Moderno: Types, Nullables e Enums',
                        'type'        => 'reading',
                        'description' => 'PHP 8+ introduziu um sistema de tipos expressivo que elimina toda uma classe de bugs antes mesmo de rodar o código. Neste módulo você vai entender typed properties, union types, intersection types, enums backed por string/int, e como o modo strict_types muda o comportamento do interpretador. A diferença entre um código PHP de 2015 e um de 2024 começa aqui.',
                        'resources'   => [
                            ['label' => 'PHP 8.3 Type System', 'url' => 'https://www.php.net/manual/en/language.types.declarations.php'],
                            ['label' => 'PHP Enums — documentação oficial', 'url' => 'https://www.php.net/manual/en/language.enumerations.php'],
                            ['label' => 'Typed Properties RFC', 'url' => 'https://wiki.php.net/rfc/typed_properties_v2'],
                        ],
                        'instructions'     => null,
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Laravel Request Lifecycle: O que acontece antes do seu código rodar',
                        'type'        => 'reading',
                        'description' => 'A maioria dos juniors trata o Laravel como magia. Entender o lifecycle — desde o index.php até o Response — é o que separa quem resolve problemas de quem apenas reproduz tutoriais. Vamos dissecar o bootstrap, os Service Providers, o Container IoC, e como middlewares formam um pipeline. Com esse conhecimento, você vai entender onde cada erro surge.',
                        'resources'   => [
                            ['label' => 'Laravel Request Lifecycle', 'url' => 'https://laravel.com/docs/lifecycle'],
                            ['label' => 'Service Container', 'url' => 'https://laravel.com/docs/container'],
                            ['label' => 'Service Providers', 'url' => 'https://laravel.com/docs/providers'],
                        ],
                        'instructions'     => null,
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Eloquent Avançado: Scopes, Accessors e Events',
                        'type'        => 'reading',
                        'description' => 'Eloquent é poderoso mas facilmente mal-usado. Vamos cobrir query scopes locais e globais para encapsular regras de negócio nas queries, accessors/mutators com casts customizados, e model events (creating, saved, deleted) para lógica que precisa disparar em mudanças de estado. Você vai aprender também quando NÃO usar Eloquent e escrever SQL direto.',
                        'resources'   => [
                            ['label' => 'Eloquent Scopes', 'url' => 'https://laravel.com/docs/eloquent#query-scopes'],
                            ['label' => 'Eloquent Mutators & Casting', 'url' => 'https://laravel.com/docs/eloquent-mutators'],
                            ['label' => 'Model Events & Observers', 'url' => 'https://laravel.com/docs/eloquent#events'],
                        ],
                        'instructions'     => null,
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Lab: Construa uma API RESTful com Autenticação',
                        'type'        => 'lab',
                        'description' => 'Coloque em prática tudo que aprendeu construindo uma API de gerenciamento de tarefas: autenticação via Sanctum, CRUD completo, policy de autorização, Form Requests com validação, API Resources para transformação de resposta, e paginação. O foco não é velocidade — é fazer cada decisão com intenção.',
                        'resources'   => [
                            ['label' => 'Laravel Sanctum', 'url' => 'https://laravel.com/docs/sanctum'],
                            ['label' => 'API Resources', 'url' => 'https://laravel.com/docs/eloquent-resources'],
                            ['label' => 'Form Request Validation', 'url' => 'https://laravel.com/docs/validation#form-request-validation'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Crie um projeto Laravel novo com `composer create-project laravel/laravel task-api`'],
                            ['id' => 2, 'text' => 'Configure Sanctum e crie a migration de users com roles (admin, user)'],
                            ['id' => 3, 'text' => 'Crie o modelo Task com: title, description, status (pending/in_progress/done), due_date, user_id'],
                            ['id' => 4, 'text' => 'Implemente TaskController com index, store, show, update, destroy — usando Form Request para validação'],
                            ['id' => 5, 'text' => 'Adicione Policy: usuário só pode ver e editar as próprias tasks'],
                            ['id' => 6, 'text' => 'Crie TaskResource com transformação de datas (Carbon) e computed field `is_overdue`'],
                            ['id' => 7, 'text' => 'Escreva 3 Feature Tests cobrindo: criar task, listar só as próprias, e tentativa não-autorizada'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Code Review: O que um Senior veria no seu código',
                        'type'        => 'reading',
                        'description' => 'Você escreveu o código, os testes passam, mas o PR foi recusado. Por quê? Este módulo cobre os padrões que seniors buscam em code reviews: N+1 queries, fat controllers vs thin controllers, métodos com múltiplas responsabilidades, nomes que mentem, e o princípio de "least surprise". Você vai revisar código ruim real e reescrever com a mentalidade certa.',
                        'resources'   => [
                            ['label' => 'Clean Code — Robert C. Martin (resumo)', 'url' => 'https://gist.github.com/wojteklu/73c6914cc446146b8b533c0988cf8d29'],
                            ['label' => 'Laravel Best Practices', 'url' => 'https://github.com/alexeymezenin/laravel-best-practices'],
                            ['label' => 'SOLID em PHP — exemplos práticos', 'url' => 'https://www.youtube.com/watch?v=_jDNAcej0JE'],
                        ],
                        'instructions'     => null,
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Challenge: Encontre e Corrija os Bugs nessa API Laravel',
                        'type'        => 'challenge',
                        'description' => 'Uma API de e-commerce está em produção com bugs críticos reportados pelos usuários. Você tem acesso ao código e aos logs. Sem stack trace fornecido — você precisa encontrar, reproduzir e corrigir cada problema. Este é o trabalho real de um desenvolvedor backend.',
                        'resources'   => [
                            ['label' => 'Laravel Debugging com dd() e dump()', 'url' => 'https://laravel.com/docs/helpers#method-dd'],
                            ['label' => 'PHP Error Levels', 'url' => 'https://www.php.net/manual/en/errorfunc.constants.php'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Clone o repositório do challenge: github.com/codecv/challenge-laravel-bugs'],
                            ['id' => 2, 'text' => 'Leia o README com a descrição de cada bug reportado pelo usuário'],
                            ['id' => 3, 'text' => 'Bug #1: "Pedidos aparecem duplicados no checkout" — encontre a causa raiz'],
                            ['id' => 4, 'text' => 'Bug #2: "Admin consegue deletar a própria conta" — corrija a autorização'],
                            ['id' => 5, 'text' => 'Bug #3: "API retorna 500 quando produto está fora de estoque" — adicione tratamento adequado'],
                            ['id' => 6, 'text' => 'Escreva um teste de regressão para cada bug corrigido'],
                            ['id' => 7, 'text' => 'Abra um PR com descrição explicando: causa raiz, impacto e solução'],
                        ],
                        'challenge_prompt' => 'Você é o único desenvolvedor de plantão. São 23h e o suporte reportou 3 bugs críticos em produção afetando o checkout. O CEO está acordado. Você tem 2 horas. Documente cada passo — sua investigação será revisada amanhã.',
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Testing Mindset: Testes que Realmente Protegem',
                        'type'        => 'lab',
                        'description' => 'Testes mal escritos dão falsa segurança e atrasam o desenvolvimento. Vamos cobrir a diferença entre unit, feature e integration tests no contexto Laravel, quando usar mocks vs banco real, como estruturar factories com estados, e como escrever assertions que capturam regressões reais. Feature tests que cobrem o caminho HTTP de ponta a ponta são sua melhor defesa.',
                        'resources'   => [
                            ['label' => 'Laravel Testing', 'url' => 'https://laravel.com/docs/testing'],
                            ['label' => 'Model Factories', 'url' => 'https://laravel.com/docs/eloquent-factories'],
                            ['label' => 'Pest PHP — modern testing', 'url' => 'https://pestphp.com/docs/installation'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Configure o ambiente de testes com SQLite in-memory no phpunit.xml'],
                            ['id' => 2, 'text' => 'Crie factories com estados: TaskFactory::pending(), TaskFactory::overdue()'],
                            ['id' => 3, 'text' => 'Escreva feature tests para: autenticação, autorização, e validação de inputs'],
                            ['id' => 4, 'text' => 'Adicione um teste que detecta N+1 usando `DB::getQueryLog()`'],
                            ['id' => 5, 'text' => 'Configure GitHub Actions para rodar os testes em cada PR'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // PATH 2 — Debugging como Profissional
            // ─────────────────────────────────────────────────────────────
            [
                'name'        => 'Debugging como Profissional',
                'description' => 'A habilidade que mais diferencia um junior de um senior não é escrever código — é encontrar onde o código está errado. Este path ensina o método científico aplicado ao debugging: hipótese, evidência, isolamento, correção. Com Xdebug, logs estruturados e análise de stack traces como ferramentas principais.',
                'steps'       => [
                    [
                        'title'       => 'Por que Juniors Não Sabem Debugar (e como mudar isso)',
                        'type'        => 'reading',
                        'description' => 'O problema não é falta de esforço — é falta de método. A maioria dos devs júniors debugging adiciona `var_dump` aleatoriamente até o bug desaparecer. Esse módulo apresenta o método científico de debugging: reproduzir de forma confiável, isolar variáveis, formar hipótese, testar, confirmar. Você nunca mais vai debugar "no escuro".',
                        'resources'   => [
                            ['label' => 'The Art of Debugging — pragmatic approach', 'url' => 'https://www.debuggingbook.org/'],
                            ['label' => 'Rubber Duck Debugging', 'url' => 'https://rubberduckdebugging.com/'],
                            ['label' => 'How to Debug — Julia Evans', 'url' => 'https://jvns.ca/blog/2022/12/21/new-zine--how-debugging-works/'],
                        ],
                        'instructions'     => null,
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Xdebug: Configuração, Breakpoints e Step Debugging',
                        'type'        => 'lab',
                        'description' => 'O `var_dump` é a ferramenta de alguém que não conhece o Xdebug. Com Xdebug e VS Code (ou PhpStorm), você pode pausar a execução em qualquer linha, inspecionar o estado completo da aplicação, e executar linha a linha enquanto observa variáveis mudarem. Isso transforma debugging de adivinhação em investigação.',
                        'resources'   => [
                            ['label' => 'Xdebug 3 — documentação oficial', 'url' => 'https://xdebug.org/docs/step_debug'],
                            ['label' => 'VS Code PHP Debug extension', 'url' => 'https://marketplace.visualstudio.com/items?itemName=xdebug.php-debug'],
                            ['label' => 'Configurando Xdebug no Docker/Lando', 'url' => 'https://xdebug.org/docs/install'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Verifique se Xdebug está instalado: `php -v` deve mostrar "with Xdebug v3"'],
                            ['id' => 2, 'text' => 'Configure o VS Code: instale a extensão "PHP Debug" e crie o launch.json com port 9003'],
                            ['id' => 3, 'text' => 'Adicione um breakpoint na linha 1 de uma rota Laravel e dispare via curl'],
                            ['id' => 4, 'text' => 'Pratique Step Over (F10), Step Into (F11), e Step Out (Shift+F11)'],
                            ['id' => 5, 'text' => 'Adicione um Watch Expression para monitorar uma variável específica'],
                            ['id' => 6, 'text' => 'Use Conditional Breakpoints: pause só quando `$user->id === 5`'],
                            ['id' => 7, 'text' => 'Demonstre o debugging completo de uma query N+1 usando breakpoints no Eloquent'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Lendo Stack Traces como um Detetive',
                        'type'        => 'reading',
                        'description' => 'Um stack trace é um mapa do crime — cada linha te diz onde o sistema estava e o que estava fazendo quando o erro aconteceu. A maioria dos juniors lê apenas a primeira linha. Neste módulo você vai aprender a ler de baixo para cima, identificar o frame do seu código vs vendor, entender exceptions encadeadas, e extrair o contexto que importa para resolver o problema.',
                        'resources'   => [
                            ['label' => 'How to Read a Stack Trace', 'url' => 'https://rollbar.com/blog/php-stack-trace/'],
                            ['label' => 'PHP Exceptions documentação', 'url' => 'https://www.php.net/manual/en/language.exceptions.php'],
                            ['label' => 'Ignition — Laravel error page explained', 'url' => 'https://flareapp.io/docs/ignition/introduction'],
                        ],
                        'instructions'     => null,
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Logs Estruturados com Monolog e Laravel Log',
                        'type'        => 'lab',
                        'description' => 'Logs são a memória da sua aplicação em produção — onde o Xdebug não existe. Logs estruturados (JSON) são pesquisáveis, filtráveis e integram com New Relic, Datadog, e qualquer stack de observabilidade. Vamos configurar channels separados por contexto, log levels corretos, e context data que torna cada log entry auto-suficiente para investigação.',
                        'resources'   => [
                            ['label' => 'Laravel Logging', 'url' => 'https://laravel.com/docs/logging'],
                            ['label' => 'Monolog documentation', 'url' => 'https://seldaek.github.io/monolog/'],
                            ['label' => 'Structured Logging best practices', 'url' => 'https://www.loggly.com/use-cases/best-practices-for-php-logging/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Configure um channel `structured` no config/logging.php com formatter JSON'],
                            ['id' => 2, 'text' => 'Adicione context global via `Log::withContext()` no middleware: user_id, request_id, ip'],
                            ['id' => 3, 'text' => 'Implemente log levels corretos: DEBUG para dev, INFO para ações do usuário, ERROR para falhas'],
                            ['id' => 4, 'text' => 'Crie um custom Processor que adiciona `memory_usage` e `duration_ms` a cada log'],
                            ['id' => 5, 'text' => 'Configure log rotation: logs diários com retenção de 30 dias'],
                            ['id' => 6, 'text' => 'Simule um bug em produção e resolva usando apenas os logs gerados'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Challenge: Resolva o Incident Usando Apenas Logs',
                        'type'        => 'challenge',
                        'description' => 'Em produção você não tem Xdebug. Tem logs. Um sistema de pagamentos está falhando silenciosamente — alguns pagamentos são processados, outros não, e o usuário só descobre horas depois. Você recebeu um dump de 48 horas de logs. Encontre o padrão, identifique a causa raiz e proponha a correção.',
                        'resources'   => [
                            ['label' => 'Log analysis techniques', 'url' => 'https://www.elastic.co/what-is/log-analysis'],
                            ['label' => 'grep, awk para análise de logs', 'url' => 'https://www.digitalocean.com/community/tutorials/how-to-use-journalctl-to-view-and-manipulate-systemd-logs'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Baixe o arquivo de logs do challenge (link no material)'],
                            ['id' => 2, 'text' => 'Use grep/awk para filtrar apenas os eventos de pagamento com status ERROR'],
                            ['id' => 3, 'text' => 'Identifique o padrão temporal: o bug acontece sempre? Só em horários específicos?'],
                            ['id' => 4, 'text' => 'Correlacione os logs de erro com logs de contexto (user_id, request_id) para traçar uma requisição completa'],
                            ['id' => 5, 'text' => 'Documente sua hipótese da causa raiz com evidências dos logs'],
                            ['id' => 6, 'text' => 'Escreva um teste que reproduz o cenário de falha identificado'],
                        ],
                        'challenge_prompt' => 'O CFO ligou. 127 pagamentos nas últimas 24h foram marcados como "pending" mas o gateway já processou e cobrou o cliente. O dinheiro saiu da conta do usuário, mas o pedido nunca foi confirmado. Você tem os logs. Tem 1 hora para encontrar a causa antes da reunião com o board.',
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Reproduzindo Bugs de Produção no Ambiente Local',
                        'type'        => 'reading',
                        'description' => 'O bug funciona em produção mas não reproduz local — a frase mais frustrante do desenvolvimento. Vamos cobrir as causas mais comuns: diferenças de dados (seeds vs produção real), timezone, race conditions, environment variables, e como usar database dumps sanitizados, feature flags e docker para aproximar o ambiente local da realidade de produção.',
                        'resources'   => [
                            ['label' => 'Reproducing production bugs locally', 'url' => 'https://blog.sentry.io/2020/06/24/what-is-a-reproduction/'],
                            ['label' => 'Database anonymization tools', 'url' => 'https://github.com/machbarmacher/gdpr-dump'],
                        ],
                        'instructions'     => null,
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Challenge Final: Debug Session — 3 Bugs, 60 Minutos',
                        'type'        => 'challenge',
                        'description' => 'Três bugs de complexidade crescente em uma aplicação Laravel real. Você tem o Xdebug, os logs, e o código-fonte. Nenhum hint. Documente cada passo da sua investigação — o processo importa tanto quanto a solução.',
                        'resources'   => [
                            ['label' => 'PHP error reporting', 'url' => 'https://www.php.net/manual/en/function.error-reporting.php'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Bug #1 (Fácil): Um endpoint retorna dados desatualizados — cache invalidation problem'],
                            ['id' => 2, 'text' => 'Bug #2 (Médio): Race condition no processo de reserva de estoque'],
                            ['id' => 3, 'text' => 'Bug #3 (Difícil): Memory leak que só aparece após 1000 requisições em um job queue'],
                            ['id' => 4, 'text' => 'Para cada bug: documente a hipótese, a evidência que a confirma, e o fix'],
                            ['id' => 5, 'text' => 'Escreva um post-mortem de 1 parágrafo para cada bug no estilo de incidents reais'],
                        ],
                        'challenge_prompt' => 'Você foi promovido ao time de platform engineering. Seu primeiro dia: a fila de incidentes tem 3 bugs abertos há mais de 72h que ninguém conseguiu resolver. Sua missão: fecha todos os três antes do end of day.',
                        'lab_url'          => null,
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // PATH 3 — APM com New Relic
            // ─────────────────────────────────────────────────────────────
            [
                'name'        => 'APM com New Relic',
                'description' => 'Application Performance Monitoring transforma sua aplicação de uma caixa-preta em um sistema transparente. Com New Relic APM você enxerga cada requisição, cada query, cada chamada externa — e sabe exatamente onde o tempo está sendo gasto antes que o usuário reclame.',
                'steps'       => [
                    [
                        'title'       => 'O que é APM e por que todo desenvolvedor deveria usar',
                        'type'        => 'reading',
                        'description' => 'APM (Application Performance Monitoring) não é só para ops — é a ferramenta que transforma um desenvolvedor em alguém que entende o sistema que construiu. Vamos cobrir os conceitos de traces, spans, métricas de throughput e error rate, e por que o percentil 99 importa mais que a média. Você vai entender como o New Relic instrumenta automaticamente PHP e o que isso significa na prática.',
                        'resources'   => [
                            ['label' => 'New Relic APM Introduction', 'url' => 'https://docs.newrelic.com/docs/apm/new-relic-apm/getting-started/introduction-apm/'],
                            ['label' => 'Four Golden Signals — Google SRE', 'url' => 'https://sre.google/sre-book/monitoring-distributed-systems/'],
                            ['label' => 'APM vs Traditional Monitoring', 'url' => 'https://newrelic.com/resources/articles/what-is-apm'],
                        ],
                        'instructions'     => null,
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Lab: Instalando e Configurando o Agente PHP da New Relic',
                        'type'        => 'lab',
                        'description' => 'O agente PHP da New Relic instrumenta automaticamente Laravel, Symfony, frameworks de banco de dados e clientes HTTP — sem alterar uma linha do seu código. Neste lab você vai instalar, configurar e validar que os dados estão chegando na plataforma.',
                        'resources'   => [
                            ['label' => 'New Relic PHP Agent Install', 'url' => 'https://docs.newrelic.com/docs/apm/agents/php-agent/installation/php-agent-installation-overview/'],
                            ['label' => 'PHP Agent Configuration', 'url' => 'https://docs.newrelic.com/docs/apm/agents/php-agent/configuration/php-agent-configuration/'],
                            ['label' => 'New Relic Free Tier', 'url' => 'https://newrelic.com/signup'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Crie uma conta gratuita em newrelic.com e obtenha sua License Key'],
                            ['id' => 2, 'text' => 'Instale o agente: `newrelic-install install` no servidor (ou via Docker no Dockerfile)'],
                            ['id' => 3, 'text' => 'Configure newrelic.ini: app_name, license_key, e enabled=true'],
                            ['id' => 4, 'text' => 'Reinicie o PHP-FPM e faça 10 requisições para sua aplicação'],
                            ['id' => 5, 'text' => 'Verifique no painel New Relic > APM > sua aplicação: dados devem aparecer em ~2 minutos'],
                            ['id' => 6, 'text' => 'Navegue para Transactions e identifique o endpoint mais lento'],
                            ['id' => 7, 'text' => 'Abra um trace individual e localize as queries SQL executadas'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Lendo Dashboards: Throughput, Error Rate e Apdex',
                        'type'        => 'reading',
                        'description' => 'Ter o New Relic instalado sem saber ler os dados é como ter um stethoscope sem saber anatomia. Vamos dissecar cada seção do APM dashboard: o que Apdex realmente mede, como interpretar o error rate sem entrar em pânico, a diferença entre latência média e p95/p99, e como o Throughput por minuto revela padrões de uso real.',
                        'resources'   => [
                            ['label' => 'Apdex Score explained', 'url' => 'https://docs.newrelic.com/docs/apm/new-relic-apm/apdex/apdex-measure-user-satisfaction/'],
                            ['label' => 'APM Summary page walkthrough', 'url' => 'https://docs.newrelic.com/docs/apm/new-relic-apm/getting-started/summary-page/'],
                            ['label' => 'Understanding percentiles', 'url' => 'https://www.honeycomb.io/blog/percentiles-vs-averages'],
                        ],
                        'instructions'     => null,
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Lab: Detectando e Corrigindo N+1 Queries com New Relic',
                        'type'        => 'lab',
                        'description' => 'O N+1 é o bug de performance mais comum em aplicações Laravel — e o mais invisível sem monitoramento. New Relic mostra exatamente quantas queries cada request executa, com o SQL completo e o tempo gasto. Neste lab você vai encontrar N+1s reais, confirmar com o Slow Query trace, e corrigir com eager loading.',
                        'resources'   => [
                            ['label' => 'N+1 Query Problem', 'url' => 'https://laravel.com/docs/eloquent-relationships#eager-loading'],
                            ['label' => 'New Relic Slow Query Traces', 'url' => 'https://docs.newrelic.com/docs/apm/new-relic-apm/getting-started/apm-agent-data-security/'],
                            ['label' => 'Laravel Debugbar', 'url' => 'https://github.com/barryvdh/laravel-debugbar'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Abra New Relic > APM > Databases e ordene por "Most time consuming"'],
                            ['id' => 2, 'text' => 'Clique no endpoint suspeito e abra um Transaction Trace'],
                            ['id' => 3, 'text' => 'Identifique queries repetidas com padrão similar (SELECT WHERE id = ?)'],
                            ['id' => 4, 'text' => 'Localize o código Eloquent correspondente e adicione eager loading `with()`'],
                            ['id' => 5, 'text' => 'Faça o deploy e compare: o número de queries no trace deve cair de N para 1+1'],
                            ['id' => 6, 'text' => 'Configure um alert: notify se transaction time > 2s por mais de 5 minutos'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Challenge: Sua App Está Lenta — Encontre o Gargalo em 30 Minutos',
                        'type'        => 'challenge',
                        'description' => 'Uma aplicação de e-commerce está com p95 de 4.2 segundos no endpoint de listagem de produtos. Os usuários estão abandonando o carrinho. Você tem acesso ao New Relic e ao código. Sem alterar infraestrutura — a solução deve ser no código.',
                        'resources'   => [
                            ['label' => 'New Relic Transaction Traces', 'url' => 'https://docs.newrelic.com/docs/apm/transactions/transaction-traces/introduction-transaction-traces/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Acesse o New Relic da aplicação de challenge e navegue para APM > Transactions'],
                            ['id' => 2, 'text' => 'Identifique o endpoint mais lento e abra o trace com maior duração'],
                            ['id' => 3, 'text' => 'Anote: quantas queries foram executadas? Qual o tempo gasto em cada segmento?'],
                            ['id' => 4, 'text' => 'Localize no código a causa do problema (pode ser N+1, query sem índice, ou processamento síncrono desnecessário)'],
                            ['id' => 5, 'text' => 'Implemente a correção e documente a melhoria esperada'],
                            ['id' => 6, 'text' => 'Escreva um relatório de 1 página: problema encontrado, evidência no New Relic, solução, resultado'],
                        ],
                        'challenge_prompt' => 'Você é o dev on-call. São segunda-feira 9h e o Black Friday é amanhã. O SLA é p95 < 500ms mas a listagem de produtos está em 4.2s. O marketing já enviou 500k emails com o link da landing page. Você tem o New Relic aberto. O que você faz primeiro?',
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Errors Inbox: Triaging e Resolvendo Erros em Produção',
                        'type'        => 'lab',
                        'description' => 'Errors Inbox é onde erros de produção vão para ser investigados antes de virarem incidentes. Vamos configurar alertas de error rate, usar o Error Fingerprinting para agrupar erros similares, analisar o impacto por número de usuários afetados, e criar um workflow de triage que evita que erros silenciosos acumulem.',
                        'resources'   => [
                            ['label' => 'New Relic Errors Inbox', 'url' => 'https://docs.newrelic.com/docs/errors-inbox/getting-started/'],
                            ['label' => 'Error alerting best practices', 'url' => 'https://docs.newrelic.com/docs/alerts-applied-intelligence/new-relic-alerts/alert-conditions/apm-metric-alert-conditions/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Navegue para New Relic > Errors Inbox e familiarize-se com o agrupamento automático'],
                            ['id' => 2, 'text' => 'Abra o error com maior impacto (mais usuários afetados) e analise o stack trace completo'],
                            ['id' => 3, 'text' => 'Crie um Alert Policy: notify no Slack quando error rate > 1% por 5 minutos'],
                            ['id' => 4, 'text' => 'Marque erros conhecidos como "Acknowledged" e configure owner + expected resolution'],
                            ['id' => 5, 'text' => 'Configure Error Fingerprinting customizado para um erro específico da sua aplicação'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Custom Instrumentation: Medindo o que o Agente Não Mede',
                        'type'        => 'lab',
                        'description' => 'O agente automático cobre 80% do que você precisa. Os outros 20% — processamento de arquivos, integrações com APIs externas, jobs complexos — precisam de instrumentação manual. Vamos usar a New Relic PHP API para criar custom transactions, segmentos, e atributos que aparecem nos traces.',
                        'resources'   => [
                            ['label' => 'New Relic PHP API', 'url' => 'https://docs.newrelic.com/docs/apm/agents/php-agent/php-agent-api/guide-using-php-agent-api/'],
                            ['label' => 'Custom Attributes', 'url' => 'https://docs.newrelic.com/docs/apm/agents/php-agent/php-agent-api/newrelic_add_custom_parameter/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Adicione `newrelic_add_custom_parameter("user_plan", $user->plan)` em um endpoint crítico'],
                            ['id' => 2, 'text' => 'Crie um custom segment para um bloco de processamento de imagens com `newrelic_start_transaction`'],
                            ['id' => 3, 'text' => 'Use `newrelic_notice_error()` para capturar exceptions de forma controlada com contexto adicional'],
                            ['id' => 4, 'text' => 'Verifique no New Relic que os custom attributes aparecem nos transaction details'],
                            ['id' => 5, 'text' => 'Crie uma NRQL query: `SELECT average(duration) FROM Transaction WHERE user_plan = \'premium\' SINCE 1 day ago`'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // PATH 4 — OpenTelemetry na Prática
            // ─────────────────────────────────────────────────────────────
            [
                'name'        => 'OpenTelemetry na Prática',
                'description' => 'OpenTelemetry é o padrão aberto para instrumentação de aplicações — vendor-neutral, portável entre qualquer backend de observabilidade. Aprenda a instrumentar aplicações PHP manualmente, criar spans customizados, e exportar dados para New Relic via OTLP.',
                'steps'       => [
                    [
                        'title'       => 'OTel 101: Traces, Spans, Metrics e Logs',
                        'type'        => 'reading',
                        'description' => 'OpenTelemetry unifica os três pilares da observabilidade — traces, metrics e logs — sob uma única API e SDK. Vamos entender o modelo de dados: o que é um Trace ID, como Spans se relacionam em parent-child, a diferença entre gauge e counter metrics, e como o W3C TraceContext propaga contexto entre serviços. É a base para tudo que vem depois.',
                        'resources'   => [
                            ['label' => 'OpenTelemetry Concepts', 'url' => 'https://opentelemetry.io/docs/concepts/'],
                            ['label' => 'Observability Primer', 'url' => 'https://opentelemetry.io/docs/concepts/observability-primer/'],
                            ['label' => 'W3C Trace Context spec', 'url' => 'https://www.w3.org/TR/trace-context/'],
                        ],
                        'instructions'     => null,
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Lab: Instalando o SDK OpenTelemetry para PHP',
                        'type'        => 'lab',
                        'description' => 'O SDK OpenTelemetry para PHP está estável e pronto para produção. Vamos instalar via Composer, configurar o Tracer Provider com exportador OTLP, e validar que spans chegam ao backend. A configuração correta do auto-instrumentation para Laravel é o passo mais importante.',
                        'resources'   => [
                            ['label' => 'OpenTelemetry PHP SDK', 'url' => 'https://opentelemetry.io/docs/languages/php/'],
                            ['label' => 'opentelemetry-php no GitHub', 'url' => 'https://github.com/open-telemetry/opentelemetry-php'],
                            ['label' => 'New Relic OTLP endpoint', 'url' => 'https://docs.newrelic.com/docs/opentelemetry/best-practices/opentelemetry-otlp/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Instale: `composer require open-telemetry/sdk open-telemetry/exporter-otlp`'],
                            ['id' => 2, 'text' => 'Instale a extensão PHP: `pecl install opentelemetry` e habilite no php.ini'],
                            ['id' => 3, 'text' => 'Instale auto-instrumentation para Laravel: `composer require open-telemetry/opentelemetry-auto-laravel`'],
                            ['id' => 4, 'text' => 'Configure via env vars: OTEL_SERVICE_NAME, OTEL_EXPORTER_OTLP_ENDPOINT, OTEL_EXPORTER_OTLP_HEADERS'],
                            ['id' => 5, 'text' => 'Execute `php artisan serve` e faça uma requisição — deve aparecer no backend de observabilidade'],
                            ['id' => 6, 'text' => 'Verifique: o trace tem spans para HTTP request, middleware, controller, e queries SQL?'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Instrumentação Manual: Criando Spans Customizados',
                        'type'        => 'lab',
                        'description' => 'O auto-instrumentation cobre frameworks e bibliotecas. Para lógica de negócio própria — processos de checkout, cálculos de score, integrações proprietárias — você precisa criar spans manualmente. Vamos cobrir como criar spans filhos, adicionar atributos semânticos, registrar eventos dentro de spans, e marcar spans como erro com o status correto.',
                        'resources'   => [
                            ['label' => 'Creating Spans manually', 'url' => 'https://opentelemetry.io/docs/languages/php/instrumentation/'],
                            ['label' => 'Semantic Conventions', 'url' => 'https://opentelemetry.io/docs/specs/semconv/'],
                            ['label' => 'OTel PHP examples', 'url' => 'https://github.com/open-telemetry/opentelemetry-php/tree/main/examples'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Injete o Tracer no seu Service via constructor: `OpenTelemetry\\API\\Globals::tracerProvider()->getTracer(\'meu-servico\')`'],
                            ['id' => 2, 'text' => 'Crie um span para o processo de checkout: `$span = $tracer->spanBuilder(\'checkout.process\')->startSpan()`'],
                            ['id' => 3, 'text' => 'Adicione atributos: `$span->setAttribute(\'checkout.total\', $total)->setAttribute(\'checkout.items\', count($items))`'],
                            ['id' => 4, 'text' => 'Crie span filho para cada etapa: validação, cálculo de frete, processamento de pagamento'],
                            ['id' => 5, 'text' => 'Em caso de exceção: `$span->recordException($e)->setStatus(StatusCode::STATUS_ERROR, $e->getMessage())`'],
                            ['id' => 6, 'text' => 'Finalize sempre com `$span->end()` no finally block'],
                            ['id' => 7, 'text' => 'Visualize o trace completo no backend e confirme a hierarquia parent-child dos spans'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Exportando para New Relic via OTLP',
                        'type'        => 'lab',
                        'description' => 'New Relic é um backend OTLP nativo — aceita traces, metrics e logs via OTLP/gRPC ou OTLP/HTTP. Vamos configurar o exporter corretamente, entender os headers de autenticação, e validar que traces criados com OTel aparecem no New Relic APM junto com os dados do agente nativo.',
                        'resources'   => [
                            ['label' => 'New Relic OTLP ingest', 'url' => 'https://docs.newrelic.com/docs/opentelemetry/best-practices/opentelemetry-otlp/'],
                            ['label' => 'OTLP Specification', 'url' => 'https://opentelemetry.io/docs/specs/otlp/'],
                            ['label' => 'NR OpenTelemetry examples', 'url' => 'https://github.com/newrelic/newrelic-opentelemetry-examples'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Configure OTEL_EXPORTER_OTLP_ENDPOINT=https://otlp.nr-data.net:4317 (ou 443 para HTTPS)'],
                            ['id' => 2, 'text' => 'Adicione header de auth: OTEL_EXPORTER_OTLP_HEADERS=api-key=SEU_NEW_RELIC_LICENSE_KEY'],
                            ['id' => 3, 'text' => 'Configure resource attributes: OTEL_RESOURCE_ATTRIBUTES=service.name=minha-app,environment=production'],
                            ['id' => 4, 'text' => 'Teste a conexão com o OTel Collector ou direto: faça requisições e verifique em NR > APM > Services > OpenTelemetry'],
                            ['id' => 5, 'text' => 'Correlacione um trace OTel com um trace do agente PHP nativo — o NR une automaticamente por trace ID'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Correlacionando Logs com Traces',
                        'type'        => 'lab',
                        'description' => 'Logs sem contexto de trace são difíceis de correlacionar com o problema que causaram. Quando você injeta trace_id e span_id nos logs, o New Relic (e qualquer backend OTel) conecta automaticamente cada linha de log ao trace correspondente. Vamos configurar Monolog para injetar automaticamente o contexto OTel em cada log entry.',
                        'resources'   => [
                            ['label' => 'Logs in Context — OTel', 'url' => 'https://opentelemetry.io/docs/specs/otel/logs/'],
                            ['label' => 'NR Logs in Context for PHP', 'url' => 'https://docs.newrelic.com/docs/logs/logs-context/configure-logs-context-php/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Adicione o bridge OTel-Monolog: `composer require open-telemetry/opentelemetry-logger-monolog`'],
                            ['id' => 2, 'text' => 'Configure o Processor que injeta trace_id e span_id automaticamente em cada log record'],
                            ['id' => 3, 'text' => 'Use o formatter JSON no channel de produção'],
                            ['id' => 4, 'text' => 'Faça uma requisição que gera logs e um erro — veja no New Relic o log vinculado ao trace'],
                            ['id' => 5, 'text' => 'Navegue: New Relic Trace > span com erro > "See logs" — deve abrir os logs filtrados por trace_id'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Challenge: Instrumente essa API de Ponta a Ponta',
                        'type'        => 'challenge',
                        'description' => 'Uma API de processamento de pedidos não tem nenhuma instrumentação. Você precisa adicionar OTel do zero: auto-instrumentation, spans customizados nos serviços de negócio, correlação de logs, e exportação para New Relic. O objetivo é que qualquer erro em produção seja rastreável de ponta a ponta em menos de 2 minutos.',
                        'resources'   => [
                            ['label' => 'OTel PHP complete guide', 'url' => 'https://opentelemetry.io/docs/languages/php/getting-started/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Clone o repositório da API sem instrumentação (link no material do challenge)'],
                            ['id' => 2, 'text' => 'Instale e configure o SDK OTel com auto-instrumentation para Laravel'],
                            ['id' => 3, 'text' => 'Adicione spans manuais nos serviços: OrderService, PaymentService, NotificationService'],
                            ['id' => 4, 'text' => 'Configure log correlation com trace_id/span_id via Monolog processor'],
                            ['id' => 5, 'text' => 'Configure exportação para New Relic via OTLP'],
                            ['id' => 6, 'text' => 'Simule um erro no PaymentService e demonstre: encontrar o erro no NR Errors Inbox, abrir o trace, navegar para os logs correlacionados — tudo em menos de 2 minutos'],
                            ['id' => 7, 'text' => 'Documente o setup em um README.md de instrumentação'],
                        ],
                        'challenge_prompt' => 'Você acabou de entrar em uma empresa que nunca instrumentou as aplicações. A primeira tarefa do seu tech lead: "instrumenta essa API antes do lançamento de amanhã — se tiver problema em produção, preciso saber em segundos, não horas". Mostre o resultado.',
                        'lab_url'          => null,
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // PATH 5 — Observabilidade Full Stack
            // ─────────────────────────────────────────────────────────────
            [
                'name'        => 'Observabilidade Full Stack',
                'description' => 'A visão completa: do clique do usuário no browser até a query no banco de dados. Aprenda a instrumentar o frontend com o Browser Agent, correlacionar traces de frontend e backend, criar dashboards customizados para seu produto, e responder a incidents em minutos com evidências concretas.',
                'steps'       => [
                    [
                        'title'       => 'Browser Monitoring: Instalando o Agente JS da New Relic',
                        'type'        => 'lab',
                        'description' => 'O backend pode estar perfeito e o usuário ainda ter uma experiência terrível — por problemas de rede, JavaScript pesado, ou APIs lentas. New Relic Browser Monitor captura performance real do usuário (RUM), Core Web Vitals, erros JS, e AJAX calls com tempos reais de cada país e dispositivo.',
                        'resources'   => [
                            ['label' => 'New Relic Browser Agent', 'url' => 'https://docs.newrelic.com/docs/browser/browser-monitoring/getting-started/introduction-browser-monitoring/'],
                            ['label' => 'Core Web Vitals', 'url' => 'https://web.dev/vitals/'],
                            ['label' => 'Browser monitoring copy/paste install', 'url' => 'https://docs.newrelic.com/docs/browser/browser-monitoring/installation/install-browser-monitoring-agent/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'No New Relic, navegue para Browser > Add data e escolha o método copy/paste'],
                            ['id' => 2, 'text' => 'Adicione o snippet JS no <head> do seu layout Nuxt via useHead() ou plugin'],
                            ['id' => 3, 'text' => 'Configure o applicationID correto para o ambiente (dev vs prod)'],
                            ['id' => 4, 'text' => 'Acesse sua aplicação em diferentes páginas e verifique em NR > Browser que pageviews aparecem'],
                            ['id' => 5, 'text' => 'Navegue para Core Web Vitals e analise LCP, FID/INP e CLS da sua aplicação'],
                            ['id' => 6, 'text' => 'Identifique o recurso mais pesado na aba "Session traces"'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Core Web Vitals e Performance Frontend',
                        'type'        => 'reading',
                        'description' => 'Google usa Core Web Vitals no ranking de busca, e os usuários abandonam páginas que demoram mais de 3 segundos. LCP (Largest Contentful Paint), FID/INP (Interaction to Next Paint) e CLS (Cumulative Layout Shift) são as três métricas que medem experiência real. Vamos entender o que cada uma mede, quais são os limites aceitáveis, e quais otimizações têm maior impacto.',
                        'resources'   => [
                            ['label' => 'Web Vitals — web.dev', 'url' => 'https://web.dev/vitals/'],
                            ['label' => 'LCP optimization guide', 'url' => 'https://web.dev/optimize-lcp/'],
                            ['label' => 'New Relic Core Web Vitals', 'url' => 'https://docs.newrelic.com/docs/browser/browser-monitoring/page-load-timing-resources/pageviewtiming-async-or-dynamic-page-details/'],
                        ],
                        'instructions'     => null,
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Distributed Tracing: Do Clique ao Banco de Dados',
                        'type'        => 'reading',
                        'description' => 'Em um sistema moderno, uma única ação do usuário dispara requests para múltiplos serviços. Sem distributed tracing, você vê apenas o sintoma (lentidão ou erro) sem saber onde ocorreu. Vamos entender como o trace context se propaga via HTTP headers (W3C TraceContext), como o New Relic une automaticamente browser + backend, e como interpretar um waterfall chart de trace.',
                        'resources'   => [
                            ['label' => 'New Relic Distributed Tracing', 'url' => 'https://docs.newrelic.com/docs/distributed-tracing/concepts/introduction-distributed-tracing/'],
                            ['label' => 'W3C TraceContext propagation', 'url' => 'https://www.w3.org/TR/trace-context/'],
                            ['label' => 'Tracing vs Logging vs Metrics', 'url' => 'https://peter.bourgon.org/blog/2017/02/21/metrics-tracing-and-logging.html'],
                        ],
                        'instructions'     => null,
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Lab: Logs em Contexto no New Relic',
                        'type'        => 'lab',
                        'description' => 'Logs em Contexto é a funcionalidade que conecta um log entry diretamente ao trace e span que o gerou — sem busca manual por timestamps ou user IDs. Vamos configurar o PHP agent para injetar automaticamente linking metadata nos logs, e usar o NR Logs para navegar de um erro em produção para o trace completo em um clique.',
                        'resources'   => [
                            ['label' => 'PHP Logs in Context', 'url' => 'https://docs.newrelic.com/docs/logs/logs-context/configure-logs-context-php/'],
                            ['label' => 'NR Log Management', 'url' => 'https://docs.newrelic.com/docs/logs/get-started/get-started-log-management/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Instale o monolog handler do New Relic: `composer require newrelic/monolog-enricher`'],
                            ['id' => 2, 'text' => 'Configure o Processor e Handler no config/logging.php no channel de produção'],
                            ['id' => 3, 'text' => 'Garanta que o log format seja JSON com os campos: trace.id, span.id, entity.guid'],
                            ['id' => 4, 'text' => 'Configure o NR agent para fazer forwarding de logs: newrelic.application_logging.forwarding.enabled=true'],
                            ['id' => 5, 'text' => 'Simule um erro: abra NR > APM > Errors > clique no erro > "See logs" — deve abrir filtrado por trace_id'],
                            ['id' => 6, 'text' => 'Navegue o caminho inverso: New Relic Logs > filtro por trace.id= > abre o trace associado'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Criando Dashboards Customizados com NRQL',
                        'type'        => 'lab',
                        'description' => 'Dashboards são a diferença entre reagir a incidentes e prever problemas. NRQL (New Relic Query Language) é SQL para telemetria — permite agregar qualquer dado coletado em visualizações customizadas para o seu produto específico. Vamos criar um dashboard de SLIs/SLOs com as métricas que importam para o seu negócio.',
                        'resources'   => [
                            ['label' => 'NRQL Reference', 'url' => 'https://docs.newrelic.com/docs/nrql/nrql-syntax-clauses-functions/'],
                            ['label' => 'Dashboard best practices', 'url' => 'https://docs.newrelic.com/docs/query-your-data/explore-query-data/dashboards/introduction-dashboards/'],
                            ['label' => 'SLI/SLO with New Relic', 'url' => 'https://docs.newrelic.com/docs/service-level-management/intro-slm/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Crie um dashboard novo: New Relic > Dashboards > Create dashboard'],
                            ['id' => 2, 'text' => 'Widget 1 — Availability: `SELECT percentage(count(*), WHERE error IS false) FROM Transaction SINCE 24 hours ago`'],
                            ['id' => 3, 'text' => 'Widget 2 — p95 Latency: `SELECT percentile(duration, 95) FROM Transaction FACET name SINCE 1 hour ago TIMESERIES`'],
                            ['id' => 4, 'text' => 'Widget 3 — Error Rate: `SELECT percentage(count(*), WHERE error IS true) FROM Transaction TIMESERIES AUTO`'],
                            ['id' => 5, 'text' => 'Widget 4 — Top Slow Queries: `SELECT average(duration) FROM DatabaseOperation FACET statement LIMIT 10`'],
                            ['id' => 6, 'text' => 'Configure alertas vinculados ao dashboard: notify se availability < 99.5% por 10 minutos'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Challenge: Rastreie um Bug do Frontend ao Banco',
                        'type'        => 'challenge',
                        'description' => 'Um usuário reportou: "Cliquei em Confirmar Pedido, a tela girou por 10 segundos e sumiu. Nada aconteceu." Você tem New Relic Browser + APM + Logs configurados. Sem reproduzir o bug — use apenas a telemetria coletada durante o report do usuário para encontrar o que houve.',
                        'resources'   => [
                            ['label' => 'New Relic Session Replay', 'url' => 'https://docs.newrelic.com/docs/browser/browser-monitoring/browser-pro-features/session-replay/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Acesse NR Browser > Session Traces e filtre pelo user_id do usuário que reportou'],
                            ['id' => 2, 'text' => 'Identifique o momento exato do "Confirmar Pedido" via AJAX call no Session Trace'],
                            ['id' => 3, 'text' => 'Copie o trace_id dessa requisição e abra em NR Distributed Tracing'],
                            ['id' => 4, 'text' => 'No trace, identifique qual span falhou (status ERROR) e em qual serviço'],
                            ['id' => 5, 'text' => 'Navegue para Logs filtrado por esse trace_id — qual a mensagem de erro completa?'],
                            ['id' => 6, 'text' => 'Localize no código o ponto exato de falha e proponha o fix'],
                            ['id' => 7, 'text' => 'Escreva um incident report de 1 página: timeline, causa raiz, impacto, correção, prevenção futura'],
                        ],
                        'challenge_prompt' => 'Um cliente enterprise está ameaçando cancelar o contrato. O bug aconteceu 3 vezes durante o demo com o time deles. Você não consegue reproduzir localmente. Mas você tem observabilidade completa do evento real. Encontre, explique e corrija antes da reunião de follow-up amanhã às 9h.',
                        'lab_url'          => null,
                    ],
                    [
                        'title'       => 'Challenge Final: Simule um Incident Response Completo',
                        'type'        => 'challenge',
                        'description' => 'Um incident de nível P1 foi declarado. Error rate subiu de 0.1% para 23% em 4 minutos. Os alertas dispararam. Você é o incident commander. Conduz a investigação, coordena a comunicação, e entrega o post-mortem. Este é o treinamento mais próximo da realidade de um SRE ou senior developer em produção.',
                        'resources'   => [
                            ['label' => 'Google SRE Incident Management', 'url' => 'https://sre.google/sre-book/managing-incidents/'],
                            ['label' => 'Post-mortem Culture', 'url' => 'https://sre.google/sre-book/postmortem-culture/'],
                            ['label' => 'Incident Response Checklist', 'url' => 'https://github.com/dastergon/awesome-sre#incident-management'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Passo 1 — Triage (primeiros 5 min): abra NR, identifique quais endpoints estão errando e qual o impacto em usuários'],
                            ['id' => 2, 'text' => 'Passo 2 — Hipótese (5-10 min): correlacione o início do spike com deploys recentes (NR Change Tracking)'],
                            ['id' => 3, 'text' => 'Passo 3 — Contenção (10-15 min): se o problema for um deploy, execute rollback; se for dados, aplique fix hotpath'],
                            ['id' => 4, 'text' => 'Passo 4 — Validação (15-20 min): confirme no NR que error rate voltou ao normal e métricas estabilizaram'],
                            ['id' => 5, 'text' => 'Passo 5 — Post-mortem (após resolução): escreva o post-mortem com: timeline, causa raiz, impacto, ações corretivas e preventivas'],
                            ['id' => 6, 'text' => 'Entregue o post-mortem no formato blameless — foco no sistema, não nas pessoas'],
                        ],
                        'challenge_prompt' => 'São 14h37 de uma sexta-feira. O Slack explodiu: "PROD DOWN - checkout com 500". O CEO está viajando mas mandou mensagem no grupo. A equipe de suporte reporta 847 tickets abertos nos últimos 4 minutos. Você tem o New Relic aberto. Comece.',
                        'lab_url'          => null,
                    ],
                ],
            ],
        ];
    }
}
