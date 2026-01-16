<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnboardingChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'task_key',
        'title',
        'description',
        'is_completed',
        'completed_at',
        'order',
        'is_required',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'is_required' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the checklist item.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Mark this task as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark this task as incomplete.
     */
    public function markAsIncomplete(): void
    {
        $this->update([
            'is_completed' => false,
            'completed_at' => null,
        ]);
    }

    /**
     * Scope to get only completed tasks.
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope to get only incomplete tasks.
     */
    public function scopeIncomplete($query)
    {
        return $query->where('is_completed', false);
    }

    /**
     * Scope to get only required tasks.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope to order tasks by their order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get the default checklist tasks for a new tenant.
     */
    public static function getDefaultTasks(): array
    {
        return [
            [
                'task_key' => 'setup_branding',
                'title' => 'Configurar Branding',
                'description' => 'Configure o logotipo e as cores da sua marca',
                'order' => 1,
                'is_required' => true,
            ],
            [
                'task_key' => 'invite_users',
                'title' => 'Convidar Utilizadores',
                'description' => 'Adicione membros da sua equipa ao sistema',
                'order' => 2,
                'is_required' => true,
            ],
            [
                'task_key' => 'setup_permissions',
                'title' => 'Configurar Permissões',
                'description' => 'Defina os papéis e permissões dos utilizadores',
                'order' => 3,
                'is_required' => true,
            ],
            [
                'task_key' => 'create_first_entity',
                'title' => 'Criar Primeira Entidade',
                'description' => 'Crie a sua primeira entidade comercial',
                'order' => 4,
                'is_required' => false,
            ],
            [
                'task_key' => 'create_first_contact',
                'title' => 'Criar Primeiro Contacto',
                'description' => 'Adicione o seu primeiro contacto ou cliente',
                'order' => 5,
                'is_required' => false,
            ],
            [
                'task_key' => 'setup_articles',
                'title' => 'Configurar Artigos',
                'description' => 'Adicione os seus produtos ou serviços',
                'order' => 6,
                'is_required' => false,
            ],
            [
                'task_key' => 'configure_vat',
                'title' => 'Configurar Taxas de IVA',
                'description' => 'Configure as taxas de IVA aplicáveis ao seu negócio',
                'order' => 7,
                'is_required' => false,
            ],
        ];
    }

    /**
     * Create default checklist tasks for a tenant.
     */
    public static function createDefaultTasksForTenant(int $tenantId): void
    {
        $tasks = self::getDefaultTasks();

        foreach ($tasks as $task) {
            self::create(array_merge($task, ['tenant_id' => $tenantId]));
        }
    }

    /**
     * Get the completion percentage for a tenant.
     */
    public static function getCompletionPercentage(int $tenantId): int
    {
        $total = self::where('tenant_id', $tenantId)->count();

        if ($total === 0) {
            return 0;
        }

        $completed = self::where('tenant_id', $tenantId)
            ->where('is_completed', true)
            ->count();

        return (int) round(($completed / $total) * 100);
    }

    /**
     * Check if all required tasks are completed for a tenant.
     */
    public static function allRequiredTasksCompleted(int $tenantId): bool
    {
        return self::where('tenant_id', $tenantId)
            ->where('is_required', true)
            ->where('is_completed', false)
            ->doesntExist();
    }
}
