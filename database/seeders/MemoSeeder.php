<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class MemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('memos')->insert([

            [
                'version' => 'v1.2',
                'date' => '2025-04-24',
                'memo' => 'API使用不可時のセーブモードに対応（DBに値を保持）',
                'created_at' => Carbon::create('2025', '04', '08', '12', '00', '00'),
                'updated_at' => Carbon::create('2025', '04', '08', '12', '00', '00'),
            ],
            [
                'version' => 'v1.1',
                'date' => '2025-04-20',
                'memo' => 'トップページデザインを宇宙風にリニューアル＋キャラ画像表示。',
                'created_at' => Carbon::create('2025', '04', '08', '12', '00', '00'),
                'updated_at' => Carbon::create('2025', '04', '08', '12', '00', '00'),
            ],
            [
                'version' => 'v1.0',
                'date' => '2025-04-18',
                'memo' => '初回リリース。UIDから最大キャラ情報取得＋遺物スコア計算を実装。',
                'created_at' => Carbon::create('2025', '04', '07', '12', '00', '00'),
                'updated_at' => Carbon::create('2025', '04', '07', '12', '00', '00'),
            ],
        ]);
    }
}
