<?php

namespace App\Http\Controllers;

use App\Group;
use Illuminate\Http\Request;

class GroupsController extends Controller
{
    /**
     * Display a listing of the group.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = Group::where(function ($query) {
            $query->where('name', 'like', '%'.request('q').'%');
        })
            ->where('creator_id', auth()->id())
            ->paginate(25);

        return view('groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new group.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new Group);

        return view('groups.create');
    }

    /**
     * Store a newly created group in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', new Group);

        $this->validate($request, [
            'name'        => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);

        $newGroup = $request->only('name', 'description');
        $newGroup['creator_id'] = auth()->id();

        $group = Group::create($newGroup);

        return redirect()->route('groups.show', $group);
    }

    /**
     * Display the specified group.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        return view('groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified group.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        $this->authorize('update', $group);

        return view('groups.edit', compact('group'));
    }

    /**
     * Update the specified group in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        $this->authorize('update', $group);

        $this->validate($request, [
            'name'        => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);

        $group->update($request->only('name', 'description'));

        return redirect()->route('groups.show', $group);
    }

    /**
     * Remove the specified group from storage.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);

        $this->validate(request(), [
            'group_id' => 'required',
        ]);

        $routeParam = request()->only('page', 'q');

        if (request('group_id') == $group->id && $group->delete()) {
            return redirect()->route('groups.index', $routeParam);
        }

        return back();
    }
}