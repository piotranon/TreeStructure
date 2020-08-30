<?php

namespace App\Http\Controllers;

use App\Node;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Exception;

class NodeController extends Controller
{
    public function getNode(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate(
            [
                'node_id' => 'required|numeric|exists:nodes,id',
            ]
        );

        $node = new Node();
        $node = $node->with('childs', 'parent')->where('owner_id', $user->id)->orderBy('order')->findOrFail($validated['node_id']);

        return response()->json($node, 200);
    }

    public function getForUser(Request $request)
    {
        $user = $request->user();

        // return response()->json($user, 200);

        $nodes = new Node();
        $nodes = $nodes->with('childs', 'parent')->whereNull('parent_id')->orderBy('order')->where('owner_id', $user->id)->get();

        return response()->json($nodes, 200);
    }
    //add node to database
    //if user specified parent id
    //add to parent
    //if user specified order
    //add at position move elements to right
    //if order not specified add at end
    public function storeNode(Request $request)
    {
        $validated = $request->validate(
            [
                'parent_id' => 'nullable|numeric|exists:nodes,id',
                'name' => 'required|string',
                'order' => 'nullable|numeric|min:1',
            ]
        );

        $newNode = new Node();
        $user = $request->user();

        // checking if user specified parent id
        // if yes search that parent node
        // check if user that made request is his owner
        if (null !== $validated['parent_id']) {
            $parentNode = new Node();
            $parentNode = $parentNode->with(['owner', 'childNodes'])->findOrFail($validated['parent_id']);

            // checking if user is owner of the parent node
            // if not error
            if ($parentNode->owner->id == $user->id) {
                $newNode->parent_id = $validated['parent_id'];
            } else {
                return response()->json(['errors' => ['parent_id' => ["You don't have rights to create Node for this Parent id."]], 'message' => "The given data was invalid."], 403);
            }

            // if order specified adding at position
            // else checking if parent node has subnodes
            // adding at position
            if (null !== $validated['order']) {
                if ($parentNode->childNodes->count() + 1 < $validated['order']) {
                    $validated['order'] = $parentNode->childNodes->count() + 1;
                }
                foreach ($parentNode->childNodes as $node) {
                    if ($node->order >= $validated['order']) {
                        $node->order = $node->order + 1;
                    }
                }
                $newNode->order = $validated['order'];
            } else {
                $order = $parentNode->childNodes->count() + 1;
                $newNode->order = $order;
            }
        } else {

            $mainNodes = new Node();
            $mainNodes = $mainNodes->whereNull('parent_id')->where('owner_id', $user->id)->get();

            // if order specified adding at position
            // else counting mainNodes (no parent, user is owner)
            // and adding at position
            if (null !== $validated['order']) {

                if ($mainNodes->count() + 1 < $validated['order']) {
                    $validated['order'] = $mainNodes->count() + 1;
                }
                foreach ($mainNodes as $node) {
                    if ($node->order >= $validated['order']) {
                        $node->order = $node->order + 1;
                        $node->save();
                    }
                }
                $newNode->order = $validated['order'];
            } else {
                $order = $mainNodes->count() + 1;
                $newNode->order = $order;
            }
        }

        $newNode->name = $validated['name'];
        $newNode->owner()->associate($user);
        $newNode->save();

        return response()->json($newNode, 201);
    }



    public function changeOrder(Request $request)
    {
        $validated = $request->validate(
            [
                'node_id' => 'required|numeric|exists:nodes,id',
                'orderAddValue' => 'required|numeric'
            ]
        );

        $user = $request->user();

        $updatedNode = new Node();
        $updatedNode = $updatedNode->with(['owner'])->findOrFail($validated['node_id']);

        // checking if user is owner of that node
        if ($updatedNode->owner->id == $user->id) {
            // checking if Node to change order has parent
            // if yes go to parent and check over his childs and change order over them
            // if no go to mainNode (get all for user where no parent) and change order over them
            $nodesToInterate = [];
            if (null !== $updatedNode->parent_id) {
                $parentNode = new Node();
                $parentNode = $parentNode->with(['owner', 'childNodes'])->findOrFail($updatedNode->parent_id);

                $nodesToInterate = $parentNode->childNodes;
            } else {
                $mainNodes = new Node();
                $mainNodes = $mainNodes->whereNull('parent_id')->where('owner_id', $user->id)->where('id', 'not like', $updatedNode->id)->get();

                $nodesToInterate = $mainNodes;
            }

            $newOrder = $updatedNode->order + $validated['orderAddValue'];

            if ($newOrder <= 0) {
                $newOrder = 1;
            }

            if ($newOrder > $nodesToInterate->count()) {
                $newOrder = $nodesToInterate->count();
            }


            // if orderAddValue positive move all elements in subtree
            // to left
            // [1,2,3]
            // we want to move 1st elem to 3rd place
            // [2,3,1]

            // else move to right
            // [1,2,3]
            // we want to move 3rd elem to 1st place
            // [3,1,2]

            foreach ($nodesToInterate as $node) {
                if ($validated['orderAddValue'] > 0) {
                    if ($node->order > $updatedNode->order && $node->order <= $newOrder) {
                        $node->order = $node->order - 1;
                        $node->save();
                    }
                } elseif ($validated['orderAddValue'] < 0) {
                    if ($node->order < $updatedNode->order  && $node->order >= $newOrder) {
                        $node->order = $node->order + 1;
                        $node->save();
                    }
                }
            }

            $updatedNode->order = $newOrder;

            $updatedNode->save();
            return response()->json(['updated' => $updatedNode], 201);
        } else {
            return response()->json(['errors' => ['parent_id' => ["You don't have rights to this Node."]], 'message' => "The given data was invalid."], 403);
        }
    }



