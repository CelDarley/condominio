<?php

namespace App\Http\Controllers;

use App\Models\Morador;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MoradorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $moradores = Morador::with('veiculos')->get();
        return response()->json($moradores);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'endereco' => 'required|string|max:255',
            'email' => 'required|email|unique:moradores,email'
        ]);

        $morador = Morador::create($request->all());
        return response()->json($morador, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Morador $morador): JsonResponse
    {
        $morador->load('veiculos');
        return response()->json($morador);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Morador $morador): JsonResponse
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'endereco' => 'required|string|max:255',
            'email' => 'required|email|unique:moradores,email,' . $morador->id
        ]);

        $morador->update($request->all());
        return response()->json($morador);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Morador $morador): JsonResponse
    {
        $morador->delete();
        return response()->json(null, 204);
    }
}
