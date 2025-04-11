<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ゆるすた遺物帳</title>
</head>
<body class="bg-blue-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl shadow-md w-full max-w-md text-center">
        <h1 class="text-2xl font-bold mb-4">ゆるすた遺物帳</h1>
        <form method="GET" action="{{ route('player.show', ['uid' => 'dummy']) }}" onsubmit="return redirectToPlayer(event)">
            <input type="text" name="uid" id="uid-input" placeholder="UIDを入力" class="border rounded p-2 w-full mb-4" required>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">スコアを見に行く！</button>
        </form>
    </div>

    <script>
        function redirectToPlayer(event) {
            event.preventDefault();
            const uid = document.getElementById('uid-input').value;
            if (!uid) return false;
            window.location.href = `/player/${uid}`;
        }
    </script>
</body>
</html>