<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Http\Requests\TodoUpdateRequest;
use App\Http\Requests\TodoAddRequest;
use App\Http\Requests\UserEditRequest ;

use Cviebrock\EloquentSluggable\Services\SlugService;

use App\Http\Resources\UserResource;
use App\Http\Resources\TodoResource;
use App\Http\Resources\TodoCollection;
use App\Http\Resources\UserCollection;

class TodoController extends Controller
{
    // init constructor
    public function __construct()
    {
        // validate authenticated users
        $this->middleware('auth:api');
    }


    //---------------------------------------    //user crud//----------------------------------------------------//

 //save item
 public function edit_user(UserEditRequest $request)
 {
     // Get the currently authenticated user's ID...
     $id = Auth::user()->id;
     try {
         $user = User::findOrFail($id);
     } catch (Exception $ex) {
         return response()->json([
             'status' => 'Error',
             'message' => $ex,
         ]);
     }
      $avatarName = null;
     //check if avatar image is provided
     if ($request->hasFile('image')) {
         $file = $request->file('image');
         //file extension and name
         $avatarName = '/avatars/' . uniqid() . '.' . $file->extension();
         //save avatar

         if ($request->filled('name') && $file->storePubliclyAs('public', $avatarName)) {
             $user->name = $request->name;
             $user->image = '/storage'.$avatarName;
             if ($user->save()) {
                 return response()->json([
                     'status' => 'success',
                     'message' => 'user details updated Successfully',
                 ], 200);
             }
         } else {
             if ($file->storePubliclyAs('public', $avatarName)) {
                 $user->image = '/storage'.$avatarName;
                 if ($user->save()) {
                     return response()->json([
                         'status' => 'success',
                         'message' => 'Profile picture updated successfully**',
                     ], 200);
                 }
             }
         }
     } else {
         if ($request->filled('name')) {
             $user->name = $request->name;
             if ($user->save()) {
                 return response()->json([
                     'status' => 'success',
                     'message' => 'user details updated Successfully',
                 ], 200);
             }
         } else {
             return response()->json([
                 'status' => 'nothing changed',
                 'message' => 'nothing has been updated',
             ], 304);
         }
     }
 }



    //---------------------------------------    //todos crud//----------------------------------------------------//

    //get all items
    public function index()
    {
        //where to query only users items
        $query = Todo::all();


        $todos = $query->where('user_id', '=', Auth::user()->id);



        $count = $todos->count();
        if ($count < 1) {
            return response()->json([
                'response' => 'user has no items',
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'my items' => $count,
                'items array' => new TodoCollection($todos) ,
            ]);
        }
    }

    //save item
    public function create_item(TodoAddRequest $request)
    {
        // Get the currently authenticated user's ID...
        $id = Auth::user()->id;
        $avatarName = null;
        //check if avatar image is provided
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            //file extension and name
            $avatarName = '/avatars/' . uniqid() . '.' . $file->extension();
            //save avatar
            $file->storePubliclyAs('public', $avatarName);
        }



        // $validatedData = $request->validated();
        $todo = Todo::create([
         'user_id' => $id,
         'title' => $request->title,
         'description' => $request->description,
         'image' => '/storage'.$avatarName
        ]);

