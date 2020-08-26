<?php

namespace App\Http\Controllers;

use App\Node;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
Use Exception; 

class NodeController extends Controller
{
    public function storeNode(Request $request)
    {
        // return response()->json(['validated:data' =>null!==$request->get('order')],201);
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
        if(null !== $request->get('parent_id'))
        {   
            $parentNode = new Node();
            $parentNode = $parentNode->with(['owner','childNodes'])->findOrFail($validated['parent_id']);
            
            // return response()->json(['data'=>$parentNode],403);
            // checking if user is owner of the parent node
            // if not error
            // return response()->json($parentNode,403);
            if($parentNode->owner->id==$user->id)
            {
                $newNode->parent_id = $validated['parent_id'];
            }else
            {
                return response()->json(['errors'=>['parent_id'=>["You don't have rights to create Node for this Parent id."]],'message'=>"The given data was invalid."],403);
            }

            // if order specified adding at position
            // else checking if parent node has subnodes
            // adding at end
            if(null !== $request->get('order'))
            {
                // user specified order
                $newNode->order = $validated['order'];
            }else
            {
                // return response()->json(['data'=>count*$parentNode->childNodes],403);
                $order = count($parentNode->childNodes)+1;
                $newNode->order = $order;
            }

        }else
        {
            // if order specified adding at position
            // else counting mainNodes (no parent, user is owner)
            // and adding at position
            if(null !== $request->get('order'))
            {
                // user specified order
                $newNode->order = $validated['order'];
            }else
            {
                $mainNodes = Node::whereNull('parent_id')->where('owner_id',$user->id);
                $order = $mainNodes->count()+1;
                $newNode->order = $order;
            }
        }
        
        $newNode->name = $validated['name'];
        $newNode->owner()->associate($user);
        $newNode->save();
        
        return response()->json($newNode,201);
    }
}
