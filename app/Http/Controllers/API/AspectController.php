<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Aspects\StoreRequest;
use App\Http\Requests\API\Aspects\UpdateRequest;
use App\Http\Resources\AspectResource;
use App\Models\Aspect;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AspectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $aspectsQuery = Aspect::where('user_id', $userId)->get();
        $aspects = AspectResource::collection($aspectsQuery);
        return response()->json([
            'items' => $aspects
        ]);
    }

    /**
     * Store a newly created aspect in storage.
     * Also file handling and uploading is done here. 
     * The icon file is stored in the 'public' disk and the path is saved in the database.
     * 
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        try {
            $icon = $request->file('icon');
            $iconPath = null;

            if ($icon) {
                $iconPath = $icon->store('aspect_icons', 'public');
            }

            $data = $request->only(['name', 'description']);
            $data["icon_path"] = $iconPath;
            $data["user_id"] = $request->user()->id;

            Aspect::create($data);

            return response()->json([
                "message" => "Aspect created!",
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified aspect.
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {

        $aspect = Aspect::find($id);

        if (!$aspect) {
            return response()->json([
                "message" => "Aspect not found!"
            ], 404);
        }

        if ($aspect->__get("user_id") !== $request->user()->id) {
            return response()->json([
                "message" => "Unauthorized access!"
            ], 403);
        }

        return response()->json(AspectResource::make($aspect));
    }

    /**
     * Update the specified aspect (name, description and icon) in storage.
     * 
     * @param UpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, $id): JsonResponse
    {
        try {
            $aspect = Aspect::find($id);

            if (!$aspect) {
                return response()->json([
                    "message" => "Aspect not found!"
                ], 404);
            }

            if ($aspect->__get("user_id") !== $request->user()->id) {
                return response()->json([
                    "message" => "Unauthorized access!"
                ], 403);
            }

            $data = $request->only(['name', 'description']);
            $icon = $request->file('icon');

            if ($icon) {
                // Delete the old icon if it exists
                if ($aspect->icon_path) {
                    \Storage::disk('public')->delete($aspect->icon_path);
                }
                // Store the new icon
                $data["icon_path"] = $icon->store('aspect_icons', 'public');
            }

            $aspect->update($data);

            return response()->json([
                "message" => "Aspect updated!",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified aspect from storage.
     * @param Request $request
     * @param  $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $aspect = Aspect::find($id);

            if (!$aspect) {
                return response()->json([
                    "message" => "Aspect not found!"
                ], 404);
            }

            if ($aspect->__get("user_id") !== $request->user()->id) {
                return response()->json([
                    "message" => "Unauthorized access!"
                ], 403);
            }

            $aspect->delete();

            return response()->json([
                "message" => "Aspect deleted!"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
