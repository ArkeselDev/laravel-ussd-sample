<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UssdController extends Controller
{
    public function handleUssd(Request $request)
    {
        $sessionID = $request->input('sessionID');
        $userID = $request->input('userID');
        $newSession = $request->input('newSession');
        $msisdn = $request->input('msisdn');
        $userData = $request->input('userData');
        $network = $request->input('network');

        if ($newSession) {
            $message = "Welcome to Arkesel Voting Portal. Please vote for your favourite service from Arkesel" .
                "\n1. SMS" .
                "\n2. Voice" .
                "\n3. Email" .
                "\n4. USSD" .
                "\n5. Payments";
            $continueSession = true;

            // Keep track of the USSD state of the user and their session
            $currentState = [
                'sessionID' => $sessionID,
                'msisdn' => $msisdn,
                'userData' => $userData,
                'network'   => $network,
                'newSession' => $newSession,
                'message' => $message,
                'level' => 1,
                'page' => 1,
            ];

            $userResponseTracker = Cache::get($sessionID);

            !$userResponseTracker
                ? $userResponseTracker = [$currentState]
                : $userResponseTracker[] = $currentState;

            Cache::put($sessionID, $userResponseTracker, 120);

            return response()->json([
                'sessionID' => $sessionID,
                'msisdn' => $msisdn,
                'userID' => $userID,
                'continueSession' => $continueSession,
                'message' => $message,
            ]);
        }

        $userResponseTracker = Cache::get($sessionID) ?? [];

        if (!(count($userResponseTracker) > 0)) {
            return response()->json([
                'sessionID' => $sessionID,
                'msisdn' => $msisdn,
                'userID' => $userID,
                'continueSession' => false,
                'message' => 'Error! Please dial code again!',
            ]);
        }

        $lastResponse = $userResponseTracker[count($userResponseTracker) - 1];

        $message = "Bad Option";
        $continueSession = false;

        if ($lastResponse['level'] === 1) {
            if (in_array($userData, ["2", "3", "4", "5"])) {

                $message = "Thank you for voting!";
                $continueSession = false;
            } else if ($userData === '1') {
                $message = "For SMS which of the features do you like best?" .
                    "\n1. From File" .
                    "\n2. Quick SMS" .
                    "\n\n #. Next Page";

                $continueSession = true;

                $currentState = [
                    'sessionID' => $sessionID,
                    'msisdn' => $msisdn,
                    'userData' => $userData,
                    'network'   => $network,
                    'newSession' => $newSession,
                    'message' => $message,
                    'level' => 2,
                    'page' => 1,
                ];

                $userResponseTracker[] = $currentState;
                Cache::put($sessionID, $userResponseTracker, 120);
            }
        } else if ($lastResponse['level'] === 2) {
            if ($lastResponse['page'] === 1 && $userData === '#') {
                $message = "For SMS which of the features do you like best?" .
                    "\n3. Bulk SMS" .
                    "\n\n*. Go Back" .
                    "\n#. Next Page";

                $continueSession = true;

                $currentState = [
                    'sessionID' => $sessionID,
                    'msisdn' => $msisdn,
                    'userData' => $userData,
                    'network'   => $network,
                    'newSession' => $newSession,
                    'message' => $message,
                    'level' => 2,
                    'page' => 2,
                ];

                $userResponseTracker[] = $currentState;
                Cache::put($sessionID, $userResponseTracker, 120);
            } else if ($lastResponse['page'] === 2 && $userData === '#') {
                // Useful Resources
                $message = "For SMS which of the features do you like best?" .
                    "\n4. SMS To Contacts" .
                    "\n\n*. Go Back";

                $continueSession = true;

                $currentState = [
                    'sessionID' => $sessionID,
                    'msisdn' => $msisdn,
                    'userData' => $userData,
                    'network'   => $network,
                    'newSession' => $newSession,
                    'message' => $message,
                    'level' => 2,
                    'page' => 3,
                ];

                $userResponseTracker[] = $currentState;
                Cache::put($sessionID, $userResponseTracker, 120);
            } else if ($lastResponse['page'] === 3 && $userData === '*') {
                $message = "For SMS which of the features do you like best?" .
                    "\n3. Bulk SMS" .
                    "\n\n*. Go Back" .
                    "\n#. Next Page";

                $continueSession = true;

                $currentState = [
                    'sessionID' => $sessionID,
                    'msisdn' => $msisdn,
                    'userData' => $userData,
                    'network'   => $network,
                    'newSession' => $newSession,
                    'message' => $message,
                    'level' => 2,
                    'page' => 2,
                ];

                $userResponseTracker[] = $currentState;
                Cache::put($sessionID, $userResponseTracker, 120);
            } else if ($lastResponse['page'] === 2 && $userData === '*') {
                $message = "For SMS which of the features do you like best?" .
                    "\n1. From File" .
                    "\n2. Quick SMS" .
                    "\n\n #. Next Page";

                $continueSession = true;
                $currentState = [
                    'sessionID' => $sessionID,
                    'msisdn' => $msisdn,
                    'userData' => $userData,
                    'network'   => $network,
                    'newSession' => $newSession,
                    'message' => $message,
                    'level' => 2,
                    'page' => 1,
                ];

                $userResponseTracker[] = $currentState;
                Cache::put($sessionID, $userResponseTracker, 120);
            } else if (in_array($userData, ["1", "2", "3", "4"])) {
                $message = "Thank you for voting!";
                $continueSession = false;
            } else {
                $message = "Bad choice!";
                $continueSession = false;
            }
        }

        return response()->json([
            'sessionID' => $sessionID,
            'msisdn' => $msisdn,
            'userID' => $userID,
            'continueSession' => $continueSession,
            'message' => $message,
        ]);
    }
}