    public function destroy(Request $request, $id)
    {
        $request['node_id'] = $id;

        $validated = $request->validate(
            [
                'node_id' => 'required|numeric|exists:nodes,id',
            ]
        );

        $deletednode = new Node();
        $deletednode = $deletednode->with(['owner', 'childNodes'])->findOrFail($validated['node_id']);

        $user = $request->user();

        if ($deletednode->owner->id == $user->id) {

            $nodesToInterate = [];
            if (null !== $deletednode->parent_id) {
                //parent exist
                $parentNode = new Node();
                $parentNode = $parentNode->with(['owner', 'childNodes'])->where('id', 'not like', $deletednode->id)->findOrFail($deletednode->parent_id);
                $nodesToInterate = $parentNode->childNodes;
            } else {
                $mainNodes = new Node();
                $mainNodes = $mainNodes->whereNull('parent_id')->where('owner_id', $user->id)->where('id', 'not like', $deletednode->id)->get();

                $nodesToInterate = $mainNodes;
            }
            foreach ($nodesToInterate as $node) {

                if ($node->order > $deletednode->order) {

                    $node->order = $node->order - 1;
                    $node->save();
                }
            }

            if ($this->destroyNodeWithSubTree($deletednode->id)) {
                return response()->json(['message' => "Node Destroyed.", 'node' => $deletednode], 200);
            } else {
                return response()->json(['message' => "Destoring a Node failed"], 403);
            }
        } else {
            return response()->json(['errors' => ['node_id' => ["You don't have rights to this Node."]], 'message' => "The given data was invalid."], 403);
        }
    }

    private function destroyNodeWithSubTree($node_id)
    {
        $node = new Node();
        $node = $node->with(['childNodes'])->findOrFail($node_id);

        if (null !== $node->childNodes) {
            foreach ($node->childNodes as $child) {
                if (!$this->destroyNodeWithSubTree($child->id))
                    return false;
            }
        }
        $node->delete();
        return true;
    }


    //if parent specified move object to parent
    //if order specified move to position others to right
    public function update(Request $request)
    {
        $validated = $request->validate(
            [
                'node_id' => 'numeric|exists:nodes,id',
                'parent_id' => 'nullable|numeric',
                'name' => 'required|string',
                'order' => 'nullable|numeric|min:1',
            ]
        );

        $updatedNode = new Node();
        $updatedNode = $updatedNode->with(['owner'])->findOrFail($validated['node_id']);

        $updatedNode->name = $validated['name'];

        // return response()->json(['message' => $updatedNode], 403);
        $user = $request->user();
        if ($updatedNode->owner->id == $user->id) {

            $nodesToInterate = [];

            if (0 !== $request['parent_id']) {
                $parentNode = new Node();
                $parentNode = $parentNode->with(['owner', 'childNodes'])->findOrFail($validated['parent_id']);

                $updatedNode->parent_id = $parentNode->id;

                $nodesToInterate = $parentNode->childNodes;
            } else {
                $mainNodes = new Node();
                $mainNodes = $mainNodes->whereNull('parent_id')->where('owner_id', $user->id)->where('id', 'not like', $updatedNode->id)->get();

                $updatedNode->parent_id = null;

                $nodesToInterate = $mainNodes;
            }

            $order = $nodesToInterate->count() + 1;

            if (null !== $request['order']) {
                if ($validated['order'] < $nodesToInterate->count() + 1)
                    $order = $validated['order'];
            }

            foreach ($nodesToInterate as $node) {
                if ($node->order >= $order) {
                    $node->order = $node->order + 1;
                    $node->save();
                }
            }

            $updatedNode->order = $order;
            $updatedNode->save();

            return response()->json(['message' => "Node Updated.", 'node' => $updatedNode], 201);
        } else {
            return response()->json(['errors' => ['node_id' => ["You don't have rights to this Node."]], 'message' => "The given data was invalid."], 403);
        }
    }

    public function getNameMapping(Request $request)
    {
        $user = $request->user();

        $nodes = new Node();
        $nodes = $nodes->where('owner_id', $user->id)->get(['id', 'name']);

        return response()->json([$nodes], 200);
    }
}
