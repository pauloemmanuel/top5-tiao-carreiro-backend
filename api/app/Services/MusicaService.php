<?php

namespace App\Services;

use App\Repositories\Interface\MusicaRepositoryInterface;
use App\Models\Musica;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MusicaService
{
    private MusicaRepositoryInterface $repo;

    public function __construct(MusicaRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->paginateAtivas($perPage);
    }

    public function top5()
    {
        return $this->repo->top5()->get();
    }

    public function demais(int $perPage = 10): LengthAwarePaginator
    {
        return $this->repo->paginateDemais($perPage);
    }

    public function store(array $data): Musica
    {
        return $this->repo->create($data);
    }

    public function show(int $id): ?Musica
    {
        return $this->repo->find($id);
    }

    public function update(Musica $musica, array $data): Musica
    {
        return $this->repo->update($musica, $data);
    }

    public function delete(Musica $musica): bool
    {
        return $this->repo->delete($musica);
    }
}
