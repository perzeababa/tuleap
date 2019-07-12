<?php
/**
 * Copyright (c) Enalean, 2015-Present. All Rights Reserved.
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
 */

require_once('pre.php');
require_once('common/wiki/lib/WikiAttachment.class.php');


$attch = new WikiAttachment();
$attch->setUri($_SERVER['REQUEST_URI']);

PHPWikiPluginRedirector::redirect();

if($attch->exist() && $attch->isActive()) {
    if($attch->isAutorized(UserManager::instance()->getCurrentUser()->getId())) {
        $attch->htmlDump();
    }
}
else {
    exit_error($Language->getText('global','error'),
               $Language->getText('wiki_attachment_upload', 'err_not_exist'));
}

// Local Variables:
// mode: php
// End:
?>