<?php

namespace App\Http\Controllers\Dashboard;


use App\Events\User\UserCreateEvent;
use App\Helpers\EncryptionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\User\UserData;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('menu.custommer.index');
    }

    public function getUsers(Request $request)
    {
        $pegawai = new UserData();

        $start = $request->input('start') + 1;
        $list = [];
        $data = $pegawai->get_datauser($request);
        foreach ($data as $item) {
            $row = [];
            $row[] = $start++;
            $row[] = "<div class='flex flex-col'> <p class='font-bold text-sm'>" . $item->name_customer . "</p> <p class='text-sm'>" . $item->email . "</p> </div>";
            $row[] = $item->getRoleNames();
            $row[] = Carbon::parse($item->created_at)->format('d-m-Y');
            $row[] = "";
            $row[4] .= "<a href='dashboard/user/edit/" . EncryptionHelper::encrypt_custom($item->id) . "' class='inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800' id='editModalBtn'>Edit</a>";
            $row[4] .= "<a href='dashboard/user/delete/" . EncryptionHelper::encrypt_custom($item->id) . "' class='inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900' >Delete</a>";
            $list[] = $row;
        }

        $output = [
            "draw" => $request->input('draw'),
            "recordsTotal" => $pegawai->count_all(),
            "recordsFiltered" => $pegawai->count_filtered($request),
            "data" => $list,
        ];

        return response()->json($output);
    }

    public function create(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required',
                'email' => 'required|unique:users,email',
                'password' => 'min:8|required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'min:8',
            ]);
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password'] ?? 'Password124@'),
            ]);

            $user->assignRole('customer');

            event(new UserCreateEvent($user));

            return response()->json(['status' => true, 'data' => $user, 'message' => 'User created successfully']);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();

            return response()->json([
                'errors' => $errors
            ], 422);
        }
    }
    public function show($id)
    {
        $id = EncryptionHelper::decrypt_custom($id);
        $user = User::find($id);
        if ($user == null) {
            return response()->json(['status' => false, 'message' => 'User not found']);
        }

        return response()->json(['status' => true, 'data' => $user]);
    }

    public function update(Request $request, $id)
    {
        try {

            $id = EncryptionHelper::decrypt_custom($id);
            $user = User::find($id);
            if ($user == null) {
                return response()->json(['status' => false, 'message' => 'User not found']);
            }

            $data = $request->validate([
                'name' => 'required',
                'email' => 'required',
            ]);
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);
            if (isset($data['password'])) {
                $user->update([
                    'password' => bcrypt($data['password']),
                ]);
            }
            return response()->json(['status' => true, 'data' => $user, 'message' => 'User updated successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $id = EncryptionHelper::decrypt_custom($id);
            $user = User::find($id);
            if ($user == null) {
                return response()->json(['status' => false, 'message' => 'User not found']);
            }
            $user->delete();
            return response()->json(['status' => true, 'message' => 'User deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => false, 'errors' => $th, 'message' => $th->getMessage()], 500);
        }
    }
}
