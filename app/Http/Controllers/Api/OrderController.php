<?php

namespace App\Http\Controllers\Api;

use App\Application\Actions\Order\ListMyOrdersAction;
use App\Application\Actions\Order\ListOrdersAction;
use App\Application\Actions\Order\PlaceOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function index(
        Request $request,
        ListOrdersAction $listAll,
        ListMyOrdersAction $listMine,
    ): AnonymousResourceCollection {
        $user = $request->user();
        $orders = $user->is_admin
            ? $listAll->execute()
            : $listMine->execute($user->id);

        return OrderResource::collection($orders);
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
