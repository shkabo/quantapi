<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;

class CsvResponse
{

    public static function generateCsv(array $headers,array $data, string $outputFile = 'file.csv')
    {
        $list = [];
        $list[] = $headers;

        foreach($data as $row) {
            $list[] = $row;
        }

        $fp = fopen('php://output', 'w');
        foreach ($list as $row) {
            fputcsv($fp, $row);
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$outputFile);

        return $response;
    }

}