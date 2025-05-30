<?php

namespace App\Traits\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

trait CriterioTrait
{
    protected function rules(): array
    {
        return [
            'page' => 'nullable|integer|min:1',
            'pageSize' => 'nullable|integer|min:1',
            'sortField' => 'nullable|string',
            'sortOrder' => 'nullable|string|in:asc,desc,null',
            'rangeField' => 'nullable|string',
            'rangeValues' => 'nullable|array',
            'filters' => 'nullable|array',
            'searchTerm' => 'nullable|string',
            'searchColumn' => 'nullable|string',
        ];
    }

    protected function validateCriteria(Request $request): array
    {
        return $request->validate($this->rules());
    }

    protected function obtenerCriterios(Request $request): array
    {
        try {
            $validatedData = $this->validateCriteria($request);

            $sortField = $validatedData['sortField'] ?? null;
            $sortOrder = $validatedData['sortOrder'] ?? null;

            if ($sortField === 'null') {
                $sortField = null;
            }
            if ($sortOrder === 'null') {
                $sortOrder = null;
            }
            return [
                'pageIndex' => $validatedData['page'] ?? 1,
                'pageSize' => $validatedData['pageSize'] ?? 5,
                'sortField' => $sortField,
                'sortOrder' => $sortOrder,
                'searchTerm' => $validatedData['searchTerm'] ?? '',
                'searchColumn' => $validatedData['searchColumn'] ?? null,
                'range' => [
                    'field' => $validatedData['rangeField'] ?? null,
                    'values' => $validatedData['rangeValues'] ?? null,
                ],
                'filters' => $validatedData['filters'] ?? [],
            ];
        } catch (ValidationException $e) {
            throw new InvalidArgumentException('Criterios de búsqueda inválidos: ' . $e->getMessage(), 400);
        }
    }
}
