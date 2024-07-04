<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property int $user_id
 * @property int $order_status_id
 * @property int|null $payment_id
 * @property string $uuid
 * @property array $products
 * @property array $address
 * @property float $delivery_fee
 * @property float $amount
 * @property \Illuminate\Support\Carbon|null $shipped_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $this->user_id,
            'payment_id' => $this->payment_id,
            'order_status_id' => $this->order_status_id,
            'products' => $this->products,
            'address' => $this->address,
            'delivery_fee' => $this->delivery_fee,
            'amount' => $this->amount,
            'shipped_at' => $this->shipped_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
