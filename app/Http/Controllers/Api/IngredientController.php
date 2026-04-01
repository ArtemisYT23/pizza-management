<?php

namespace App\Http\Controllers\Api;

use App\Application\Actions\Ingredient\CreateIngredientAction;
use App\Application\Actions\Ingredient\DeleteIngredientAction;
use App\Application\Actions\Ingredient\ListIngredientsAction;
use App\Application\Actions\Ingredient\UpdateIngredientAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreIngredientRequest;
use App\Http\Requests\Api\UpdateIngredientRequest;
use App\Http\Resources\IngredientResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IngredientController extends Controller
{
    public function index(ListIngredientsAction $action): AnonymousResourceCollection
    {
        return IngredientResource::collection($action->execute());
    }

    public function store(
        StoreIngredientRequest $request,
        CreateIngredientAction $action,
    ): JsonResponse {
        $ingredient = $action->execute($request->toDto());

        return (new IngredientResource($ingredient))
            ->response()
            ->setStatusCode(201);
    }

    public function update(
        UpdateIngredientRequest $request,
        string $ingredient,
        UpdateIngredientAction $action,
    ): IngredientResource {
        return new IngredientResource(
            $action->execute($ingredient, $request->toDto()),
        );
    }

    public function destroy(
        string $ingredient,
        DeleteIngredientAction $action,
    ): JsonResponse {
        $action->execute($ingredient);

        return response()->json(null, 204);
    }
}
