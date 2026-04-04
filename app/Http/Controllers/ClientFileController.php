<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DbStrExportService;
use App\Services\SkillsExportService;
use App\Services\SpellsExportService;

class ClientFileController extends Controller
{
    public function index()
    {
        return view('client-files.index');
    }

    public function export(
        Request $request,
        DbStrExportService $dbstrExporter,
        SpellsExportService $spellsExporter,
        SkillsExportService $skillsExporter/* ,
        BaseDataExportService $baseDataExporter */
    ) {
        $fileType = $request->query('file');

        switch ($fileType) {
            case 'dbstr':
                $result = $dbstrExporter->export();
                break;

            case 'spells':
                $result = $spellsExporter->export();
                break;

            case 'skills':
                $result = $skillsExporter->export();
                break;

            case 'basedata':
                $result = $baseDataExporter->export();
                break;

            default:
                return back()->with('error', 'Unknown file type.');
        }

        return back()->with('success', "Exported {$result['file']} ({$result['count']} records)");
    }
}
