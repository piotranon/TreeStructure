<?php

namespace App\Http\Controllers;

use App\Node;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Exception;

class NodeController extends Controller
{
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
        if (null !== $request->get('parent_id')) {
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
            // adding at end
            if (null !== $request->get('order')) {
                // user specified order
                // checking if order is already in table
                $orderArray = [];
                foreach ($parentNode->childNodes as $node) {
                    array_push($orderArray, $node->order);
                }

                //checking if order is already in childNodes
                //if is add at end if not add at position
                if (in_array($validated['order'], $orderArray)) {
                    $order = $parentNode->childNodes->count() + 1;
                    $newNode->order = $order;
                } else {
                    $newNode->order = $validated['order'];
                }
            } else {
                $order = count($parentNode->childNodes) + 1;
                $newNode->order = $order;
            }
        } else {

            $mainNodes = new Node();
            $mainNodes = $mainNodes->whereNull('parent_id')->where('owner_id', $user->id)->get();

            // if order specified adding at position
            // else counting mainNodes (no parent, user is owner)
            // and adding at position
            if (null !== $request->get('order')) {
                $orderArray = [];
                foreach ($mainNodes as $node) {
                    array_push($orderArray, $node->order);
                }

                if (in_array($validated['order'], $orderArray)) {
                    $order = $mainNodes->count() + 1;
                    $newNode->order = $order;
                } else {
                    $newNode->order = $validated['order'];
                }
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
            $newOrder = $updatedNode->order + $validated['orderAddValue'];

            if ($newOrder <= 0) {
                $newOrder = 1;
            }

            // checking if Node to change order has parent
            // if yes go to parent and check over his childs and change order over them
            // if no go to mainNode (get all for user where no parent) and change order over them
            if (null !== $updatedNode->parent_id) {
                $parentNode = new Node();
                $parentNode = $parentNode->with(['owner', 'childNodes'])->findOrFail($updatedNode->parent_id);

                if ($newOrder > $parentNode->childNodes->count() + 1) {
                    $newOrder = $parentNode->childNodes->count();
                }

                foreach ($parentNode->childNodes as $node) {
                    // if orderAddValue positive move all elements in subtree
                    // to left
                    // [1,2,3]
                    // we want to move 1st elem to 3rd place
                    // [2,3,1]

                    // else move to right
                    // [1,2,3]
                    // we want to move 3rd elem to 1st place
                    // [3,1,2]

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
            } else {
                $mainNodes = new Node();
                $mainNodes = $mainNodes->whereNull('parent_id')->where('owner_id', $user->id)->where('id', 'not like', $updatedNode->id)->get();

                if ($newOrder > $mainNodes->count() + 1) {
                    $newOrder = $mainNodes->count() + 1;
                }
                foreach ($mainNodes as $node) {
                    // if orderAddValue positive move all elements in subtree
                    // to left
                    // [1,2,3]
                    // we want to move 1st elem to 3rd place
                    // [2,3,1]

                    // else move to right
                    // [1,2,3]
                    // we want to move 3rd elem to 1st place
                    // [3,1,2]

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
            }
            $updatedNode->save();
            return response()->json(['updated' => $updatedNode], 201);
        } else {
            return response()->json(['errors' => ['parent_id' => ["You don't have rights to this Node."]], 'message' => "The given data was invalid."], 403);
        }
    }



    public function destroy(Request $request)
    {
        $validated = $request->validate(
            [
                'node_id' => 'numeric|exists:nodes,id',
            ]
        );

        $deletednode = new Node();
        $deletednode = $deletednode->with(['owner'])->findOrFail($validated['node_id']);

        $user = $request->user();

        if ($deletednode->owner->id == $user->id) {

            if (null !== $deletednode->parent_id) {
                //parent exist
                $parentNode = new Node();
                $parentNode = $parentNode->with(['owner', 'childNodes'])->where('id', 'not like', $deletednode->id)->findOrFail($deletednode->parent_id);

                foreach ($parentNode->childNodes as $node) {
                    if ($node->order > $deletednode->order) {
                        $node->order = $node->order - 1;
                        $node->save();
                    }
                }
            }

            $node->delete();
            return response()->json(['message' => "Node Destroyed.", 'node' => $deletednode], 403);
        } else {
            return response()->json(['errors' => ['node_id' => ["You don't have rights to this Node."]], 'message' => "The given data was invalid."], 403);
        }
    }



    public function update(Request $request)
    {
        $validated = $request->validate(
            [
                'node_id' => 'numeric|exists:nodes,id',
                'parent_id' => 'nullable|numeric|exists:nodes,id',
                'name' => 'nullable|string',
                'order' => 'nullable|numeric|min:1',
            ]
        );

        $updatedNode = new Node();
        $updatedNode = $updatedNode->with(['owner'])->findOrFail($validated['node_id']);

        $user = $request->user();

        if ($updatedNode->owner->id == $user->id) {
            if (null !== $validated['parent_id']) {
                $parentNode = new Node();
                $parentNode = $parentNode->with(['owner', 'childNodes'])->findOrFail($updatedNode->parent_id);

                if ($parentNode->owner->id == $user->id) {

                    if (null !== $validated['order']) {
                    } else {
                        $parentNode->childNodes->count() + 1;
                    }

                    $updatedNode->parent_id = $validated['parent_id'];
                } else {
                    return response()->json(['errors' => ['parent_id' => ["You don't have rights to this Node."]], 'message' => "The given data was invalid."], 403);
                }
            }
            if (null !== $validated['name']) {
                $updatedNode->name = $validated['name'];
            }

            //if user specified order
            //change order of other elements so item can fit that spot

            if (null !== $validated['order']) {
                if (null !== $updatedNode->parent_id) {
                    $parentNode = new Node();
                    $parentNode = $parentNode->with(['owner', 'childNodes'])->findOrFail($updatedNode->parent_id);

                    if ($parentNode->owner->id == $user->id) {

                        $differenceInOrder = $updatedNode->order - $validated['order'];

                        if ($validated['order'] > $parentNode->childNodes->count() + 1) {
                            $validated['order'] = $parentNode->childNodes->count() + 1;
                        }
                        foreach ($parentNode->childNodes as $node) {
                            if ($differenceInOrder > 0) {
                                if ($node->order > $updatedNode->order && $node->order <= $validated['order']) {
                                    $node->order = $node->order - 1;
                                    $node->save();
                                }
                            } elseif ($differenceInOrder < 0) {
                                if ($node->order < $updatedNode->order  && $node->order >= $validated['order']) {
                                    $node->order = $node->order + 1;
                                    $node->save();
                                }
                            }
                        }

                        $updatedNode->order = $validated['order'];
                    } else {
                        return response()->json(['errors' => ['parent_id' => ["You don't have rights to this Node."]], 'message' => "The given data was invalid."], 403);
                    }
                } else {
                    //no parent id

                    $mainNodes = new Node();
                    $mainNodes = $mainNodes->whereNull('parent_id')->where('owner_id', $user->id)->where('id', 'not like', $updatedNode->id)->get();

                    $differenceInOrder = $updatedNode->order - $validated['order'];

                    if ($validated['order'] > $mainNodes->count() + 1) {
                        $validated['order'] = $mainNodes->count() + 1;
                    }
                    foreach ($mainNodes as $node) {
                        if ($differenceInOrder > 0) {
                            if ($node->order > $updatedNode->order && $node->order <= $validated['order']) {
                                $node->order = $node->order - 1;
                                $node->save();
                            }
                        } elseif ($differenceInOrder < 0) {
                            if ($node->order < $updatedNode->order  && $node->order >= $validated['order']) {
                                $node->order = $node->order + 1;
                                $node->save();
                            }
                        }
                    }

                    $updatedNode->order = $validated['order'];
                }
            }

            $node->save();

            return response()->json(['message' => "Node Updated.", 'node' => $node], 403);
        } else {
            return response()->json(['errors' => ['node_id' => ["You don't have rights to this Node."]], 'message' => "The given data was invalid."], 403);
        }
    }
}
