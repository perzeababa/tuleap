<?php
/**
 * Copyright (c) Enalean, 2011. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once 'common/project/ProjectManager.class.php';
require_once 'common/project/UGroupManager.class.php';
require_once 'common/user/UserManager.class.php';

/**
 * Return groups of a user given by name to use them externally
 *
 */
class ExternalPermissions {
    
    public static $status = array(
        User::STATUS_RESTRICTED => 'site_restricted',
        User::STATUS_ACTIVE     => 'site_active'
    );
    
    public function getUserGroups($userName) {
        $user = $this->getValidUserByName($userName);
        if (!$user) {
            return array();
        }
        $groups = array(self::$status[$user->getStatus()]);
        $groups = $this->appendProjectGroups($user, $groups);
        $groups = $this->appendUgroups($user, $groups);
        
        return $groups;        
    }
    
    protected function appendProjectGroups($user, array $groups = array()) {
        $user_projects = $user->getProjects(true);
        foreach($user_projects as $user_project) {
            $project_name = strtolower($user_project['unix_group_name']);
            $group_id     = $user_project['group_id'];
            $groups[] = $project_name.'_project_members';
            if ($user->isMember($group_id, 'A')) {
                $groups[] = $project_name.'_project_admin';
            }
        }
        return $groups;
    }
    
    protected function appendUgroups($user, array $groups = array()) {
        $ugroups = $user->getAllUgroups();
        foreach ($ugroups as $row) {
            $groups[] = 'ug_'.$row['ugroup_id'];
        }
        return $groups;
    } 
    
    protected function getValidUserByName($username) {
        $user = UserManager::instance()->getUserByUserName($username);
        if ($user && isset(self::$status[$user->getStatus()])) {
            return $user;
        }
        return false;
    }

}
?>