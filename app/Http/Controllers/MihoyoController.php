<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\RelicScoreService;
use App\Models\SavedPlayer;


class MihoyoController extends Controller
{
    protected $relicService;

    public function __construct(RelicScoreService $relicService)
    {
        $this->relicService = $relicService;
    }

    public function show(Request $request, $uid)
    {
        try {
            $response = Http::timeout(5)->get("https://api.mihomo.me/sr_info_parsed/{$uid}?lang=jp");

            if ($response->successful()) {
                $data = $response->json();

                // $data['player'] = [];
                // $data['characters'] = [];

                // APIは成功したが、player または characters が空の場合はDBから復元
                if (empty($data['player']) || empty($data['characters'])) {
                    throw new \Exception('APIレスポンスにプレイヤーまたはキャラ情報が含まれていません');
                }

                $player = $data['player'];
                $characters = $data['characters'];
                $service = new RelicScoreService();

                foreach ($characters as $index => $char) {
                    $charId = $char['id'];
                    $weights = $request->input("weights.$charId", []);
                    $totalScore = 0;

                    foreach ($char['relics'] ?? [] as $relicIndex => $relic) {
                        $score = $service->calculateRelicScore($relic, $weights);
                        $characters[$index]['relics'][$relicIndex]['score'] = $score;
                        $totalScore += $score['total'] ?? 0;
                    }

                    $characters[$index]['sumScore'] = round($totalScore, 1);
                    $characters[$index]['sumGrade'] = match (true) {
                        $totalScore >= 600 => 'SS',
                        $totalScore >= 540 => 'S',
                        $totalScore >= 360 => 'A',
                        $totalScore >= 240 => 'B',
                        $totalScore >= 60 => 'C',
                        default => 'D',
                    };
                    $characters[$index]['weights'] = $weights;
                }

                // 成功時にDB保存
                // SavedPlayer::updateOrCreate(
                //     ['uid' => $uid],
                //     ['player_data' => json_encode(['player' => $player, 'characters' => $characters], JSON_UNESCAPED_UNICODE)]
                // );

                SavedPlayer::updateOrCreate(
                    ['uid' => $uid],
                    [
                        'player_data' => json_encode(['player' => $player, 'characters' => $characters], JSON_UNESCAPED_UNICODE),
                        'updated_at' => now() // ← これで常に更新される
                    ]
                );

                return view('player', [
                    'player' => $player,
                    'characters' => $characters,
                    'mode' => 'api'
                ]);
            } else {
                throw new \Exception('APIが失敗ステータスを返しました');
            }
        } catch (\Throwable $e) {
            Log::error('API取得エラー', ['uid' => $uid, 'exception' => $e]);

            // DBから保存データを取得（あれば）
            $saved = SavedPlayer::where('uid', $uid)->first();
            if ($saved) {
                $savedData = json_decode($saved->player_data, true);
                $player = $savedData['player'] ?? null;
                $characters = $savedData['characters'] ?? [];

                $service = new RelicScoreService();

                foreach ($characters as $index => $char) {
                    $charId = $char['id'];
                    $weights = []; // 重みは保存されていない前提
                    $totalScore = 0;

                    foreach ($char['relics'] ?? [] as $relicIndex => $relic) {
                        $score = $service->calculateRelicScore($relic, $weights);
                        $characters[$index]['relics'][$relicIndex]['score'] = $score;
                        $totalScore += $score['total'] ?? 0;
                    }

                    $characters[$index]['sumScore'] = round($totalScore, 1);
                    $characters[$index]['sumGrade'] = match (true) {
                        $totalScore >= 600 => 'SS',
                        $totalScore >= 540 => 'S',
                        $totalScore >= 360 => 'A',
                        $totalScore >= 240 => 'B',
                        $totalScore >= 60 => 'C',
                        default => 'D',
                    };
                    $characters[$index]['weights'] = $weights;
                }

                return view('player', [
                    'player' => $player,
                    'characters' => $characters,
                    'mode' => 'saved'
                ]);
            }

            // DBにもない場合は完全に失敗
            return view('player', [
                'player' => null,
                'error' => '現在メンテナンス中です。しばらくしてから再度お試しください。',
                'mode' => 'error'
            ]);
        }
    }


    public function recalculate(Request $request)
    {
        $weights = $request->input('weights', []);
        $relics = $request->input('relics', []);

        $service = new RelicScoreService();

        foreach ($relics as &$relic) {
            $relic['score'] = $service->calculateRelicScore($relic, $weights);
        }

        return response()->json($relics);
    }

}
