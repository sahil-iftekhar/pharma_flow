<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\UpdateCartItemRequest;
use App\Models\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 * name="Cart",
 * description="API Endpoints for Cart Management"
 * )
 *
 * @OA\Schema(
 * schema="CartItem",
 * title="CartItem",
 * description="Cart Item model",
 * @OA\Property(property="id", type="integer", format="int64", description="Cart Item ID"),
 * @OA\Property(property="cart_id", type="integer", format="int64", description="ID of the parent cart"),
 * @OA\Property(property="medicine_id", type="integer", format="int64", description="ID of the medicine in the cart"),
 * @OA\Property(property="quantity", type="integer", description="Quantity of the medicine"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp of creation"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of last update"),
 * @OA\Property(property="medicine", ref="#/components/schemas/Medicine", description="The medicine details"),
 * example={
 * "id": 1, "cart_id": 1, "medicine_id": 10, "quantity": 2,
 * "created_at": "2023-01-01T12:00:00.000000Z", "updated_at": "2023-01-01T12:00:00.000000Z",
 * "medicine": {"id": 10, "name": "Ibuprofen",}
 * }
 * )
 */
class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     * path="/api/cart",
     * operationId="getUserCart",
     * tags={"Cart"},
     * summary="Get the authenticated user's cart items",
     * description="Retrieves all items in the authenticated user's shopping cart.",
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/CartItem")
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
        $user = Auth::user();
        
        $cart = $user->cart()->with('items.medicine')->first();
        
        if (!$cart) {
            return response()->json(['items' => []]);
        }
        
        return response()->json($cart->items);
    }

    /**
     * @OA\Put(
     * path="/api/cart/items/{id}",
     * operationId="updateCartItem",
     * tags={"Cart"},
     * summary="Update a cart item's quantity",
     * description="Updates the quantity of a specific cart item. Only the owner of the cart can update an item.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer"),
     * description="ID of the cart item to update"
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"quantity"},
     * @OA\Property(property="quantity", type="integer", example=5)
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Cart item updated successfully",
     * @OA\JsonContent(ref="#/components/schemas/CartItem")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: User is not authorized to update this item.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Cart item not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function update(UpdateCartItemRequest $request, string $id): JsonResponse
    {
        $cartItem = CartItem::find($id);

        if (!$cartItem) {
            return response()->json([
                'errors' => 'Cart item not found.'
            ], 404);
        }
        
        if ($cartItem->cart->user_id !== Auth::id()) {
            return response()->json([
                'errors' => 'You are not authorized to update this cart item.'
            ], 403);
        }

        try {
            $cartItem->update($request->validated());
            $cartItem->load('medicine');
            return response()->json($cartItem);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => 'Failed to update cart item.'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/cart/items/{id}",
     * operationId="deleteCartItem",
     * tags={"Cart"},
     * summary="Delete a cart item",
     * description="Deletes a specific cart item. Only the owner of the cart can delete the item.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * @OA\Schema(type="integer"),
     * description="ID of the cart item to delete"
     * ),
     * @OA\Response(
     * response=200,
     * description="Cart item deleted successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Cart item deleted successfully.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: User is not authorized to delete this item.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Cart item not found",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $cartItem = CartItem::find($id);
        
        if (!$cartItem) {
            return response()->json([
                'errors' => 'Cart item not found.'
            ], 404);
        }
        
        if ($cartItem->cart->user_id !== Auth::id()) {
            return response()->json([
                'errors' => 'You are not authorized to delete this cart item.'
            ], 403);
        }

        $cartItem->delete();
        return response()->json([
            'success' => 'Cart item deleted successfully.'
        ], 200);
    }
}