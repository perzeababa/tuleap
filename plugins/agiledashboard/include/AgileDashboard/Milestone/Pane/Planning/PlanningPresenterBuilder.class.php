<?php
/**
 * Copyright Enalean (c) 2013. All rights reserved.
 *
 * Tuleap and Enalean names and logos are registrated trademarks owned by
 * Enalean SAS. All other trademarks or names are properties of their respective
 * owners.
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

class AgileDashboard_Milestone_Pane_Planning_PlanningPresenterBuilder {

    /** @var AgileDashboard_Milestone_Backlog_BacklogRowCollectionFactory */
    private $collection_factory;

    /** @var AgileDashboard_Milestone_Backlog_BacklogStrategyFactory */
    private $strategy_factory;

    public function __construct(
        AgileDashboard_Milestone_Backlog_BacklogStrategyFactory $strategy_factory,
        AgileDashboard_Milestone_Backlog_BacklogRowCollectionFactory $collection_factory,
        Planning_MilestoneFactory $milestone_factory
    ) {
        $this->strategy_factory   = $strategy_factory;
        $this->collection_factory = $collection_factory;
        $this->milestone_factory  = $milestone_factory;
    }

    public function getMilestonePlanningPresenter(PFUser $user, Planning_ArtifactMilestone $milestone) {
        $redirect_paremeter     = new Planning_MilestoneRedirectParameter();
        $backlog_strategy       = $this->strategy_factory->getBacklogStrategy($milestone);
        $redirect_to_self = $redirect_paremeter->getPlanningRedirectToSelf($milestone, AgileDashboard_Milestone_Pane_Planning_PlanningPaneInfo::IDENTIFIER);

        $backlog_collection = $this->collection_factory->getUnplannedOpenCollection(
            $user,
            $milestone,
            $backlog_strategy,
            $redirect_to_self
        );
        return new AgileDashboard_Milestone_Pane_Planning_PlanningPresenter(
            $backlog_collection,
            $this->getSubmilestoneCollection($user, $milestone),
            $backlog_collection->getParentItemName(),
            $backlog_strategy->getItemName(),
            $milestone
        );
    }

    private function getSubmilestoneCollection(PFUser $user, Planning_ArtifactMilestone $milestone) {
        $submilestone_collection = new AgileDashboard_Milestone_Pane_Planning_PlanningSubMilestonePresenterCollection();

        $submilestones = $this->milestone_factory->getSubMilestones($user, $milestone);
        foreach ($submilestones as $submilestone) {
            $submilestone_collection->push(new AgileDashboard_Milestone_Pane_Planning_PlanningSubMilestonePresenter($submilestone));
        }

        return $submilestone_collection;
    }
}
?>
