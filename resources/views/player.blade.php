{{-- <!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>キャラクター一覧</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-blue-100 min-h-screen p-6">
    <h2 class="text-3xl font-bold mb-6 text-center">キャラクター一覧</h2>

    <!-- 重み設定フォーム -->
    <form method="GET" class="mb-8 max-w-4xl mx-auto bg-white p-4 rounded-xl shadow-md">
        <h3 class="text-xl font-bold mb-4">サブステータス重み設定</h3>
        @foreach ($characters as $char)
            @php $charId = $char['id']; @endphp
            <details class="mb-4">
                <summary class="cursor-pointer font-semibold mb-2">{{ $char['name'] }}（ID: {{ $charId }}）</summary>
                <div class="grid grid-cols-2 gap-4 p-2">
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
                            <label class="mr-2 text-sm w-24">{{ $label }}</label>
                            <input type="number" step="0.1" min="0" max="1"
                                   name="weights[{{ $charId }}][{{ $key }}]"
                                   value="{{ request('weights')[$charId][$key] ?? 0 }}"
                                   class="w-20 text-right rounded-md border border-gray-300 px-2 py-1 text-sm">
                        </div>
                    @endforeach
                </div>
            </details>
        @endforeach
        <div class="text-center">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                スコアを再計算
            </button>
        </div>
    </form>

    <!-- キャラクターカード表示 -->
    <div class="grid grid-cols-1 gap-6 max-w-7xl mx-auto">
        @foreach ($characters as $char)
            <div class="relative overflow-hidden rounded-2xl shadow-lg"
                 style="background-image: url('{{ asset('./' . $char['portrait']) }}'); background-size: cover; background-position: center;">
                <div class="bg-black/40 backdrop-blur-sm"></div>
                <div class="relative flex gap-6 p-6 z-10 bg-white/80 rounded-2xl backdrop-blur-sm">
                    <!-- 左：ステータス -->
                    <div class="w-[52%]">
                        <h2 class="text-2xl font-bold mb-4">
                            {{ $char['name'] ?? '名前不明' }}
                            <span class="text-sm text-gray-600">Lv. {{ $char['level'] }}</span>
                        </h2>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            @php
                                $attrHp = collect($char['attributes'])->firstWhere('field', 'hp')['value'] ?? 0;
                                $addHp = collect($char['additions'])->firstWhere('field', 'hp')['value'] ?? 0;
                                $attrAtk = collect($char['attributes'])->firstWhere('field', 'atk')['value'] ?? 0;
                                $addAtk = collect($char['additions'])->firstWhere('field', 'atk')['value'] ?? 0;
                                $attrDef = collect($char['attributes'])->firstWhere('field', 'def')['value'] ?? 0;
                                $addDef = collect($char['additions'])->firstWhere('field', 'def')['value'] ?? 0;
                                $attrSpd = collect($char['attributes'])->firstWhere('field', 'spd')['value'] ?? 0;
                                $addSpd = collect($char['additions'])->firstWhere('field', 'spd')['value'] ?? 0;
                                $attrCrit_rate = collect($char['attributes'])->firstWhere('field', 'crit_rate')['value'] ?? 0;
                                $addCrit_rate = collect($char['additions'])->firstWhere('field', 'crit_rate')['value'] ?? 0;
                                $attrCrit_dmg = collect($char['attributes'])->firstWhere('field', 'crit_dmg')['value'] ?? 0;
                                $addCrit_dmg = collect($char['additions'])->firstWhere('field', 'crit_dmg')['value'] ?? 0;
                                $addBreak_dmg = collect($char['additions'])->firstWhere('field', 'break_dmg')['value'] ?? 0;
                            @endphp
                            <div>❤️ HP</div><div class="text-right">{{ floor($attrHp + $addHp) }}</div>
                            <div>🗡️ 攻撃力</div><div class="text-right">{{ floor($attrAtk + $addAtk) }}</div>
                            <div>🛡️ 防御力</div><div class="text-right">{{ floor($attrDef + $addDef) }}</div>
                            <div>⚡ 速度</div><div class="text-right">{{ floor($attrSpd + $addSpd) }}</div>
                            <div>🎯 会心率</div><div class="text-right">{{ number_format(($attrCrit_rate + $addCrit_rate) * 100, 1) }}%</div>
                            <div>💥 会心ダメ</div><div class="text-right">{{ number_format(($attrCrit_dmg + $addCrit_dmg) * 100, 1) }}%</div>
                            <div>💣 撃破特効</div><div class="text-right">{{ number_format($addBreak_dmg * 100, 1) }}%</div>
                        </div>
                    </div>

                    <!-- 右：光円錐＋スコア -->
                    <div class="w-[48%] flex flex-col items-center">
                        @if (!empty($char['icon']))
                            <img src="{{ asset('./' . $char['icon']) }}" alt="キャラ画像" class="w-28 h-auto rounded-xl mb-4">
                        @endif

                        <img src="{{ asset('./' . $char['light_cone']['preview']) }}" alt="光円錐" class="w-28 h-auto rounded-xl mb-4">

                        <div class="bg-gray-100 p-3 rounded-lg text-sm w-full mb-2">
                            <div class="font-semibold mb-1">
                                {{ 'Lv. ' . $char['light_cone']['level'] . ' ' . $char['light_cone']['name'] }}
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($char['light_cone']['attributes'] as $attr)
                                    <div>{{ $attr['name'] }}</div>
                                    <div class="text-right">{{ $attr['display'] }}</div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 総合スコア -->
                        @php
                            $totalScore = array_sum(array_column(array_column($char['relics'], 'score'), 'total'));
                            $scoreGrade = match (true) {
                                $totalScore >= 600 => 'SS',
                                $totalScore >= 540 => 'S',
                                $totalScore >= 360 => 'A',
                                $totalScore >= 240 => 'B',
                                $totalScore >= 60  => 'C',
                                default => 'D',
                            };
                            $scoreColor = match (true) {
                                $totalScore >= 600 => 'text-yellow-500',
                                $totalScore >= 540 => 'text-purple-500',
                                $totalScore >= 360 => 'text-blue-500',
                                $totalScore >= 240 => 'text-green-500',
                                $totalScore >= 60  => 'text-gray-500',
                                default => 'text-red-500',
                            };
                        @endphp
                        <div class="bg-white text-center px-4 py-2 rounded-xl w-full shadow-sm border mt-2">
                            <div class="text-xs text-gray-700">総合スコア（6部位合算）</div>
                            <div class="text-2xl font-bold {{ $scoreColor }}">
                                {{ number_format($totalScore, 1) }}
                                <span class="ml-2 text-sm {{ $scoreColor }}">{{ $scoreGrade }}</span>
                            </div>
                        </div>

                        <!-- 各遺物スコア -->
                        @if (!empty($char['relics']))
                            <div class="mt-3 bg-white/90 p-4 rounded-xl shadow-inner text-sm w-full">
                                <h3 class="font-bold mb-2 text-center text-gray-700">装備遺物スコア一覧</h3>
                                <div class="space-y-2">
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
                                            <div class="bg-blue-100 rounded-md px-3 py-2 flex justify-between items-center">
                                                <div class="flex items-center gap-2">
                                                    @if ($iconPath)
                                                        <img src="{{ $iconPath }}" alt="遺物アイコン" class="w-6 h-6">
                                                    @endif
                                                    <span class="font-semibold">{{ $relic['name'] ?? '不明な遺物' }}</span>
                                                </div>
                                                <div class="text-xs text-right text-gray-700">
                                                    メイン: {{ number_format($m, 1) }} /
                                                    サブ: {{ number_format($s, 1) }} /
                                                    合計: <span class="font-bold {{ $color }}">{{ number_format($t, 1) }}</span>
                                                    <span class="ml-1 {{ $color }}">{{ $grade }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</body>
</html> --}}

{{-- <!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>キャラクター一覧</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        function toggleDetail(index) {
            const detail = document.getElementById(`char-detail-${index}`);
            detail.classList.toggle('hidden');
        }
    </script>
</head>

<body class="bg-blue-100 min-h-screen p-6">
    <h2 class="text-3xl font-bold mb-6 text-center">キャラクター一覧</h2>

    <!-- 重み設定フォーム -->
    <form method="GET" class="mb-8 max-w-4xl mx-auto bg-white p-4 rounded-xl shadow-md">
        <h3 class="text-xl font-bold mb-4">サブステータス重み設定</h3>
        @foreach ($characters as $char)
            @php $charId = $char['id']; @endphp
            <details class="mb-4">
                <summary class="cursor-pointer font-semibold mb-2">
                    {{ $char['name'] }}（ID: {{ $charId }}）
                </summary>
                <div class="grid grid-cols-2 gap-4 p-2">
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
                            <label class="mr-2 text-sm w-24">{{ $label }}</label>
                            <input type="number" step="0.1" min="0" max="1"
                                   name="weights[{{ $charId }}][{{ $key }}]"
                                   value="{{ request('weights')[$charId][$key] ?? 0 }}"
                                   class="w-20 text-right rounded-md border border-gray-300 px-2 py-1 text-sm">
                        </div>
                    @endforeach
                </div>
            </details>
        @endforeach
        <div class="text-center">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                スコアを再計算
            </button>
        </div>
    </form>

    <!-- キャラクター一覧 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-7xl mx-auto">
        @foreach ($characters as $index => $char)
            <div class="relative overflow-hidden rounded-2xl shadow-lg">
                <button onclick="toggleDetail({{ $index }})"
                        class="block w-full h-64 bg-cover bg-center"
                        style="background-image: url('{{ asset('./' . $char['portrait']) }}');">
                </button>

                <div id="char-detail-{{ $index }}" class="p-4 bg-white rounded-b-2xl hidden">
                    <h3 class="text-xl font-bold mb-2">{{ $char['name'] }} Lv.{{ $char['level'] }}</h3>

                    <!-- スコアまとめ -->
                    @php
                        $totalScore = array_sum(array_column(array_column($char['relics'], 'score'), 'total'));
                        $scoreGrade = match (true) {
                            $totalScore >= 600 => 'SS',
                            $totalScore >= 540 => 'S',
                            $totalScore >= 360 => 'A',
                            $totalScore >= 240 => 'B',
                            $totalScore >= 60  => 'C',
                            default => 'D',
                        };
                        $scoreColor = match (true) {
                            $totalScore >= 600 => 'text-yellow-500',
                            $totalScore >= 540 => 'text-purple-500',
                            $totalScore >= 360 => 'text-blue-500',
                            $totalScore >= 240 => 'text-green-500',
                            $totalScore >= 60  => 'text-gray-500',
                            default => 'text-red-500',
                        };
                    @endphp
                    <div class="text-center mb-4">
                        <div class="text-sm text-gray-700">総合スコア（6部位合算）</div>
                        <div class="text-2xl font-bold {{ $scoreColor }}">
                            {{ number_format($totalScore, 1) }} <span class="ml-1 text-base">{{ $scoreGrade }}</span>
                        </div>
                    </div>

                    <!-- 遺物一覧 -->
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
                            <div class="bg-gray-100 rounded-lg px-4 py-2 flex justify-between items-center mb-1">
                                <div class="flex items-center gap-2">
                                    @if ($iconPath)
                                        <img src="{{ $iconPath }}" alt="遺物アイコン" class="w-6 h-6">
                                    @endif
                                    <span class="font-semibold text-sm">{{ $relic['name'] ?? '不明な遺物' }}</span>
                                </div>
                                <div class="text-xs text-right text-gray-700">
                                    メイン: {{ number_format($m, 1) }} /
                                    サブ: {{ number_format($s, 1) }} /
                                    合計: <span class="font-bold {{ $color }}">{{ number_format($t, 1) }}</span>
                                    <span class="ml-1 {{ $color }}">{{ $grade }}</span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</body>

</html> --}}

{{-- <!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>キャラクター一覧</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        function toggleDetail(index) {
            const detail = document.getElementById(`char-detail-${index}`);
            detail.classList.toggle('hidden');
        }
    </script>
</head>

<body class="bg-blue-100 min-h-screen p-6">
    <h2 class="text-3xl font-bold mb-6 text-center">キャラクター一覧</h2>

    <!-- 重み設定フォーム -->
    <form method="GET" class="mb-8 max-w-4xl mx-auto bg-white p-4 rounded-xl shadow-md">
        <h3 class="text-xl font-bold mb-4">サブステータス重み設定</h3>
        @foreach ($characters as $char)
            @php $charId = $char['id']; @endphp
            <details class="mb-4">
                <summary class="cursor-pointer font-semibold mb-2">
                    {{ $char['name'] }}（ID: {{ $charId }}）
                </summary>
                <div class="grid grid-cols-2 gap-4 p-2">
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
                            <label class="mr-2 text-sm w-24">{{ $label }}</label>
                            <input type="number" step="0.1" min="0" max="1"
                                   name="weights[{{ $charId }}][{{ $key }}]"
                                   value="{{ request('weights')[$charId][$key] ?? 0 }}"
                                   class="w-20 text-right rounded-md border border-gray-300 px-2 py-1 text-sm">
                        </div>
                    @endforeach
                </div>
            </details>
        @endforeach
        <div class="text-center">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                スコアを再計算
            </button>
        </div>
    </form>

    <!-- キャラクター一覧 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-7xl mx-auto">
        @foreach ($characters as $index => $char)
            <div class="relative overflow-hidden rounded-2xl shadow-lg">
                <button onclick="toggleDetail({{ $index }})"
                        class="block w-full h-64 bg-contain bg-no-repeat bg-center"
                        style="background-image: url('{{ asset('./' . $char['portrait']) }}');">
                </button>

                <div id="char-detail-{{ $index }}" class="p-4 bg-white rounded-b-2xl hidden">
                    <h3 class="text-xl font-bold mb-2">{{ $char['name'] }} Lv.{{ $char['level'] }}</h3>

                    <!-- スコアまとめ -->
                    @php
                        $totalScore = array_sum(array_column(array_column($char['relics'], 'score'), 'total'));
                        $scoreGrade = match (true) {
                            $totalScore >= 600 => 'SS',
                            $totalScore >= 540 => 'S',
                            $totalScore >= 360 => 'A',
                            $totalScore >= 240 => 'B',
                            $totalScore >= 60  => 'C',
                            default => 'D',
                        };
                        $scoreColor = match (true) {
                            $totalScore >= 600 => 'text-yellow-500',
                            $totalScore >= 540 => 'text-purple-500',
                            $totalScore >= 360 => 'text-blue-500',
                            $totalScore >= 240 => 'text-green-500',
                            $totalScore >= 60  => 'text-gray-500',
                            default => 'text-red-500',
                        };
                    @endphp
                    <div class="text-center mb-4">
                        <div class="text-sm text-gray-700">総合スコア（6部位合算）</div>
                        <div class="text-2xl font-bold {{ $scoreColor }}">
                            {{ number_format($totalScore, 1) }} <span class="ml-1 text-base">{{ $scoreGrade }}</span>
                        </div>
                    </div>

                    <!-- 遺物一覧 -->
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
                            <div class="bg-gray-100 rounded-lg px-4 py-2 flex justify-between items-center mb-1">
                                <div class="flex items-center gap-2">
                                    @if ($iconPath)
                                        <img src="{{ $iconPath }}" alt="遺物アイコン" class="w-6 h-6">
                                    @endif
                                    <span class="font-semibold text-sm">{{ $relic['name'] ?? '不明な遺物' }}</span>
                                </div>
                                <div class="text-xs text-right text-gray-700">
                                    メイン: {{ number_format($m, 1) }} /
                                    サブ: {{ number_format($s, 1) }} /
                                    合計: <span class="font-bold {{ $color }}">{{ number_format($t, 1) }}</span>
                                    <span class="ml-1 {{ $color }}">{{ $grade }}</span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</body>

</html> --}}

{{-- <!DOCTYPE html>
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
                    "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').content,
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

                return `
                    <div class="bg-gray-100 rounded-lg px-4 py-2 flex justify-between items-center mb-1">
                        <div class="flex items-center gap-2">
                            ${r.icon ? `<img src="./${r.icon}" class="w-6 h-6" />` : ''}
                            <span class="font-semibold text-sm">${r.name}</span>
                        </div>
                        <div class="text-xs text-right text-gray-700">
                            メイン: ${main} / サブ: ${sub} / 合計: <span class="font-bold ${color}">${total}</span>
                            <span class="ml-1 ${color}">${grade}</span>
                        </div>
                    </div>
                `;
            }).join('');
        }
    </script>
