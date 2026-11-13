<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function getCountries()
    {
        $countries = Country::orderBy('name')->get();

        // Auto-fetch if empty (Self-healing database)
        if ($countries->isEmpty()) {
            try {
                $response = Http::get('https://countriesnow.space/api/v0.1/countries/positions');
                if ($response->successful()) {
                    $apiCountries = $response->json()['data'] ?? [];
                    foreach ($apiCountries as $country) {
                        $name = trim((string) ($country['name'] ?? ''));
                        if ($name === '') {
                            continue;
                        }

                        Country::firstOrCreate(
                            ['name' => $name],
                            ['code' => $country['iso2'] ?? null]
                        );
                    }
                    $countries = Country::orderBy('name')->get();
                } else {
                    Log::warning('Location API failed for countries | Response: ' . $response->body());
                }
            } catch (\Exception $e) {
                Log::error('Location API exception for countries: ' . $e->getMessage());
            }
        }

        // Minimal local fallback if external API is unavailable.
        if ($countries->isEmpty()) {
            Country::firstOrCreate(['name' => 'Pakistan'], ['code' => 'PK']);
            $countries = Country::orderBy('name')->get();
        }

        return response()->json($countries);
    }

    public function getStates($countryName)
    {
        $country = Country::where('name', $countryName)->first();
        if (!$country) return response()->json([]);

        $states = $country->states()->orderBy('name')->get();

        // Auto-fetch if empty (Self-healing database)
        if ($states->isEmpty()) {
            try {
                $response = Http::post('https://countriesnow.space/api/v0.1/countries/states', [
                    'country' => $countryName
                ]);
                if ($response->successful()) {
                    $apiStates = $response->json()['data']['states'] ?? [];
                    foreach ($apiStates as $s) {
                        State::firstOrCreate([
                            'country_id' => $country->id, 
                            'name' => $s['name']
                        ]);
                    }
                    $states = $country->states()->orderBy('name')->get();
                }
            } catch (\Exception $e) {
                Log::error("Location API exception for states: " . $e->getMessage());
            }
        }

        return response()->json($states);
    }

    public function getCities($stateId)
    {
        $state = State::find($stateId);
        if (!$state) return response()->json([]);

        $cities = $state->cities()->orderBy('name')->get();

        // Auto-fetch if empty (Self-healing database)
        if ($cities->isEmpty()) {
            $country = Country::find($state->country_id);
            if (!$country) {
                return response()->json([]);
            }
            try {
                $response = Http::post('https://countriesnow.space/api/v0.1/countries/state/cities', [
                    'country' => $country->name,
                    'state' => $state->name
                ]);
                
                if ($response->successful()) {
                    $apiCities = $response->json()['data'] ?? [];
                    foreach ($apiCities as $c) {
                        City::firstOrCreate([
                            'state_id' => $state->id, 
                            'name' => $c
                        ]);
                    }
                    $cities = $state->cities()->orderBy('name')->get();
                } else {
                    Log::warning("Location API failed for cities: " . $country->name . " -> " . $state->name . " | Response: " . $response->body());
                }
            } catch (\Exception $e) {
                Log::error("Location API exception for cities: " . $e->getMessage());
            }
        }

        return response()->json($cities);
    }
}
