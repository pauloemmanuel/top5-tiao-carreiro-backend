<?php

namespace App\Repositories;

use App\Models\Sugestao;

interface SugestaoRepositoryInterface
{
    public function all();
    public function create(array $data): Sugestao;
    public function find(int $id): ?Sugestao;
    public function update(Sugestao $sugestao, array $data): Sugestao;
    public function delete(Sugestao $sugestao): bool;
}
