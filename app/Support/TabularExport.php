<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Response;

final class TabularExport
{
    /**
     * Build a minimal HTML document containing a table.
     *
     * @param array<int,string> $headings
     * @param iterable<array<int, mixed>> $rows
     */
    public static function htmlTable(array $headings, iterable $rows): string
    {
        $html = '<html><head><meta charset="UTF-8"></head><body>';
        $html .= '<table border="1" cellspacing="0" cellpadding="4">';
        $html .= '<thead><tr>';
        foreach ($headings as $heading) {
            $html .= '<th>' . self::escape($heading) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . self::escape($cell) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';
        return $html;
    }

    public static function excelResponse(string $filename, string $html): Response
    {
        $bom = "\xEF\xBB\xBF";

        return response($bom . $html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private static function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
