<?php

namespace App\Http\Controllers;

use App\Models\Choice;
use App\Models\Poll;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PollController extends Controller
{
    public function addPoll(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'deadline' => 'required|date',
            'choices.*' => 'required|distinct|min:2',
        ]);

        if($validate->fails()){
            return response()->json([
                'message' => $validate->errors()
            ], 422);
        }

        $poll = new Poll();
        $poll -> user_id = Auth::id();
        $poll -> title = $request->title;
        $poll -> description = $request->description;
        $poll -> deadline = $request->deadline;
        $poll -> created_by = auth()->id();
        $poll -> save();

        if($poll){
            foreach($request->choices as $item){
                $choice = new Choice();
                $choice -> choices = $item;
                $choice -> poll_id = $poll->id;
                $choice -> save();
            }
            return response()->json([
                'message' => 'data added successfully',
                'data' => $poll->with('choices')->latest()->first(),
            ], 200);
        }else{
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
    }

    public function getPoll($poll_id)
    {
        $poll = Poll::where('id', $poll_id)->first();
        if ($poll) {
            $poll->creator = $poll->user->username;
            $voted = Vote::where(['user_id' => Auth::id(), 'poll_id' => $poll->id])->first();
            foreach ($poll->choices as $choice) {
                if (auth()->user()->role == 'admin' || $poll->deadline < Carbon::now() || $voted) {
                    if ($poll->votes->count() > 0) {
                        $percentage = Vote::where('choice_id', $choice->id)->count() / Vote::where('poll_id', $poll_id)->count() * 100;
                    } else {
                        $percentage = null;
                    }
                    $choice->percentage = $percentage;
                } else {
                    $choice->percentage = null;
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'Poll',
                'data' => $poll
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid poll ID'
            ], 422);
        }
    }

    public function getAllPoll()
    {
        $polls = Poll::latest()->get();
        $data = array();
        foreach ($polls as $poll){
            $voted = Vote::where(['user_id' => Auth::id(), 'poll_id' => $poll->id])->first();
            foreach ($poll->choices as $choice){
                if(auth()->user()->role == 'admin' || $poll->deadline < Carbon::now() || $voted){
                    if($poll->votes->count() > 0){
                        $percentage = Vote::where('choice_id', $choice->id)->count() / Vote::where('poll_id', $poll->id)->count() * 100;
                    }else{
                        $percentage = null;
                    }
                    $choice->percentage = $percentage;
                    $poll->total_vote = Vote::where('poll_id', $poll->id)->count();
                }else{
                    $choice->percentage = null;
                    $poll->total_vote = null;
                }
            }
            unset($poll->votes);
            array_push($data, $poll);
        }

        return response()->json([
            'success' => true,
            'message' => 'Polls',
            'data' => $data,
        ], 200);
    }

    public function deletePoll($poll_id)
    {
        $polls = Poll::where('id', $poll_id);
        if(auth()->user()->role == 'admin'){
            if($polls->delete()){

                return response()->json([
                    'message' => 'Successfully deleted'
                ], 200);
            }
        }
        return response()->json(['message' => 'Something wrong'], 401);
    }

    public function vote($poll_id, $choice_id)
    {
        if(auth()->user()->role === 'admin'){
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
        $poll = Poll::where('id', $poll_id)->first();
        $choice = Choice::where('id', $choice_id)->first();
        if($poll && $choice){
            $voted = Vote::where(['user_id' => Auth::id(), 'poll_id' => $poll_id])->first();
            if($voted){
                return response()->json([
                    'message' => 'already voted'
                ], 422);
            }elseif($poll->deadline > Carbon::now()){
                return response()->json([
                    'message' => 'voting deadline',
                ], 422);
            }
            $vote = new Vote();
            $vote -> choice_id = $choice_id;
            $vote -> user_id = Auth::id();
            $vote -> poll_id = $poll_id;
            $vote -> division_id = auth()->user()->division_id;
            $vote->save();

            return response()->json([
                'message' => 'Voting success'
            ], 200);
        }else{
            return response()->json([
                'message' => 'invalid choice'
            ], 422);
        }
    }
}
