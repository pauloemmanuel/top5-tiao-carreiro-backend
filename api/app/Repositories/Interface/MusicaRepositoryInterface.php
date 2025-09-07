<?php

namespace App\Repositories\Interface;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Musica;

interface MusicaRepositoryInterface
{
    public function paginateAtivas(int $perPage = 15): LengthAwarePaginator;
    public function top5();
    public function paginateDemais(int $perPage = 10): LengthAwarePaginator;
    public function create(array $data): Musica;
    public function find(int $id): ?Musica;
    public function update(Musica $musica, array $data): Musica;
    public function delete(Musica $musica): bool;
}
