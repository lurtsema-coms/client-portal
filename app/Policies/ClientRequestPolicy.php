<?php

namespace App\Policies;

use App\Models\ClientRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClientRequestPolicy
{

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ClientRequest $clientRequest): bool
    {
        return $user->id === $clientRequest->user_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ClientRequest $clientRequest): bool
    {
        return ($user->id === $clientRequest->user_id && $clientRequest->status === 'PENDING') || $user->role === 'admin';
    }

    public function edit(User $user, ClientRequest $clientRequest): bool
    {
        return ($user->id === $clientRequest->user_id && $clientRequest->status === 'PENDING') || $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ClientRequest $clientRequest): bool
    {
        return $user->id === $clientRequest->user_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ClientRequest $clientRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ClientRequest $clientRequest): bool
    {
        return false;
    }
}
