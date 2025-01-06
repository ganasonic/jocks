<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ユーザーを追加
        User::insert([
        //User::create([
            [ 'name' => '社長', 'email' => 'ohno@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => '長尾美知子', 'email' => 'nagao@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 1],
            [ 'name' => '小林喜彦', 'email' => 'kobayashi@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => 'あべ', 'email' => 'abe@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => 'ゲン', 'email' => 'gen@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => 'ヒデ', 'email' => 'hide@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => 'さいとう', 'email' => 'saito@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => 'ナオミ', 'email' => 'naomi@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 1],
            [ 'name' => 'ムロ', 'email' => 'muro@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 1],
            [ 'name' => '長島', 'email' => 'gana@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => 'ヒロタニ', 'email' => 'hirotani@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => '坂本', 'email' => 'sakamoto@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 1],
            [ 'name' => '山本', 'email' => 'yamamoto@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => '平野', 'email' => 'hirano@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => 'ネギちゃん', 'email' => 'negi@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => '中村', 'email' => 'yocfumi@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => 'じんぼ', 'email' => 'jinbo@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => '安井', 'email' => 'yasui@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => 'ユータ', 'email' => 'yuta@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => '遠藤', 'email' => 'endo@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => 'ゆうき', 'email' => 'yuki@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => 'たけ', 'email' => 'take@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => '達郎', 'email' => 'tatsuro@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => 'JP', 'email' => 'jp@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 0],
            [ 'name' => 'ネギちゃん嫁', 'email' => 'negiyome@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 1],
            [ 'name' => '倉持心星', 'email' => 'kirari@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 48],
            [ 'name' => '今泉るか', 'email' => 'ruka@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 48],
            [ 'name' => '今泉ルイ', 'email' => 'rui@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 16],
            [ 'name' => '齊藤奏真', 'email' => 'soma@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 16],
            [ 'name' => '外山玲央', 'email' => 'reo@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 16],
            [ 'name' => '小川東吾', 'email' => 'togo@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 16],
            [ 'name' => '山口紅', 'email' => 'beni@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 48],
            [ 'name' => '心星パパ', 'email' => 'kira_pp@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 144],
            [ 'name' => '今泉パパ', 'email' => 'rui_pp@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 144],
            [ 'name' => '奏真パパ', 'email' => 'soma_pp@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 144],
            [ 'name' => '玲央パパ', 'email' => 'reo_pp@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 144],
            [ 'name' => '東吾パパ', 'email' => 'togo_pp@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 144],
            [ 'name' => '心星ママ', 'email' => 'kira_mm@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 176],
            [ 'name' => '今泉ママ', 'email' => 'rui_mm@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 176],
            [ 'name' => '奏真ママ', 'email' => 'soma_mm@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 176],
            [ 'name' => '玲央ママ', 'email' => 'reo_mm@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 176],
            [ 'name' => '東吾ママ', 'email' => 'togo_mm@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 176],
            [ 'name' => '紅パパ', 'email' => 'beni_pp@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 144],
            [ 'name' => '紅ママ', 'email' => 'beni_mm@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 176],
            [ 'name' => 'みずき', 'email' => 'mizuki@jocks.jp', 'password' => Hash::make('jocks'), 'property' => 1],

            //'name' => 'John Doe',
            //'email' => 'john@example.com',
            //'password' => Hash::make('password123'),  // パスワードはハッシュ化する必要があります
        ]);
    }
}
