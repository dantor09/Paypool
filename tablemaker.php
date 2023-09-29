<?php

function makeTable($data, $showHeader = true) {
    $tableStr = "";

    $tableStr .= "<table style='border-collapse: separate;'>";

    foreach($data as $row) {
        if ($showHeader) {
            $tableStr .= "<tr>";
            foreach($row as $columnName => $columnValue) {
                $tableStr .= sprintf("<th style='padding-left: 5px; padding-right: 5px;'>%s</th>", $columnName);
            }
            $tableStr .= "</tr>";
            $showHeader = false;
        }
        $tableStr .= "<tr>";
        foreach($row as $columnName => $columnValue) {
            $tableStr .= sprintf("<td style='padding-left: 5px; padding-right: 5px;' >%s</td>", $columnValue);
        }
        $tableStr .= "</tr>";
    }

    $tableStr .= "</table>";

    return $tableStr;
}

?>