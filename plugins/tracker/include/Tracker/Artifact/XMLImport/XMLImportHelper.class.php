<?php
/**
 * Copyright (c) Enalean, 2014 - 2015. All Rights Reserved.
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

class Tracker_Artifact_XMLImport_XMLImportHelper extends XMLImportHelper {

    /** @var UserManager */
    private $user_manager;

    public function __construct(UserManager $user_manager) {
        $this->user_manager = $user_manager;
    }

    /**
     * @param SimpleXMLElement $xml_element
     * @return PFUser
     */
    public function getUser(SimpleXMLElement $xml_element) {
        $submitter = $this->user_manager->getUserByIdentifier($this->getUserFormat($xml_element));
        if (! $submitter) {
            $submitter = $this->user_manager->getUserAnonymous();
            $submitter->setEmail((string) $xml_element);
        }

        return $submitter;
    }
}