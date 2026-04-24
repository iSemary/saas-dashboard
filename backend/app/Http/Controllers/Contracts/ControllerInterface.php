<?php

namespace App\Http\Controllers\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Controller Interface following Interface Segregation Principle
 * Defines the contract for all controllers
 */
interface ControllerInterface
{
    /**
     * Display a listing of the resource
     */
    public function index(): View|JsonResponse;

    /**
     * Show the form for creating a new resource
     */
    public function create(): View;

    /**
     * Store a newly created resource in storage
     */
    public function store(Request $request): JsonResponse;

    /**
     * Display the specified resource
     */
    public function show(int $id): View|JsonResponse;

    /**
     * Show the form for editing the specified resource
     */
    public function edit(int $id): View|JsonResponse;

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, int $id): JsonResponse;

    /**
     * Remove the specified resource from storage
     */
    public function destroy(int $id): JsonResponse;
}
