<?php

namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="CreateCategoryRequest",
 *     type="object",
 *     title="CreateCategoryRequest",
 *     description="Create Category request schema",
 *     required={"title"},
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
 *     )
 * )
 */
class CreateCategoryRequest {}
