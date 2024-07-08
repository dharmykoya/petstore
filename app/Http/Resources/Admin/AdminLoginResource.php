<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $uuid
 * @property string $first_name
 * @property string $last_name
 * @property bool $is_admin
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $avatar
 * @property string|null $address
 * @property string|null $phone_number
 * @property string $token
 */
class AdminLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'is_admin' => (bool) $this->is_admin,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'avatar' => $this->avatar,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'token' => $this->token,
        ];
    }
}
