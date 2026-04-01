<?php

namespace App\Http\Controllers\Api;

use App\Application\Actions\Order\ListOrdersAction;
use App\Application\Actions\Order\PlaceOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function index(ListOrdersAction $action): AnonymousResourceCollection
    {
        return OrderResource::collection($action->execute());
    }

    public function store(
        PlaceOrderRequest $request,
        PlaceOrderAction $action,
    ): JsonResponse {
        $order = $action->execute($request->toDto());

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }
}
