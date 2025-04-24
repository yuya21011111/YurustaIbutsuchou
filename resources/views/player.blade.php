<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>キャラクター一覧</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        function toggleDetail(index) {
            const detail = document.getElementById(`char-detail-${index}`);
            detail.classList.toggle('hidden');
        }

        async function onWeightChange(charId) {
            const formData = new FormData(document.querySelector('form'));
            const weights = {};
            formData.forEach((val, key) => {
                const match = key.match(new RegExp(`weights\\[${charId}\\]\\[(.+?)\\]`));
                if (match) weights[match[1]] = parseFloat(val);
            });

            const relicData = window.relicsByCharId?.[charId] || [];

            const res = await fetch("{{ route('score.recalculate') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    weights,
                    relics: relicData
                })
            });

            const updatedRelics = await res.json();
            window.relicsByCharId[charId] = updatedRelics;
            updateRelicDisplay(charId, updatedRelics);
            updateTotalScore(charId, updatedRelics);
        }

        function updateRelicDisplay(charId, relics) {
            const container = document.getElementById(`relic-list-${charId}`);
            if (!container) return;

            container.innerHTML = relics.map(r => {
                const total = parseFloat(r.score.total || 0).toFixed(1);
                const main = parseFloat(r.score.main || 0).toFixed(1);
                const sub = parseFloat(r.score.sub || 0).toFixed(1);
                let grade = 'D';
                if (total >= 100) grade = 'SS';
                else if (total >= 90) grade = 'S';
                else if (total >= 60) grade = 'A';
                else if (total >= 40) grade = 'B';
                else if (total >= 10) grade = 'C';

                let color = 'text-red-500';
                if (total >= 100) color = 'text-yellow-500';
                else if (total >= 90) color = 'text-purple-500';
                else if (total >= 60) color = 'text-blue-500';
                else if (total >= 40) color = 'text-green-500';
                else if (total >= 10) color = 'text-gray-500';

                const iconUrl = r.icon ? `{{ asset('') }}` + r.icon : '';

                // メイン効果の表示
                const mainAffixHtml = r.main_affix ?
                    `<div class="text-xs text-gray-700 mt-1">メイン効果: ${r.main_affix.name} ${r.main_affix.display}</div>` :
                    '';

                // サブステータスの表示
                const subAffixHtml = r.sub_affix?.length ?
                    `<div class="grid grid-cols-2 gap-1 mt-1 text-xs text-gray-600">
                    ${r.sub_affix.map(sub => `<div>${sub.name}: ${sub.display}</div>`).join('')}
               </div>` :
                    '';

                return `
            <div class="bg-gray-100 rounded-lg px-3 py-2 flex flex-col mb-2">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        ${r.icon ? `<img src="${iconUrl}" class="w-6 h-6" />` : ''}
                        <span class="font-semibold text-xs">${r.name}</span>
                    </div>
                    <div class="text-xs text-right text-gray-700">
                        メイン: ${main} / サブ: ${sub}<br>
                        合計: <span class="font-bold ${color}">${total}</span>
                        <span class="${color}">${grade}</span>
                    </div>
                </div>
                ${mainAffixHtml}
                ${subAffixHtml}
            </div>
        `;
            }).join('');
        }

        function updateTotalScore(charId, relics) {
            const totalScore = relics.reduce((sum, r) => sum + parseFloat(r.score?.total || 0), 0);
            const scoreEl = document.getElementById(`total-score-${charId}`);
            const gradeEl = document.getElementById(`total-grade-${charId}`);

            let grade = 'D';
            if (totalScore >= 600) grade = 'SS';
            else if (totalScore >= 540) grade = 'S';
            else if (totalScore >= 360) grade = 'A';
            else if (totalScore >= 240) grade = 'B';
            else if (totalScore >= 60) grade = 'C';

            let color = 'text-red-500';
            if (totalScore >= 600) color = 'text-yellow-500';
            else if (totalScore >= 540) color = 'text-purple-500';
            else if (totalScore >= 360) color = 'text-blue-500';
            else if (totalScore >= 240) color = 'text-green-500';
            else if (totalScore >= 60) color = 'text-gray-500';

            scoreEl.textContent = totalScore.toFixed(1);
            scoreEl.className = `text-2xl font-bold ${color}`;
            gradeEl.textContent = grade;
            gradeEl.className = `ml-1 text-base ${color}`;
        }
    </script>
