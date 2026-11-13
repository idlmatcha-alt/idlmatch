@php
    $payload = $this->payload();
    $stats = $payload['stats'];
    $growth = $payload['growth'];
    $gender = $payload['gender'];
    $recentUsers = $payload['recent_users'];
    $activity = $payload['activity'];
    $quickActions = $payload['quick_actions'];
    $system = $payload['system'];
    $online = $this->isFullyOnline();
    $chart = $this->growthChart($growth);
    $ring = $this->donut($gender);
@endphp

<x-filament::page>
    <div class="adm">

        {{-- ── page heading ─────────────────────────────────────────────── --}}
        <div class="adm-head">
            <div class="adm-head-text">
                <h1 class="adm-title">Dashboard</h1>
                <p class="adm-sub">
                    Welcome back, <strong>{{ $this->adminName() }}</strong>!
                    Here's what's happening with your platform.
                </p>
            </div>

            <div class="adm-head-meta">
                <span class="adm-date">
                    <x-dynamic-component :component="'heroicon-o-calendar'" class="w-5 h-5" />
                    <span>
                        <em>{{ $payload['generated_day'] }}</em>
                        <small>Last updated: {{ $payload['generated_at'] }}</small>
                    </span>
                </span>

                <span class="adm-pill {{ $online ? 'is-ok' : 'is-warn' }}">
                    <i></i>
                    {{ $online ? 'System Online' : 'Check Services' }}
                </span>
            </div>
        </div>

        <div class="adm-shell">
            <div class="adm-main">

                {{-- ── stat cards ───────────────────────────────────────── --}}
                <div class="adm-cards">
                    @foreach ($stats as $stat)
                        @php
                            $spark = $this->sparkline($stat['series']);
                            $delta = $stat['delta'];
                            $flat = $delta === null || abs($delta) < 0.5;
                            $up = ! $flat && $delta > 0;
                            $tone = $this->toneColor($stat['tone']);
                        @endphp
                        <a class="adm-card" href="{{ $stat['href'] }}">
                            <div class="adm-card-top">
                                <span class="adm-tile" style="{{ $this->toneStyle($stat['tone']) }}">
                                    <x-dynamic-component :component="$stat['icon']" class="w-6 h-6" />
                                </span>
                                <span class="adm-card-label">{{ $stat['label'] }}</span>
                            </div>

                            <p class="adm-card-value">{{ $stat['value'] }}</p>

                            <div class="adm-card-foot">
                                <span class="adm-delta {{ $flat ? 'is-flat' : ($up ? 'is-up' : 'is-down') }}">
                                    @unless ($flat)
                                        <x-dynamic-component
                                            :component="$up ? 'heroicon-o-arrow-sm-up' : 'heroicon-o-arrow-sm-down'"
                                            class="w-3 h-3"
                                        />
                                    @endunless
                                    {{ $flat ? 'No change' : ($up ? '+' : '-') . number_format(abs($delta), abs($delta) < 10 ? 1 : 0) . '%' }}
                                </span>
                                <span class="adm-vs">vs. last month</span>
                            </div>

                            <svg class="adm-spark" viewBox="0 0 {{ $spark['width'] }} {{ $spark['height'] }}"
                                 preserveAspectRatio="none" aria-hidden="true">
                                <polygon points="{{ $spark['area'] }}" fill="{{ $tone }}" opacity="0.12"></polygon>
                                <polyline points="{{ $spark['line'] }}" fill="none" stroke="{{ $tone }}"
                                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></polyline>
                            </svg>
                        </a>
                    @endforeach
                </div>

                {{-- ── charts ───────────────────────────────────────────── --}}
                <div class="adm-row is-charts">
                    <section class="adm-panel">
                        <header class="adm-panel-head">
                            <h2>User Growth</h2>
                            <ul class="adm-legend">
                                @foreach ($chart['series'] as $line)
                                    <li><i style="background: {{ $line['color'] }}"></i>{{ $line['label'] }}</li>
                                @endforeach
                            </ul>
                        </header>

                        <div class="adm-chart">
                            <svg viewBox="0 0 {{ $chart['width'] }} {{ $chart['height'] }}" role="img"
                                 aria-label="User growth over the last twelve months">
                                <defs>
                                    @foreach ($chart['series'] as $index => $line)
                                        <linearGradient id="adm-fill-{{ $index }}" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="{{ $line['color'] }}" stop-opacity="0.22"/>
                                            <stop offset="100%" stop-color="{{ $line['color'] }}" stop-opacity="0.02"/>
                                        </linearGradient>
                                    @endforeach
                                </defs>

                                @foreach ($chart['grid'] as $line)
                                    <line x1="{{ $chart['plot']['x'] }}" y1="{{ $line['y'] }}"
                                          x2="{{ $chart['plot']['x'] + $chart['plot']['width'] }}" y2="{{ $line['y'] }}"
                                          stroke="#e6ebf4" stroke-width="1"></line>
                                    <text x="{{ $chart['plot']['x'] - 10 }}" y="{{ $line['y'] + 4 }}"
                                          text-anchor="end" class="adm-axis">{{ $line['label'] }}</text>
                                @endforeach

                                @foreach ($chart['x_labels'] as $label)
                                    <text x="{{ $label['x'] }}" y="{{ $chart['height'] - 8 }}"
                                          text-anchor="middle" class="adm-axis">{{ $label['label'] }}</text>
                                @endforeach

                                @foreach ($chart['series'] as $index => $line)
                                    @if ($line['data']['area'])
                                        <polygon points="{{ $line['data']['area'] }}" fill="url(#adm-fill-{{ $index }})"></polygon>
                                    @endif
                                    <polyline points="{{ $line['data']['line'] }}" fill="none"
                                              stroke="{{ $line['color'] }}" stroke-width="2.5"
                                              stroke-linecap="round" stroke-linejoin="round"></polyline>
                                    @foreach ($line['data']['dots'] as $dot)
                                        <circle cx="{{ $dot[0] }}" cy="{{ $dot[1] }}" r="3"
                                                fill="#fff" stroke="{{ $line['color'] }}" stroke-width="2"></circle>
                                    @endforeach
                                @endforeach
                            </svg>
                        </div>
                    </section>

                    <section class="adm-panel">
                        <header class="adm-panel-head">
                            <h2>User Gender Ratio</h2>
                        </header>

                        <div class="adm-donut">
                            <svg viewBox="0 0 {{ $ring['size'] }} {{ $ring['size'] }}" role="img"
                                 aria-label="Gender split of profiles">
                                <circle cx="{{ $ring['center'] }}" cy="{{ $ring['center'] }}" r="{{ $ring['radius'] }}"
                                        fill="none" stroke="#eef2f8" stroke-width="{{ $ring['stroke'] }}"></circle>
                                @foreach ($ring['segments'] as $segment)
                                    <circle cx="{{ $ring['center'] }}" cy="{{ $ring['center'] }}" r="{{ $ring['radius'] }}"
                                            fill="none" stroke="{{ $segment['color'] }}" stroke-width="{{ $ring['stroke'] }}"
                                            stroke-dasharray="{{ $segment['dash'] }}" stroke-dashoffset="{{ $segment['offset'] }}"
                                            transform="rotate(-90 {{ $ring['center'] }} {{ $ring['center'] }})"></circle>
                                @endforeach
                            </svg>

                            <div class="adm-donut-center">
                                <strong>{{ number_format($gender['total']) }}</strong>
                                <span>Profiles</span>
                            </div>
                        </div>

                        <ul class="adm-ratio">
                            @foreach ($gender['segments'] as $segment)
                                <li>
                                    <i style="background: {{ $this->toneColor($segment['tone']) }}"></i>
                                    <span>{{ $segment['label'] }}</span>
                                    <b>{{ $segment['percent'] }}%</b>
                                    <em>({{ number_format($segment['value']) }})</em>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                </div>

                {{-- ── recent users + activity ──────────────────────────── --}}
                <div class="adm-row is-lists">
                    <section class="adm-panel">
                        <header class="adm-panel-head">
                            <h2>Recent Users</h2>
                            <a class="adm-more" href="{{ $this->panelUrl('filament.resources.users.index') }}">
                                View All Users
                                <x-dynamic-component :component="'heroicon-o-arrow-narrow-right'" class="w-4 h-4" />
                            </a>
                        </header>

                        <div class="adm-table-wrap">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Gender</th>
                                        <th>Joined</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentUsers as $user)
                                        <tr>
                                            <td class="adm-id">{{ $user['id'] }}</td>
                                            <td>
                                                <span class="adm-person">
                                                    @if ($user['photo'])
                                                        <img src="{{ $user['photo'] }}" alt="" loading="lazy" />
                                                    @else
                                                        <span class="adm-initials">{{ $user['initials'] }}</span>
                                                    @endif
                                                    <b>{{ $user['name'] }}</b>
                                                </span>
                                            </td>
                                            <td class="adm-mail">{{ $user['email'] }}</td>
                                            <td><span class="adm-tag is-{{ $user['gender'] }}">{{ ucfirst($user['gender']) }}</span></td>
                                            <td>{{ $user['joined'] }}</td>
                                            <td><span class="adm-tag is-status-{{ $user['status'] }}">{{ ucfirst($user['status']) }}</span></td>
                                            <td class="adm-action">
                                                <a href="{{ $user['href'] }}" title="Open profile" aria-label="Open {{ $user['name'] }}">
                                                    <x-dynamic-component :component="'heroicon-o-dots-vertical'" class="w-4 h-4" />
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="adm-empty">No members yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="adm-panel">
                        <header class="adm-panel-head">
                            <h2>Recent Activity</h2>
                            <a class="adm-more" href="{{ $this->panelUrl('filament.resources.users.index') }}">
                                View All
                                <x-dynamic-component :component="'heroicon-o-arrow-narrow-right'" class="w-4 h-4" />
                            </a>
                        </header>

                        <ul class="adm-feed">
                            @forelse ($activity as $item)
                                <li>
                                    <span class="adm-tile is-sm" style="{{ $this->toneStyle($item['tone']) }}">
                                        <x-dynamic-component :component="$item['icon']" class="w-4 h-4" />
                                    </span>
                                    <span class="adm-feed-text">
                                        <b>{{ $item['title'] }}</b>
                                        <em>{{ $item['detail'] }}</em>
                                    </span>
                                    <time>{{ $item['when'] }}</time>
                                </li>
                            @empty
                                <li class="adm-empty">Nothing has happened yet.</li>
                            @endforelse
                        </ul>
                    </section>
                </div>

                {{-- The clickable "Member overview" / "Website overview" stat
                     widgets are deliberately not rendered here: the approved
                     reference ends the dashboard after the two list panels, and
                     every number they carried is already reachable through the
                     sidebar resources or the stat cards above. $this->
                     overviewWidgets() still returns them if that ever changes. --}}
            </div>

            {{-- ── right rail ───────────────────────────────────────────── --}}
            <aside class="adm-rail">
                <section class="adm-panel">
                    <header class="adm-panel-head">
                        <h2>Quick Actions</h2>
                    </header>

                    <ul class="adm-actions">
                        @foreach ($quickActions as $action)
                            <li>
                                <a href="{{ $action['href'] }}">
                                    <span class="adm-tile" style="{{ $this->toneStyle($action['tone']) }}">
                                        <x-dynamic-component :component="$action['icon']" class="w-5 h-5" />
                                    </span>
                                    <span class="adm-action-text">
                                        <b>{{ $action['label'] }}</b>
                                        <em>{{ $action['detail'] }}</em>
                                    </span>
                                    @if (($action['badge'] ?? 0) > 0)
                                        <span class="adm-count">{{ $action['badge'] }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>

                <section class="adm-panel">
                    <header class="adm-panel-head">
                        <h2>System Status</h2>
                    </header>

                    <ul class="adm-status">
                        @foreach ($system as $row)
                            @php $rowTone = $row['ok'] ? 'green' : 'rose'; @endphp
                            <li>
                                <span class="adm-tile is-sm" style="{{ $this->toneStyle($rowTone) }}">
                                    <x-dynamic-component
                                        :component="$row['ok'] ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle'"
                                        class="w-4 h-4"
                                    />
                                </span>
                                <span class="adm-status-text">
                                    <b>{{ $row['label'] }}</b>
                                    <em class="adm-note">{{ $row['note'] }}</em>
                                </span>
                                <span class="adm-state {{ $row['ok'] ? 'is-ok' : 'is-bad' }}">
                                    {{ $row['ok'] ? 'Online' : 'Offline' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </section>

                <section class="adm-promo">
                    <span class="adm-promo-heart">
                        <x-dynamic-component :component="'heroicon-o-heart'" class="w-6 h-6" />
                    </span>
                    <h3>Better Matches, Happier People</h3>
                    <p>Together we build a safer, smarter and more connected community.</p>
                    <a href="{{ url('/') }}" target="_blank" rel="noopener">
                        View Platform
                        <x-dynamic-component :component="'heroicon-o-arrow-narrow-right'" class="w-4 h-4" />
                    </a>
                </section>
            </aside>
        </div>
    </div>
</x-filament::page>
