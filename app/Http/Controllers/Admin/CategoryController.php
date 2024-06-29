<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CategoryResource;
use App\Http\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService) {
        $this->categoryService = $categoryService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories",
     *     summary="Get all categories",
     *     description="Retrieve a list of all categories with pagination, sorting, and filtering options.",
     *     operationId="getAllCategories",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="The page number for pagination",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="The number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=15
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="The field to sort by",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="created_at"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         description="Sort in descending order",
     *         required=false,
     *         @OA\Schema(
     *             type="boolean",
     *             example=true
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categories fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Categories fetched successfully."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CategoryResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Server Error"
     *             )
     *         )
     *     )
     * )
     */
    public function getAllCategories(Request $request) {
        try {
            $categories = $this->categoryService->getAllCategories($request);

            return CategoryResource::collection($categories)->additional([
                'status' => true,
                'message' => 'Categories fetched successfully.'
            ]);
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }
}
