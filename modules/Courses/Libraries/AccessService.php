<?php namespace Modules\Courses\Libraries;

use Modules\Courses\Models\AccessLevelModel;

/**
 * Decisão central de acesso ao conteúdo (§7.3 do plano — "acesso único estilo AUVP").
 *
 * canAccess = (conteúdo público) OU (usuário tem o nível manual exigido) OU (membership ativo).
 * Na Fase 4a implementamos as duas primeiras; o membership é um SEAM: hasActiveMembership()
 * retorna false por enquanto e será preenchido na Fase 4b (gateway + webhooks + regra de corte).
 */
class AccessService
{
    protected AccessLevelModel $levels;

    public function __construct()
    {
        $this->levels = new AccessLevelModel();
    }

    /** Nível de acesso efetivo de uma aula: override da aula, senão o do curso (null = livre). */
    public function effectiveLevelId(array $lesson, array $course): ?int
    {
        $lid = $lesson['access_level_id'] ?? null;
        if ($lid !== null && (int) $lid > 0) {
            return (int) $lid;
        }
        $cid = $course['access_level_id'] ?? null;
        return ($cid !== null && (int) $cid > 0) ? (int) $cid : null;
    }

    public function canAccessCourse(int $userId, array $course): bool
    {
        $required = $course['access_level_id'] ?? null;
        return $this->passes($userId, $required !== null ? (int) $required : null);
    }

    public function canAccessLesson(int $userId, array $lesson, array $course): bool
    {
        // aula-amostra (free preview) é sempre acessível — o "episódio grátis" estilo Netflix
        if (!empty($lesson['is_free_preview'])) {
            return true;
        }
        return $this->passes($userId, $this->effectiveLevelId($lesson, $course));
    }

    /** Núcleo: público OU rank suficiente OU membership ativo. */
    protected function passes(int $userId, ?int $requiredLevelId): bool
    {
        // conteúdo público/free
        if ($requiredLevelId === null || $requiredLevelId <= 0) {
            return true;
        }
        if ($userId <= 0) {
            return false;
        }
        // membership ativo desbloqueia tudo (seam da Fase 4b)
        if ($this->hasActiveMembership($userId)) {
            return true;
        }
        // nível manual: rank do usuário >= rank exigido
        $requiredRank = $this->levels->rankOf($requiredLevelId);
        return $this->levels->maxRankForUser($userId) >= $requiredRank;
    }

    /**
     * SEAM da Fase 4b: membership anual (paid|client_comp). Enquanto o sub-módulo de
     * pagamento não existe, ninguém tem membership → acesso pago vem só de nível manual.
     * A Fase 4b troca este corpo por uma checagem na tabela `memberships` (status ativo).
     */
    public function hasActiveMembership(int $userId): bool
    {
        return false;
    }
}
