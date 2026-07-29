<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $itemsToCreate = [];
            $requestData = $request->validated();

            foreach ($requestData['items'] as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->lockForUpdate()
                    ->first();

                if ($product->stock < $item['quantity']) {
                    throw new Exception("Stok produk {$product->name} tidak mencukupi");
                }

                $product->stock -= $item['quantity'];
                $product->save();

                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price,
                ];
            }

            $order = Order::create([
                'total_amount' => $totalAmount,
            ]);

            $order->items()->createMany($itemsToCreate);
            DB::commit();

            return $this->responseSuccess('Order berhasil dibuat', $order, 201);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Order gagal dibuat');
        }
    }
}
