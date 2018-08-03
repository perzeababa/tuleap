/*
 * Copyright (c) Enalean, 2018. All Rights Reserved.
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

import { getProjectList, getTrackerList } from "../api/rest-querier.js";

export async function loadTrackerList(context, project_id) {
    try {
        context.commit("setAreTrackerLoading", true);
        const trackerList = await getTrackerList(project_id);

        getAsyncTrackerList(context.commit, await trackerList.json());
    } catch (e) {
        const { error } = await e.response.json();
        context.commit("setErrorMessage", error.message);
    } finally {
        context.commit("setAreTrackerLoading", false);
    }
}

export async function loadProjectList(context) {
    try {
        const projectList = await getProjectList();

        getAsyncProjectList(context.commit, await projectList.json());
    } catch (e) {
        const { error } = await e.response.json();
        context.commit("setErrorMessage", error.message);
    } finally {
        context.commit("setIsLoadingInitial", false);
    }
}

function getAsyncProjectList(commit, projectList) {
    commit("saveProjects", projectList);
}

function getAsyncTrackerList(commit, trackerList) {
    commit("saveTrackers", trackerList);
}
