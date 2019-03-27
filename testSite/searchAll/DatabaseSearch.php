<?php
class DatabaseSearch {

    var $fm;
    var $database;

    var $search_layout;
    var $result_layout;

    var $results;

    function __construct($fm, $database) {
        $this->$fm = $fm;
        $this->$database = $database;
    }

    function setSearchLayout($search_layout) {
        $this->$search_layout = $search_layout;
    }

    function setResultLayout($result_layout) {
        $this->$result_layout = $result_layout;
    }

    function setResults($results) {
        $this->$results = $results;
    }

    function getFM() {
        return $this->$fm;
    }

    function getDatabase() {
        return $this->$database;
    }

    function getSearchLayout() {
        return $this->$search_layout;
    }

    function getResultLayout() {
        return $this->$result_layout;
    }

    function getResults() {
        return $this->$results;
    }
}