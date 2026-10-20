<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page; // Importar el modelo Page

class VideoController extends Controller
{
    public function index()
    {
        // Obtener todas las páginas ordenadas por el campo 'order'
        $pages = Page::orderBy('order')->get();

        // Pasar las páginas a la vista
        return view('video', compact('pages'));
    }
}
