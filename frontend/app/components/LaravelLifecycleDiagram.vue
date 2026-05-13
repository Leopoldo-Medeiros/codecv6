<template>
  <div class="laravel-lifecycle-wrapper overflow-x-auto">
    <div
      class="relative"
      style="width: 1640px; font-family: 'Inter', system-ui, sans-serif; color: #e6edf7;"
    >

      <!-- ── Header ──────────────────────────────────────────────────── -->
      <header class="px-16 pt-14 pb-6">
        <div class="flex items-end justify-between">
          <div class="max-w-3xl">
            <div class="flex items-center gap-3 mb-4">
              <span class="w-7 h-7 rounded-md bg-emerald-500/15 border border-emerald-500/40 grid place-items-center">
                <span class="w-2 h-2 rounded-full bg-emerald-400" />
              </span>
              <span class="font-mono text-[11px] tracking-[0.22em] uppercase text-slate2-300">System&nbsp;Design&nbsp;/&nbsp;Framework Internals · 014</span>
            </div>
            <h1 class="text-[44px] leading-[1.05] font-extrabold tracking-tight text-white">
              The Lifecycle of a Laravel Request
              <span class="text-emerald-400"> · </span>
              <span class="text-amber-300">with Observability Injection</span>
            </h1>
            <p class="mt-5 text-[15px] leading-relaxed text-slate2-200 max-w-2xl">
              Every HTTP request that reaches a Laravel app traverses a deterministic pipeline — from
              <span class="font-mono text-emerald-300">public/index.php</span> down to a
              <span class="font-mono text-emerald-300">Response</span>. Below: each stage, what runs there, and where
              tracing, structured logs, metrics, and exception capture are injected.
            </p>
          </div>
          <div class="flex flex-col items-end gap-3">
            <div class="flex flex-col items-end gap-1 text-[12px] text-slate2-300 font-mono">
              <span class="whitespace-nowrap"><span class="text-slate2-500">Runtime&nbsp;·&nbsp;</span>PHP-FPM&nbsp;8.4</span>
              <span class="whitespace-nowrap"><span class="text-slate2-500">Framework&nbsp;·&nbsp;</span>Laravel&nbsp;12.x</span>
              <span class="whitespace-nowrap"><span class="text-slate2-500">Trace API&nbsp;·&nbsp;</span>OpenTelemetry</span>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border border-line bg-ink-800/60">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400" />
              <span class="font-mono text-[11px] text-slate2-200">request path</span>
              <span class="mx-2 w-px h-3 bg-line" />
              <span class="w-1.5 h-1.5 rounded-full bg-amber-400" />
              <span class="font-mono text-[11px] text-slate2-200">telemetry tap</span>
              <span class="mx-2 w-px h-3 bg-line" />
              <span class="w-1.5 h-1.5 rounded-full bg-sky-400" />
              <span class="font-mono text-[11px] text-slate2-200">return path</span>
            </div>
          </div>
        </div>
      </header>

      <!-- ── Main Diagram Canvas ─────────────────────────────────────── -->
      <section
        class="mx-16 rounded-2xl border border-line bg-ink-900 ll-grid-bg relative overflow-hidden"
        style="height: 1340px;"
      >
        <!-- Ambient vignettes -->
        <div
          class="absolute inset-0 pointer-events-none"
          style="background:
            radial-gradient(800px 400px at 12% 8%, rgba(52,211,153,0.08), transparent 60%),
            radial-gradient(700px 380px at 88% 92%, rgba(56,189,248,0.07), transparent 60%),
            radial-gradient(700px 380px at 50% 50%, rgba(251,191,36,0.04), transparent 60%);"
        />

        <!-- ── Observability Rail (top band) ───────────────────────── -->
        <div class="absolute" style="left:48px; top:36px; right:48px; height:148px;">
          <div class="absolute inset-0 rounded-xl border border-amber-500/25 ll-hatch" />
          <div class="absolute -top-3 left-6 px-3 py-1 rounded-md bg-ink-900 border border-amber-500/40 whitespace-nowrap">
            <span class="ll-stage-label text-amber-300">Observability&nbsp;SDK</span>
          </div>
          <div class="absolute inset-0 px-6 pt-7 pb-4 grid grid-cols-4 gap-5">
            <div class="rounded-lg bg-ink-800/80 border border-amber-500/25 px-4 py-3">
              <div class="flex items-center gap-2 mb-1.5">
                <svg width="14" height="14" viewBox="0 0 14 14"><circle cx="7" cy="7" r="5.5" fill="none" stroke="#fbbf24" stroke-width="1.4" /><circle cx="7" cy="7" r="2" fill="#fbbf24" /></svg>
                <div class="font-semibold text-amber-200 text-[13px]">Tracer</div>
                <span class="ml-auto font-mono text-[9.5px] text-slate2-400 whitespace-nowrap">OTel</span>
              </div>
              <div class="text-[11px] text-slate2-300 leading-snug">Wraps each stage in a span. Parent ID propagates via <span class="font-mono text-amber-300/90">traceparent</span> header.</div>
            </div>
            <div class="rounded-lg bg-ink-800/80 border border-amber-500/25 px-4 py-3">
              <div class="flex items-center gap-2 mb-1.5">
                <svg width="14" height="14" viewBox="0 0 14 14"><rect x="2" y="2.5" width="10" height="9" rx="1.2" fill="none" stroke="#fbbf24" stroke-width="1.4" /><line x1="4" y1="5.5" x2="10" y2="5.5" stroke="#fbbf24" stroke-width="1.2" /><line x1="4" y1="7.5" x2="10" y2="7.5" stroke="#fbbf24" stroke-width="1.2" /><line x1="4" y1="9.5" x2="8" y2="9.5" stroke="#fbbf24" stroke-width="1.2" /></svg>
                <div class="font-semibold text-amber-200 text-[13px]">Logger</div>
                <span class="ml-auto font-mono text-[9.5px] text-slate2-400 whitespace-nowrap">Monolog</span>
              </div>
              <div class="text-[11px] text-slate2-300 leading-snug">Auto-binds <span class="font-mono text-amber-300/90">trace_id</span>, <span class="font-mono text-amber-300/90">user_id</span>, <span class="font-mono text-amber-300/90">request_id</span> to log context.</div>
            </div>
            <div class="rounded-lg bg-ink-800/80 border border-amber-500/25 px-4 py-3">
              <div class="flex items-center gap-2 mb-1.5">
                <svg width="14" height="14" viewBox="0 0 14 14"><polyline points="2,11 5,7 8,9 12,3" fill="none" stroke="#fbbf24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /><line x1="2" y1="12" x2="13" y2="12" stroke="#fbbf24" stroke-width="1.2" /></svg>
                <div class="font-semibold text-amber-200 text-[13px]">Metrics</div>
                <span class="ml-auto font-mono text-[9.5px] text-slate2-400 whitespace-nowrap">Prom</span>
              </div>
              <div class="text-[11px] text-slate2-300 leading-snug">Counters / histograms: <span class="font-mono text-amber-300/90">http_requests_total</span>, <span class="font-mono text-amber-300/90">db_query_ms</span>.</div>
            </div>
            <div class="rounded-lg bg-ink-800/80 border border-amber-500/25 px-4 py-3">
              <div class="flex items-center gap-2 mb-1.5">
                <svg width="14" height="14" viewBox="0 0 14 14"><path d="M7 1.5 L13 12 H1 Z" fill="none" stroke="#fbbf24" stroke-width="1.4" stroke-linejoin="round" /><line x1="7" y1="5.5" x2="7" y2="8.5" stroke="#fbbf24" stroke-width="1.4" /><circle cx="7" cy="10.2" r="0.7" fill="#fbbf24" /></svg>
                <div class="font-semibold text-amber-200 text-[13px]">Errors</div>
                <span class="ml-auto font-mono text-[9.5px] text-slate2-400 whitespace-nowrap">Sentry</span>
              </div>
              <div class="text-[11px] text-slate2-300 leading-snug">Hooks <span class="font-mono text-amber-300/90">Handler::report()</span>; attaches breadcrumbs from open spans.</div>
            </div>
          </div>
        </div>

        <!-- ── Client (left) ───────────────────────────────────────── -->
        <div class="absolute" style="left:64px; top:230px; width:170px;">
          <div class="rounded-xl bg-ink-800 border border-line p-4">
            <div class="flex items-center gap-2 mb-2">
              <svg width="16" height="16" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6.5" fill="none" stroke="#34d399" stroke-width="1.4" /><ellipse cx="8" cy="8" rx="2.6" ry="6.5" fill="none" stroke="#34d399" stroke-width="1.2" /><line x1="1.5" y1="8" x2="14.5" y2="8" stroke="#34d399" stroke-width="1.2" /></svg>
              <span class="ll-stage-label text-emerald-300">Client</span>
            </div>
            <div class="text-[14px] font-semibold text-white leading-tight">HTTP Request</div>
            <div class="mt-1 text-[11.5px] text-slate2-300 leading-snug">Browser, mobile, or upstream service issues a request.</div>
            <div class="mt-3 rounded-md bg-ink-900 border border-line/70 px-2.5 py-2 font-mono text-[10.5px] leading-snug text-slate2-200">
              <div><span class="text-emerald-400">GET</span> /api/orders/42</div>
              <div class="text-slate2-400">traceparent: 00-a1f2…-b3-01</div>
              <div class="text-slate2-400">x-request-id: 7e3c…9a</div>
            </div>
          </div>
          <div class="mt-2 text-center">
            <span class="ll-num-chip text-slate2-400">CLIENT</span>
          </div>
        </div>

        <!-- ── Main Pipeline (center) ──────────────────────────────── -->
        <div class="absolute" style="left:264px; top:206px; right:230px; height:780px;">
          <div class="absolute inset-0 rounded-2xl border border-line bg-ink-900/40" />
          <div class="absolute -top-3 left-6 px-3 py-1 rounded-md bg-ink-900 border border-line">
            <span class="font-mono text-[10.5px] tracking-[0.2em] uppercase text-emerald-300">Laravel&nbsp;Request&nbsp;Pipeline</span>
          </div>

          <!-- Row 1 — entry / bootstrap -->
          <div class="absolute" style="left:24px; top:40px; right:24px; height:166px;">
            <div class="grid grid-cols-3 gap-7 h-full">
              <div class="relative rounded-xl bg-ink-800/90 border border-line p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-1">
                  <span class="ll-num-chip px-1.5 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">01</span>
                  <span class="ll-stage-label text-slate2-400">Entry Point</span>
                </div>
                <div class="font-semibold text-[14px] text-white leading-tight">public/index.php</div>
                <div class="font-mono text-[10.5px] text-slate2-400 mt-0.5">FPM hands off here</div>
                <div class="mt-auto pt-2 font-mono text-[10.5px] leading-snug text-slate2-200">
                  require __DIR__.<span class="text-emerald-300">'/../vendor/autoload.php'</span>;<br>
                  $app = require_once <span class="text-emerald-300">'../bootstrap/app.php'</span>;
                </div>
              </div>
              <div class="relative rounded-xl bg-ink-800/90 border border-line p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-1">
                  <span class="ll-num-chip px-1.5 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">02</span>
                  <span class="ll-stage-label text-slate2-400">Bootstrap</span>
                </div>
                <div class="font-semibold text-[14px] text-white leading-tight">bootstrap/app.php</div>
                <div class="font-mono text-[10.5px] text-slate2-400 mt-0.5">Builds the Application</div>
                <div class="mt-auto pt-2 text-[11.5px] text-slate2-300 leading-snug">
                  Instantiates <span class="font-mono text-emerald-300">Illuminate\Foundation\Application</span> — the IoC container — and binds the three core kernels.
                </div>
              </div>
              <div class="relative rounded-xl bg-ink-800/90 border border-line p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-1">
                  <span class="ll-num-chip px-1.5 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">03</span>
                  <span class="ll-stage-label text-slate2-400">Kernel</span>
                </div>
                <div class="font-semibold text-[14px] text-white leading-tight">HTTP Kernel<span class="text-slate2-400 font-normal text-[12px]"> · handle()</span></div>
                <div class="font-mono text-[10.5px] text-slate2-400 mt-0.5">Foundation\Http\Kernel</div>
                <div class="mt-auto pt-2 text-[11.5px] text-slate2-300 leading-snug">
                  Captures the <span class="font-mono text-emerald-300">Request</span>, runs <span class="font-mono text-emerald-300">bootstrappers[]</span>, sends it through middleware → router.
                </div>
              </div>
            </div>
          </div>

          <!-- Row 2 — providers / middleware -->
          <div class="absolute" style="left:24px; top:226px; right:24px; height:166px;">
            <div class="grid grid-cols-3 gap-7 h-full">
              <div class="relative rounded-xl bg-sky-500/[0.08] border border-sky-500/40 p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-1">
                  <span class="ll-num-chip px-1.5 py-0.5 rounded bg-sky-500/20 text-sky-300 border border-sky-500/30">04</span>
                  <span class="ll-stage-label text-sky-300">Service Providers</span>
                </div>
                <div class="font-semibold text-[14px] text-white leading-tight">register() &nbsp;→&nbsp; boot()</div>
                <div class="font-mono text-[10.5px] text-slate2-400 mt-0.5">config/app.php providers[]</div>
                <div class="mt-auto pt-2 text-[11.5px] text-slate2-300 leading-snug">
                  Two passes. <span class="text-sky-300">register</span> binds singletons into the container; <span class="text-sky-300">boot</span> uses them. Order matters.
                </div>
              </div>
              <div class="relative rounded-xl bg-purple-500/[0.08] border border-purple-500/40 p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-1">
                  <span class="ll-num-chip px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/30">05</span>
                  <span class="ll-stage-label text-purple-300">IoC Container</span>
                </div>
                <div class="font-semibold text-[14px] text-white leading-tight">Service Resolution</div>
                <div class="font-mono text-[10.5px] text-slate2-400 mt-0.5">make() · auto-wire</div>
                <div class="mt-auto pt-2 text-[11.5px] text-slate2-300 leading-snug">
                  Reflection-based DI resolves typed constructor params on demand; singletons cached for the lifetime of the request.
                </div>
              </div>
              <div class="relative rounded-xl bg-ink-800/90 border border-line p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-1">
                  <span class="ll-num-chip px-1.5 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">06</span>
                  <span class="ll-stage-label text-slate2-400">Middleware Pipeline · in</span>
                </div>
                <div class="font-semibold text-[14px] text-white leading-tight">Global Middleware</div>
                <div class="font-mono text-[10.5px] text-slate2-400 mt-0.5">TrustProxies · CORS · CSRF</div>
                <div class="mt-auto pt-2 text-[11.5px] text-slate2-300 leading-snug">
                  Onion model. Each layer may short-circuit (return early) or pass <span class="font-mono text-emerald-300">$next($request)</span> deeper.
                </div>
              </div>
            </div>
          </div>

          <!-- Row 3 — router / route middleware / controller -->
          <div class="absolute" style="left:24px; top:412px; right:24px; height:166px;">
            <div class="grid grid-cols-3 gap-7 h-full">
              <div class="relative rounded-xl bg-ink-800/90 border border-line p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-1">
                  <span class="ll-num-chip px-1.5 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">07</span>
                  <span class="ll-stage-label text-slate2-400">Routing</span>
                </div>
                <div class="font-semibold text-[14px] text-white leading-tight">Router::dispatch()</div>
                <div class="font-mono text-[10.5px] text-slate2-400 mt-0.5">routes/{web,api}.php</div>
                <div class="mt-auto pt-2 text-[11.5px] text-slate2-300 leading-snug">
                  Matches verb + URI to a <span class="font-mono text-emerald-300">Route</span>, extracts parameters, resolves model bindings.
                </div>
              </div>
              <div class="relative rounded-xl bg-ink-800/90 border border-line p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-1">
                  <span class="ll-num-chip px-1.5 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">08</span>
                  <span class="ll-stage-label text-slate2-400">Route Middleware</span>
                </div>
                <div class="font-semibold text-[14px] text-white leading-tight">auth · throttle · can</div>
                <div class="font-mono text-[10.5px] text-slate2-400 mt-0.5">per-route guards</div>
                <div class="mt-auto pt-2 text-[11.5px] text-slate2-300 leading-snug">
                  Group- and route-scoped middleware run after the global stack — closer to the controller.
                </div>
              </div>
              <div class="relative rounded-xl bg-emerald-500/[0.08] border border-emerald-500/40 p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-1">
                  <span class="ll-num-chip px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">09</span>
                  <span class="ll-stage-label text-emerald-300">Application Code</span>
                </div>
                <div class="font-semibold text-[14px] text-white leading-tight">Controller&nbsp;Action</div>
                <div class="font-mono text-[10.5px] text-slate2-400 mt-0.5">OrderController@show</div>
                <div class="mt-auto pt-2 text-[11.5px] text-slate2-300 leading-snug">
                  Your business logic. Returns a <span class="font-mono text-emerald-300">View</span>, array, model, or <span class="font-mono text-emerald-300">Response</span>.
                </div>
              </div>
            </div>
          </div>

          <!-- Row 4 — model / view / response building -->
          <div class="absolute" style="left:24px; top:598px; right:24px; height:166px;">
            <div class="grid grid-cols-3 gap-7 h-full">
              <div class="relative rounded-xl bg-ink-800/90 border border-line p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-1">
                  <span class="ll-num-chip px-1.5 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">10</span>
                  <span class="ll-stage-label text-slate2-400">Data Layer</span>
                </div>
                <div class="font-semibold text-[14px] text-white leading-tight">Eloquent&nbsp;/&nbsp;Query&nbsp;Builder</div>
                <div class="font-mono text-[10.5px] text-slate2-400 mt-0.5">PDO · MySQL · Postgres</div>
                <div class="mt-auto pt-2 text-[11.5px] text-slate2-300 leading-snug">
                  Models hydrate from rows. Cache + queue dispatchers fire here too. N+1 risk lives in this box.
                </div>
              </div>
              <div class="relative rounded-xl bg-ink-800/90 border border-line p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-1">
                  <span class="ll-num-chip px-1.5 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">11</span>
                  <span class="ll-stage-label text-slate2-400">Render</span>
                </div>
                <div class="font-semibold text-[14px] text-white leading-tight">Blade&nbsp;/&nbsp;API&nbsp;Resource</div>
                <div class="font-mono text-[10.5px] text-slate2-400 mt-0.5">View::render() · toArray()</div>
                <div class="mt-auto pt-2 text-[11.5px] text-slate2-300 leading-snug">
                  Compiles to PHP, then HTML. For APIs, <span class="font-mono text-emerald-300">JsonResource</span> shapes the payload.
                </div>
              </div>
              <div class="relative rounded-xl bg-ink-800/90 border border-line p-4 flex flex-col">
                <div class="flex items-center gap-2 mb-1">
                  <span class="ll-num-chip px-1.5 py-0.5 rounded bg-sky-500/20 text-sky-300 border border-sky-500/30">12</span>
                  <span class="ll-stage-label text-sky-300">Middleware · out</span>
                </div>
                <div class="font-semibold text-[14px] text-white leading-tight">Response unwinds the onion</div>
                <div class="font-mono text-[10.5px] text-slate2-400 mt-0.5">cookies · headers · cache</div>
                <div class="mt-auto pt-2 text-[11.5px] text-slate2-300 leading-snug">
                  Each middleware that called <span class="font-mono text-emerald-300">$next()</span> now mutates the outgoing <span class="font-mono text-emerald-300">Response</span> on the way back.
                </div>
              </div>
            </div>
          </div>

          <!-- Pipeline arrows (forward + return path) -->
          <svg class="absolute inset-0 pointer-events-none" width="100%" height="100%">
            <defs>
              <marker id="ll-arrow-mint" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto">
                <path d="M 0 0 L 10 5 L 0 10 z" fill="#34d399" />
              </marker>
              <marker id="ll-arrow-sky" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto">
                <path d="M 0 0 L 10 5 L 0 10 z" fill="#38bdf8" />
              </marker>
              <marker id="ll-arrow-amber" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="6" markerHeight="6" orient="auto">
                <path d="M 0 0 L 10 5 L 0 10 z" fill="#fbbf24" />
              </marker>
              <marker id="ll-arrow-purple" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto">
                <path d="M 0 0 L 10 5 L 0 10 z" fill="#a855f7" />
              </marker>
            </defs>
            <!-- Row 1: left → right -->
            <g stroke="#34d399" stroke-width="1.6" fill="none" marker-end="url(#ll-arrow-mint)">
              <line x1="307" y1="123" x2="346" y2="123" />
              <line x1="694" y1="123" x2="733" y2="123" />
            </g>
            <!-- Row 1 → Row 2 (down right) -->
            <g stroke="#34d399" stroke-width="1.6" fill="none" marker-end="url(#ll-arrow-mint)">
              <line x1="980" y1="206" x2="980" y2="244" />
            </g>
            <!-- Row 2: right → left -->
            <g stroke="#34d399" stroke-width="1.6" fill="none" marker-end="url(#ll-arrow-mint)">
              <line x1="694" y1="309" x2="655" y2="309" />
              <line x1="307" y1="309" x2="268" y2="309" />
            </g>
            <!-- Row 2 → Row 3 (down left) -->
            <g stroke="#34d399" stroke-width="1.6" fill="none" marker-end="url(#ll-arrow-mint)">
              <line x1="161" y1="392" x2="161" y2="430" />
            </g>
            <!-- Row 3: left → right -->
            <g stroke="#34d399" stroke-width="1.6" fill="none" marker-end="url(#ll-arrow-mint)">
              <line x1="307" y1="495" x2="346" y2="495" />
              <line x1="694" y1="495" x2="733" y2="495" />
            </g>
            <!-- Row 3 → Row 4 (transition to sky = return path) -->
            <g stroke="#38bdf8" stroke-width="1.6" fill="none" marker-end="url(#ll-arrow-sky)">
              <line x1="980" y1="578" x2="980" y2="616" />
            </g>
            <!-- Row 4: right → left (response unwinds) -->
            <g stroke="#38bdf8" stroke-width="1.6" fill="none" marker-end="url(#ll-arrow-sky)">
              <line x1="694" y1="681" x2="655" y2="681" />
              <line x1="307" y1="681" x2="268" y2="681" />
            </g>
          </svg>
        </div>

        <!-- ── Response (right) ────────────────────────────────────── -->
        <div class="absolute" style="right:64px; top:548px; width:170px;">
          <div class="rounded-xl bg-ink-800 border border-line p-4">
            <div class="flex items-center gap-2 mb-2">
              <svg width="16" height="16" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="10" rx="1.4" fill="none" stroke="#38bdf8" stroke-width="1.4" /><line x1="2" y1="6" x2="14" y2="6" stroke="#38bdf8" stroke-width="1.2" /><circle cx="4" cy="4.5" r="0.5" fill="#38bdf8" /><circle cx="5.5" cy="4.5" r="0.5" fill="#38bdf8" /></svg>
              <span class="ll-stage-label text-sky-300">Response</span>
            </div>
            <div class="text-[14px] font-semibold text-white leading-tight">HTTP Response</div>
            <div class="mt-1 text-[11.5px] text-slate2-300 leading-snug">Sent back to client. Then <span class="font-mono text-sky-300">terminate()</span> fires deferred work.</div>
            <div class="mt-3 rounded-md bg-ink-900 border border-line/70 px-2.5 py-2 font-mono text-[10.5px] leading-snug text-slate2-200">
              <div><span class="text-sky-300">200 OK</span> · 184ms</div>
              <div class="text-slate2-400">content-type: application/json</div>
              <div class="text-slate2-400">server-timing: db;dur=42</div>
            </div>
          </div>
          <div class="mt-2 text-center">
            <span class="ll-num-chip text-slate2-400">CLIENT ←</span>
          </div>
        </div>

        <!-- ── Entry / exit arrows (section level) ────────────────── -->
        <svg class="absolute inset-0 pointer-events-none" width="100%" height="100%">
          <g stroke="#34d399" stroke-width="2" fill="none" marker-end="url(#ll-arrow-mint)">
            <path d="M 234 290 L 282 290" />
          </g>
          <g stroke="#38bdf8" stroke-width="2" fill="none" marker-end="url(#ll-arrow-sky)">
            <path d="M 1410 681 L 1402 681 L 1402 612 L 1410 612" />
          </g>
        </svg>

        <!-- ── Telemetry taps (dashed lines from rail to pipeline) ── -->
        <svg class="absolute inset-0 pointer-events-none ll-dashed-arrow" width="100%" height="100%">
          <g stroke="#fbbf24" stroke-width="1.2" fill="none" stroke-opacity="0.7">
            <path d="M 230 184 L 230 220 L 700 220 L 700 240" marker-end="url(#ll-arrow-amber)" />
            <path d="M 600 184 L 600 422 L 420 422" marker-end="url(#ll-arrow-amber)" />
            <path d="M 970 184 L 970 608 L 1120 608" marker-end="url(#ll-arrow-amber)" />
            <path d="M 1330 184 L 1330 794 L 880 794" marker-end="url(#ll-arrow-amber)" />
            <path d="M 280 184 L 280 994 L 1300 994" marker-end="url(#ll-arrow-amber)" />
          </g>
          <g fill="#fbbf24">
            <circle cx="700" cy="240" r="3" />
            <circle cx="420" cy="422" r="3" />
            <circle cx="1120" cy="608" r="3" />
            <circle cx="880" cy="794" r="3" />
            <circle cx="1300" cy="994" r="3" />
          </g>
        </svg>

        <!-- ── Telemetry Sinks (bottom) ───────────────────────────── -->
        <div class="absolute" style="left:48px; top:1070px; right:48px; height:240px;">
          <div class="absolute -top-3 left-6 px-3 py-1 rounded-md bg-ink-900 border border-line">
            <span class="font-mono text-[10.5px] tracking-[0.2em] uppercase text-slate2-200">Telemetry&nbsp;Egress&nbsp;·&nbsp;async&nbsp;/&nbsp;deferred</span>
          </div>
          <div class="absolute inset-0 rounded-xl border border-line bg-ink-900/40 px-6 pt-7 pb-5 grid grid-cols-12 gap-5">
            <div class="col-span-3 rounded-lg bg-purple-500/[0.08] border border-purple-500/35 p-4">
              <div class="flex items-center gap-2 mb-1">
                <span class="ll-num-chip px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/35">13</span>
                <span class="ll-stage-label text-purple-300">Kernel · terminate()</span>
              </div>
              <div class="font-semibold text-[13.5px] text-white leading-tight">Post-response work</div>
              <div class="mt-1.5 text-[11.5px] text-slate2-300 leading-snug">
                Response is already on the wire. Terminable middleware flushes spans, rotates session, dispatches queued jobs.
              </div>
              <div class="mt-2 font-mono text-[10.5px] text-purple-300/90">FlushTelemetry · SessionEnd</div>
            </div>
            <div class="col-span-3 rounded-lg bg-ink-800/80 border border-line p-4">
              <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-amber-400" />
                <span class="ll-stage-label text-amber-300">Collector</span>
              </div>
              <div class="font-semibold text-[13.5px] text-white leading-tight">OTel Collector</div>
              <div class="mt-1.5 text-[11.5px] text-slate2-300 leading-snug">Batches spans, logs, metrics. Sampling + redaction here, not in the app.</div>
              <div class="mt-2 font-mono text-[10.5px] text-slate2-400">gRPC · 4317</div>
            </div>
            <div class="col-span-6 rounded-lg bg-ink-800/60 border border-line p-4">
              <div class="flex items-center gap-2 mb-2">
                <span class="ll-stage-label text-slate2-300">Storage&nbsp;·&nbsp;Query&nbsp;·&nbsp;Alert</span>
              </div>
              <div class="grid grid-cols-4 gap-3">
                <div class="rounded-md bg-ink-900 border border-line/70 px-3 py-2.5">
                  <div class="text-[12px] font-semibold text-amber-200">Tempo&nbsp;/&nbsp;Jaeger</div>
                  <div class="font-mono text-[10px] text-slate2-400 mt-0.5">trace store</div>
                </div>
                <div class="rounded-md bg-ink-900 border border-line/70 px-3 py-2.5">
                  <div class="text-[12px] font-semibold text-amber-200">Loki&nbsp;/&nbsp;ELK</div>
                  <div class="font-mono text-[10px] text-slate2-400 mt-0.5">log store</div>
                </div>
                <div class="rounded-md bg-ink-900 border border-line/70 px-3 py-2.5">
                  <div class="text-[12px] font-semibold text-amber-200">Prometheus</div>
                  <div class="font-mono text-[10px] text-slate2-400 mt-0.5">metrics&nbsp;TSDB</div>
                </div>
                <div class="rounded-md bg-ink-900 border border-line/70 px-3 py-2.5">
                  <div class="text-[12px] font-semibold text-amber-200">Sentry-like</div>
                  <div class="font-mono text-[10px] text-slate2-400 mt-0.5">error&nbsp;triage</div>
                </div>
              </div>
              <div class="mt-3 flex items-center gap-2 text-[11px] text-slate2-300">
                <span class="font-mono text-slate2-400">join key</span>
                <span class="font-mono text-amber-300 px-1.5 py-0.5 rounded bg-amber-500/10 border border-amber-500/30">trace_id</span>
                <span class="text-slate2-500">stitches all four sinks into one timeline.</span>
              </div>
            </div>
          </div>
          <!-- Arrows from pipeline bottom to sinks -->
          <svg class="absolute pointer-events-none" style="left:0; top:-60px; width:100%; height:80px;">
            <g stroke="#a855f7" stroke-width="1.6" fill="none" marker-end="url(#ll-arrow-purple)">
              <line x1="200" y1="0" x2="200" y2="60" />
            </g>
            <g stroke="#fbbf24" stroke-width="1.6" fill="none" stroke-dasharray="4 4">
              <line x1="500" y1="0" x2="500" y2="60" />
              <line x1="900" y1="0" x2="900" y2="60" />
            </g>
          </svg>
        </div>

      </section>

      <!-- ── Key Insights ────────────────────────────────────────────── -->
      <section class="px-16 mt-12 grid grid-cols-3 gap-8">
        <div class="rounded-xl border border-line bg-ink-900 p-5">
          <div class="flex items-center gap-2 mb-2">
            <span class="font-mono text-[10.5px] tracking-[0.2em] uppercase text-emerald-300">Insight · 01</span>
          </div>
          <div class="font-semibold text-[15px] text-white leading-snug mb-1.5">The IoC Container is the magic</div>
          <div class="text-[12.5px] text-slate2-300 leading-relaxed">
            Service Providers are the only place the framework knows how to construct your dependencies.
            <span class="font-mono text-emerald-300">register()</span> declares,
            <span class="font-mono text-emerald-300">boot()</span> consumes — never invert the two.
          </div>
        </div>
        <div class="rounded-xl border border-line bg-ink-900 p-5">
          <div class="flex items-center gap-2 mb-2">
            <span class="font-mono text-[10.5px] tracking-[0.2em] uppercase text-amber-300">Insight · 02</span>
          </div>
          <div class="font-semibold text-[15px] text-white leading-snug mb-1.5">Instrument boundaries, not bodies</div>
          <div class="text-[12.5px] text-slate2-300 leading-relaxed">
            Wrap the kernel, middleware, controller, and DB driver — five spans give you 90% of the picture.
            Sprinkling <span class="font-mono text-amber-300">Trace::start()</span> inside business logic creates noise, not signal.
          </div>
        </div>
        <div class="rounded-xl border border-line bg-ink-900 p-5">
          <div class="flex items-center gap-2 mb-2">
            <span class="font-mono text-[10.5px] tracking-[0.2em] uppercase text-sky-300">Insight · 03</span>
          </div>
          <div class="font-semibold text-[15px] text-white leading-snug mb-1.5">Middleware is an onion, both ways</div>
          <div class="text-[12.5px] text-slate2-300 leading-relaxed">
            The same layer that mutated <span class="font-mono text-sky-300">$request</span> on the way in
            gets to mutate <span class="font-mono text-sky-300">$response</span> on the way out — that's why
            cookies, CORS headers, and cache directives all live there.
          </div>
        </div>
      </section>

      <!-- ── Footer ─────────────────────────────────────────────────── -->
      <footer class="px-16 py-10 mt-8 flex items-center justify-between text-[11.5px] text-slate2-400 font-mono border-t border-line/40">
        <div>CODECV&nbsp;·&nbsp;Framework&nbsp;Internals&nbsp;Series</div>
        <div class="flex items-center gap-6">
          <span><span class="text-slate2-500">rev&nbsp;</span>2026.05</span>
          <span><span class="text-slate2-500">scope&nbsp;</span>Laravel 12 · synchronous HTTP path</span>
          <span><span class="text-slate2-500">excludes&nbsp;</span>Octane, Queues, Console kernel</span>
        </div>
      </footer>

    </div>
  </div>
