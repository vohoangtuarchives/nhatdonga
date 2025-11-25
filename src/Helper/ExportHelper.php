<?php

namespace Tuezy\Helper;

/**
 * ExportHelper - Helper for exporting data
 * Provides utilities for exporting data to CSV, Excel, etc.
 */
class ExportHelper
{
    /**
     * Export array to CSV
     * 
     * @param array $data Data to export
     * @param array $headers Column headers
     * @param string $filename Output filename
     * @return void
     */
    public function exportToCSV(array $data, array $headers = [], string $filename = 'export.csv'): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Write headers
        if (!empty($headers)) {
            fputcsv($output, $headers);
        }

        // Write data
        foreach ($data as $row) {
            if (empty($headers)) {
                // Use array keys as headers if not provided
                fputcsv($output, array_keys($row));
                $headers = array_keys($row);
            } else {
                // Write row with same order as headers
                $orderedRow = [];
                foreach ($headers as $header) {
                    $orderedRow[] = $row[$header] ?? '';
                }
                fputcsv($output, $orderedRow);
            }
        }

        fclose($output);
        exit;
    }

    /**
     * Export array to Excel (CSV format with Excel compatibility)
     * 
     * @param array $data Data to export
     * @param array $headers Column headers
     * @param string $filename Output filename
     * @return void
     */
    public function exportToExcel(array $data, array $headers = [], string $filename = 'export.xls'): void
    {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Write headers
        if (!empty($headers)) {
            fputcsv($output, $headers, "\t");
        }

        // Write data
        foreach ($data as $row) {
            if (empty($headers)) {
                fputcsv($output, array_keys($row), "\t");
                $headers = array_keys($row);
            } else {
                $orderedRow = [];
                foreach ($headers as $header) {
                    $orderedRow[] = $row[$header] ?? '';
                }
                fputcsv($output, $orderedRow, "\t");
            }
        }

        fclose($output);
        exit;
    }

    /**
     * Export array to JSON
     * 
     * @param array $data Data to export
     * @param string $filename Output filename
     * @return void
     */
    public function exportToJSON(array $data, string $filename = 'export.json'): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Prepare data for export (sanitize, format)
     * 
     * @param array $data Raw data
     * @param array $mapping Field mapping (old => new)
     * @return array Prepared data
     */
    public function prepareData(array $data, array $mapping = []): array
    {
        $prepared = [];

        foreach ($data as $row) {
            $preparedRow = [];

            if (empty($mapping)) {
                $preparedRow = $row;
            } else {
                foreach ($mapping as $oldKey => $newKey) {
                    $preparedRow[$newKey] = $row[$oldKey] ?? '';
                }
            }

            // Format dates
            foreach ($preparedRow as $key => $value) {
                if (strpos($key, 'date') !== false && is_numeric($value)) {
                    $preparedRow[$key] = date('d/m/Y H:i', $value);
                }
            }

            $prepared[] = $preparedRow;
        }

        return $prepared;
    }
}

