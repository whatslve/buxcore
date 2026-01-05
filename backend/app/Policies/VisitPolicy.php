<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Visit;

class VisitPolicy
{
    // Просмотр списка
    public function viewAny(User $user): bool
    {
        return true; // или своя логика
    }

    // Просмотр конкретной записи
    public function view(User $user, Visit $visit): bool
    {
        return $visit->user_id === $user->id;
    }

    // Создание
    public function create(User $user): bool
    {
        return true; // любой авторизованный может создавать
    }

    // Обновление
    public function update(User $user, Visit $visit): bool
    {
        return $visit->user_id === $user->id;
    }

    // Удаление
    public function delete(User $user, Visit $visit): bool
    {
        return $visit->user_id === $user->id;
    }
}