</head>

<body class="bg-blue-100 min-h-screen p-6">
    <h2 class="text-3xl font-bold mb-6 text-center">キャラクター一覧</h2>

    <form method="GET" class="mb-8 max-w-4xl mx-auto bg-white p-4 rounded-xl shadow-md">
        <h3 class="text-xl font-bold mb-4">サブステータス重み設定</h3>
        @foreach ($characters as $char)
            @php $charId = $char['id']; @endphp
            <details class="mb-4">
                <summary class="cursor-pointer font-semibold mb-2">{{ $char['name'] }}（ID: {{ $charId }}）</summary>
                <div class="grid grid-cols-2 gap-4 p-2">
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
                            <label class="mr-2 text-sm w-24">{{ $label }}</label>
                            <input type="number" step="0.1" min="0" max="1"
                                name="weights[{{ $charId }}][{{ $key }}]"
                                value="{{ request('weights')[$charId][$key] ?? 0 }}"
                                onchange="onWeightChange({{ $charId }})"
                                class="w-20 text-right rounded-md border border-gray-300 px-2 py-1 text-sm">
                        </div>
                    @endforeach
                </div>
            </details>
        @endforeach
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-7xl mx-auto">
        @foreach ($characters as $index => $char)
            <div class="relative overflow-hidden rounded-2xl shadow-lg">
                <button onclick="toggleDetail({{ $index }})"
                    class="block w-full h-64 bg-contain bg-no-repeat bg-center"
                    style="background-image: url('{{ asset('./' . $char['portrait']) }}');">
                </button>

                <div id="char-detail-{{ $index }}" class="p-4 bg-white rounded-b-2xl hidden">
                    <h3 class="text-xl font-bold mb-2">{{ $char['name'] }} Lv.{{ $char['level'] }}</h3>

                    @php
                        $totalScore = array_sum(array_column(array_column($char['relics'], 'score'), 'total'));
                        $scoreGrade = match (true) {
                            $totalScore >= 600 => 'SS', $totalScore >= 540 => 'S', $totalScore >= 360 => 'A',
                            $totalScore >= 240 => 'B', $totalScore >= 60 => 'C', default => 'D'
                        };
                        $scoreColor = match (true) {
                            $totalScore >= 600 => 'text-yellow-500', $totalScore >= 540 => 'text-purple-500',
                            $totalScore >= 360 => 'text-blue-500', $totalScore >= 240 => 'text-green-500',
                            $totalScore >= 60 => 'text-gray-500', default => 'text-red-500'
                        };
                    @endphp
                    <div class="text-center mb-4">
                        <div class="text-sm text-gray-700">総合スコア（6部位合算）</div>
                        <div class="text-2xl font-bold {{ $scoreColor }}">
                            {{ number_format($totalScore, 1) }} <span class="ml-1 text-base">{{ $scoreGrade }}</span>
                        </div>
                    </div>

                    <div id="relic-list-{{ $char['id'] }}">
                        @foreach ($char['relics'] as $relic)
                            @if (isset($relic['score']))
                                @php
                                    $t = $relic['score']['total'] ?? 0;
                                    $m = $relic['score']['main'] ?? 0;
                                    $s = $relic['score']['sub'] ?? 0;
                                    $grade = match (true) {
                                        $t >= 100 => 'SS', $t >= 90 => 'S', $t >= 60 => 'A',
                                        $t >= 40 => 'B', $t >= 10 => 'C', default => 'D'
                                    };
                                    $color = match (true) {
                                        $t >= 100 => 'text-yellow-500', $t >= 90 => 'text-purple-500',
                                        $t >= 60 => 'text-blue-500', $t >= 40 => 'text-green-500',
                                        $t >= 10 => 'text-gray-500', default => 'text-red-500'
                                    };
                                    $iconPath = isset($relic['icon']) ? asset('./' . $relic['icon']) : '';
                                @endphp
                                <div class="bg-gray-100 rounded-lg px-4 py-2 flex justify-between items-center mb-1">
                                    <div class="flex items-center gap-2">
                                        @if ($iconPath)
                                            <img src="{{ $iconPath }}" class="w-6 h-6" alt="遺物アイコン">
                                        @endif
                                        <span class="font-semibold text-sm">{{ $relic['name'] }}</span>
                                    </div>
                                    <div class="text-xs text-right text-gray-700">
                                        メイン: {{ number_format($m, 1) }} / サブ: {{ number_format($s, 1) }} /
                                        合計: <span class="font-bold {{ $color }}">{{ number_format($t, 1) }}</span>
                                        <span class="ml-1 {{ $color }}">{{ $grade }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        window.relicsByCharId = @json(collect($characters)->mapWithKeys(fn($c) => [$c['id'] => $c['relics']])->toArray());
    </script>
</body>

</html> --}}

