<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ゆるすた遺物帳</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css'])
</head>

<body
    class="bg-gradient-to-b from-indigo-950 via-purple-900 to-black text-white min-h-screen font-sans relative overflow-x-hidden">

    <!-- 背景（星空画像） -->
    <div class="absolute inset-0 bg-cover opacity-20 z-0 min-h-screen"
        style="background-image: url('{{ asset('./img/stars-bg.png') }}');">
    </div>

    <!-- メインコンテンツ -->
    <div class="relative z-20 max-w-4xl mx-auto p-6 sm:p-10">

        <!-- タイトル＋キャラ画像 -->
        <div class="text-center mb-10">
            <h1 class="text-5xl font-bold text-yellow-300 mb-2 drop-shadow-lg">ゆるすた遺物帳</h1>

            <!-- キャラ画像 -->
            <img src="{{ asset('./img/10.png') }}" alt="パム"
                class="mx-auto mt-6 w-40 sm:w-48 md:w-56 lg:w-64 rounded-full shadow-lg border-4 border-indigo-300 bg-indigo-900/20" />
        </div>

        <!-- UID入力フォーム -->
        <div
            class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl shadow-xl p-6 sm:p-8 mb-10 text-white">
            <h2 class="text-xl font-semibold mb-4 text-center text-blue-200">📩 UIDを入力してスコアをチェック！【テストUID: 807624117】</h2>
            <form method="GET" action="{{ route('player.show', ['uid' => 'dummy']) }}"
                onsubmit="return redirectToPlayer(event)">
                <input type="text" name="uid" id="uid-input" placeholder="ゲーム内UIDを入力" required
                    class="w-full bg-black/30 text-white border border-blue-500 rounded-lg px-4 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold py-2 px-4 rounded-lg hover:brightness-110 transition">
                    スコアを見に行く！
                </button>
            </form>
        </div>

        <!-- 説明セクション -->
        <div
            class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl shadow p-6 sm:p-8 text-sm sm:text-base text-gray-200 leading-relaxed mb-10">
            <h2 class="text-xl font-bold text-yellow-200 mb-4">📖 ゆるすた遺物帳とは？</h2>
            <p class="mb-4">
                「ゆるすた遺物帳」は、<strong class="text-white">UIDを入力するだけ</strong>で、
                巡星ビザの最大キャラ情報を取得し、<span class="text-blue-300">6部位の遺物スコア</span>を自動で評価する非公式ツールです。
            </p>
            <p class="mb-4">
                スコア計算の基準は、
                <a href="https://game8.jp/houkaistarrail/625727" target="_blank"
                    class="text-blue-400 underline hover:text-blue-300">Game8様の評価値</a> に準拠。
                メインステは固定スコア1、サブは評価値に応じてスコアリングされます。
            </p>

            <h3 class="text-lg font-bold text-pink-200 mt-6 mb-2">⚠️ ご注意ください</h3>
            <ul class="list-disc list-inside space-y-1">
                <li>このツールは非公式です。<span class="font-semibold text-white">知人・友人間での共有はOK</span>ですが、<span
                        class="text-red-300 font-semibold">SNSなどへの無断公開は禁止</span>です。</li>
                <li>使用画像やデータはすべて原著作権者に帰属します。</li>
                <li>『崩壊: スターレイル』は COGNOSPHERE. の登録商標です。</li>
                <li>当ツールはCOGNOSPHERE.とは一切関係ありません。</li>
            </ul>
        </div>

        <!-- 更新履歴 -->
        <div
            class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl shadow p-6 sm:p-8 text-sm sm:text-base text-gray-200 leading-relaxed space-y-6 mb-20">
            <h2 class="text-xl font-bold text-blue-200">🛠 更新履歴</h2>

            <div class="space-y-4">
                @foreach($memos as $memo)
                <div class="border-l-4 border-indigo-400 pl-4">
                    <p class="text-sm text-gray-400">{{ $memo->date }}</p>
                    <p><span class="font-semibold text-white">{{ $memo->version }}</span> {{ $memo->memo }}</p>
                </div>
                @endforeach
                {{-- <div class="border-l-4 border-indigo-400 pl-4">
                    <p class="text-sm text-gray-400">2025/04/12</p>
                    <p><span class="font-semibold text-white">v1.1</span> - トップページデザインを宇宙風にリニューアル＋キャラ画像表示。</p>
                </div> --}}
                {{-- <div class="border-l-4 border-indigo-400 pl-4">
                    <p class="text-sm text-gray-400">2025/04/13</p>
                    <p><span class="font-semibold text-white">v1.2</span> - 更新履歴セクションを追加。スマホ表示時のレイアウト調整。</p>
                </div> --}}
            </div>
        </div>

    </div>

    <!-- UIDジャンプ処理 -->
    <script>
        function redirectToPlayer(event) {
            event.preventDefault();
            const uid = document.getElementById('uid-input').value;
            if (uid) {
                window.location.href = `/player/${uid}`;
            }
        }
    </script>
    <footer class="footer">
        @include('Layouts.footer')
    </footer>
</body>

</html>
