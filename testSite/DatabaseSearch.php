<?php

/**
 * Class DatabaseSearch
 * Represents a database the client is currently searching in.
 */
class DatabaseSearch {

    /**
     * The FileMaker instance conencted to this databse
     * @var FileMaker
     */
    private FileMaker $fileMaker;

    /**
     * The name of this databse
     * @var string 
     */
    private string $name;

    private FileMaker_Layout $search_layout;
    private FileMaker_Layout $result_layout;

    private $results;

    function __construct($fm, $database) {
        $this->fileMaker = $fm;
        $this->name = $database;

        $this->setLayouts();
    }

    /**
     * @param $search_layout
     * @deprecated Layout should be set on constructor and never changed
     */
    function setSearchLayout($search_layout) {
        $this->search_layout = $search_layout;
    }

    /**
     * @param $result_layout
     * @deprecated Layout should be set on constructor and never changed
     */
    function setResultLayout($result_layout) {
        $this->result_layout = $result_layout;
    }

    function setResults($results) {
        $this->results = $results;
    }

    function getFileMaker(): FileMaker
    {
        return $this->fileMaker;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getSearchLayout(): FileMaker_Layout
    {
        return $this->search_layout;
    }

    function getResultLayout(): FileMaker_Layout
    {
        return $this->result_layout;
    }

    function getResults() {
        return $this->results;
    }

    /**
     * Searches all available layouts from FMP and sets the correct Search and
     * Result layout for this DatabaseSearch.
     */
    private function setLayouts() {
        # list of layout names!
        $availableLayouts = $this->fileMaker->listLayouts();
        
        foreach ($availableLayouts as $layoutName) {
            if ($this->name === 'mi') {
                if ($layoutName == 'search-MI') {
                    $this->search_layout = $this->fileMaker->getLayout($layoutName);
                } else if ($layoutName == 'results-MI') {
                    $this->result_layout = $this->fileMaker->getLayout($layoutName);
                }
            } else {
                if (str_contains($layoutName, 'search')) {
                    $this->search_layout = $this->fileMaker->getLayout($layoutName);
                } else if (str_contains($layoutName, 'results')) {
                    $this->result_layout = $this->fileMaker->getLayout($layoutName);
                }
            }
        }
    }

    /**
     * Will query the FileMakerPro API for a result item. The query takes query fields,
     * and also deals with:
     * - data pagination with the pageNumber field
     * - sorting the data with the sortType and sortQuery
     *
     * @param int $maxResponseAmount amount of responses to query for
     * @param string[] $getFields query fields to use, must be not empty
     * @param string $logicalOperator on of 'or' or 'and'
     * @param string|null $sortQuery
     * @param int $pageNumber used to calculate a multiplier of maxResponseAmount for pagination
     * @param string|null $sortType one of ascend or descend
     * @return FileMaker_Result|FileMaker_Error Result or Error (Error if no entries for query too)
     */
    function queryForResults(int $maxResponseAmount, array $getFields, string $logicalOperator, ?string $sortQuery,
                             int $pageNumber, ?string $sortType): FileMaker_Result|FileMaker_Error
    {

        // Find on all inputs with values
        $findCommand = $this->fileMaker->newFindCommand($this->search_layout->getName());

        $findCommand->setLogicalOperator(operator: strtoupper($logicalOperator));

        /**
         * TODO Fix the Fossils collection, searching is not working! Not even in deployed app.
         */

        # The different strings used in FMP to mean the access number or ID
        $accessionNumberOptions = ['Accession_Number', 'catalogNumber', 'Accession_No'];

        # handle all regular search fields
        foreach ($getFields as $fieldName => $fieldValue) {

            $layoutField = str_replace("_", " ", $fieldName);

            # handle image field
            if ($fieldName == 'hasImage') {
                $layoutField = 'Photographs::photoFileName' or 'Imaged'; # TODO fix this!

                $findCommand->addFindCriterion(
                    fieldname: $layoutField,
                    testvalue: $this->name == 'entomology' ? 'Photographed' : '*'
                );
            }
            # handle accession number 'ID' field
            else if (in_array($fieldName, $accessionNumberOptions)) {

                switch ($this->name) {
                    case 'vwsp'; case 'bryophytes';
                    case 'fungi'; case 'lichen'; case 'algae':
                    $findCommand->addFindCriterion(
                        fieldname: is_numeric($fieldValue) ? "Accession Numerical" : "Accession Number",
                        testvalue: $fieldValue
                    );
                    break;
                    case 'fossil'; case 'avian';
                    case 'herpetology'; case 'mammal':
                    $findCommand->addFindCriterion(
                        fieldname: is_numeric($fieldValue) ? "SortNum" : "catalogNumber",
                        testvalue: $fieldValue
                    );
                    break;
                    case 'mi'; case 'miw':
                    $findCommand->addFindCriterion(
                        fieldname: is_numeric($fieldValue) ? "SortNum" : 'Accession No',
                        testvalue: $fieldValue,
                    );
                    break;
                }
            }

            # all other fields just go in as they come
            else {
                $findCommand->addFindCriterion($layoutField, $fieldValue);
            }
        }

        # handle the sort property
        if ($sortQuery and $sortQuery != '') {

            # preliminary sort query, will only change in certain situations
            $sortBy = $sortQuery;

            # accession number sort is different for databases, handle it here
            if (mapField($sortQuery) === 'Accession Number') {
                if ($this->name == 'vwsp' or $this->name == 'bryophytes' or
                    $this->name == 'fungi' or $this->name == 'lichen' or $this->name == 'algae') {
                    $sortBy = 'Accession Numerical';
                }
                else {
                    $sortBy = 'sortNum';
                }
            }

            # for entomology and fish we can only sort by accession number? TODO check this!
            if($this->name == 'entomology') {
                $sortBy = 'SEM #';
            }
            if($this->name == 'fish') {
                $sortBy = 'accessionNo';
            }

            # handles the order of the sort
            $findCommand->addSortRule(fieldname:  str_replace('+', ' ', $sortBy), precedence: 1,
                order: $sortType === 'Descend' ? FILEMAKER_SORT_DESCEND : FILEMAKER_SORT_ASCEND);
        }

        # handle different table pages
        if ($pageNumber) {
            $findCommand->setRange(skip: ($pageNumber - 1) * $maxResponseAmount, max: $maxResponseAmount);
        }

        return $findCommand->execute();
    }
}