<?php

namespace App\Models\Traits;

trait CascadeDelete
{
    protected static function bootCascadeDelete(): void
    {
        static::deleting(function ($model) {
            $model->runCascadeDelete();
        });

        if(method_exists(static::class, 'restoring')) {
            static::restoring(function ($model) {
                $model->runCascadeRestore();
            });
        }
    }

    protected function runCascadeDelete(): void
    {
        foreach ($this->getActiveCascadeDeletes() as $relationship) {
            $this->cascadeSoftDeletes($relationship);
        }
    }

    protected function runCascadeRestore(): void
    {
        foreach ($this->getCascadeDeletes() as $relationship) {
            $this->{$relationship}()->onlyTrashed()->get()->map(function ($model) {
                // Restore only relationships that has been cascade deleted
                // Avoid restore old deleted relationships
                if (!!$this->getAttribute('deleted_at') && $model->deleted_at?->betweenIncluded(
                        $this->getAttribute('deleted_at')->copy()->subSeconds(10),
                        $this->getAttribute('deleted_at')->copy()->addSeconds(10),
                    )) {
                    $model->restore();
                }
            });
        }
    }

    protected function getActiveCascadeDeletes(): array
    {
        return array_filter($this->getCascadeDeletes(), function ($relationship) {
            return $this->{$relationship}()->exists();
        });
    }

    protected function getCascadeDeletes(): array
    {
        return $this->cascadeDeletes ?? [];
    }

    protected function cascadeSoftDeletes($relationship): void
    {
        $delete = $this->forceDeleting ? 'forceDelete' : 'delete';

        foreach ($this->{$relationship}()->get() as $model) {
            $model->pivot ? $model->pivot->{$delete}() : $model->{$delete}();
        }
    }
}
