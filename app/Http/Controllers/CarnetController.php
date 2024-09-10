<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carnet;
use Illuminate\Support\Facades\Storage;

class CarnetController extends Controller
{
    public function index()
    {
        return Carnet::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required',
            'nom' => 'required',
            'prenom' => 'required',
            'adresse' => 'required',
            'photo' => 'nullable|image'
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $validated['photo'] = $path;
        }

        $carnet = Carnet::create($validated);
        return response()->json($carnet, 201);
    }

    public function show(Carnet $carnet)
    {
        return $carnet;
    }

    public function update(Request $request, Carnet $carnet)
    {
        $validated = $request->validate([
            'numero' => 'sometimes|required',
            'nom' => 'sometimes|required',
            'prenom' => 'sometimes|required',
            'adresse' => 'sometimes|required',
            'photo' => 'nullable|image'
        ]);

        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si nécessaire
            if ($carnet->photo) {
                Storage::disk('public')->delete($carnet->photo);
            }
            $path = $request->file('photo')->store('photos', 'public');
            $validated['photo'] = $path;
        }

        $carnet->update($validated);
        return response()->json($carnet, 200);
    }

    public function destroy(Carnet $carnet)
    {
        if ($carnet->photo) {
            Storage::disk('public')->delete($carnet->photo);
        }
        $carnet->delete();
        return response()->json(null, 204);
    }
}
