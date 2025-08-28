<?php

namespace App\Http\Controllers;

use App\Models\Veiculo;
use App\Models\Morador;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VeiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $veiculos = Veiculo::with('morador')->get();
        return response()->json($veiculos);
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
            'placa' => 'required|string|max:10|unique:veiculos,placa',
            'tipo' => 'required|string|in:Carro,Moto,Caminhão',
            'cor' => 'required|string|max:50',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'morador_id' => 'required|exists:moradores,id'
        ]);

        $veiculo = Veiculo::create($request->all());
        $veiculo->load('morador');
        return response()->json($veiculo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Veiculo $veiculo): JsonResponse
    {
        $veiculo->load('morador');
        return response()->json($veiculo);
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
    public function update(Request $request, Veiculo $veiculo): JsonResponse
    {
        $request->validate([
            'placa' => 'required|string|max:10|unique:veiculos,placa,' . $veiculo->id,
            'tipo' => 'required|string|in:Carro,Moto,Caminhão',
            'cor' => 'required|string|max:50',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'morador_id' => 'required|exists:moradores,id'
        ]);

        $veiculo->update($request->all());
        $veiculo->load('morador');
        return response()->json($veiculo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Veiculo $veiculo): JsonResponse
    {
        $veiculo->delete();
        return response()->json(null, 204);
    }

    /**
     * Buscar moradores para o select
     */
    public function getMoradores(): JsonResponse
    {
        $moradores = Morador::select('id', 'nome')->get();
        return response()->json($moradores);
    }
}
