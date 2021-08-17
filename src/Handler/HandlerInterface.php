<?php

namespace ItkDev\Adgangsstyring\Handler;

/**
 * Interface HandlerInterface
 *
 * Interface allowing users to implement how to handle
 * create a user deletion list, how to remove users from and
 * how to handle the list upon finishing the Azure AD Delta Sync flow.
 *
 * @package ItkDev\Adgangsstyring\Handler
 */
interface HandlerInterface
{
    /**
     * Collect users and create deletion list.
     */
    public function collectUsersForDeletionList(): void;

    /**
     * Remove users from the deletion list.
     *
     * @param array $users
     */
    public function removeUsersFromDeletionList(array $users): void;

    /**
     * Handle user deletion list, as no more users will be removed.
     */
    public function commitDeletionList(): void;
}