{{-- <!DOCTYPE html>
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

        // 👇 asset() を使ってルート絶対パス化
        const iconUrl = r.icon ? `{{ asset('') }}` + r.icon : '';

        return `
            <div class="bg-gray-100 rounded-lg px-4 py-2 flex justify-between items-center mb-1">
                <div class="flex items-center gap-2">
                    ${r.icon ? `<img src="${iconUrl}" class="w-6 h-6" />` : ''}
                    <span class="font-semibold text-sm">${r.name}</span>
                </div>
                <div class="text-xs text-right text-gray-700">
                    メイン: ${main} / サブ: ${sub} / 合計: <span class="font-bold ${color}">${total}</span>
                    <span class="ml-1 ${color}">${grade}</span>
                </div>
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

<body class="bg-blue-100 min-h-screen p-6">
    <h2 class="text-3xl font-bold mb-6 text-center">キャラクター一覧</h2>

   <!-- 重み設定フォーム -->
<form method="GET" class="mb-8 max-w-4xl mx-auto bg-white p-4 rounded-xl shadow-md">
    <h3 class="text-xl font-bold mb-4">サブステータス重み設定</h3>
    @foreach ($characters as $char)
        @php $charId = $char['id']; @endphp
        <details class="mb-4">
            <summary class="cursor-pointer font-semibold mb-2 flex items-center gap-2">
                <img src="{{ asset('./icon/character/' . $charId . '.png') }}" alt="キャラ画像" class="w-8 h-8 rounded">
                {{ $char['name'] }}（ID: {{ $charId }}）
            </summary>
            <div class="grid grid-cols-2 gap-4 p-2">
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
                        <label class="mr-2 text-sm w-24">{{ $label }}</label>
                        <input type="number" step="0.1" min="0" max="1"
                               name="weights[{{ $charId }}][{{ $key }}]"
                               value="{{ request('weights')[$charId][$key] ?? 0 }}"
                               onchange="onWeightChange({{ $charId }})"
                               class="w-20 text-right rounded-md border border-gray-300 px-2 py-1 text-sm">
                    </div>
                @endforeach
            </div>
        </details>
    @endforeach
</form>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-7xl mx-auto">
        @foreach ($characters as $index => $char)
            <div class="relative overflow-hidden rounded-2xl shadow-lg">
                <button onclick="toggleDetail({{ $index }})"
                        class="block w-full h-64 bg-contain bg-no-repeat bg-center"
                        style="background-image: url('{{ asset('./' . $char['portrait']) }}');">
                </button>

                <div id="char-detail-{{ $index }}" class="p-4 bg-white rounded-b-2xl hidden">
                    <h3 class="text-xl font-bold mb-2">{{ $char['name'] }} Lv.{{ $char['level'] }}</h3>

                    <div class="text-center mb-4">
                        <div class="text-sm text-gray-700">総合スコア（6部位合算）</div>
                        <div id="total-score-{{ $char['id'] }}" class="text-2xl font-bold text-gray-500">
                            {{ number_format(array_sum(array_column(array_column($char['relics'], 'score'), 'total')), 1) }}
                        </div>
                        <div id="total-grade-{{ $char['id'] }}" class="ml-1 text-base text-gray-500">
                            {{ match(true) {
                                array_sum(array_column(array_column($char['relics'], 'score'), 'total')) >= 600 => 'SS',
                                array_sum(array_column(array_column($char['relics'], 'score'), 'total')) >= 540 => 'S',
                                array_sum(array_column(array_column($char['relics'], 'score'), 'total')) >= 360 => 'A',
                                array_sum(array_column(array_column($char['relics'], 'score'), 'total')) >= 240 => 'B',
                                array_sum(array_column(array_column($char['relics'], 'score'), 'total')) >= 60 => 'C',
                                default => 'D'
                            } }}
                        </div>
                    </div>

                    <div id="relic-list-{{ $char['id'] }}">
                        @foreach ($char['relics'] as $relic)
                            @if (isset($relic['score']))
                                @php
                                    $t = $relic['score']['total'] ?? 0;
                                    $m = $relic['score']['main'] ?? 0;
                                    $s = $relic['score']['sub'] ?? 0;
                                    $grade = match (true) {
                                        $t >= 100 => 'SS', $t >= 90 => 'S', $t >= 60 => 'A',
                                        $t >= 40 => 'B', $t >= 10 => 'C', default => 'D'
                                    };
                                    $color = match (true) {
                                        $t >= 100 => 'text-yellow-500', $t >= 90 => 'text-purple-500',
                                        $t >= 60 => 'text-blue-500', $t >= 40 => 'text-green-500',
                                        $t >= 10 => 'text-gray-500', default => 'text-red-500'
                                    };
                                    $iconPath = isset($relic['icon']) ? asset('./' . $relic['icon']) : '';
                                @endphp
                                <div class="bg-gray-100 rounded-lg px-4 py-2 flex justify-between items-center mb-1">
                                    <div class="flex items-center gap-2">
                                        @if ($iconPath)
                                            <img src="{{ $iconPath }}" class="w-6 h-6" alt="遺物アイコン">
                                        @endif
                                        <span class="font-semibold text-sm">{{ $relic['name'] }}</span>
                                    </div>
                                    <div class="text-xs text-right text-gray-700">
                                        メイン: {{ number_format($m, 1) }} / サブ: {{ number_format($s, 1) }} /
                                        合計: <span class="font-bold {{ $color }}">{{ number_format($t, 1) }}</span>
                                        <span class="ml-1 {{ $color }}">{{ $grade }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        window.relicsByCharId = @json(collect($characters)->mapWithKeys(fn($c) => [$c['id'] => $c['relics']])->toArray());
    </script>
</body>

</html> --}}

{{-- <!DOCTYPE html>
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

                return `
                    <div class="bg-gray-100 rounded-lg px-3 py-2 flex justify-between items-center mb-1">
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

<body class="bg-blue-100 min-h-screen p-4 sm:p-6">
    <h2 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6 text-center">キャラクター一覧</h2>

    <!-- 重み設定フォーム -->
    <form method="GET" class="mb-6 max-w-4xl mx-auto bg-white p-4 rounded-xl shadow-md">
        <h3 class="text-lg sm:text-xl font-bold mb-3">サブステータス重み設定</h3>
        @foreach ($characters as $char)
            @php $charId = $char['id']; @endphp
            <details class="mb-4">
                <summary class="cursor-pointer font-semibold mb-2 flex items-center gap-2">
                    <img src="{{ asset('./icon/character/' . $charId . '.png') }}" alt="キャラ画像" class="w-8 h-8 rounded">
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

    <!-- キャラクターカード一覧 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 max-w-7xl mx-auto">
        @foreach ($characters as $index => $char)
            <div class="overflow-hidden rounded-2xl shadow-lg bg-white">
                <button onclick="toggleDetail({{ $index }})"
                        class="block w-full h-48 md:h-64 bg-contain bg-no-repeat bg-center"
                        style="background-image: url('{{ asset('./' . $char['portrait']) }}');">
                </button>

                <div id="char-detail-{{ $index }}" class="p-4 hidden">
                    <h3 class="text-lg sm:text-xl font-bold mb-2">{{ $char['name'] }} Lv.{{ $char['level'] }}</h3>

                    <div class="text-center mb-3">
                        <div class="text-xs text-gray-700">総合スコア（6部位合算）</div>
                        <div id="total-score-{{ $char['id'] }}" class="text-2xl font-bold text-gray-500">
                            {{ number_format(array_sum(array_column(array_column($char['relics'], 'score'), 'total')), 1) }}
                        </div>
                        <div id="total-grade-{{ $char['id'] }}" class="ml-1 text-base text-gray-500">
                            {{ match(true) {
                                array_sum(array_column(array_column($char['relics'], 'score'), 'total')) >= 600 => 'SS',
                                array_sum(array_column(array_column($char['relics'], 'score'), 'total')) >= 540 => 'S',
                                array_sum(array_column(array_column($char['relics'], 'score'), 'total')) >= 360 => 'A',
                                array_sum(array_column(array_column($char['relics'], 'score'), 'total')) >= 240 => 'B',
                                array_sum(array_column(array_column($char['relics'], 'score'), 'total')) >= 60 => 'C',
                                default => 'D'
                            } }}
                        </div>
                    </div>

                    <div id="relic-list-{{ $char['id'] }}">
                        @foreach ($char['relics'] as $relic)
                            @if (isset($relic['score']))
                                @php
                                    $t = $relic['score']['total'] ?? 0;
                                    $m = $relic['score']['main'] ?? 0;
                                    $s = $relic['score']['sub'] ?? 0;
                                    $grade = match (true) {
                                        $t >= 100 => 'SS', $t >= 90 => 'S', $t >= 60 => 'A',
                                        $t >= 40 => 'B', $t >= 10 => 'C', default => 'D'
                                    };
                                    $color = match (true) {
                                        $t >= 100 => 'text-yellow-500', $t >= 90 => 'text-purple-500',
                                        $t >= 60 => 'text-blue-500', $t >= 40 => 'text-green-500',
                                        $t >= 10 => 'text-gray-500', default => 'text-red-500'
                                    };
                                    $iconPath = isset($relic['icon']) ? asset('./' . $relic['icon']) : '';
                                @endphp
                                <div class="bg-gray-100 rounded-lg px-3 py-2 flex justify-between items-center mb-1">
                                    <div class="flex items-center gap-2">
                                        @if ($iconPath)
                                            <img src="{{ $iconPath }}" class="w-6 h-6" alt="遺物アイコン">
                                        @endif
                                        <span class="font-semibold text-xs">{{ $relic['name'] }}</span>
                                    </div>
                                    <div class="text-xs text-right text-gray-700">
                                        メイン: {{ number_format($m, 1) }} / サブ: {{ number_format($s, 1) }}<br>
                                        合計: <span class="font-bold {{ $color }}">{{ number_format($t, 1) }}</span>
                                        <span class="{{ $color }}">{{ $grade }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        window.relicsByCharId = @json(collect($characters)->mapWithKeys(fn($c) => [$c['id'] => $c['relics']])->toArray());
    </script>
</body>
</html> --}}


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
        const mainAffixHtml = r.main_affix
            ? `<div class="text-xs text-gray-700 mt-1">メイン効果: ${r.main_affix.name} ${r.main_affix.display}</div>`
            : '';

        // サブステータスの表示
        const subAffixHtml = r.sub_affix?.length
            ? `<div class="grid grid-cols-2 gap-1 mt-1 text-xs text-gray-600">
                    ${r.sub_affix.map(sub => `<div>${sub.name}: ${sub.display}</div>`).join('')}
               </div>`
            : '';

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

<body class="bg-blue-100 min-h-screen p-4 sm:p-6">
    <h2 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6 text-center">キャラクター一覧</h2>

    <!-- 重み設定フォーム -->
    <form method="GET" class="mb-6 max-w-4xl mx-auto bg-white p-4 rounded-xl shadow-md">
        <h3 class="text-lg sm:text-xl font-bold mb-3">サブステータス重み設定</h3>
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

    <!-- キャラカード一覧 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 max-w-7xl mx-auto">
        @foreach ($characters as $index => $char)
            <div class="overflow-hidden rounded-2xl shadow-lg bg-white">
                <button onclick="toggleDetail({{ $index }})"
                    class="block w-full h-48 md:h-64 bg-contain bg-no-repeat bg-center"
                    style="background-image: url('{{ asset('./' . $char['portrait']) }}');">
                </button>

                <div id="char-detail-{{ $index }}" class="p-4 hidden">
                    <h3 class="text-lg sm:text-xl font-bold mb-2">{{ $char['name'] }} Lv.{{ $char['level'] }}</h3>

                    @if (isset($char['light_cone']))
                        <div class="flex items-start gap-3 mb-4">
                            <img src="{{ asset('./' . $char['light_cone']['portrait']) }}" alt="光円錐画像"
                                class="w-16 h-24 object-contain rounded shadow-md">
                            <div>
                                <div class="text-sm font-bold">{{ $char['light_cone']['name'] }}</div>
                                <div class="text-xs text-gray-600">
                                    Lv.{{ $char['light_cone']['level'] }} / 重ね: {{ $char['light_cone']['rank'] }}
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
                                            メイン: {{ number_format($m, 1) }} / サブ: {{ number_format($s, 1) }}<br>
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

    <script>
        window.relicsByCharId = @json(collect($characters)->mapWithKeys(fn($c) => [$c['id'] => $c['relics']])->toArray());
    </script>
    <footer class="footer">
        @include('layouts.footer')
    </footer>
</body>

</html>
