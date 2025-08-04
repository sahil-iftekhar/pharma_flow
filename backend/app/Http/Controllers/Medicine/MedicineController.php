<?php

namespace App\Http\Controllers\Medicine;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medicine\RegisterMedicineRequest;
use App\Http\Requests\Medicine\UpdateMedicineRequest;
use App\Models\Medicine;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 * name="Medicines",
 * description="API Endpoints for Medicine Management"
 * )
 *
 * @OA\Schema(
 * schema="Medicine",
 * title="Medicine",
 * description="Medicine model",
 * @OA\Property(property="id", type="integer", format="int64", description="Medicine ID"),
 * @OA\Property(property="name", type="string", description="Name of the medicine"),
 * @OA\Property(property="description", type="string", nullable=true, description="Description of the medicine"),
 * @OA\Property(property="price", type="number", format="float", description="Price of the medicine"),
 * @OA\Property(property="dosage", type="string", description="Dosage information"),
 * @OA\Property(property="brand", type="string", description="Brand of the medicine"),
 * @OA\Property(property="image_url", type="string", format="url", nullable=true, description="URL of the medicine's image"),
 * @OA\Property(property="stock", type="integer", description="Current stock quantity"),
 * @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/Category")),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp of creation"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of last update"),
 * example={
 * "id": 1, "name": "Aspirin", "description": "Pain relief medicine", "price": 5.99, "dosage": "500mg",
 * "brand": "Bayer", "image_url": "http://example.com/aspirin.jpg", "stock": 100,
 * "categories": {{"id": 1, "name": "Painkillers"}},
 * "created_at": "2023-01-01T12:00:00.000000Z", "updated_at": "2023-01-01T12:00:00.000000Z"
 * }
 * )
 */
class MedicineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     * path="/api/medicines",
     * operationId="getMedicinesList",
     * tags={"Medicines"},
     * summary="Get all medicines",
     * description="Retrieves a list of all medicines with their categories.",
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/Medicine")
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
        $medicines = Medicine::with('categories')->get();
        return response()->json($medicines);
    }

    /**
     * @OA\Post(
     * path="/api/medicines",
     * operationId="createMedicine",
     * tags={"Medicines"},
     * summary="Create a new medicine",
     * description="Creates a new medicine and links it to categories. Requires admin privileges.",
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "price", "dosage", "brand", "stock", "category_ids"},
     * @OA\Property(property="name", type="string", example="Paracetamol"),
     * @OA\Property(property="description", type="string", nullable=true, example="Fever and pain reducer."),
     * @OA\Property(property="price", type="number", format="float", example=7.50),
     * @OA\Property(property="dosage", type="string", example="500mg"),
     * @OA\Property(property="brand", type="string", example="Panadol"),
     * @OA\Property(property="image_url", type="string", nullable=true, format="url", example="http://example.com/paracetamol.jpg"),
     * @OA\Property(property="stock", type="integer", example=200),
     * @OA\Property(property="category_ids", type="array", @OA\Items(type="integer"), example={1, 2})
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Medicine created successfully",
     * @OA\JsonContent(ref="#/components/schemas/Medicine")
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
    public function store(RegisterMedicineRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $categoryIds = $validated['category_ids'];
        unset($validated['category_ids']);

        try {
            $medicine = Medicine::create($validated);
            $medicine->categories()->attach($categoryIds);
            $medicine->load('categories');
            return response()->json($medicine, 201);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => 'Failed to create medicine.'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/medicines/{id}",
     * operationId="getMedicineById",
     * tags={"Medicines"},
     * summary="Get medicine by ID",
     * description="Retrieves a specific medicine by its ID with its categories.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer"),
     * description="ID of the medicine"
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/Medicine")
     * ),
     * @OA\Response(
     * response=404,
     * description="Medicine not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $medicine = Medicine::with('categories')->find($id);
        
        if (!$medicine) {
            return response()->json([
                'errors' => 'Medicine not found.'
            ], 404);
        }
        
        return response()->json($medicine);
    }

    /**
     * @OA\Put(
     * path="/api/medicines/{id}",
     * operationId="updateMedicine",
     * tags={"Medicines"},
     * summary="Update a medicine",
     * description="Updates an existing medicine. Requires admin privileges.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer"),
     * description="ID of the medicine to update"
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="name", type="string", example="New Aspirin"),
     * @OA\Property(property="price", type="number", format="float", example=6.50),
     * @OA\Property(property="category_ids", type="array", @OA\Items(type="integer"), example={1, 3})
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Medicine updated successfully",
     * @OA\JsonContent(ref="#/components/schemas/Medicine")
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
     * description="Medicine not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function update(UpdateMedicineRequest $request, string $id): JsonResponse
    {
        $medicine = Medicine::find($id);

        if (!$medicine) {
            return response()->json([
                'errors' => 'Medicine not found.'
            ], 404);
        }

        $validated = $request->validated();

        try {
            if (isset($validated['category_ids'])) {
                $medicine->categories()->sync($validated['category_ids']);
                unset($validated['category_ids']);
            }
            
            $medicine->update($validated);
            $medicine->load('categories');
            
            return response()->json($medicine);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => 'Failed to update medicine.'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/medicines/{id}",
     * operationId="deleteMedicine",
     * tags={"Medicines"},
     * summary="Delete a medicine",
     * description="Deletes a medicine by ID. Requires admin privileges.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer"),
     * description="ID of the medicine to delete"
     * ),
     * @OA\Response(
     * response=200,
     * description="Medicine deleted successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Medicine deleted successfully.")
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
     * description="Medicine not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $medicine = Medicine::find($id);
        
        if (!$medicine) {
            return response()->json([
                'errors' => 'Medicine not found.'
            ], 404);
        }
        
        $medicine->delete();
        return response()->json([
            'success' => 'Medicine deleted successfully.'
        ], 200);
    }
}