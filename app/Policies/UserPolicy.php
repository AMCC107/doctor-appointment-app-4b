<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can delete the model.
     * Un usuario no puede eliminarse a sí mismo.
     */
    public function delete(User $authUser, User $userToDelete): bool
    {
        // Retorna false si el usuario intenta eliminarse a sí mismo
        return $authUser->id !== $userToDelete->id;
    }
}
