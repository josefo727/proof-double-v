<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerCollection;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('view-any', Customer::class);

        $search = $request->get('search', '');

        $customers = Customer::query()
            ->search($search)
            ->latest()
            ->paginate();

        return response()->success(CustomerCollection::make($customers));
    }

    public function store(CustomerStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Customer::class);

        $validated = $request->validated();

        $customer = Customer::query()->create($validated);

        return response()->success(new CustomerResource($customer), 'Registro creado exitosamente', Response::HTTP_CREATED);
    }

    public function show(Request $request, Customer $customer): JsonResponse
    {
        $this->authorize('view', $customer);

        return response()->success(new CustomerResource($customer));
    }

    public function update(CustomerUpdateRequest $request, Customer $customer): JsonResponse
    {
        $this->authorize('update', $customer);

        $validated = $request->validated();

        $customer->update($validated);

        return response()->success(new CustomerResource($customer), 'Registro actualizado exitosamente', Response::HTTP_OK);
    }

    public function destroy(Request $request, Customer $customer): JsonResponse
    {
        $this->authorize('delete', $customer);

        $customer->delete();

        return response()->success(null, null, Response::HTTP_NO_CONTENT);
    }
}
