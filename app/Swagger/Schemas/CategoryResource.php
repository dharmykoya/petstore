<?php
namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="CategoryResource",
 *     type="object",
 *     title="Category",
 *     description="Category schema",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the category",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="uuid",
 *         type="string",
 *         format="uuid",
 *         description="UUID of the category",
 *         example="123e4567-e89b-12d3-a456-426614174000"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Title of the category",
 *         example="Electronics"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="Slug of the category",
 *         example="electronics"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the category was created",
 *         example="2024-01-01T00:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the category was last updated",
 *         example="2024-01-01T00:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="deleted_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the category was deleted",
 *         example="2024-01-01T00:00:00Z",
 *         nullable=true
 *     )
 * )
 */
class CategoryResource {}
