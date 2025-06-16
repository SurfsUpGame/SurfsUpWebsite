<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Relaticle\Flowforge\Filament\Pages\KanbanBoardPage;

class PublicRoadmapController extends Controller
{
    public function show()
    {
        // Manually render the Filament page as a component
        $page = app(KanbanBoardPage::class);
        // return $page->mountAction(); // If needed, or just:
        return $page->render();
    }
}
