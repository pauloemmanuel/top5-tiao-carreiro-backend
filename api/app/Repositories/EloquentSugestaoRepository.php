<?php

namespace App\Repositories;

use App\Repositories\Interface\SugestaoRepositoryInterface;
use App\Models\Sugestao;
use App\Models\User;
use App\Models\Musica;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentSugestaoRepository implements SugestaoRepositoryInterface
{
    public function all()
    {
        return Sugestao::all();
    }

    public function create(array $data): Sugestao
    {
        return Sugestao::create($data);
    }

    public function find(int $id): ?Sugestao
    {
        return Sugestao::find($id);
    }

    public function update(Sugestao $sugestao, array $data): Sugestao
    {
        $sugestao->update($data);
        return $sugestao;
    }

    public function delete(Sugestao $sugestao): bool
    {
        return $sugestao->delete();
    }

    public function paginate(int $perPage = 15, ?string $status = null): LengthAwarePaginator
    {
        $query = Sugestao::query();
        if ($status) {
            $query->where('status', $status);
        }
        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function existsByYoutubeId(string $youtubeId, array $ignoreStatus = []): bool
    {
        $query = Sugestao::where('youtube_id', $youtubeId);
        if (!empty($ignoreStatus)) {
            $query->whereNotIn('status', $ignoreStatus);
        }
        return $query->exists();
    }

    public function aprovar(Sugestao $sugestao, User $user, ?string $observacoes = null)
    {
        $sugestao->status = 'aprovada';
        $sugestao->user_id = $user->id;
        $sugestao->observacoes = $observacoes;
        $sugestao->save();
    }

    public function converterParaMusica(Sugestao $sugestao): Musica
    {
        return Musica::create([
            'titulo' => $sugestao->titulo,
            'youtube_id' => $sugestao->youtube_id,
            'thumb' => $sugestao->thumb,
            'visualizacoes' => $sugestao->visualizacoes,
        ]);
    }

    public function rejeitar(Sugestao $sugestao, User $user, ?string $observacoes = null)
    {
        $sugestao->status = 'rejeitada';
        $sugestao->user_id = $user->id;
        $sugestao->observacoes = $observacoes;
        $sugestao->save();
    }
}
