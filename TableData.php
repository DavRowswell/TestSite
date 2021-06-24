<?php


use airmoi\FileMaker\FileMakerException;
use airmoi\FileMaker\Object\Result;

require_once ('utilities.php');
require_once ('TableRow.php');
require_once ('Specimen.php');

class TableData
{

    static array $ignoredFields = ['SortNum', 'Accession Numerical', 'Imaged', 'IIFRNo', 'Photographs::photoFileName',
        'Event::eventDate', 'card01', 'Has Image', 'imaged'];

    private Result $result;
    private array $usefulFields;


    public function __construct(Result $result, array $resultLayoutFields)
    {
        $this->result = $result;

        # filter out unnecessary fields
        $this->usefulFields = array_diff($resultLayoutFields, TableData::$ignoredFields);
    }

    /**
     * Returns a list of table heads. At the moment it only returns the id and href of
     * each column.
     * @param int $page
     * @param string $databaseName
     * @param string $requestUri
     * @param string $sortOrder
     * @return array
     */
    public function getTableHeads(int $page, string $databaseName,
                                  string $requestUri, string $sortOrder = 'Descend'): array
    {

        # a list with $id => $href for all table heads
        $data = array();

        foreach($this->usefulFields as $field) {
            $id = htmlspecialchars(Specimen::FormatFieldName($field));

            $payloadList = [
                'Database' => $databaseName,
                'Sort' => $field,
                'SortOrder' => $sortOrder === 'Descend' ? 'Ascend' : 'Descend',
                'Page' => $page,
            ];

            $href = substr($requestUri, 0, strpos($requestUri, '?')) . '?' . http_build_query($payloadList);
            $href = str_replace('%3A', ':', $href);

            $data[$id] = $href;

            $icon_class = $sortOrder === 'Descend' ? 'oi-sort-descending' : 'oi-sort-ascending';
        }

        return $data;
    }

    /**
     * Returns a list of TableRow objects, each object
     * corresponds to a result object from FMP
     * @param string $databaseName
     * @return TableRow[]
     * @throws FileMakerException
     */
    public function getTableRows(string $databaseName): array
    {
        # a list with tableRow ID => TableRow object
        $rows = array();

        foreach ($this->result->getRecords() as $record) {
            $tableRow = new TableRow();

            foreach ($this->usefulFields as $field) {
                # ID field logic
                if (Specimen::FormatFieldName($field) === 'Accession Number' or $field === 'SEM #') {
                    $url = htmlspecialchars($databaseName) . '&AccessionNo=' . htmlspecialchars($record->getField($field));
                    $id = htmlspecialchars(trim($record->getField($field)));

                    $hasImage = false;
                    if ($databaseName === 'entomology' and $record->getField("Imaged") === "Photographed") $hasImage = true;
                    else if ($databaseName === 'fish' and $record->getField("imaged") === "Yes") $hasImage = true;
                    else if ($databaseName === 'mammal' or $databaseName === 'avian' or $databaseName === 'herpetology') {
                        if ($record->getField("Photographs::photoFileName") !== "") $hasImage = true;
                    } # for vwsp lichen bryophytes fungi algae
                    else if ($record->getField("Imaged") === "Yes") $hasImage = true;

                    $tableRow->setId($id);
                    $tableRow->setUrl($url);
                    $tableRow->setHasImage($hasImage);
                } else {
                    $value = $record->getField($field);
                    if ($value == '' or $value == null) $tableRow->addField('---');
                    else $tableRow->addField($value);
                }
            }

            $rows[$tableRow->getId()] = $tableRow;
        }

        return $rows;
    }

    /**
     * @return Result
     */
    public function getResult(): Result
    {
        return $this->result;
    }

    /**
     * @return array
     */
    public function getUsefulFields(): array
    {
        return $this->usefulFields;
    }


}