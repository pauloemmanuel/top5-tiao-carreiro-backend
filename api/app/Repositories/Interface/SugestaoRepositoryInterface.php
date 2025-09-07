<?php

namespace App\Repositories\Interface;

use App\Models\Sugestao;

interface SugestaoRepositoryInterface
{
    public function all();
    public function create(array $data): Sugestao;
    public function find(int $id): ?Sugestao;
    public function update(Sugestao $sugestao, array $data): Sugestao;
    public function delete(Sugestao $sugestao): bool;

    public function paginate(int $perPage = 15, ?string $status = null);
    public function existsByYoutubeId(string $youtubeId, array $ignoreStatus = []): bool;
    public function aprovar(Sugestao $sugestao, \App\Models\User $user, ?string $observacoes = null);
    public function converterParaMusica(Sugestao $sugestao);
    public function rejeitar(Sugestao $sugestao, \App\Models\User $user, ?string $observacoes = null): void;
}
