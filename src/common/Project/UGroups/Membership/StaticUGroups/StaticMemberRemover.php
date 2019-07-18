<?php
/**
 * Copyright (c) Enalean, 2019-Present. All Rights Reserved.
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
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 *
 */

declare(strict_types=1);

namespace Tuleap\Project\UGroups\Membership\StaticUGroups;

class StaticMemberRemover
{
    public function removeUser(\ProjectUGroup $ugroup, \PFUser $user) : void
    {
        $this->removeUserFromStaticGroup((int) $ugroup->getProjectId(), (int) $ugroup->getId(), (int) $user->getId());
    }

    public function removeUserFromStaticGroup(int $group_id, int $ugroup_id, int $user_id) : void
    {
        /** @psalm-suppress MissingFile */
        include_once __DIR__.'/../../../../../www/project/admin/ugroup_utils.php';
        ugroup_remove_user_from_ugroup($group_id, $ugroup_id, $user_id);
    }
}