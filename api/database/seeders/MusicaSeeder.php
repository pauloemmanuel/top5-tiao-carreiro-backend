<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Musica;

class MusicaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $musicas = [
            [
                'titulo' => 'O Mineiro e o Italiano',
                'visualizacoes' => 5200000,
                'youtube_id' => 's9kVG2ZaTS4',
                'thumb' => 'https://img.youtube.com/vi/s9kVG2ZaTS4/hqdefault.jpg',
                'status' => 'ativa'
            ],
            [
                'titulo' => 'Pagode em Brasília',
                'visualizacoes' => 5000000,
                'youtube_id' => 'lpGGNA6_920',
                'thumb' => 'https://img.youtube.com/vi/lpGGNA6_920/hqdefault.jpg',
                'status' => 'ativa'
            ],
            [
                'titulo' => 'Rio de Lágrimas',
                'visualizacoes' => 153000,
                'youtube_id' => 'FxXXvPL3JIg',
                'thumb' => 'https://img.youtube.com/vi/FxXXvPL3JIg/hqdefault.jpg',
                'status' => 'ativa'
            ],
            [
                'titulo' => 'Tristeza do Jeca',
                'visualizacoes' => 154000,
                'youtube_id' => 'tRQ2PWlCcZk',
                'thumb' => 'https://img.youtube.com/vi/tRQ2PWlCcZk/hqdefault.jpg',
                'status' => 'ativa'
            ],
            [
                'titulo' => 'Terra roxa',
                'visualizacoes' => 3300000,
                'youtube_id' => '4Nb89GFu2g4',
                'thumb' => 'https://img.youtube.com/vi/4Nb89GFu2g4/hqdefault.jpg',
                'status' => 'ativa'
            ]
        ];

        foreach ($musicas as $musica) {
            Musica::create($musica);
        }
    }
}
