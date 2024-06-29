<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Resources\Admin\CategoryResource;
use App\Http\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    /**
     * @OA\Post(
     *     path="/api/v1/category/create",
     *     summary="Create a new category",
     *     description="Create a new category. Only accessible by admins.",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreateCategoryRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="category created successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/CategoryResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Server Error")
     *         )
     *     )
     * )
     */
    public function createCategory(CreateCategoryRequest $request) {
        try {
            $category = $this->categoryService->createCategory($request->validated());
            return  $this->successResponse("category created successfully.", new CategoryResource($category), Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/category/{uuid}",
     *     summary="Update a category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="UUID of the category to update"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string", example="Updated Category Title"),
     *             @OA\Property(property="description", type="string", example="Updated Category Description"),
     *             @OA\Property(property="slug", type="string", example="updated-category-title")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category edited successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category edited successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Updated Category Title"),
     *                 @OA\Property(property="slug", type="string", example="updated-category-title"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Server Error")
     *         )
     *     )
     * )
     */
    public function updateCategory(UpdateCategoryRequest $request, $uuid) {
        try {
            $category = $this->categoryService->updateCategory($request->validated(), $uuid);
            if (!$category['status']) {
                return $this->failedResponse($category['message']);
            }
            return  $this->successResponse("Category edited successfully", new CategoryResource($category['data']));
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }

    /**
     * Delete a category by UUID.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Delete(
     *     path="/api/v1/category/{uuid}",
     *     summary="Delete a category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="UUID of the category to delete",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to delete category",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unable to delete category.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden: User does not have permission",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You don't have permission to operate this route.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server Error")
     *         )
     *     )
     * )
     */
    public function deleteCategory($uuid) {
        try {
            $category = $this->categoryService->deleteCategory($uuid);
            if (!$category['status']) {
                return $this->failedResponse($category['message']);
            }
            return  $this->successResponse("Category deleted successfully.");
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }

    /**
     * Retrieve a category by UUID.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/v1/category/{uuid}",
     *     summary="Get a category by UUID",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="UUID of the category to retrieve",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/CategoryResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized: Authentication failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden: User does not have permission",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You don't have permission to operate this route.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Server Error")
     *         )
     *     )
     * )
     */
    public function getCategory($uuid) {
        try {
            $status = $this->categoryService->getCategory($uuid);
            if (!$status['status']) {
                return $this->failedResponse($status['message'], Response::HTTP_NOT_FOUND);
            }
            return  $this->successResponse("", new CategoryResource($status['data']));
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }
}
