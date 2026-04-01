<?php

namespace App\Http\Controllers\Api;

use App\Application\Actions\Pizza\CreatePizzaAction;
use App\Application\Actions\Pizza\DeletePizzaAction;
use App\Application\Actions\Pizza\GetPizzaAction;
use App\Application\Actions\Pizza\ListPizzasAction;
use App\Application\Actions\Pizza\UpdatePizzaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ListPizzasRequest;
use App\Http\Requests\Api\StorePizzaRequest;
use App\Http\Requests\Api\UpdatePizzaRequest;
use App\Http\Resources\PizzaResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PizzaController extends Controller
{
    public function index(
        ListPizzasRequest $request,
        ListPizzasAction $action,
    ): AnonymousResourceCollection {
        $validated = $request->validated();

        return PizzaResource::collection(
            $action->execute(
                (int) $validated['per_page'],
                (int) $validated['page'],
            ),
        );
    }

    public function show(
        string $pizza,
        GetPizzaAction $action,
    ): PizzaResource {
        return new PizzaResource($action->execute($pizza));
    }

    public function store(
        StorePizzaRequest $request,
        CreatePizzaAction $action,
    ): JsonResponse {
        $pizza = $action->execute($request->toDto());

        return (new PizzaResource($pizza))
            ->response()
            ->setStatusCode(201);
    }

    public function update(
        UpdatePizzaRequest $request,
        string $pizza,
        UpdatePizzaAction $action,
    ): PizzaResource {
        return new PizzaResource(
            $action->execute($pizza, $request->toDto()),
        );
    }

    public function destroy(
        string $pizza,
        DeletePizzaAction $action,
    ): JsonResponse {
        $action->execute($pizza);

        return response()->json(null, 204);
    }
}
