<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class IndexController extends Controller
{

    /**
     * @return View
     */
    public function index(): View
    {
        return view('main.index', [
            'categories' => Category::query()->limit(4)->get(),
            'tools' => Tool::with('category')->limit(8)->get(),
            'faqItems' => [
                [
                    'question' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry.",
                    'answer' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
                ],
                [
                    'question' => "Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
                    'answer' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
                ],
            ],
        ]);
    }

    /**
     * @param string $category_slug
     * @return View
     */
    public function category(string $category_slug): View
    {
        $category = Category::query()->where('slug', $category_slug)->first();
        if (!$category) {
            abort(404);
        }
        return view('main.category', [
            'category' => $category,
            'tools' => Tool::with('category')
                ->where('category_id', $category->id)
                ->paginate(24)
        ]);
    }

    /**
     * @param string $category_slug
     * @param string $tool_slug
     * @return View
     */
    public function tool(string $category_slug, string $tool_slug): View
    {
        $tool = Tool::with('category')->where('slug', $tool_slug)->first();
        if (!$tool) {
            abort(404);
        }
        $category = $tool->category;
        if ($category->slug != $category_slug) {
            abort(404);
        }
        return view('main.tool', [
            'category' => $category,
            'tool' => $tool,
            'similarTools' => Tool::query()
                ->where('category_id', $category->id)
                ->where('id','!=', $tool->id)
                ->inRandomOrder()
                ->limit(4)
                ->get(),
        ]);
    }

    /**
     * @return View
     */
    public function categories(): View
    {
        return view('main.categories', [
            'categories' => Category::query()->paginate(24)
        ]);
    }

    /**
     * @return View
     */
    public function tools(): View
    {
        return view('main.tools', [
            'tools' => Tool::with('category')
                ->orderBy('id', 'desc')
                ->paginate(24)
        ]);
    }

    /**
     * @param Request $request
     * @return View
     */
    public function search(Request $request): View
    {
        $query = $request->input('query');
        return view('main.search', [
            'query' => $query,
            'tools' => Tool::with('category')
                ->where('name','like',"%$query%")
                ->orWhere('short_desc','like',"%$query%")
                ->limit(24)
                ->get()
        ]);
    }

    /**
     * @return View
     */
    public function policy(): View
    {
        return view('main.policy');
    }

    /**
     * @return View
     */
    public function terms(): View
    {
        return view('main.terms');
    }

    /**
     * @return View
     */
    public function about(): View
    {
        return view('main.about');
    }
}
