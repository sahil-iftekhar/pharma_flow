<?php

namespace App\Http\Controllers\Medicine;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medicine\RegisterCategoryRequest;
use App\Http\Requests\Medicine\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 * name="Categories",
 * description="API Endpoints for Category Management"
 * )
 *
 * @OA\Schema(
 * schema="Category",
 * title="Category",
 * description="Category model",
 * @OA\Property(property="id", type="integer", format="int64", description="Category ID"),
 * @OA\Property(property="name", type="string", description="Category name"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp of creation"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of last update"),
 * example={"id": 1, "name": "Painkillers", "created_at": "2023-01-01T12:00:00.000000Z", "updated_at": "2023-01-01T12:00:00.000000Z"}
 * )
 */
class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     * path="/api/categories",
     * operationId="getCategoriesList",
     * tags={"Categories"},
     * summary="Get all categories",
     * description="Retrieves a list of all medicine categories.",
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/Category")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function index(): JsonResponse
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    /**
     * @OA\Post(
     * path="/api/categories",
     * operationId="createCategory",
     * tags={"Categories"},
     * summary="Create a new category",
     * description="Creates a new category. Requires admin privileges.",
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name"},
     * @OA\Property(property="name", type="string", example="Antibiotics")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Category created successfully",
     * @OA\JsonContent(ref="#/components/schemas/Category")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: User lacks admin privileges.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function store(RegisterCategoryRequest $request): JsonResponse
    {
        try {
            $category = Category::create($request->validated());
            return response()->json($category, 201);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => 'Failed to create category.'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/categories/{id}",
     * operationId="getCategoryById",
     * tags={"Categories"},
     * summary="Get category by ID",
     * description="Retrieves a specific category by its ID.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer"),
     * description="ID of the category"
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/Category")
     * ),
     * @OA\Response(
     * response=404,
     * description="Category not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'errors' => 'Category not found.'
            ], 404);
        }
        
        return response()->json($category);
    }

    /**
     * @OA\Put(
     * path="/api/categories/{id}",
     * operationId="updateCategory",
     * tags={"Categories"},
     * summary="Update a category",
     * description="Updates an existing category. Requires admin privileges.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer"),
     * description="ID of the category to update"
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="name", type="string", example="Analgesics")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Category updated successfully",
     * @OA\JsonContent(ref="#/components/schemas/Category")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: User lacks admin privileges.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Category not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function update(UpdateCategoryRequest $request, string $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'errors' => 'Category not found.'
            ], 404);
        }
        
        try {
            $category->update($request->validated());
            return response()->json($category);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => 'Failed to update category.'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/categories/{id}",
     * operationId="deleteCategory",
     * tags={"Categories"},
     * summary="Delete a category",
     * description="Deletes a category by ID. Requires admin privileges.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer"),
     * description="ID of the category to delete"
     * ),
     * @OA\Response(
     * response=200,
     * description="Category deleted successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Category deleted successfully.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: User lacks admin privileges.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Category not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'errors' => 'Category not found.'
            ], 404);
        }

        $category->delete();
        return response()->json([
            'success' => 'Category deleted successfully.'
        ], 200);
    }
}