</template>

<script setup lang="ts">
useHead({
  link: [
    {
      rel: 'stylesheet',
      href: 'https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap',
    },
  ],
})
</script>

<style>
/* ── Custom color utilities (scoped under .laravel-lifecycle-wrapper) ── */

.laravel-lifecycle-wrapper {
  background: #060911;
  color: #e6edf7;
}

/* Ink palette */
.laravel-lifecycle-wrapper .bg-ink-900  { background-color: #0a0f1c; }
.laravel-lifecycle-wrapper .bg-ink-800  { background-color: #121a2c; }
.laravel-lifecycle-wrapper .bg-ink-800\/60  { background-color: rgba(18, 26, 44, 0.6); }
.laravel-lifecycle-wrapper .bg-ink-800\/80  { background-color: rgba(18, 26, 44, 0.8); }
.laravel-lifecycle-wrapper .bg-ink-800\/90  { background-color: rgba(18, 26, 44, 0.9); }
.laravel-lifecycle-wrapper .bg-ink-900\/40  { background-color: rgba(10, 15, 28, 0.4); }

/* Line (border / divider color) */
.laravel-lifecycle-wrapper .border-line      { border-color: #23304a; }
.laravel-lifecycle-wrapper .border-line\/40  { border-color: rgba(35, 48, 74, 0.4); }
.laravel-lifecycle-wrapper .border-line\/70  { border-color: rgba(35, 48, 74, 0.7); }
.laravel-lifecycle-wrapper .bg-line          { background-color: #23304a; }

/* Slate2 text palette */
.laravel-lifecycle-wrapper .text-slate2-200  { color: #b6c2d6; }
.laravel-lifecycle-wrapper .text-slate2-300  { color: #8c9bb6; }
.laravel-lifecycle-wrapper .text-slate2-400  { color: #67768f; }
.laravel-lifecycle-wrapper .text-slate2-500  { color: #4a5670; }

/* Dot-grid background */
.laravel-lifecycle-wrapper .ll-grid-bg {
  background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.05) 1px, transparent 0);
  background-size: 24px 24px;
}

/* Amber hatch pattern (observability rail) */
.laravel-lifecycle-wrapper .ll-hatch {
  background-image: repeating-linear-gradient(
    45deg,
    rgba(251, 191, 36, 0.10) 0,
    rgba(251, 191, 36, 0.10) 1px,
    transparent 1px,
    transparent 7px
  );
}

/* Monospace chip / label utilities */
.laravel-lifecycle-wrapper .ll-num-chip {
  font-family: 'JetBrains Mono', monospace;
  font-weight: 700;
  font-size: 11px;
  letter-spacing: 0.04em;
}
.laravel-lifecycle-wrapper .ll-stage-label {
  font-family: 'JetBrains Mono', monospace;
  font-size: 10.5px;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

/* Dashed SVG paths (telemetry tap lines) */
.ll-dashed-arrow path {
  stroke-dasharray: 4 4;
}
</style>
