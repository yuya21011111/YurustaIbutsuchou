<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\RelicScoreService;

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

                if (!isset($data['player']) || !isset($data['characters'])) {
                    return view('player', [
                        'player' => null,
                        'error' => '現在メンテナンス中です。しばらくしてから再度お試しください。',
                    ]);
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

                return view('player', compact('player', 'characters'));
            } else {
                // APIが応答したけど status code が 200 以外
                return view('player', [
                    'player' => null,
                    'error' => '現在メンテナンス中です。しばらくしてから再度お試しください。',
                ]);
            }
        } catch (\Throwable $e) {
            // ここではエラー詳細はログにだけ残しておく
            \Log::error('API取得エラー', ['uid' => $uid, 'exception' => $e]);

            return view('player', [
                'player' => null,
                'error' => '現在メンテナンス中です。しばらくしてから再度お試しください。',
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
