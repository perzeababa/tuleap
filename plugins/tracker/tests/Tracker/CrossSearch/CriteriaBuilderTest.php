<?php
/**
 * Copyright (c) Enalean, 2012. All Rights Reserved.
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
require_once dirname(__FILE__) . '/../../Test_Tracker_FormElement_Builder.php';
require_once dirname(__FILE__) . '/../../../include/Tracker/CrossSearch/ViewBuilder.class.php';
require_once dirname(__FILE__) . '/../../../include/Tracker/CrossSearch/SemanticValueFactory.class.php';
require_once dirname(__FILE__) . '/../../../include/Tracker/TrackerFactory.class.php';
require_once 'common/include/Codendi_Request.class.php';
require_once 'Test_CriteriaBuilder.php';
require_once dirname(__FILE__) . '/../../../include/Tracker/CrossSearch/SemanticStatusReportField.class.php';

Mock::generate('Tracker_FormElementFactory');
Mock::generate('Tracker_CrossSearch_Search');
Mock::generate('Tracker_CrossSearch_SearchContentView');
Mock::generate('TrackerFactory');
Mock::generate('Project');
Mock::generate('Tracker_Report');
Mock::generate('Tracker_CrossSearch_SemanticValueFactory');

class Tracker_CrossSearch_CriteriaBuilderTest extends TuleapTestCase {
    
    public function setUp() {
        parent::setUp();
        $this->formElementFactory   = new MockTracker_FormElementFactory();
        $this->semantic_factory     = new MockTracker_CrossSearch_SemanticValueFactory();
        $this->planning_tracker_ids = array();
    }
}

class Tracker_CrossSearch_CriteriaBuilder_WithAllCriteriaTypesTest extends Tracker_CrossSearch_CriteriaBuilderTest {


    public function testNoValueSubmittedShouldNotSelectAnythingInCriterion() {
        $this->request = new Codendi_Request(array(
            'group_id' => '66',
            'criteria' => array(),
            'semantic_criteria' => array('title' => '', 'status' => ''),
        ));
        
        $fields = array(aTextField()->withId(220)->build());
        $this->formElementFactory->setReturnValue('getProjectSharedFields', $fields);
        
        $criteria = $this->getCriteria();
        $this->assertEqual($criteria[0]->field->getCriteriaValue($criteria[0]), array());
    }
    
    public function testSubmittedValueIsSelectedInCriterion() {
        $this->request = new Codendi_Request(array(
            'group_id' => '66', 
            'criteria' => array('220' => array('values' => array('350'))),
            'semantic_criteria' => array('title' => '', 'status' => '')
        ));
        
        $fields = array(aTextField()->withId(220)->build());
        $this->formElementFactory->setReturnValue('getProjectSharedFields', $fields);
        
        $criteria = $this->getCriteria();
        $this->assertEqual($criteria[0]->field->getCriteriaValue($criteria[0]), array(350));
    }
    
    public function testSubmittedValuesAreSelectedInCriterion() {
        $this->request = new Codendi_Request(array(
            'group_id' => '66', 
            'criteria' => array('220' => array('values' => array('350', '351')),
                                '221' => array('values' => array('352'))),
            'semantic_criteria' => array('title' => '', 'status' => '')
        ));
        
        $fields = array(aTextField()->withId(220)->build(),
                        aTextField()->withId(221)->build());
        $this->formElementFactory->setReturnValue('getProjectSharedFields', $fields);
        
        $criteria = $this->getCriteria();
        $this->assertEqual(count($criteria), 2);
        $this->assertEqual($criteria[0]->field->getCriteriaValue($criteria[0]), array(350, 351));
        $this->assertEqual($criteria[1]->field->getCriteriaValue($criteria[1]), array(352));
    }
    
    public function testAllCriteriaHaveAReport() {
        
    }

    public function testAllCriteriaAreAdvancedCriteria() {
    
    }
    
    public function testGetCriteriaAssemblesAllCriteriaTypes() {
    
    }
    
    
    
    
    private function getCriteria() {
        $searchViewBuilder     = new Tracker_CrossSearch_CriteriaBuilder($this->formElementFactory, $this->semantic_factory, $this->planning_tracker_ids);
        $cross_search_criteria = aCrossSearchCriteria()
                                ->withSharedFieldsCriteria($this->request->get('criteria'))
                                ->build();

        $project = new MockProject();
        $report  = new MockTracker_Report();
        return $searchViewBuilder->getSharedFieldsCriteria($project, $report, $cross_search_criteria);
    }

}

class Tracker_CrossSearch_CriteriaBuilder_WithSemanticTest extends Tracker_CrossSearch_CriteriaBuilderTest {
    
    public function itPassesTheSearchedTitleToTheField() {
        
        $cross_search_criteria = aCrossSearchCriteria()
                                ->withSemanticCriteria(array('title' => 'Foo', 'status' => ''))
                                ->build();
        $report_criteria = $this->getSemanticCriteria($cross_search_criteria);
        
        $actual_field          = $report_criteria[0]->field;
        $expected_field        = new Tracker_CrossSearch_SemanticTitleReportField('Foo', $this->semantic_factory);
        
        $this->assertEqual($expected_field, $actual_field);
    }

    public function itPassesTheSearchedStatusToTheField() {
        $cross_search_criteria = aCrossSearchCriteria()
                                ->forOpenItems()
                                ->build();
        $report_criteria       = $this->getSemanticCriteria($cross_search_criteria);
        $actual_field          = $report_criteria[1]->field;
        $expected_field        = new Tracker_CrossSearch_SemanticStatusReportField(Tracker_CrossSearch_SemanticStatusReportField::STATUS_OPEN,
                                                                                   new MockTracker_CrossSearch_SemanticValueFactory());
        
        $this->assertEqual($expected_field, $actual_field);
    }
    
    protected function getSemanticCriteria($cross_search_criteria) {
        $builder               = new Tracker_CrossSearch_CriteriaBuilder($this->formElementFactory, $this->semantic_factory, $this->planning_tracker_ids);
        $report                = new MockTracker_Report();
        return $builder->getSemanticFieldsCriteria($report, $cross_search_criteria);
    }
}

class Tracker_CrossSearch_CriteriaBuilder_WithNoArtifactIDTest extends Tracker_CrossSearch_CriteriaBuilderTest {
    
    public function itDoesntCreateACriteriaAtAllWhenArtifactIdsArentSet() {
        $criteria = aCrossSearchCriteria()->build();
        
        $builder  = new Tracker_CrossSearch_CriteriaBuilder($this->formElementFactory, $this->semantic_factory, $this->planning_tracker_ids);
        $artifact_criteria = $builder->getArtifactLinkCriteria($criteria);
        
        $this->assertEqual(array(), $artifact_criteria);
    }       
    
    public function itDoesntCreateACriteriaAtAllWhenArtifactIdsAreEmpty() {
        $criteria = aCrossSearchCriteria()->withArtifactIds(array())->build();
        
        $builder  = new Tracker_CrossSearch_CriteriaBuilder($this->formElementFactory, $this->semantic_factory, $this->planning_tracker_ids);
        $artifact_criteria = $builder->getArtifactLinkCriteria($criteria);
        
        $this->assertEqual(array(), $artifact_criteria);
    }       
}

class Tracker_CrossSearch_CriteriaBuilder_WithOneArtifactListTest extends Tracker_CrossSearch_CriteriaBuilderTest {
    
    public function itCreatesASingleArtifactIdCriteria() {
        $criteria = aCrossSearchCriteria()->withArtifactIds(array(999 => array(1)))->build();
        
        $builder  = new Tracker_CrossSearch_CriteriaBuilder($this->formElementFactory, $this->semantic_factory, array(999));
        $artifact_criteria = $builder->getArtifactLinkCriteria($criteria);

        $expected_criterion = new Tracker_CrossSearch_ArtifactReportField(999, array(1));
        $this->assertEqual(count($artifact_criteria), 1);
        $this->assertNotNull($artifact_criteria[0]);
        $this->assertEqual($artifact_criteria[0]->field, $expected_criterion);
    }
    // tout le monde a isadvanced == true et un report
}

class Tracker_CrossSearch_CriteriaBuilder_WithSeveralArtifactListsTest extends Tracker_CrossSearch_CriteriaBuilderTest {
    
    public function itCreatesSeveralArtifactIdCriteria() {
        $criteria = aCrossSearchCriteria()->withArtifactIds(array(999=>array(1, 512), 666 => array(33)))->build();
        $builder  = new Tracker_CrossSearch_CriteriaBuilder($this->formElementFactory, $this->semantic_factory, array(999, 666));
        $artifact_criteria = $builder->getArtifactLinkCriteria($criteria);

        $expected_criterion1 = new Tracker_CrossSearch_ArtifactReportField(999, array(1, 512));
        $expected_criterion2 = new Tracker_CrossSearch_ArtifactReportField(666, array(33));
        $this->assertEqual(count($artifact_criteria), 2);
        $this->assertEqual($artifact_criteria[0]->field, $expected_criterion1);
        $this->assertEqual($artifact_criteria[1]->field, $expected_criterion2);
        
    }
    // tout le monde a isadvanced == true et un report
    
    
}

?>