</head>
@if (isset($error))
    <div class="text-center py-12">
        <p class="text-red-600 text-lg font-semibold">{{ $error }}</p>
        <form method="GET">
            <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                再読み込み
            </button>
        </form>
        <a href="{{ route('top') }}"
            class="mt-4 inline-block bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            トップに戻る
        </a>
    </div>
@else

    <body class="bg-blue-100 min-h-screen p-4 sm:p-6">
        <div class="flex justify-end mt-4">
            <a href="{{ route('top') }}" class="inline-block bg-red-400 text-white px-8 py-2 rounded hover:bg-red-600">
                トップに戻る
            </a>
        </div>
        <h2 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6 text-center">キャラクター一覧</h2>

        @if (isset($mode) && $mode === 'api')
            <p class="text-center text-sm text-green-600 mb-4">現在表示中：<strong>APIモード</strong></p>
        @elseif(isset($mode) && $mode === 'saved')
            <p class="text-center text-sm text-yellow-600 mb-4">現在表示中：<strong>セーブモード（保存データ）</strong></p>
        @elseif(isset($mode) && $mode === 'error')
            <p class="text-center text-sm text-red-600 mb-4">現在表示中：<strong>エラー</strong></p>
        @endif

        <!-- 重み設定フォーム -->
        <form method="GET" class="mb-6 max-w-4xl mx-auto bg-white p-4 rounded-xl shadow-md">
            <h3 class="text-lg sm:text-xl font-bold mb-3">サブステータス評価値設定</h3>
            @foreach ($characters as $char)
                @php $charId = $char['id']; @endphp
                <details class="mb-4">
                    <summary class="cursor-pointer font-semibold mb-2 flex items-center gap-2">
                        <img src="{{ asset('./icon/character/' . $charId . '.png') }}" alt="キャラ画像"
                            class="w-8 h-8 rounded">
                        {{ $char['name'] }}（ID: {{ $charId }}）
                    </summary>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-2">
                        @foreach ([
        'HPDelta' => 'HP実数',
        'AttackDelta' => '攻撃実数',
        'DefenceDelta' => '防御実数',
        'HPAddedRatio' => 'HP%',
        'AttackAddedRatio' => '攻撃%',
        'DefenceAddedRatio' => '防御%',
        'CriticalChanceBase' => '会心率',
        'CriticalDamageBase' => '会心ダメ',
        'StatusProbabilityBase' => '効果命中',
        'BreakDamageAddedRatioBase' => '撃破特効',
        'StatusResistanceBase' => '効果抵抗',
        'SpeedDelta' => '速度',
    ] as $key => $label)
                            <div class="flex items-center justify-between">
                                <label class="text-sm w-24">{{ $label }}</label>
                                <input type="number" step="0.1" min="0" max="1"
                                    name="weights[{{ $charId }}][{{ $key }}]"
                                    value="{{ request('weights')[$charId][$key] ?? 0 }}"
                                    onchange="onWeightChange({{ $charId }})"
                                    class="w-20 text-right rounded border border-gray-300 px-2 py-1 text-sm">
                            </div>
                        @endforeach
                    </div>
                </details>
            @endforeach
        </form>

        @if (empty($characters) || count($characters) === 0)
            <!-- キャラデータがないとき -->
            <div class="text-center py-12">
                <p class="text-lg text-gray-700 mb-4">キャラクター情報が取得できませんでした。</p>
                <form method="GET">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        再読み込み
                    </button>
                </form>
            </div>
        @else
            <!-- キャラカード一覧 -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 max-w-7xl mx-auto">
                @foreach ($characters as $index => $char)
                    <div class="overflow-hidden rounded-2xl shadow-lg bg-white">
                        <button onclick="toggleDetail({{ $index }})"
                            class="block w-full h-48 md:h-64 bg-contain bg-no-repeat bg-center"
                            style="background-image: url('{{ asset('./' . $char['portrait']) }}');">
                        </button>

                        <div id="char-detail-{{ $index }}" class="p-4 hidden">
                            <h3 class="text-lg sm:text-xl font-bold mb-2">{{ $char['name'] }} Lv.{{ $char['level'] }}
                            </h3>

                            @if (isset($char['light_cone']))
                                <div class="flex items-start gap-3 mb-4">
                                    <img src="{{ asset('./' . $char['light_cone']['portrait']) }}" alt="光円錐画像"
                                        class="w-16 h-24 object-contain rounded shadow-md">
                                    <div>
                                        <div class="text-sm font-bold">{{ $char['light_cone']['name'] }}</div>
                                        <div class="text-xs text-gray-600">
                                            Lv.{{ $char['light_cone']['level'] }} / 重ね:
                                            {{ $char['light_cone']['rank'] }}
                                        </div>
                                        <div class="text-xs text-gray-700 mt-1">
                                            {{ $char['light_cone']['desc'] ?? '説明なし' }}
                                        </div>
                                        @if (isset($char['light_cone']['attributes']))
                                            <div class="text-xs text-gray-700 mt-1">
                                                @foreach ($char['light_cone']['attributes'] as $attr)
                                                    <div>{{ $attr['name'] }}: {{ $attr['display'] }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="text-center mb-3">
                                <div class="text-xs text-gray-700">総合スコア（6部位合算）</div>
                                <div id="total-score-{{ $char['id'] }}" class="text-2xl font-bold text-gray-500">
                                    {{ number_format(array_sum(array_column(array_column($char['relics'], 'score'), 'total')), 1) }}
                                </div>
                                <div id="total-grade-{{ $char['id'] }}" class="ml-1 text-base text-gray-500">
                                    {{ match (true) {
                                        array_sum(array_column(array_column($char['relics'], 'score'), 'total')) >= 600 => 'SS',
                                        array_sum(array_column(array_column($char['relics'], 'score'), 'total')) >= 540 => 'S',
                                        array_sum(array_column(array_column($char['relics'], 'score'), 'total')) >= 360 => 'A',
                                        array_sum(array_column(array_column($char['relics'], 'score'), 'total')) >= 240 => 'B',
                                        array_sum(array_column(array_column($char['relics'], 'score'), 'total')) >= 60 => 'C',
                                        default => 'D',
                                    } }}
                                </div>
                            </div>

                            <!-- 遺物のスコア表示部分 -->
                            <div id="relic-list-{{ $char['id'] }}">
                                @foreach ($char['relics'] as $relic)
                                    @if (isset($relic['score']))
                                        @php
                                            $t = $relic['score']['total'] ?? 0;
                                            $m = $relic['score']['main'] ?? 0;
                                            $s = $relic['score']['sub'] ?? 0;
                                            $grade = match (true) {
                                                $t >= 100 => 'SS',
                                                $t >= 90 => 'S',
                                                $t >= 60 => 'A',
                                                $t >= 40 => 'B',
                                                $t >= 10 => 'C',
                                                default => 'D',
                                            };
                                            $color = match (true) {
                                                $t >= 100 => 'text-yellow-500',
                                                $t >= 90 => 'text-purple-500',
                                                $t >= 60 => 'text-blue-500',
                                                $t >= 40 => 'text-green-500',
                                                $t >= 10 => 'text-gray-500',
                                                default => 'text-red-500',
                                            };
                                            $iconPath = isset($relic['icon']) ? asset('./' . $relic['icon']) : '';
                                        @endphp
                                        <div class="bg-gray-100 rounded-lg px-3 py-2 flex flex-col mb-2">
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center gap-2">
                                                    @if ($iconPath)
                                                        <img src="{{ $iconPath }}" class="w-6 h-6" alt="遺物アイコン">
                                                    @endif
                                                    <span class="font-semibold text-xs">{{ $relic['name'] }}</span>
                                                </div>
                                                <div class="text-xs text-right text-gray-700">
                                                    メイン: {{ number_format($m, 1) }} / サブ:
                                                    {{ number_format($s, 1) }}<br>
                                                    合計: <span
                                                        class="font-bold {{ $color }}">{{ number_format($t, 1) }}</span>
                                                    <span class="{{ $color }}">{{ $grade }}</span>
                                                </div>
                                            </div>

                                            @if (isset($relic['main_affix']))
                                                <div class="text-xs text-gray-700 mt-1">
                                                    メイン効果: {{ $relic['main_affix']['name'] }}
                                                    {{ $relic['main_affix']['display'] }}
                                                </div>
                                            @endif

                                            @if (isset($relic['sub_affix']))
                                                <div class="grid grid-cols-2 gap-1 mt-1 text-xs text-gray-600">
                                                    @foreach ($relic['sub_affix'] as $sub)
                                                        <div>{{ $sub['name'] }}: {{ $sub['display'] }}</div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <script>
            window.relicsByCharId = @json(collect($characters)->mapWithKeys(fn($c) => [$c['id'] => $c['relics']])->toArray());
        </script>

        <footer class="footer">
            @include('layouts.footer')
        </footer>
    </body>

@endif
