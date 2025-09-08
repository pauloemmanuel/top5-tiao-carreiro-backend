<?php

namespace App\Repositories;

use App\Models\Musica;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Repositories\Interface\MusicaRepositoryInterface;

class EloquentMusicaRepository implements MusicaRepositoryInterface
{
    public function paginateAtivas(int $perPage = 15): LengthAwarePaginator
    {
        return Musica::ativas()->ordenadaPorVisualizacoes()->paginate($perPage);
    }

    public function top5()
    {
        return Musica::top5();
    }

    public function paginateDemais(int $perPage = 10): LengthAwarePaginator
    {
        $topIds = Musica::ativas()
            ->ordenadaPorVisualizacoes()
            ->limit(5)
            ->pluck('id')
            ->toArray();

        return Musica::ativas()
            ->ordenadaPorVisualizacoes()
            ->whereNotIn('id', $topIds)
            ->paginate($perPage);
    }

    public function create(array $data): Musica
    {
        return Musica::create($data);
    }

    public function find(int $id): ?Musica
    {
        return Musica::find($id);
    }

    public function update(Musica $musica, array $data): Musica
    {
        $musica->update($data);
        return $musica->fresh();
    }

    public function delete(Musica $musica): bool
    {
        return (bool) $musica->delete();
    }
}
