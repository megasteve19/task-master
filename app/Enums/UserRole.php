<?php

namespace App\Enums;

enum UserRole: string
{
    /**
     * User with the highest level of access, only one user can have this role.
     *
     * Only purpose of this role is to be able to manage admin users.
     */
    case Owner = 'owner';

    /**
     * User with the second highest level of access, can manage other users and access everything.
     */
    case Admin = 'admin';

    /**
     * User with the lowest level of access, can only access given resources.
     */
    case User = 'user';

    /**
     * Returns roles that can be many.
     */
    public static function many(): array
    {
        return [
            self::Admin,
            self::User,
        ];
    }

    /**
     * Returns the human-readable label for the user role.
     *
     * @return string
     */
    public function prettyName(): string
    {
        return match ($this) {
            UserRole::Owner => 'Owner',
            UserRole::Admin => 'Administrator',
            UserRole::User => 'User',
        };
    }
}
