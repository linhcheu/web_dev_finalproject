<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    private const IMAGE_RULE = 'image|mimes:jpeg,jpg,png,gif,webp|max:2048';

    public function index(Request $request): View
    {
        $users = $this->api->paginate('admin/users', User::class, $request->only(
            'user_id', 'role', 'name', 'email', 'sort_by', 'sort_order', 'page'
        ));

        return view('user_management', [
            'users' => $users,
            'totalUsers' => $users->extra['total_all'],
            'roles' => collect($users->extra['roles']),
            'sortBy' => $request->input('sort_by', 'user_id'),
            'sortOrder' => $request->input('sort_order', 'asc'),
        ]);
    }

    public function create(): View
    {
        return view('user_create', ['roles' => ['user', 'hospital_admin']]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:user,hospital_admin',
            'password' => 'required|string|min:8|confirmed',
            'profile_picture' => 'nullable|'.self::IMAGE_RULE,
        ]);

        $this->api->post('admin/users', array_filter($data, fn ($v) => $v !== null));

        return redirect()->route('user.management')->with('success', 'User created successfully!');
    }

    public function edit(int $id): View
    {
        return view('user_edit', ['user' => User::fromApi($this->api->get("admin/users/$id")['data'])]);
    }

    /** The edit page has one form per "update_type". */
    public function update(Request $request, int $id): RedirectResponse
    {
        $data = match ($request->input('update_type')) {
            'profile_picture' => $request->validate(['profile_picture' => 'required|'.self::IMAGE_RULE]),
            'basic_info' => $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'phone' => 'nullable|string|max:20',
            ]),
            'email' => $request->validate(['email' => 'required|email|max:100']),
            'password' => ['password' => $request->validate(['new_password' => 'required|string|min:8|confirmed'])['new_password']],
            default => null,
        };

        if ($data === null) {
            return back()->with('error', 'Invalid update type.');
        }

        $this->api->post("admin/users/$id", $data, ['password' => 'new_password']);

        return redirect()->route('user.edit', $id)->with('success', 'User updated successfully!');
    }

    public function destroy(int $id): JsonResponse
    {
        return response()->json(['success' => true] + $this->api->delete("admin/users/$id"));
    }
}