        if ($todo) {
            return response()->json([
                'status' => 'success',
                'message' => 'Item created successfully',
                'todo' => new TodoResource($todo),
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Item not created',
            ], 504);
        }
    }

    //get specific item
    public function show($id)
    {
        try {
            $todo =  Todo::findOrFail($id)->first();
            $todo = new TodoResource($todo);
            //$todo = Todo::where('id', '=', $id)->get();
        } catch (\Exception $e) {
            return response()->json(['message' => 'item not found!'], 404);
        }

        $user = Auth::user();
        if (Gate::allows('can_get', $todo)||Gate::allows('isAdmin', $user) || Gate::allows('isManager', $user)) {
            return response()->json([
                'status' => true,
                'details' => $todo,
            ]);
        } else {
            return response()->json([
                'status' => 'not allowed to view item',
            ], 400);
        }
    }

    //edit
    public function update(TodoUpdateRequest $request, $id)
    {
        try {
            $todo = Todo::findOrFail($id);
        } catch (Exception $ex) {
            return response()->json([
                'status' => 'Error',
                'message' => $ex,
            ]);
        }

        $todo->title = $request->title;
        $todo->description = $request->description;

        if ($todo->save()) {
            return response()->json([
                'status' => 'success',
                'message' => 'updated successfully',
                'todo' => $todo,
            ]);
        }
    }

    //delete
    public function destroy($id)
    {
        // $affectedRows = Todo::where('id', '=', $id)->where('user_id', '=', Auth::user()->id);

        try {
            $todo = Todo::findOrFail($id);
            //$todo = Todo::where('id', '=', $id)->get();
        } catch (\Exception$e) {
            return response()->json(['message' => 'item not found!'], 404);
        }

        $user = Auth::user();

        if (Gate::allows('can_delete', $todo)) {
            if ($todo->delete()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Item With Index: ' . $id . ' deleted successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Deleting of ' . $id . ' failed ',

                ]);
            }
        } else {
            if (Gate::allows('isAdmin', $user) || Gate::allows('isManager', $user)) {
                if ($todo->delete()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Item With Index: ' . $id . ' deleted successfully',

                    ]);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Deleting of ' . $id . ' failed ',

                    ]);
                }
            }
        }
    }


    //---------------------------------misc admin functions------------------------------------------------------------//

    public function get_user()
    {
        $id = Auth::user()->id;
        $user = User::where('id', '=', $id)->get()->first();
        return response()->json([
            'status' => 'success',
            //  'message' => 'user: '.$user->name,
            'user' => new UserResource($user),
        ]);
    }

    public function get_my_todos()
    {
        $id = Auth::user()->id;

        try {
            $todos = Todo::where('user_id', '=', $id)->get();
        } catch (\Exception$e) {
            return response()->json(['message' => 'item not found!'], 404);
        }
        //use first if returning collection to return single object  -> use first to get an object not collection
        $user = User::where('id', '=', $id)->get()->first();

        $user = new UserResource($user);
        $count_todos = $todos->count();

        if ($count_todos < 1) {
            $todos = 'user has no items';
        }

        return response()->json([
            'status' => 'success',
            //  'message' => 'user: '.$user->name,
            'user' => $user,
            'todos' => new TodoCollection ($todos),
        ]);
    }


    public function get_specific_user_todos($id)
    {
        $current_user = Auth::user();
        if (Gate::allows('isAdmin', $current_user) || Gate::allows('isManager', $current_user)) {
            try {
                $todos = Todo::where('user_id', '=', $id)->get();
            } catch (\Exception$e) {
                return response()->json(['message' => 'item not found!'], 404);
            }
            //use first if returning collection to return single object  -> use first to get an object not collection
            $user = User::where('id', '=', $id)->get()->first();

            $user = new UserResource($user);
            $count_todos = $todos->count();

            if ($count_todos < 1) {
                $todos = 'user has no items';
            }

            return response()->json([
                'status' => 'success',
                //  'message' => 'user: '.$user->name,
                'user' => $user,
                'todos' => $todos,
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'only admins can do this',
            ], 400);
        }
    }




    public function get_all_users_admin(Request $request)
    {
        $user = Auth::user();
        if (Gate::allows('isAdmin', $user) || Gate::allows('isManager', $user)) {
            if ($users =new UserCollection(User::all())) {
                $count = $users->count();
                if ($count < 1) {
                    return response()->json([
                        'response' => 'no users',
                    ]);
                } else {
                    return response()->json([
                        'status' => true,
                        'No of users :' => $count.' users',
                         $users
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Fetch method failed',

                ], 504);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'You must be admin or superuser to view this',

            ], 400);
        }
    }

    public function get_item_count(Request $request)
    {
        $user = Auth::user();
        if (Gate::allows('isAdmin', $user) || Gate::allows('isManager', $user)) {
            if ($todos = TOdo::all()) {
                $count = $todos->count();
                if ($count < 1) {
                    return response()->json([
                        'response' => 'no items',
                    ]);
                } else {
                    return response()->json([
                        'status' => 'success',
                        'No of items :' => $count.' items',
                        'items' => $todos
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Fetch method failed',

                ], 504);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'You must be admin or superuser to view this',
            ], 400);
        }
    }

    public function get_all_users_and_their_items(Request $request)
    {
        $user = Auth::user();
        if (Gate::allows('isAdmin', $user) || Gate::allows('isManager', $user)) {
            if ($users = User::all()) {
                $users =  new UserCollection($users);
                $count_users = $users->count();

                if ($count_users < 1) {
                    return response()->json([
                        'response' => 'no users',
                    ]);
                } else {
                    $response =  array();
                    //iterate through all users
                    foreach ($users as $user) {
                        $todos = Todo::all()->where('user_id', '=', $user->id);
                        $todos = new TodoCollection($todos);
                        $count = $todos->count();

                        if ($count < 1) {
                            array_push($response, $user, 'user: '.$user->name .' has no items', );
                        } else {
                            array_push(
                                $response,
                                [
                                'user: ' => $user,
                                'User '.$user->name.' number of items: ' => $count,
                                'User '.$user->name.' items: ' => $todos,]
                            );
                            // array_push($response,$user->toJson(JSON_PRETTY_PRINT));
                        }
                    }

                    return response()->json([
                        'status: ' => true,
                        'No of users: ' => $count_users,
                        $response
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Fetch method failed',

                ], 504);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'You must be admin or superuser to view this',

            ], 400);
        }
    }
}
