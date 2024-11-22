<?php

namespace App\Enums;

enum Permission: string
{
    case VIEW_PROFILE = 'view_profile';
    case UPDATE_PROFILE = 'update_profile';
    case CREATE_USERS = 'create_users';
    case VIEW_USERS = 'view_users';
    case UPDATE_USERS = 'update_users';
    case DELETE_USERS = 'delete_users';
    case VIEW_ROLES = 'view_roles';
    case VIEW_PERMISSIONS = 'view_permissions';
}
