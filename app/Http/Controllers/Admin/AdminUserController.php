<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminEditUserRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Services\UserService;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     summary="Get all users",
     *     description="Retrieve a paginated list of users with search and sorting options.",
     *     operationId="getUsers",
     *     tags={"Admin"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *         @OA\Schema(type="string", example="created_at")
     *     ),
     *     @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         description="Sort in descending order",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         description="Filter by first name",
     *         required=false,
     *         @OA\Schema(type="string", example="John")
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
     *         in="query",
     *         description="Filter by last name",
     *         required=false,
     *         @OA\Schema(type="string", example="Doe")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Filter by email",
     *         required=false,
     *         @OA\Schema(type="string", example="john.doe@example.com")
     *     ),
     *     @OA\Parameter(
     *         name="phone_number",
     *         in="query",
     *         description="Filter by phone number",
     *         required=false,
     *         @OA\Schema(type="string", example="1234567890")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Users fetched successfully."),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/UserResource")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Server Error")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function getUsers(Request $request) {
        try {
            $orders = $this->userService->getUsers($request);
            return UserResource::collection($orders)->additional([
                'status' => true,
                'message' => 'orders fetched successfully.'
            ]);
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/admin/user-edit/{uuid}",
     *     summary="Edit User",
     *     description="Edit an existing user by UUID",
     *     tags={"Admin"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID of the user to edit",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AdminEditUserRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User edited successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/UserResource"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Bad Request"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
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
    public function editUser(AdminEditUserRequest $request, $uuid) {
        try {
            $user = $this->userService->editUser($request->validated(), $uuid);
            if (!$user['status']) {
                return $this->failedResponse($user['message']);
            }
            return  $this->successResponse("", new UserResource($user['data']));
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/admin/user-delete/{uuid}",
     *     summary="Delete a user",
     *     description="Delete a user account by UUID.",
     *     tags={"Admin"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="UUID of the user to delete",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User account deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="User account has been deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Unable to delete account",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unable to delete account.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Server Error")
     *         )
     *     )
     * )
     */
    public function deleteUser($uuid) {
        try {
            $user = $this->userService->deleteUser($uuid);
            if (!$user['status']) {
                return $this->failedResponse("Unable to delete account.");
            }
            return  $this->successResponse("User account has been deleted successfully.");
        } catch (\Exception $exception) {
            return $this->serverErrorResponse("Server Error", $exception);
        }
    }
}
