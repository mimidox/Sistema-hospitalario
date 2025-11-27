<?php
namespace App\Http\Controllers\Api;
use App\Http\Requests;               //Requests
use App\Http\Controllers\Controller; //Herencia de controlador
use Illuminate\Support\Facades\DB; //Base de datos
use Illuminate\Http\Request;       //Request pero que es illuminate? Respuesta: una libreria que contiene request
use Illuminate\Http\JsonResponse; //para Json response
class ControlInicio extends Controller
{
    public function index()
    {
        return view('pages.inicio');
    }
    public function main()
    {
        return view('pages.main');
    }
    public function login()
    {
        return view('pages.login');
    }
}
