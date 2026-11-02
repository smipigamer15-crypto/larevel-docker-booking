<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'sort' => ['nullable', 'in:newest,price_low,price_high,popular'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = Service::with('category')->where('is_active', true);

        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        if ($request->has('min_price') && $request->min_price != '') {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price != '') {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('duration') && $request->duration != '') {
            $query->where('duration', $request->duration);
        }

        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->withCount('bookings')->orderBy('bookings_count', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $services = $query->paginate(12)->withQueryString();
        $categories = Category::all();
        $durations = Service::where('is_active', true)->distinct()->pluck('duration')->sort()->values();
        $maxPrice = Service::where('is_active', true)->max('price') ?? 1000;

        return view('services.index', compact('services', 'categories', 'durations', 'maxPrice'));
    }

    public function show(Service $service)
    {
        abort_unless($service->is_active, 404);
        $relatedServices = Service::where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        $averageRating = $service->reviews()->where('is_approved', true)->avg('rating') ?? 0;
        $reviewsCount = $service->reviews()->where('is_approved', true)->count();

        return view('services.show', compact('service', 'relatedServices', 'averageRating', 'reviewsCount'));
    }
}
