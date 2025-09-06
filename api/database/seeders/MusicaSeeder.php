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
                'titulo' => 'Terra roxa',
                'visualizacoes' => 3300000,
                'youtube_id' => '4Nb89GFu2g4',
                'thumb' => 'https://img.youtube.com/vi/4Nb89GFu2g4/hqdefault.jpg',
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
                'titulo' => 'Rio de Lágrimas',
                'visualizacoes' => 153000,
                'youtube_id' => 'FxXXvPL3JIg',
                'thumb' => 'https://img.youtube.com/vi/FxXXvPL3JIg/hqdefault.jpg',
                'status' => 'ativa'
            ],
            [
                'titulo' => 'Rei do Gado',
                'visualizacoes' => 2100000,
                'youtube_id' => 'XeVZPRjGkG4',
                'thumb' => 'https://img.youtube.com/vi/XeVZPRjGkG4/hqdefault.jpg',
                'status' => 'ativa'
            ],
            [
                'titulo' => 'Cabocla Teresa',
                'visualizacoes' => 1800000,
                'youtube_id' => 'nEBMHcTZQp0',
                'thumb' => 'https://img.youtube.com/vi/nEBMHcTZQp0/hqdefault.jpg',
                'status' => 'ativa'
            ],
            [
                'titulo' => 'Índia',
                'visualizacoes' => 1500000,
                'youtube_id' => 'dq8YcRi7DD4',
                'thumb' => 'https://img.youtube.com/vi/dq8YcRi7DD4/hqdefault.jpg',
                'status' => 'ativa'
            ],
            [
                'titulo' => 'Chico Mineiro',
                'visualizacoes' => 1200000,
                'youtube_id' => 'WKGJ7rV3nX8',
                'thumb' => 'https://img.youtube.com/vi/WKGJ7rV3nX8/hqdefault.jpg',
                'status' => 'ativa'
            ],
            [
                'titulo' => 'Boi Soberano',
                'visualizacoes' => 980000,
                'youtube_id' => 'lNDjJUz-LB4',
                'thumb' => 'https://img.youtube.com/vi/lNDjJUz-LB4/hqdefault.jpg',
                'status' => 'ativa'
            ]
        ];

        foreach ($musicas as $musica) {
            Musica::create($musica);
        }
    }
}
